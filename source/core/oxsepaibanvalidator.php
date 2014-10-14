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
 * SEPA (Single Euro Payments Area) validation class
 *
 * @package core
 */
class oxSepaIBANValidator
{
    const IBAN_ALGORITHM_MOD_VALUE = 97;

    protected $_aCodeLengths = array();

    /**
     * International bank account number validation
     *
     * An IBAN is validated by converting it into an integer and performing a basic mod-97 operation (as described in ISO 7064) on it.
     * If the IBAN is valid, the remainder equals 1.
     *
     * @param string $sIBAN code to check
     *
     * @return bool
     */
    public function isValid( $sIBAN )
    {
        $blValid = false;
        $sIBAN = strtoupper( trim( $sIBAN ) );

        if ( $this->_isLengthValid( $sIBAN ) ) {
            $blValid = $this->_isAlgorithmValid( $sIBAN );
        }

        return $blValid;
    }

    /**
     * Validation of IBAN registry
     *
     * @param array $aCodeLengths
     *
     * @return bool
     */
    public function isValidCodeLengths( $aCodeLengths )
    {
        $blValid = false;

        if ( $this->_isNotEmptyArray( $aCodeLengths ) )
        {
            $blValid = $this->_isEachCodeLengthValid( $aCodeLengths );
        }

        return $blValid;
    }

    /**
     * Set IBAN Registry
     *
     * @param array $aCodeLengths
     *
     * @return bool
     */
    public function setCodeLengths( $aCodeLengths )
    {
        if ( $this->isValidCodeLengths( $aCodeLengths ) ) {
            $this->_aCodeLengths = $aCodeLengths;

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
    public function getCodeLengths()
    {
        return $this->_aCodeLengths;
    }


    /**
     * Check if the total IBAN length is correct as per country. If not, the IBAN is invalid.
     * @param $sIBAN
     *
     * @return bool
     */
    protected function _isLengthValid( $sIBAN )
    {
        $iActualLength = getStr()->strlen( $sIBAN );

        $iCorrectLength = $this->_getLengthForCountry( $sIBAN );

        return !is_null( $iCorrectLength ) && $iActualLength === $iCorrectLength;
    }


    /**
     * @param $sIBAN
     *
     * @return null
     */
    protected function _getLengthForCountry( $sIBAN )
    {
        $aIBANRegistry = $this->getCodeLengths();

        $sCountryCode   = getStr()->substr( $sIBAN, 0, 2 );

        $iCorrectLength = ( isset ( $aIBANRegistry[$sCountryCode] ) ) ? $aIBANRegistry[$sCountryCode] : null;

        return $iCorrectLength;
    }

    /**
     * Checks if IBAN is valid according to checksum algorithm
     * @param $sIBAN
     *
     * @return bool
     */
    protected function _isAlgorithmValid( $sIBAN )
    {
        $sIBAN = $this->_moveInitialCharactersToEnd( $sIBAN );

        $sIBAN = $this->_replaceLettersToNumbers( $sIBAN );

        return $this->_isIBANChecksumValid( $sIBAN );
    }

    /**
     * Move the four initial characters to the end of the string.
     * @param $sIBAN
     *
     * @return string
     */
    protected function _moveInitialCharactersToEnd( $sIBAN )
    {
        $oStr          = getStr();

        $sInitialChars = $oStr->substr( $sIBAN, 0, 4 );
        $sIBAN         = $oStr->substr( $sIBAN, 4 );
        $sIBAN         = $sIBAN . $sInitialChars;

        return $sIBAN;
    }

    /**
     * Replace each letter in the string with two digits, thereby expanding the string, where A = 10, B = 11, ..., Z = 35.
     * @param $sIBAN
     *
     * @return string
     */
    protected function _replaceLettersToNumbers( $sIBAN )
    {
        $aReplaceArray = array(
            'A' => 10,
            'B' => 11,
            'C' => 12,
            'D' => 13,
            'E' => 14,
            'F' => 15,
            'G' => 16,
            'H' => 17,
            'I' => 18,
            'J' => 19,
            'K' => 20,
            'L' => 21,
            'M' => 22,
            'N' => 23,
            'O' => 24,
            'P' => 25,
            'Q' => 26,
            'R' => 27,
            'S' => 28,
            'T' => 29,
            'U' => 30,
            'V' => 31,
            'W' => 32,
            'X' => 33,
            'Y' => 34,
            'Z' => 35
        );

        $sIBAN = str_replace(
            array_keys( $aReplaceArray ),
            $aReplaceArray,
            $sIBAN
        );

        return $sIBAN;
    }

    /**
     * Interpret the string as a decimal integer and compute the remainder of that number on division by 97.
     * @param $sIBAN
     *
     * @return bool
     */
    protected function _isIBANChecksumValid( $sIBAN )
    {
        $blValid = true;

        $sModulus = bcmod( $sIBAN, self::IBAN_ALGORITHM_MOD_VALUE );
        if ( (int) $sModulus != 1 ) {
            $blValid = false;
        }

        return $blValid;
    }

    /**
     * Checks if Code length is non empty array
     * @param $aCodeLengths
     *
     * @return bool
     */
    protected function _isNotEmptyArray( $aCodeLengths )
    {
        return is_array( $aCodeLengths ) && !empty( $aCodeLengths );
    }

    /**
     * @param $aCodeLengths
     *
     * @return bool
     */
    protected function _isEachCodeLengthValid( $aCodeLengths )
    {
        $blValid = true;

        foreach ( $aCodeLengths as $sCountryAbbr => $iLength ) {

            if ( !$this->_isCodeLengthKeyValid( $sCountryAbbr ) ||
                 !$this->_isCodeLengthValueValid( $iLength ) ) {

                $blValid = false;
                break;
            }

        }

        return $blValid;
    }

    /**
     * Checks if country code is valid
     *
     * @param $sCountryAbbr
     *
     * @return bool
     */
    protected function _isCodeLengthKeyValid( $sCountryAbbr )
    {
        return (int) preg_match( "/^[A-Z]{2}$/", $sCountryAbbr ) !== 0;
    }

    /**
     * Checks if value is numeric and does not contain whitespaces
     *
     * @param $iLength
     *
     * @return bool
     */
    protected function _isCodeLengthValueValid( $iLength )
    {
        return is_numeric( $iLength ) && (int) preg_match( "/\./", $iLength ) !== 1;
    }

}