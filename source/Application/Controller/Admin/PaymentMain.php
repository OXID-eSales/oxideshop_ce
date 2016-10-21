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
use oxField;

/**
 * Admin article main payment manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Payment Methods -> Main.
 */
class PaymentMain extends \oxAdminDetails
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
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oPayment = oxNew("oxpayment");
            $oPayment->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oPayment->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oPayment->loadInLang(key($oOtherLang), $soxId);
            }
            $this->_aViewData["edit"] = $oPayment;

            // remove already created languages
            $aLang = array_diff(oxRegistry::getLang()->getLanguageNames(), $oOtherLang);
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
            $this->_aViewData['aFieldNames'] = oxRegistry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);
        }

        if (oxRegistry::getConfig()->getRequestParameter("aoc")) {
            $oPaymentMainAjax = oxNew('payment_main_ajax');
            $this->_aViewData['oxajax'] = $oPaymentMainAjax->getColumns();

            return "popups/payment_main.tpl";
        }

        $this->_aViewData["editor"] = $this->_generateTextEditor("100%", 300, $oPayment, "oxpayments__oxlongdesc");

        return "payment_main.tpl";
    }

    /**
     * Saves payment parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxpayments__oxactive'])) {
            $aParams['oxpayments__oxactive'] = 0;
        }
        if (!isset($aParams['oxpayments__oxchecked'])) {
            $aParams['oxpayments__oxchecked'] = 0;
        }

        $oPayment = oxNew("oxpayment");

        if ($soxId != "-1") {
            $oPayment->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxpayments__oxid'] = null;
            //$aParams = $oPayment->ConvertNameArray2Idx( $aParams);
        }

        $oPayment->setLanguage(0);
        $oPayment->assign($aParams);

        // setting add sum calculation rules
        $aRules = (array) oxRegistry::getConfig()->getRequestParameter("oxpayments__oxaddsumrules");
        // if sum eqals 0, show notice, that default value will be used.
        if (empty($aRules)) {
            $this->_aViewData["noticeoxaddsumrules"] = 1;
        }
        $oPayment->oxpayments__oxaddsumrules = new oxField(array_sum($aRules));


        //#708
        if (!is_array($this->_aFieldArray)) {
            $this->_aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);
        }

        // build value
        $sValdesc = "";
        foreach ($this->_aFieldArray as $oField) {
            $sValdesc .= $oField->name . "__@@";
        }

        $oPayment->oxpayments__oxvaldesc = new oxField($sValdesc, oxField::T_RAW);
        $oPayment->setLanguage($this->_iEditLang);
        $oPayment->save();

        // set oxid if inserted
        $this->setEditObjectId($oPayment->getId());
    }

    /**
     * Saves payment parameters data in dofferent language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oObj = oxNew("oxpayment");

        if ($soxId != "-1") {
            $oObj->loadInLang($this->_iEditLang, $soxId);
        } else {
            $aParams['oxpayments__oxid'] = null;
            //$aParams = $oObj->ConvertNameArray2Idx( $aParams);
        }

        $oObj->setLanguage(0);
        $oObj->assign($aParams);

        // apply new language
        $oObj->setLanguage(oxRegistry::getConfig()->getRequestParameter("new_lang"));
        $oObj->save();

        // set oxid if inserted
        $this->setEditObjectId($oObj->getId());
    }

    /**
     * Deletes field from field array and stores object
     *
     * @return null
     */
    public function delFields()
    {
        $oPayment = oxNew("oxpayment");
        if ($oPayment->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            $aDelFields = oxRegistry::getConfig()->getRequestParameter("aFields");
            $this->_aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);

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
     *
     * @return null
     */
    public function addField()
    {
        $oPayment = oxNew("oxpayment");
        if ($oPayment->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            $this->_aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oPayment->oxpayments__oxvaldesc->value);

            $oField = new stdClass();
            $oField->name = oxRegistry::getConfig()->getRequestParameter("sAddField");

            if (!empty($oField->name)) {
                $this->_aFieldArray[] = $oField;
            }
            $this->save();
        }
    }
}
