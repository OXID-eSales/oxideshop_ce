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
use stdClass;

DEFINE("ERR_SUCCESS", 1);
DEFINE("ERR_REQUIREDMISSING", -1);
DEFINE("ERR_POSOUTOFBOUNDS", -2);

/**
 * Admin article main selectlist manager.
 * Performs collection and updatind (on user submit) main item information.
 */
class SelectListMain extends \oxAdminDetails
{
    /**
     * Keeps all act. fields to store
     */
    public $aFieldArray = null;

    /**
     * Executes parent method parent::render(), creates oxCategoryList object,
     * passes it's data to Smarty engine and returns name of template file
     * "selectlist_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sOxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        $sArticleTable = getViewName('oxarticles');

        //create empty edit object
        $this->_aViewData["edit"] = oxNew("oxselectlist");

        if (isset($sOxId) && $sOxId != "-1") {
            // generating category tree for select list
            // A. hack - passing language by post as lists uses only language passed by POST/GET/SESSION
            $_POST["language"] = $this->_iEditLang;
            $this->_createCategoryTree("artcattree", $sOxId);

            // load object
            $oAttr = oxNew("oxselectlist");
            $oAttr->loadInLang($this->_iEditLang, $sOxId);

            $aFieldList = $oAttr->getFieldList();
            if (is_array($aFieldList)) {
                foreach ($aFieldList as $key => $oField) {
                    if ($oField->priceUnit == '%') {
                        $oField->price = $oField->fprice;
                    }
                }
            }

            $oOtherLang = $oAttr->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oAttr->loadInLang(key($oOtherLang), $sOxId);
            }
            $this->_aViewData["edit"] = $oAttr;

            // Disable editing for derived items.
            if ($oAttr->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

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

            $iErr = oxRegistry::getSession()->getVariable("iErrorCode");

            if (!$iErr) {
                $iErr = ERR_SUCCESS;
            }

            $this->_aViewData["iErrorCode"] = $iErr;
            oxRegistry::getSession()->setVariable("iErrorCode", ERR_SUCCESS);

        }
        if (oxRegistry::getConfig()->getRequestParameter("aoc")) {
            $oSelectlistMainAjax = oxNew('selectlist_main_ajax');
            $this->_aViewData['oxajax'] = $oSelectlistMainAjax->getColumns();

            return "popups/selectlist_main.tpl";
        }

        return "selectlist_main.tpl";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oAttr = oxNew("oxselectlist");

        if ($sOxId != "-1") {
            $oAttr->loadInLang($this->_iEditLang, $sOxId);
        } else {
            $aParams['oxselectlist__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oAttr->isDerived()) {
            return;
        }

        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oAttr->setLanguage(0);
        $oAttr->assign($aParams);

        //#708
        if (!is_array($this->aFieldArray)) {
            $this->aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oAttr->oxselectlist__oxvaldesc->getRawValue());
        }
        // build value
        $oAttr->oxselectlist__oxvaldesc = new oxField("", oxField::T_RAW);
        foreach ($this->aFieldArray as $oField) {
            $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . $oField->name, oxField::T_RAW);
            if (isset($oField->price) && $oField->price) {
                $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . "!P!" . trim(str_replace(",", ".", $oField->price)), oxField::T_RAW);
                if ($oField->priceUnit == '%') {
                    $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . '%', oxField::T_RAW);
                }
            }
            $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . "__@@", oxField::T_RAW);
        }

        $oAttr->setLanguage($this->_iEditLang);
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId($oAttr->getId());
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $sOxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter("editval");

        $oObj = oxNew("oxselectlist");

        if ($sOxId != "-1") {
            $oObj->loadInLang($this->_iEditLang, $sOxId);
        } else {
            $aParams['oxselectlist__oxid'] = null;
        }

        //Disable editing for derived items
        if ($oObj->isDerived()) {
            return;
        }

        parent::save();

        //$aParams = $oObj->ConvertNameArray2Idx( $aParams);
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
        $oSelectlist = oxNew("oxselectlist");
        if ($oSelectlist->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            // Disable editing for derived items.
            if ($oSelectlist->isDerived()) {
                return;
            }

            $aDelFields = oxRegistry::getConfig()->getRequestParameter("aFields");
            $this->aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oSelectlist->oxselectlist__oxvaldesc->getRawValue());

            if (is_array($aDelFields) && count($aDelFields)) {
                foreach ($aDelFields as $sDelField) {
                    $sDel = $this->parseFieldName($sDelField);
                    foreach ($this->aFieldArray as $sKey => $oField) {
                        if ($oField->name == $sDel) {
                            unset($this->aFieldArray[$sKey]);
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
        $oSelectlist = oxNew("oxselectlist");
        if ($oSelectlist->loadInLang($this->_iEditLang, $this->getEditObjectId())) {

            //Disable editing for derived items.
            if ($oSelectlist->isDerived()) {
                return;
            }

            $sAddField = oxRegistry::getConfig()->getRequestParameter("sAddField");
            if (empty($sAddField)) {
                oxRegistry::getSession()->setVariable("iErrorCode", ERR_REQUIREDMISSING);

                return;
            }

            $this->aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oSelectlist->oxselectlist__oxvaldesc->getRawValue());

            $oField = new stdClass();
            $oField->name = $sAddField;
            $oField->price = oxRegistry::getConfig()->getRequestParameter("sAddFieldPriceMod");
            $oField->priceUnit = oxRegistry::getConfig()->getRequestParameter("sAddFieldPriceModUnit");

            $this->aFieldArray[] = $oField;
            if ($iPos = oxRegistry::getConfig()->getRequestParameter("sAddFieldPos")) {
                if ($this->_rearrangeFields($oField, $iPos - 1)) {
                    return;
                }
            }

            $this->save();
        }
    }

    /**
     * Modifies field from field array's first elem. and stores object
     *
     * @return null
     */
    public function changeField()
    {
        $sAddField = oxRegistry::getConfig()->getRequestParameter("sAddField");
        if (empty($sAddField)) {
            oxRegistry::getSession()->setVariable("iErrorCode", ERR_REQUIREDMISSING);

            return;
        }

        $aChangeFields = oxRegistry::getConfig()->getRequestParameter("aFields");
        if (is_array($aChangeFields) && count($aChangeFields)) {
            $oSelectlist = oxNew("oxselectlist");
            if ($oSelectlist->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
                $this->aFieldArray = oxRegistry::getUtils()->assignValuesFromText($oSelectlist->oxselectlist__oxvaldesc->getRawValue());
                $sChangeFieldName = $this->parseFieldName($aChangeFields[0]);

                foreach ($this->aFieldArray as $sKey => $oField) {
                    if ($oField->name == $sChangeFieldName) {
                        $this->aFieldArray[$sKey]->name = $sAddField;
                        $this->aFieldArray[$sKey]->price = oxRegistry::getConfig()->getRequestParameter("sAddFieldPriceMod");
                        $this->aFieldArray[$sKey]->priceUnit = oxRegistry::getConfig()->getRequestParameter("sAddFieldPriceModUnit");
                        if ($iPos = oxRegistry::getConfig()->getRequestParameter("sAddFieldPos")) {
                            if ($this->_rearrangeFields($this->aFieldArray[$sKey], $iPos - 1)) {
                                return;
                            }
                        }
                        break;
                    }
                }
                $this->save();
            }
        }
    }

    /**
     * Resorts fields list and moves $oField to $iPos,
     * uses $this->aFieldArray for fields storage.
     *
     * @param object  $oField field to be moved
     * @param integer $iPos   new pos of the field
     *
     * @return bool - true if failed.
     */
    protected function _rearrangeFields($oField, $iPos)
    {
        if (!isset($this->aFieldArray) || !is_array($this->aFieldArray)) {
            return true;
        }

        $iFieldCount = count($this->aFieldArray);
        if ($iPos < 0 || $iPos >= $iFieldCount) {
            oxRegistry::getSession()->setVariable("iErrorCode", ERR_POSOUTOFBOUNDS);

            return true;
        }

        $iCurrentPos = -1;
        for ($i = 0; $i < $iFieldCount; $i++) {
            if ($this->aFieldArray[$i] == $oField) {
                $iCurrentPos = $i;
                break;
            }
        }

        if ($iCurrentPos == -1) {
            return true;
        }

        if ($iCurrentPos == $iPos) {
            return false;
        }

        $sField = $this->aFieldArray[$iCurrentPos];
        if ($iCurrentPos < $iPos) {
            for ($i = $iCurrentPos; $i < $iPos; $i++) {
                $this->aFieldArray[$i] = $this->aFieldArray[$i + 1];
            }
            $this->aFieldArray[$iPos] = $sField;

            return false;
        } else {
            for ($i = $iCurrentPos; $i > $iPos; $i--) {
                $this->aFieldArray[$i] = $this->aFieldArray[$i - 1];
            }
            $this->aFieldArray[$iPos] = $sField;

            return false;
        }
    }

    /**
     * Parses field name from given string
     * String format is: "someNr__@@someName__@@someTxt"
     *
     * @param string $sInput given string
     *
     * @return string - name
     */
    public function parseFieldName($sInput)
    {
        $aInput = explode('__@@', $sInput, 3);

        return $aInput[1];
    }
}
