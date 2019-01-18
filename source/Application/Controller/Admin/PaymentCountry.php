<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use stdClass;
use oxField;

/**
 * Admin article main payment manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Payment Methods -> Main.
 */
class PaymentCountry extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxlist object,
     * passes it's data to Smarty engine and retutns name of template
     * file "payment_main.tpl".
     *
     * @return string
     */
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
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
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

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oPaymentCountryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\PaymentCountryAjax::class);
            $this->_aViewData['oxajax'] = $oPaymentCountryAjax->getColumns();

            return "popups/payment_country.tpl";
        }

        return "payment_country.tpl";
    }

    /**
     * Adds chosen user group (groups) to delivery list
     */
    public function addcountry()
    {
        $sOxId = $this->getEditObjectId();
        $aChosenCntr = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("allcountries");
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
        $aChosenCntr = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("countries");
        if (isset($sOxId) && $sOxId != "-1" && is_array($aChosenCntr)) {
            foreach ($aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Payment->init('oxobject2payment');
                $oObject2Payment->delete($sChosenCntr);
            }
        }
    }
}
