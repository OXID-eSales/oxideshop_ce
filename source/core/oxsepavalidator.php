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
class oxSepaValidator
{
    /**
     * @var array IBAN Code Length array
     */
    protected $_aIBANCodeLengths = array(
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

    /**
     * Business identifier code validation
     *
     * @param string $sBIC code to check
     *
     * @return bool
     */
    public function isValidBIC( $sBIC )
    {
        $oBICValidator = oxNew( 'oxSepaBICValidator' );

        return $oBICValidator->isValid( $sBIC );
    }

    /**
     * International bank account number validation
     *
     * @param string $sIBAN code to check
     *
     * @return bool
     */
    public function isValidIBAN( $sIBAN )
    {
        $oIBANValidator = oxNew( 'oxSepaIBANValidator' );
        $oIBANValidator->setCodeLengths( $this->getIBANCodeLengths() );

        return $oIBANValidator->isValid( $sIBAN );
    }

    /**
    * Validation of IBAN registry
    *
    * @param array $aIBANRegistry
    * @deprecated since v5.2.0 (2013-12-11); Use oxSepaIBANValidator::isCodeLengthsValid().
    *
    * @return bool
    */
    public function isValidIBANRegistry( $aIBANRegistry = null )
    {
        $oIBANValidator = oxNew( 'oxSepaIBANValidator' );

        if ( is_null( $aIBANRegistry ) ) {
            $aIBANRegistry = $this->getIBANCodeLengths();
        }

        return $oIBANValidator->isCodeLengthsValid( $aIBANRegistry );
    }


    /**
     * Set IBAN Registry
     *
     * @param array $aIBANRegistry
     * @deprecated since v5.2.0 (2013-12-11); Use oxSepaIBANValidator::setCodeLengths().
     *
     * @return bool
     */
    public function setIBANRegistry( $aIBANRegistry )
    {
        if ( $this->isValidIBANRegistry( $aIBANRegistry ) ) {
            $this->_aIBANCodeLengths = $aIBANRegistry;

            return true;
        } else {
            return false;
        }
    }

    /**
     * Get IBAN length by country data
     * @deprecated since v5.2.0 (2013-12-11); Use oxSepaValidator::getIBANCodeLengths().
     *
     * @return array
     */
    public function getIBANRegistry()
    {
        return $this->_aIBANCodeLengths;
    }

    /**
     * Get IBAN length by country data
     *
     * @return array
     */
    public function getIBANCodeLengths()
    {
        return $this->_aIBANCodeLengths;
    }


}