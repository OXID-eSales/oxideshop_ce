<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * String manipulation class
 */
class UtilsString
{
    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     */
    public function __construct()
    {
    }

    /**
     * Prepares passed string for CSV format
     *
     * @param string $sInField String to prepare
     *
     * @return string
     */
    public function prepareCSVField($sInField)
    {
        $oStr = getStr();
        if ($oStr->strstr($sInField, '"')) {
            return '"' . str_replace('"', '""', $sInField) . '"';
        } elseif ($oStr->strstr($sInField, ';')) {
            return '"' . $sInField . '"';
        }

        return $sInField;
    }

    /**
     * shortens a string to a size $iLenght, multiple spaces are removed
     * and leading and ending whitespaces are removed. If string ends with "," then
     * "," is removed from string end
     *
     * @param string $sString input string
     * @param int    $iLength maximum length of result string , -1 -> no truncation
     *
     * @return string a string of maximum length $iLength without multiple spaces and commas
     */
    public function minimizeTruncateString($sString, $iLength)
    {
        //leading and ending whitespaces
        $sString = trim($sString);
        $oStr = getStr();

        //multiple whitespaces
        $sString = $oStr->preg_replace("/[ \t\n\r]+/", " ", $sString);
        if ($oStr->strlen($sString) > $iLength && $iLength != -1) {
            $sString = $oStr->substr($sString, 0, $iLength);
        }

        return $oStr->preg_replace("/,+$/", "", $sString);
    }

    /**
     * Prepares and returns string for search engines.
     *
     * @param string $sSearchStr String to prepare for search engines
     *
     * @return string
     */
    public function prepareStrForSearch($sSearchStr)
    {
        $oStr = getStr();
        if ($oStr->hasSpecialChars($sSearchStr)) {
            return $oStr->recodeEntities($sSearchStr, true, ['&amp;'], ['&']);
        }

        return '';
    }
}
