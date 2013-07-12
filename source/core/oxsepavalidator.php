<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id
 */

/**
 * SEPA (Single Euro Payments Area) validation class
 *
 * @package core
 */
class oxSepaValidator
{
    /**
     * Business identifier code validation
     *
     * Structure
     *  - 4 letters: Institution Code or bank code.
     *  - 2 letters: ISO 3166-1 alpha-2 country code
     *  - 2 letters or digits: location code
     *  - 3 letters or digits: branch code, optional
     *
     * @param string $sBIC code to check
     *
     * @return bool
     */
    public function isValidBIC($sBIC)
    {
        return (bool)getStr()->preg_match("([a-zA-Z]{4}[a-zA-Z]{2}[a-zA-Z0-9]{2}([a-zA-Z0-9]{3})?)", $sBIC);
    }

    /**
     * International bank account number validation
     *
     * An IBAN is validated by converting it into an integer and performing a basic mod-97 operation (as described in ISO 7064) on it.
     * If the IBAN is valid, the remainder equals 1.
     *
     * @param string $sBIC code to check
     *
     * @return bool
     */
    public function isValidIBAN($sIBAN)
    {
        $blValid = true;

        $oStr = getStr();
        $sIBAN = strtoupper( trim($sIBAN) );
        $aIBANRegistry = $this->getIBANRegistry();

        // 1. Check that the total IBAN length is correct as per the country. If not, the IBAN is invalid.
        $sLangAbbr = $oStr->substr($sIBAN, 0, 2);
        $iLength = $aIBANRegistry[$sLangAbbr];
        if ( !is_null($iLength) && $oStr->strlen($sIBAN) != $iLength ) {
            $blValid = false;
        }

        // 2. Move the four initial characters to the end of the string.
        $sInitialChars = $oStr->substr($sIBAN, 0, 4);
        $sIBAN = substr_replace($sIBAN, '', 0, 4);
        $sIBAN = $sIBAN . $sInitialChars;

        // 3. Replace each letter in the string with two digits, thereby expanding the string, where A = 10, B = 11, ..., Z = 35.
        $sIBAN= str_replace(
            array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'),
            array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35),
            $sIBAN
        );

        // 4. Interpret the string as a decimal integer and compute the remainder of that number on division by 97.
        $sModulus = bcmod($sIBAN, 97);
        if ( (int)$sModulus != 1 ) {
            $blValid = false;
        }

        // 5. Return validation result
        return $blValid;
    }

    /**
     * Validation of IBAN registry
     *
     * @param array $aIBANRegistry
     *
     * @return bool
     */
    public function isValidIBANRegistry($aIBANRegistry = null)
    {
        $blValid = true;

        // If not passed, validate default IBAN registry
        if ( is_null($aIBANRegistry) ) {
            $aIBANRegistry = $this->getIBANRegistry();
        }

        if ( !is_array($aIBANRegistry) || empty($aIBANRegistry) ) {
            $blValid = false;
        }

        foreach ($aIBANRegistry as $sCountryAbbr => $iLength) {
            if ( (int)preg_match("/^[A-Z]{2}$/", $sCountryAbbr) === 0 ) {
                $blValid = false;
                break;
            }
            if ( !is_numeric($iLength) || (int)preg_match("/\./", $iLength) === 1 ) {
                $blValid = false;
                break;
            }

        }

        return $blValid;
    }

    /**
     * Set IBAN Registry
     *
     * @param array $aIBANRegistry
     *
     * @return bool
     */
    public function setIBANRegistry($aIBANRegistry)
    {
        if ( $this->isValidIBANRegistry($aIBANRegistry) ) {
            $this->_aIBANRegistry = $aIBANRegistry;
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get IBAN length by country data
     *
     * @return array
     */
    public function getIBANRegistry()
    {
        return $this->_aIBANRegistry;
    }

    /**
     * @var array IBAN Registry
     */
    protected $_aIBANRegistry = array(
        'AL' => 28,
        'AD' => 24,
        'AT' => 20,
        'AZ' => 28,
        'BH' => 22,
        'BE' => 16,
        'BA' => 20,
        'BR' => 29,
        'BG' => 22,
        'CR' => 21,
        'HR' => 21,
        'CY' => 28,
        'CZ' => 24,
        'DK' => 18, // Same DENMARK
        'FO' => 18, // Same DENMARK
        'GL' => 18, // Same DENMARK
        'DO' => 28,
        'EE' => 20,
        'FI' => 18,
        'FR' => 27,
        'GE' => 22,
        'DE' => 22,
        'GI' => 23,
        'GR' => 27,
        'GT' => 28,
        'HU' => 28,
        'IS' => 26,
        'IE' => 22,
        'IL' => 23,
        'IT' => 27,
        'KZ' => 20,
        'KW' => 30,
        'LV' => 21,
        'LB' => 28,
        'LI' => 21,
        'LT' => 20,
        'LU' => 20,
        'MK' => 19,
        'MT' => 31,
        'MR' => 27,
        'MU' => 30,
        'MD' => 24,
        'MC' => 27,
        'ME' => 22,
        'NL' => 18,
        'NO' => 15,
        'PK' => 24,
        'PS' => 29,
        'PL' => 28,
        'PL' => 28,
        'PT' => 25,
        'RO' => 24,
        'SM' => 27,
        'SA' => 24,
        'RS' => 22,
        'SK' => 24,
        'SI' => 19,
        'ES' => 24,
        'SE' => 24,
        'CH' => 21,
        'TN' => 24,
        'TR' => 26,
        'AE' => 23,
        'GB' => 22,
        'VG' => 24
    );
}