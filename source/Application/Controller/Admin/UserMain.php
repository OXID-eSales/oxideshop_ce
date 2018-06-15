<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
class UserMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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
        $oAuthUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oAuthUser->loadAdminUser();
        $blisMallAdmin = $oAuthUser->oxuser__oxrights->value == "malladmin";

        // User rights
        $aUserRights = [];
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
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
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->load($soxId);
            $this->_aViewData["edit"] = $oUser;

            if (!($oUser->oxuser__oxrights->value == "malladmin" && !$blisMallAdmin)) {
                // generate selected right
                reset($aUserRights);
                foreach ($aUserRights as $val) {
                    if ($val->id == $oUser->oxuser__oxrights->value) {
                        $val->selected = 1;
                        break;
                    }
                }
            }
        }

        // passing country list
        $oCountryList = oxNew(\OxidEsales\Eshop\Application\Model\CountryList::class);
        $oCountryList->loadActiveCountries($oLang->getObjectTplLanguage());

        $this->_aViewData["countrylist"] = $oCountryList;

        $this->_aViewData["rights"] = $aUserRights;

        if ($this->_sSaveError) {
            $this->_aViewData["sSaveError"] = $this->_sSaveError;
        }

        if (!$this->_allowAdminEdit($soxId)) {
            $this->_aViewData['readonly'] = true;
        }
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oUserMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\UserMainAjax::class);
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
            $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

            // checkbox handling
            if (!isset($aParams['oxuser__oxactive'])) {
                $aParams['oxuser__oxactive'] = 0;
            }

            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            if ($soxId != "-1") {
                $oUser->load($soxId);
            } else {
                $aParams['oxuser__oxid'] = null;
            }

            //setting new password
            if (($sNewPass = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("newPassword"))) {
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
     * @param \OxidEsales\Eshop\Application\Model\User $user
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    protected function onUserCreation($user)
    {
        return $user;
    }
}
