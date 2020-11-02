<?php

declare(strict_types=1);

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
     *
     * @return string
     */
    public function formdate($oConvObject, string $sFieldType = null, bool $blPassedValue = false): ?string
    {
        // creating fake bject
        if ($blPassedValue || \is_string($oConvObject)) {
            $sValue = $oConvObject;
            $oConvObject = new \OxidEsales\Eshop\Core\Field();
            $oConvObject->fldmax_length = '0';
            $oConvObject->fldtype = $sFieldType;
            $oConvObject->setValue($sValue);
        }

        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        // if such format applies to this type of field - sets formatted value to passed object
        if (!$myConfig->getConfigParam('blSkipFormatConversion')) {
            if ('datetime' === $oConvObject->fldtype || 'datetime' === $sFieldType) {
                \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDateTime($oConvObject);
            } elseif ('timestamp' === $oConvObject->fldtype || 'timestamp' === $sFieldType) {
                \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBTimestamp($oConvObject);
            } elseif ('date' === $oConvObject->fldtype || 'date' === $sFieldType) {
                \OxidEsales\Eshop\Core\Registry::getUtilsDate()->convertDBDate($oConvObject);
            }
        }

        return $oConvObject->value;
    }
}
