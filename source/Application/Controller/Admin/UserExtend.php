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
use oxField;

/**
 * Admin user extended settings manager.
 * Collects user extended settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Extended.
 */
class UserExtend extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Executes parent method parent::render(), creates oxuser object and
     * returns name of template file "user_extend.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->load($soxId);

            //show country in active language
            $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
            $oCountry->loadInLang(\OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage(), $oUser->oxuser__oxcountryid->value);
            $oUser->oxuser__oxcountry = new \OxidEsales\Eshop\Core\Field($oCountry->oxcountry__oxtitle->value);

            $this->_aViewData["edit"] = $oUser;
        }

        if (!$this->_allowAdminEdit($soxId)) {
            $this->_aViewData['readonly'] = true;
        }

        return "user_extend.tpl";
    }

    /**
     * Saves user extended information.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();

        if (!$this->_allowAdminEdit($soxId)) {
            return false;
        }

        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($soxId != "-1") {
            $oUser->load($soxId);
        } else {
            $aParams['oxuser__oxid'] = null;
        }

        // checkbox handling
        $aParams['oxuser__oxactive'] = $oUser->oxuser__oxactive->value;

        $blNewsParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editnews");
        if (isset($blNewsParams)) {
            $oNewsSubscription = $oUser->getNewsSubscription();
            $oNewsSubscription->setOptInStatus((int) $blNewsParams);
            $oNewsSubscription->setOptInEmailStatus((int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("emailfailed"));
        }

        $oUser->assign($aParams);
        $oUser->save();

        // set oxid if inserted
        $this->setEditObjectId($oUser->getId());
    }
}
