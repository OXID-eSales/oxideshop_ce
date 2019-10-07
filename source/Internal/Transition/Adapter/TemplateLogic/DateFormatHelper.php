<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class DateFormatHelper
{

    /**
     * @param string $format
     * @param int    $timestamp
     *
     * @return string
     */
    public function fixWindowsTimeFormat($format, $timestamp)
    {
        $winFormatSearch = ['%D', '%h', '%n', '%r', '%R', '%t', '%T'];
        $winFormatReplace = ['%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S'];
        if (strpos($format, '%e') !== false) {
            $winFormatSearch[] = '%e';
            $winFormatReplace[] = sprintf('%\' 2d', date('j', $timestamp));
        }
        if (strpos($format, '%l') !== false) {
            $winFormatSearch[] = '%l';
            $winFormatReplace[] = sprintf('%\' 2d', date('h', $timestamp));
        }
        $format = str_replace($winFormatSearch, $winFormatReplace, $format);

        return $format;
    }
}
