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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Price calculation class. Responsible for simple price calculations. Basically contains Brutto, Netto prices and VAT values.
 */
class oxPrice
{

    /**
     * Brutto price
     *
     * @var double
     */
    protected $_dBrutto = 0.0;

    /**
     * Netto price
     *
     * @var double
     */
    protected $_dNetto = 0.0;

    /**
     * VAT percent
     *
     * @var double
     */
    protected $_dVat = 0.0;


    /**
     * Assigned discount array
     *
     * @var array
     */
    protected $_aDiscounts = null;


    /**
     * Price entering mode
     * Reference to myConfig->blEnterNetPrice
     * Then true  - setPrice sets netto price and calculates brutto price
     * Then false - setPrice sets brutto price and calculates netto price
     *
     * @var boolean
     */
    protected $_blNetPriceMode;

    /**
     * Class constructor. Gets price entering mode.
     *
     * @param double $dPrice given price
     *
     * @return oxPrice
     */
    public function __construct($dPrice = null)
    {
        $this->setNettoMode(oxRegistry::getConfig()->getConfigParam('blEnterNetPrice'));

        if (!is_null($dPrice)) {
            $this->setPrice($dPrice);
        }
    }

    /**
     * Netto price mode setter
     *
     * @param bool $blNetto State to set price to net mode (default true).
     */
    public function setNettoMode($blNetto = true)
    {
        $this->_blNetPriceMode = $blNetto;
    }

    /**
     * return true if mode is netto
     *
     * @return bool
     */
    public function isNettoMode()
    {
        return $this->_blNetPriceMode;
    }

    /**
     * Netto price mode setter
     */
    public function setNettoPriceMode()
    {
        $this->setNettoMode();
    }

    /**
     * Brutto price mode setter
     */
    public function setBruttoPriceMode()
    {
        $this->setNettoMode(false);
    }

    /**
     * Sets new VAT percent, and recalculates price.
     *
     * @param double $dVat vat percent
     */
    public function setVat($dVat)
    {
        $this->_dVat = (double) $dVat;
    }

    /**
     * Sets new base VAT percent, recalculates brutto, and then netto price (in brutto mode).
     * if bruttoMode then BruttoPrice =(BruttoPrice - oldVAT% ) + newVat;
     * oldVAT = newVat;
     * finally recalculate;
     * USE ONLY TO CHANGE BASE VAT (in case when local VAT differs from user VAT),
     * USE setVat() in usual case !!!
     *
     * @param double $newVat vat percent
     */
    public function setUserVat($newVat)
    {
        if (!$this->isNettoMode() && $newVat != $this->_dVat) {
            $this->_dBrutto = self::Netto2Brutto(self::Brutto2Netto($this->_dBrutto, $this->_dVat), (double) $newVat);
        }
        $this->_dVat = (double) $newVat;
    }

    /**
     * Returns VAT percent
     *
     * @return double
     */
    public function getVat()
    {
        return $this->_dVat;
    }

    /**
     * Sets new price and VAT percent(optional). Recalculates price by
     * price entering mode
     *
     * @param double $dPrice new price
     * @param double $dVat   VAT
     */
    public function setPrice($dPrice, $dVat = null)
    {
        if (!is_null($dVat)) {
            $this->setVat($dVat);
        }

        if ($this->isNettoMode()) {
            $this->_dNetto = $dPrice;
        } else {
            $this->_dBrutto = $dPrice;
        }
    }

    /**
     * Returns price depending on mode brutto or netto
     *
     * @return double
     */
    public function getPrice()
    {
        if ($this->isNettoMode()) {
            return $this->getNettoPrice();
        } else {
            return $this->getBruttoPrice();
        }
    }

    /**
     * Returns brutto price
     *
     * @return double
     */
    public function getBruttoPrice()
    {
        if ($this->isNettoMode()) {
            return $this->getNettoPrice() + $this->getVatValue();
        } else {
            return oxRegistry::getUtils()->fRound($this->_dBrutto);
        }
    }

    /**
     * Returns netto price
     *
     * @return double
     */
    public function getNettoPrice()
    {
        if ($this->isNettoMode()) {
            return oxRegistry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

    /**
     * Returns absolute VAT value
     *
     * @return double
     */
    public function getVatValue()
    {
        if ($this->isNettoMode()) {
            $dVatValue = $this->getNettoPrice() * $this->getVat() / 100;
        } else {
            $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
        }

        return oxRegistry::getUtils()->fRound($dVatValue);
    }

    /**
     * Subtracts given percent from price depending  on price entering mode,
     * and recalculates price
     *
     * @param double $dValue percent to subtract from price
     */
    public function subtractPercent($dValue)
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice - self::percent($dPrice, $dValue));
    }

    /**
     * Adds given percent to price depending  on price entering mode,
     * and recalculates price
     *
     * @param double $dValue percent to add to price
     */
    public function addPercent($dValue)
    {
        $this->subtractPercent(-$dValue);
    }

    /**
     * Adds another oxPrice object and recalculates current method.
     *
     * @param oxPrice $oPrice object
     */
    public function addPrice(oxPrice $oPrice)
    {
        if ($this->isNettoMode()) {
            $this->add($oPrice->getNettoPrice());
        } else {
            $this->add($oPrice->getBruttoPrice());
        }
    }

    /**
     * Adds given value to price depending  on price entering mode,
     * and recalculates price
     *
     * @param double $dValue value to add to price
     */
    public function add($dValue)
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice + $dValue);
    }

    /**
     * Subtracts given value from price depending  on price entering mode,
     * and recalculates price
     *
     * @param double $dValue value to subtracts from price
     */
    public function subtract($dValue)
    {
        $this->add(-$dValue);
    }

    /**
     * Multiplies price by given value depending on price entering mode,
     * and recalculates price
     *
     * @param double $dValue value for multiplying price
     */
    public function multiply($dValue)
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice * $dValue);
    }

    /**
     * Divides price by given value depending on price entering mode,
     * and recalculates price
     *
     * @param double $dValue value for dividing price
     */
    public function divide($dValue)
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice / $dValue);
    }

    /**
     * Compares this object to another oxPrice objects. Comparison is performed on brutto price.
     * Result is equal to:
     *   0 - when prices are equal.
     *   1 - when this price is larger than $oPrice.
     *  -1 - when this price is smaller than $oPrice.
     *
     * @param oxPrice $oPrice price object
     *
     * @return null
     */
    public function compare(oxPrice $oPrice)
    {
        $dBruttoPrice1 = $this->getBruttoPrice();
        $dBruttoPrice2 = $oPrice->getBruttoPrice();

        if ($dBruttoPrice1 == $dBruttoPrice2) {
            $iRes = 0;
        } elseif ($dBruttoPrice1 > $dBruttoPrice2) {
            $iRes = 1;
        } else {
            $iRes = -1;
        }

        return $iRes;
    }

    /**
     * Private function for percent value calculations
     *
     * @param double $dValue   value
     * @param double $dPercent percent
     *
     * @return double
     */
    public static function percent($dValue, $dPercent)
    {
        return ((double) $dValue * (double) $dPercent) / 100.0;
    }

    /**
     * Converts Brutto price to Netto using formula:
     * X + $dVat% = $dBrutto
     * X/100 = $dBrutto/(100+$dVAT)
     * X= ($dBrutto/(100+$dVAT))/100
     * returns X
     *
     * @param double $dBrutto brutto price
     * @param double $dVat    vat
     *
     * @return double
     */
    public static function brutto2Netto($dBrutto, $dVat)
    {
        // if VAT = -100% Return 0 because we subtract all what we have.
        // made to avoid division by zero in formula.
        if ($dVat == -100) {
            return 0;
        }

        return (double) ((double) $dBrutto * 100.0) / (100.0 + (double) $dVat);
    }

    /**
     * Converts Netto price to Brutto using formula:
     * X = $dNetto + $dVat%
     * returns X
     *
     * @param double $dNetto netto price
     * @param double $dVat   vat
     *
     * @return double
     */
    public static function netto2Brutto($dNetto, $dVat)
    {
        return (double) $dNetto + self::percent($dNetto, $dVat);
    }

    /**
     * Returns price multiplied by current currency
     *
     * @param string $dPrice price value
     *
     * @return double
     */
    public static function getPriceInActCurrency($dPrice)
    {
        $oCur = oxRegistry::getConfig()->getActShopCurrencyObject();

        return (( double ) $dPrice) * $oCur->rate;
    }


    /**
     * Sets discount to price
     *
     * @param double $dValue discount value
     * @param string $sType  discount type: abs or %
     */
    public function setDiscount($dValue, $sType)
    {
        $this->_aDiscounts[] = array('value' => $dValue, 'type' => $sType);
    }

    /**
     * Returns assigned discounts
     *
     * @return array
     */
    public function getDiscounts()
    {
        return $this->_aDiscounts;
    }

    /**
     * Flush assigned discounts
     */
    protected function _flushDiscounts()
    {
        $this->_aDiscounts = null;
    }

    /**
     * Calculates price: affects discounts
     */
    public function calculateDiscount()
    {
        $dPrice = $this->getPrice();
        $aDiscounts = $this->getDiscounts();

        if ($aDiscounts) {
            foreach ($aDiscounts as $aDiscount) {

                if ($aDiscount['type'] == 'abs') {
                    $dPrice = $dPrice - $aDiscount['value'];
                } else {
                    $dPrice = $dPrice * (100 - $aDiscount['value']) / 100;
                }
            }
            if ($dPrice < 0) {
                $this->setPrice(0);
            } else {
                $this->setPrice($dPrice);
            }

            $this->_flushDiscounts();
        }
    }
}
