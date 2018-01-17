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
class PaymentMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Keeps all act. fields to store
     */
    protected $_aFieldArray = null;

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
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

        if (isset($soxId) && $soxId != "-1") {
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

            // #708
            $this->_aViewData['aFieldNames'] = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);
        }

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oPaymentMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\PaymentMainAjax::class);
            $this->_aViewData['oxajax'] = $oPaymentMainAjax->getColumns();

            return "popups/payment_main.tpl";
        }

        $this->_aViewData["editor"] = $this->_generateTextEditor("100%", 300, $oPayment, "oxpayments__oxlongdesc");

        return "payment_main.tpl";
    }

    /**
     * Saves payment parameters changes.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxpayments__oxactive'])) {
            $aParams['oxpayments__oxactive'] = 0;
        }
        if (!isset($aParams['oxpayments__oxchecked'])) {
            $aParams['oxpayments__oxchecked'] = 0;
        }

        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

        if ($soxId != "-1") {
            $oPayment->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxpayments__oxid'] = null;
            //$aParams = $oPayment->ConvertNameArray2Idx( $aParams);
        }

        $oPayment->setLanguage(0);
        $oPayment->assign($aParams);

        // setting add sum calculation rules
        $aRules = (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxpayments__oxaddsumrules");
        // if sum eqals 0, show notice, that default value will be used.
        if (empty($aRules)) {
            $this->_aViewData["noticeoxaddsumrules"] = 1;
        }
        $oPayment->oxpayments__oxaddsumrules = new \OxidEsales\Eshop\Core\Field(array_sum($aRules));


        //#708
        if (!is_array($this->_aFieldArray)) {
            $this->_aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);
        }

        // build value
        $sValdesc = "";
        foreach ($this->_aFieldArray as $oField) {
            $sValdesc .= $oField->name . "__@@";
        }

        $oPayment->oxpayments__oxvaldesc = new \OxidEsales\Eshop\Core\Field($sValdesc, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oPayment->setLanguage($this->_iEditLang);
        $oPayment->save();

        // set oxid if inserted
        $this->setEditObjectId($oPayment->getId());
    }

    /**
     * Saves payment parameters data in dofferent language (eg. english).
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oObj = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

        if ($soxId != "-1") {
            $oObj->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxpayments__oxid'] = null;
            //$aParams = $oObj->ConvertNameArray2Idx( $aParams);
        }

        $oObj->setLanguage(0);
        $oObj->assign($aParams);

        // apply new language
        $oObj->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang"));
        $oObj->save();

        // set oxid if inserted
        $this->setEditObjectId($oObj->getId());
    }

    /**
     * Deletes field from field array and stores object
     */
    public function delFields()
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            $aDelFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aFields");
            $this->_aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);

            if (is_array($aDelFields) && count($aDelFields)) {
                foreach ($aDelFields as $sDelField) {
                    foreach ($this->_aFieldArray as $sKey => $oField) {
                        if ($oField->name == $sDelField) {
                            unset($this->_aFieldArray[$sKey]);
                            break;
                        }
                    }
                }
                $this->save();
            }
        }
    }

    /**
     * Adds a field to field array and stores object
     */
    public function addField()
    {
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
        if ($oPayment->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            $this->_aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);

            $oField = new stdClass();
            $oField->name = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddField");

            if (!empty($oField->name)) {
                $this->_aFieldArray[] = $oField;
            }
            $this->save();
        }
    }
}
