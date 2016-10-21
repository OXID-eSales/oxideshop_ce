<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;
use Exception;

/**
 * Admin article main user manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: User Administration -> Users -> Main.
 */
class UserMain extends \oxAdminDetails
{

    private $_sSaveError = null;

    /**
     * Executes parent method parent::render(), creates oxuser, oxshops and oxlist
     * objects, passes data to Smarty engine and returns name of template
     * file "user_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        // malladmin stuff
        $oAuthUser = oxNew('oxuser');
        $oAuthUser->loadAdminUser();
        $blisMallAdmin = $oAuthUser->oxuser__oxrights->value == "malladmin";

        // User rights
        $aUserRights = array();
        $oLang = oxRegistry::getLang();
        $iTplLang = $oLang->getTplLanguage();

        $iPos = count($aUserRights);
        $aUserRights[$iPos] = new stdClass();
        $aUserRights[$iPos]->name = $oLang->translateString("user", $iTplLang);
        $aUserRights[$iPos]->id = "user";

        if ($blisMallAdmin) {
            $iPos = count($aUserRights);
            $aUserRights[$iPos] = new stdClass();
            $aUserRights[$iPos]->id = "malladmin";
            $aUserRights[$iPos]->name = $oLang->translateString("Admin", $iTplLang);
        }

        $aUserRights = $this->calculateAdditionalRights($aUserRights);

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oUser = oxNew("oxuser");
            $oUser->load($soxId);
            $this->_aViewData["edit"] = $oUser;

            if (!($oUser->oxuser__oxrights->value == "malladmin" && !$blisMallAdmin)) {
                // generate selected right
                reset($aUserRights);
                while (list(, $val) = each($aUserRights)) {
                    if ($val->id == $oUser->oxuser__oxrights->value) {
                        $val->selected = 1;
                        break;
                    }
                }
            }
        }

        // passing country list
        $oCountryList = oxNew("oxCountryList");
        $oCountryList->loadActiveCountries($oLang->getObjectTplLanguage());

        $this->_aViewData["countrylist"] = $oCountryList;

        $this->_aViewData["rights"] = $aUserRights;

        if ($this->_sSaveError) {
            $this->_aViewData["sSaveError"] = $this->_sSaveError;
        }

        if (!$this->_allowAdminEdit($soxId)) {
            $this->_aViewData['readonly'] = true;
        }
        if (oxRegistry::getConfig()->getRequestParameter("aoc")) {
            $oUserMainAjax = oxNew('user_main_ajax');
            $this->_aViewData['oxajax'] = $oUserMainAjax->getColumns();

            return "popups/user_main.tpl";
        }

        return "user_main.tpl";
    }

    /**
     * Saves main user parameters.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        //allow admin information edit only for MALL admins
        $soxId = $this->getEditObjectId();
        if ($this->_allowAdminEdit($soxId)) {

            $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

            // checkbox handling
            if (!isset($aParams['oxuser__oxactive'])) {
                $aParams['oxuser__oxactive'] = 0;
            }

            $oUser = oxNew("oxuser");
            if ($soxId != "-1") {
                $oUser->load($soxId);
            } else {
                $aParams['oxuser__oxid'] = null;
            }

            //setting new password
            if (($sNewPass = oxRegistry::getConfig()->getRequestParameter("newPassword"))) {
                $oUser->setPassword($sNewPass);
            }

            //FS#2167 V checks for already used email
            if ($oUser->checkIfEmailExists($aParams['oxuser__oxusername'])) {
                $this->_sSaveError = 'EXCEPTION_USER_USEREXISTS';

                return;
            }

            $oUser->assign($aParams);

            //seting shop id for ONLY for new created user
            if ($soxId == "-1") {
                $this->onUserCreation($oUser);
            }

            // A. changing field type to save birth date correctly
            $oUser->oxuser__oxbirthdate->fldtype = 'char';

            try {
                $oUser->save();

                // set oxid if inserted
                $this->setEditObjectId($oUser->getId());
            } catch (Exception $oExcp) {
                $this->_sSaveError = $oExcp->getMessage();
            }
        }
    }

    /**
     * If we need to add more rights / modify current rights by any conditions.
     *
     * @param array $userRights
     *
     * @return array
     */
    protected function calculateAdditionalRights($userRights)
    {
        return $userRights;
    }

    /**
     * Additional actions on user creation.
     *
     * @param oxUser $user
     */
    protected function onUserCreation($user)
    {
        return $user;
    }
}
