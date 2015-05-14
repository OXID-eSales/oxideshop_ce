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
 * Price list class. Deals with a list of oxPrice object.
 * The main reason why we can't just sum oxPrice objects is that they have different VAT percents.
 */

class oxPriceList
{

    /**
     * Array containing oxPrice objects
     *
     * @var array
     */
    protected $_aList = array();

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * Returns Brutto price sum
     *
     * @return double
     */
    public function getBruttoSum()
    {
        $dSum = 0;
        foreach ($this->_aList as $oPrice) {
            $dSum += $oPrice->getBruttoPrice();
        }

        return $dSum;
    }

    /**
     * Returns the sum of list Netto prices
     *
     * @return double
     */
    public function getNettoSum()
    {
        $dSum = 0;
        foreach ($this->_aList as $oPrice) {
            $dSum += $oPrice->getNettoPrice();
        }

        return $dSum;
    }

    /**
     * Returns the sum of list Netto prices
     *
     * @param bool $isNettoMode mode in which calculate sum, default netto
     *
     * @return double
     */
    public function getSum($isNettoMode = true)
    {
        if ($isNettoMode) {
            return $this->getNettoSum();
        } else {
            return $this->getBruttoSum();
        }
    }

    /**
     * Returns VAT values sum separated to different array elements depending on VAT
     *
     * @param bool $isNettoMode mode in which calculate sum, default netto
     *
     * @return array
     */
    public function getVatInfo($isNettoMode = true)
    {
        $aVatValues = array();
        $aPrices = array();
        foreach ($this->_aList as $oPrice) {
            $sKey = ( string ) $oPrice->getVat();
            if (!isset($aPrices[$sKey])) {
                $aPrices[$sKey]['sum'] = 0;
                $aPrices[$sKey]['vat'] = $oPrice->getVat();
            }
            $aPrices[$sKey]['sum'] += $oPrice->getPrice();
        }

        foreach ($aPrices as $sKey => $aPrice) {
            if ($isNettoMode) {
                $dPrice = $aPrice['sum'] * $aPrice['vat'] / 100;
            } else {
                $dPrice = $aPrice['sum'] * $aPrice['vat'] / (100 + $aPrice['vat']);
            }
            $aVatValues[$sKey] = $dPrice;
        }

        return $aVatValues;
    }


    /**
     * Return prices separated to different array elements depending on VAT
     *
     * @return array
     */
    public function getPriceInfo()
    {
        $aPrices = array();
        foreach ($this->_aList as $oPrice) {
            $sVat = ( string ) $oPrice->getVat();
            if (!isset($aPrices[$sVat])) {
                $aPrices[$sVat] = 0;
            }
            $aPrices[$sVat] += $oPrice->getBruttoPrice();
        }

        return $aPrices;
    }

    /**
     * Iterates through applied VATs and fetches VAT for delivery.
     * If not VAT was applied - default VAT (myConfig->dDefaultVAT) will be used
     *
     * @return double
     */
    public function getMostUsedVatPercent()
    {
        $aPrices = $this->getPriceInfo();
        if (count($aPrices) == 0) {
            return;
        }

        $aVats = array_keys($aPrices, max($aPrices));

        return max($aVats);
    }

    /**
     * Iterates through applied VATs and calculates proportional VAT
     *
     * @return double
     */
    public function getProportionalVatPercent()
    {
        $dTotalSum = 0;

        foreach ($this->_aList as $oPrice) {
            $dTotalSum += $oPrice->getNettoPrice();
        }

        $dProportionalVat = 0;

        foreach ($this->_aList as $oPrice) {
            if ($dTotalSum > 0) {
                $dProportionalVat += $oPrice->getNettoPrice() / $dTotalSum * $oPrice->getVat();
            }
        }

        return $dProportionalVat;
    }


    /**
     * Add an oxPrice object to prices array
     *
     * @param oxprice $oPrice oxprice object
     */
    public function addToPriceList($oPrice)
    {
        $this->_aList[] = $oPrice;
    }

    /**
     * Recalculate price list to one price: sum total value of prices, and calculate VAT
     *
     * @return null
     */
    public function calculateToPrice()
    {
        if (count($this->_aList) == 0) {
            return;
        }

        $dNetoTotal = 0;
        $dVatTotal = 0;
        $dVat = 0;

        foreach ($this->_aList as $oPrice) {
            $dNetoTotal += $oPrice->getNettoPrice();
            $dVatTotal += $oPrice->getVatValue();
        }

        $oPrice = oxNew('oxPrice');

        if ($dNetoTotal) {
            $dVat = $dVatTotal * 100 / $dNetoTotal;

            $oPrice->setNettoPriceMode();
            $oPrice->setPrice($dNetoTotal);
            $oPrice->setVat($dVat);
        }

        return $oPrice;
    }

    /**
     * Return count of added oxPrices
     *
     * @return int
     */
    public function getCount()
    {
        return count($this->_aList);
    }
}
