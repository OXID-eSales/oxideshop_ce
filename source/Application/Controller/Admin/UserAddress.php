<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

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

    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->load($soxId);

            // load adress
            $sAddressIdParameter = Registry::getRequest()->getRequestEscapedParameter("oxaddressid");
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

        if (!$this->allowAdminEdit($soxId)) {
            $this->_aViewData['readonly'] = true;
        }

        return "user_address";
    }

    /**
     * Saves user addressing information.
     */
    public function save()
    {
        parent::save();

        if ($this->allowAdminEdit($this->getEditObjectId())) {
            $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");
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
        if ($this->allowAdminEdit($this->getEditObjectId())) {
            $aParams = Registry::getRequest()->getRequestEscapedParameter("editval");
            if (isset($aParams['oxaddress__oxid']) && $aParams['oxaddress__oxid'] != "-1") {
                $oAdress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
                $this->_blDelete = $oAdress->delete($aParams['oxaddress__oxid']);
            }
        }
    }
}
