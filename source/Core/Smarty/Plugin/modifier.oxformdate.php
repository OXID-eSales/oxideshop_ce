<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty modifier
 * -------------------------------------------------------------
 * Name:     smarty_modifier_oxformdate<br>
 * Purpose:  Conterts date/timestamp/datetime type value to user defined format
 * Example:  {$object|oxformdate:"foo"}
 * -------------------------------------------------------------
 *
 * @param object $oConvObject   oxField object
 * @param string $sFieldType    additional type if field (this may help to force formatting)
 * @param bool   $blPassedValue bool if true, will simulate object as sometimes we need to apply formatting to some regulat values
 *
 * @return string
 */
function smarty_modifier_oxformdate($oConvObject, $sFieldType = null, $blPassedValue = false)
{
   // creating fake bject
    if ($blPassedValue || is_string($oConvObject)) {
        $sValue = $oConvObject;
        $oConvObject = new \OxidEsales\Eshop\Core\Field();
        $oConvObject->fldmax_length = "0";
        $oConvObject->fldtype = $sFieldType;
        $oConvObject->setValue($sValue);
    }

    $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

    // if such format applies to this type of field - sets formatted value to passed object
    if (!$myConfig->getConfigParam('blSkipFormatConversion')) {
        if ($oConvObject->fldtype == "datetime" || $sFieldType == "datetime") {
            \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($oConvObject);
        } elseif ($oConvObject->fldtype == "timestamp" || $sFieldType == "timestamp") {
            \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBTimestamp($oConvObject);
        } elseif ($oConvObject->fldtype == "date" || $sFieldType == "date") {
            \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($oConvObject);
        }
    }

    return $oConvObject->value;
}

/* vim: set expandtab: */
