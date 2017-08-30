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

/**
 * Admin user address setting manager.
 * Collects user address settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Addresses.
 */
class UserAddress extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * If true, means that address was deleted
     *
     * @var bool
     */
    protected $_blDelete = false;

    /**
     * Executes parent method parent::render(), creates oxuser and oxbase objects,
     * passes data to Smarty engine and returns name of template file
     * "user_address.tpl".
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

            // load adress
            $sAddressIdParameter = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxaddressid");
            $soxAddressId = isset($this->sSavedOxid) ? $this->sSavedOxid : $sAddressIdParameter;
            if ($soxAddressId != "-1" && isset($soxAddressId)) {
                $oAdress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
                $oAdress->load($soxAddressId);
                $this->_aViewData["edit"] = $oAdress;
            }

            $this->_aViewData["oxaddressid"] = $soxAddressId;

            // generate selected
            $oAddressList = $oUser->getUserAddresses();
            foreach ($oAddressList as $oAddress) {
                if ($oAddress->oxaddress__oxid->value == $soxAddressId) {
                    $oAddress->selected = 1;
                    break;
                }
            }

            $this->_aViewData["edituser"] = $oUser;
        }

        $oCountryList = oxNew(\OxidEsales\Eshop\Application\Model\CountryList::class);
        $oCountryList->loadActiveCountries(\OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage());

        $this->_aViewData["countrylist"] = $oCountryList;

        if (!$this->_allowAdminEdit($soxId)) {
            $this->_aViewData['readonly'] = true;
        }

        return "user_address.tpl";
    }

    /**
     * Saves user addressing information.
     */
    public function save()
    {
        parent::save();

        if ($this->_allowAdminEdit($this->getEditObjectId())) {
            $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
            $oAdress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
            if (isset($aParams['oxaddress__oxid']) && $aParams['oxaddress__oxid'] == "-1") {
                $aParams['oxaddress__oxid'] = null;
            } else {
                $oAdress->load($aParams['oxaddress__oxid']);
            }

            $oAdress->assign($aParams);
            $oAdress->save();

            $this->sSavedOxid = $oAdress->getId();
        }
    }

    /**
     * Deletes user addressing information.
     */
    public function delAddress()
    {
        $this->_blDelete = false;
        if ($this->_allowAdminEdit($this->getEditObjectId())) {
            $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
            if (isset($aParams['oxaddress__oxid']) && $aParams['oxaddress__oxid'] != "-1") {
                $oAdress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
                $this->_blDelete = $oAdress->delete($aParams['oxaddress__oxid']);
            }
        }
    }
}
