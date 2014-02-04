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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * String manipulation class
 */
class oxUtilsString
{
    /**
     * oxUtils class instance.
     *
     * @var oxutils instance
     */
    private static $_instance = null;

   /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * Returns string manipulation utility instance
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxUtilsString") instead.
     *
     * @return oxUtilsString
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxUtilsString");
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
            return '"'.str_replace('"', '""', $sInField).'"';
        } elseif ($oStr->strstr($sInField, ';')) {
            return '"'.$sInField.'"';
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
    public function minimizeTruncateString( $sString, $iLength )
    {
        //leading and ending whitespaces
        $sString = trim( $sString );
        $oStr = getStr();

        //multiple whitespaces
        $sString = $oStr->preg_replace( "/[ \t\n\r]+/", " ", $sString );
        if ( $oStr->strlen( $sString ) > $iLength && $iLength != -1 ) {
            $sString = $oStr->substr( $sString, 0, $iLength );
        }

        $sString = $oStr->preg_replace( "/,+$/", "", $sString );
        return $sString;
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
        if ( $oStr->hasSpecialChars( $sSearchStr ) ) {
            return $oStr->recodeEntities( $sSearchStr, true, array( '&amp;' ), array( '&' ) );
        }

        return '';
    }
}
