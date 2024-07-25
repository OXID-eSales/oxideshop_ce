<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use stdClass;

/**
 * Admin article main payment manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Payment Methods -> Main.
 */
class PaymentCountry extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        // remove itm from list
        unset($this->_aViewData["sumtype"][2]);

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
            $oPayment->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oPayment->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oPayment->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oPayment;

            // remove already created languages
            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if (Registry::getRequest()->getRequestEscapedParameter("aoc")) {
            $oPaymentCountryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\PaymentCountryAjax::class);
            $this->_aViewData['oxajax'] = $oPaymentCountryAjax->getColumns();

            return "popups/payment_country";
        }

        return "payment_country";
    }

    /**
     * Adds chosen user group (groups) to delivery list
     */
    public function addcountry()
    {
        $sOxId = $this->getEditObjectId();
        $aChosenCntr = Registry::getRequest()->getRequestEscapedParameter("allcountries");
        if (isset($sOxId) && $sOxId != "-1" && is_array($aChosenCntr)) {
            foreach ($aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Payment->init('oxobject2payment');
                $oObject2Payment->oxobject2payment__oxpaymentid = new \OxidEsales\Eshop\Core\Field($sOxId);
                $oObject2Payment->oxobject2payment__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenCntr);
                $oObject2Payment->oxobject2payment__oxtype = new \OxidEsales\Eshop\Core\Field("oxcountry");
                $oObject2Payment->save();
            }
        }
    }

    /**
     * Removes chosen user group (groups) from delivery list
     */
    public function removecountry()
    {
        $sOxId = $this->getEditObjectId();
        $aChosenCntr = Registry::getRequest()->getRequestEscapedParameter("countries");
        if (isset($sOxId) && $sOxId != "-1" && is_array($aChosenCntr)) {
            foreach ($aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Payment->init('oxobject2payment');
                $oObject2Payment->delete($sChosenCntr);
            }
        }
    }
}
