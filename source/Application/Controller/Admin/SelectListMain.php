<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxField;
use stdClass;

if (!defined('ERR_SUCCESS')) {
    DEFINE("ERR_SUCCESS", 1);
}
if (!defined('ERR_REQUIREDMISSING')) {
    DEFINE("ERR_REQUIREDMISSING", -1);
}
if (!defined('ERR_POSOUTOFBOUNDS')) {
    DEFINE("ERR_POSOUTOFBOUNDS", -2);
}

/**
 * Admin article main selectlist manager.
 * Performs collection and updatind (on user submit) main item information.
 */
class SelectListMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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

        //create empty edit object
        $this->_aViewData["edit"] = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);

        if (isset($sOxId) && $sOxId != "-1") {
            // generating category tree for select list
            // A. hack - passing language by post as lists uses only language passed by POST/GET/SESSION
            $_POST["language"] = $this->_iEditLang;
            $this->_createCategoryTree("artcattree", $sOxId);

            // load object
            $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);
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

            $iErr = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iErrorCode");

            if (!$iErr) {
                $iErr = ERR_SUCCESS;
            }

            $this->_aViewData["iErrorCode"] = $iErr;
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iErrorCode", ERR_SUCCESS);
        }
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc")) {
            $oSelectlistMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\SelectListMainAjax::class);
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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);

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
            $this->aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oAttr->oxselectlist__oxvaldesc->getRawValue());
        }
        // build value
        $oAttr->oxselectlist__oxvaldesc = new \OxidEsales\Eshop\Core\Field("", \OxidEsales\Eshop\Core\Field::T_RAW);
        foreach ($this->aFieldArray as $oField) {
            $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . $oField->name, \OxidEsales\Eshop\Core\Field::T_RAW);
            if (isset($oField->price) && $oField->price) {
                $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . "!P!" . trim(str_replace(",", ".", $oField->price)), \OxidEsales\Eshop\Core\Field::T_RAW);
                if ($oField->priceUnit == '%') {
                    $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . '%', \OxidEsales\Eshop\Core\Field::T_RAW);
                }
            }
            $oAttr->oxselectlist__oxvaldesc->setValue($oAttr->oxselectlist__oxvaldesc->getRawValue() . "__@@", \OxidEsales\Eshop\Core\Field::T_RAW);
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
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oObj = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);

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
        $oObj->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang"));
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
        $oSelectlist = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);
        if ($oSelectlist->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            // Disable editing for derived items.
            if ($oSelectlist->isDerived()) {
                return;
            }

            $aDelFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aFields");
            $this->aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oSelectlist->oxselectlist__oxvaldesc->getRawValue());

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
        $oSelectlist = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);
        if ($oSelectlist->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
            //Disable editing for derived items.
            if ($oSelectlist->isDerived()) {
                return;
            }

            $sAddField = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddField");
            if (empty($sAddField)) {
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iErrorCode", ERR_REQUIREDMISSING);

                return;
            }

            $this->aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oSelectlist->oxselectlist__oxvaldesc->getRawValue());

            $oField = new stdClass();
            $oField->name = $sAddField;
            $oField->price = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddFieldPriceMod");
            $oField->priceUnit = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddFieldPriceModUnit");

            $this->aFieldArray[] = $oField;
            if ($iPos = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddFieldPos")) {
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
        $sAddField = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddField");
        if (empty($sAddField)) {
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iErrorCode", ERR_REQUIREDMISSING);

            return;
        }

        $aChangeFields = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aFields");
        if (is_array($aChangeFields) && count($aChangeFields)) {
            $oSelectlist = oxNew(\OxidEsales\Eshop\Application\Model\SelectList::class);
            if ($oSelectlist->loadInLang($this->_iEditLang, $this->getEditObjectId())) {
                $this->aFieldArray = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($oSelectlist->oxselectlist__oxvaldesc->getRawValue());
                $sChangeFieldName = $this->parseFieldName($aChangeFields[0]);

                foreach ($this->aFieldArray as $sKey => $oField) {
                    if ($oField->name == $sChangeFieldName) {
                        $this->aFieldArray[$sKey]->name = $sAddField;
                        $this->aFieldArray[$sKey]->price = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddFieldPriceMod");
                        $this->aFieldArray[$sKey]->priceUnit = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddFieldPriceModUnit");
                        if ($iPos = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sAddFieldPos")) {
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
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iErrorCode", ERR_POSOUTOFBOUNDS);

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
