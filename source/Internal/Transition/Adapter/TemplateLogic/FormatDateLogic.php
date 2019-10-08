<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class FormatDateLogic
{

    /**
     * @param object|string $oConvObject
     * @param string        $sFieldType
     * @param bool          $blPassedValue
     *
     * @return string
     */
    public function formdate($oConvObject, string $sFieldType = null, bool $blPassedValue = false): ?string
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
}
