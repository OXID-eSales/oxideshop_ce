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
 * Credit card validation class
 *
 * @package core
 */
class oxCcValidator
{
    /**
     * Credit card identification check array
     *
     * @var array
     */
    protected $_aCardsInfo = array(                                           // name              // digits     // starting digits
                                   "amx" => '/^3[47].{13}$/',                 // American Express     16            34, 37
                                   "dlt" => '/^4.{15}$/',                     // Delta                16            4
                                   "dnc" => '/^30[0-5].{11}$|^3[68].{12}$/',  // Diners Club          14            300-305, 36, 38
                                   "dsc" => '/^6011.{12}$/',                  // Discover             16            6011
                                   "enr" => '/^2014.{11}$|^2149.{11}$/',      // enRoute              15            2014, 2149
                                   "jcb" => '/^3.{15}$|^2131|1800.{11}$/',    // JCB                  15/16         3/ 2131, 1800
                                   "mcd" => '/^5[1-5].{14}$/',                // MasterCard           16            51-55
                                   "swi" => '/^[456].{15}$|^[456].{17,18}$/', // Switch               16, 18, 19    4-6
                                   "vis" => '/^4.{15}$|^4.{12}$/',            // Visa                 13, 16        4
                                 );

    /**
     * Checks credit card type. Returns TRUE if card is valid
     *
     * @param string $sType   credit card type
     * @param string $sNumber credit card number
     *
     * @return bool
     */
    protected function _isValidType( $sType, $sNumber )
    {
        $blValid = true;

        // testing if card type is known and matches pattern
        if ( isset( $this->_aCardsInfo[$sType] ) ) {
            $blValid = preg_match( $this->_aCardsInfo[$sType], $sNumber );
        }
        return $blValid;
    }

    /**
     * Checks credit card expiration date. Returns TRUE if card is not expired
     *
     * @param string $sDate credit card type
     *
     * @return bool
     */
    protected function _isExpired( $sDate )
    {
        $blExpired = false;

        if ( $sDate ) {
            $sYears = substr( $sDate, 2, 2 );
            $sMonth = substr( $sDate, 0, 2 );
            $sDay   = date( "t", mktime( 11, 59, 59, $sMonth, 1, $sYears ) );

            $iExpDate = mktime( 23, 59, 59, $sMonth, $sDay, $sYears );
            if ( time() > $iExpDate  ) {
                $blExpired = true;
            }
        }

        return $blExpired;
    }

    /**
     * checks credit card number. Returns TRUE if card number is valid
     *
     * @param string $sNumber credit card number
     *
     * @return bool
     */
    protected function _isValidNumer( $sNumber )
    {
        $blValid = false;
        if ( ( $iLength = strlen( $sNumber ) ) ) {
            $iModSum = 0;
            $iMod = $iLength % 2;

            // Luhn algorithm
            for ( $iPos = 0; $iPos < $iLength; $iPos++ ) {

                // taking digit to check..
                $iCurrDigit = ( int ) $sNumber{$iPos};

                // multiplying if needed..
                $iAddValue  = ( ( $iPos % 2 == $iMod ) ? 2 : 1 ) * $iCurrDigit;

                // adding prepared current digit
                $iModSum += ( $iAddValue > 9 ) ? $iAddValue - 9 : $iAddValue;
            }

            $blValid = ( $iModSum % 10 ) == 0;
        }
        return $blValid;
    }

    /**
     * Checks if provided credit card information is valid. Returns TRUE if valid
     *
     * @param object $sNumber credit card number
     * @param string $sType   credit card type [optional]
     * @param string $sDate   card expiration date [optional]
     *
     * @return bool
     */
    public function isValidCard( $sNumber, $sType = "", $sDate = "" )
    {
        // cleanup
        $sNumber = preg_replace( "/[^0-9]/", "", $sNumber );
        return ( !$this->_isExpired( $sDate ) && $this->_isValidType( $sType, $sNumber ) && $this->_isValidNumer( $sNumber ) );
    }
}