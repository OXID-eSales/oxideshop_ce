<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Credit card validation class
 *
 */
class CreditCardValidator
{
    /**
     * Credit card identification check array
     *
     * @var array
     */
    protected $_aCardsInfo = [ // name              // digits     // starting digits
        "amx" => '/^3[47].{13}$/', // American Express     16            34, 37
        "dlt" => '/^4.{15}$/', // Delta                16            4
        "dnc" => '/^30[0-5].{11}$|^3[68].{12}$/', // Diners Club          14            300-305, 36, 38
        "dsc" => '/^6011.{12}$/', // Discover             16            6011
        "enr" => '/^2014.{11}$|^2149.{11}$/', // enRoute              15            2014, 2149
        "jcb" => '/^3.{15}$|^2131|1800.{11}$/', // JCB                  15/16         3/ 2131, 1800
        "mcd" => '/^5[1-5].{14}$/', // MasterCard           16            51-55
        "swi" => '/^[456].{15}$|^[456].{17,18}$/', // Switch               16, 18, 19    4-6
        "vis" => '/^4.{15}$|^4.{12}$/', // Visa                 13, 16        4
    ];

    /**
     * Checks credit card type. Returns TRUE if card is valid
     *
     * @param string $type   credit card type
     * @param string $number credit card number
     *
     * @return bool
     */
    protected function _isValidType($type, $number)
    {
        // testing if card type is known and matches pattern
        if (isset($this->_aCardsInfo[$type])) {
            return preg_match($this->_aCardsInfo[$type], $number);
        }

        return true;
    }

    /**
     * Checks credit card expiration date. Returns TRUE if card is not expired
     *
     * @param string $date credit card type
     *
     * @return bool
     */
    protected function _isExpired($date)
    {
        if ($date) {
            $years = substr($date, 2, 2);
            $month = substr($date, 0, 2);
            $day = date("t", mktime(11, 59, 59, $month, 1, $years));

            $expDate = mktime(23, 59, 59, $month, $day, $years);
            if (time() > $expDate) {
                return true;
            }
        }

        return false;
    }

    /**
     * checks credit card number. Returns TRUE if card number is valid
     *
     * @param string $number credit card number
     *
     * @return bool
     */
    protected function _isValidNumer($number)
    {
        $valid = false;
        if (($length = strlen($number))) {
            $modSum = 0;
            $mod = $length % 2;

            // Luhn algorithm
            for ($pos = 0; $pos < $length; $pos++) {
                // taking digit to check..
                $currDigit = ( int ) $number{$pos};

                // multiplying if needed..
                $addValue = (($pos % 2 == $mod) ? 2 : 1) * $currDigit;

                // adding prepared current digit
                $modSum += ($addValue > 9) ? $addValue - 9 : $addValue;
            }

            $valid = ($modSum % 10) == 0;
        }

        return $valid;
    }

    /**
     * Checks if provided credit card information is valid. Returns TRUE if valid
     *
     * @param object $number credit card number
     * @param string $type   credit card type [optional]
     * @param string $date   card expiration date [optional]
     *
     * @return bool
     */
    public function isValidCard($number, $type = "", $date = "")
    {
        // cleanup
        $number = preg_replace("/[^0-9]/", "", $number);

        return (!$this->_isExpired($date) && $this->_isValidType($type, $number) && $this->_isValidNumer($number));
    }
}
