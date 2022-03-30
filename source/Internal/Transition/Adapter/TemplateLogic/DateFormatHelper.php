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
        if (str_contains($format, '%e')) {
            $winFormatSearch[] = '%e';
            $winFormatReplace[] = sprintf('%\' 2d', date('j', $timestamp));
        }
        if (str_contains($format, '%l')) {
            $winFormatSearch[] = '%l';
            $winFormatReplace[] = sprintf('%\' 2d', date('h', $timestamp));
        }

        return str_replace($winFormatSearch, $winFormatReplace, $format);
    }
}
