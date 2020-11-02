<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Price calculation class. Responsible for simple price calculations. Basically contains Brutto, Netto prices and VAT values.
 */
class Price
{
    /**
     * Brutto price.
     *
     * @var float
     */
    protected $_dBrutto = 0.0;

    /**
     * Netto price.
     *
     * @var float
     */
    protected $_dNetto = 0.0;

    /**
     * VAT percent.
     *
     * @var float
     */
    protected $_dVat = 0.0;

    /**
     * Assigned discount array.
     *
     * @var array
     */
    protected $_aDiscounts = null;

    /**
     * Price entering mode
     * Reference to myConfig->blEnterNetPrice
     * Then true  - setPrice sets netto price and calculates brutto price
     * Then false - setPrice sets brutto price and calculates netto price.
     *
     * @var bool
     */
    protected $_blNetPriceMode;

    /**
     * Class constructor. Gets price entering mode.
     *
     * @param float $dPrice given price
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function __construct($dPrice = null)
    {
        $this->setNettoMode(\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blEnterNetPrice'));

        if (null !== $dPrice) {
            $this->setPrice($dPrice);
        }
    }

    /**
     * Netto price mode setter.
     *
     * @param bool $blNetto state to set price to net mode (default true)
     */
    public function setNettoMode($blNetto = true): void
    {
        $this->_blNetPriceMode = $blNetto;
    }

    /**
     * return true if mode is netto.
     *
     * @return bool
     */
    public function isNettoMode()
    {
        return $this->_blNetPriceMode;
    }

    /**
     * Netto price mode setter.
     */
    public function setNettoPriceMode(): void
    {
        $this->setNettoMode();
    }

    /**
     * Brutto price mode setter.
     */
    public function setBruttoPriceMode(): void
    {
        $this->setNettoMode(false);
    }

    /**
     * Sets new VAT percent, and recalculates price.
     *
     * @param float $dVat vat percent
     */
    public function setVat($dVat): void
    {
        $this->_dVat = (float)$dVat;
    }

    /**
     * Sets new base VAT percent, recalculates brutto, and then netto price (in brutto mode).
     * if bruttoMode then BruttoPrice =(BruttoPrice - oldVAT% ) + newVat;
     * oldVAT = newVat;
     * finally recalculate;
     * USE ONLY TO CHANGE BASE VAT (in case when local VAT differs from user VAT),
     * USE setVat() in usual case !!!
     *
     * @param float $newVat vat percent
     */
    public function setUserVat($newVat): void
    {
        if (!$this->isNettoMode() && $newVat !== $this->_dVat) {
            $this->_dBrutto = self::Netto2Brutto(self::Brutto2Netto($this->_dBrutto, $this->_dVat), (float)$newVat);
        }
        $this->_dVat = (float)$newVat;
    }

    /**
     * Returns VAT percent.
     *
     * @return float
     */
    public function getVat()
    {
        return $this->_dVat;
    }

    /**
     * Sets new price and VAT percent(optional). Recalculates price by
     * price entering mode.
     *
     * @param float $dPrice new price
     * @param float $dVat   VAT
     */
    public function setPrice($dPrice, $dVat = null): void
    {
        if (null !== $dVat) {
            $this->setVat($dVat);
        }

        if ($this->isNettoMode()) {
            $this->_dNetto = $dPrice;
        } else {
            $this->_dBrutto = $dPrice;
        }
    }

    /**
     * Returns price depending on mode brutto or netto.
     *
     * @return float
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
     * Returns brutto price.
     *
     * @return float
     */
    public function getBruttoPrice()
    {
        if ($this->isNettoMode()) {
            return $this->getNettoPrice() + $this->getVatValue();
        } else {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dBrutto);
        }
    }

    /**
     * Returns netto price.
     *
     * @return float
     */
    public function getNettoPrice()
    {
        if ($this->isNettoMode()) {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($this->_dNetto);
        } else {
            return $this->getBruttoPrice() - $this->getVatValue();
        }
    }

    /**
     * Returns absolute VAT value.
     *
     * @return float
     */
    public function getVatValue()
    {
        if ($this->isNettoMode()) {
            $dVatValue = $this->getNettoPrice() * $this->getVat() / 100;
        } else {
            $dVatValue = $this->getBruttoPrice() * $this->getVat() / (100 + $this->getVat());
        }

        return \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($dVatValue);
    }

    /**
     * Subtracts given percent from price depending  on price entering mode,
     * and recalculates price.
     *
     * @param float $dValue percent to subtract from price
     */
    public function subtractPercent($dValue): void
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice - self::percent($dPrice, $dValue));
    }

    /**
     * Adds given percent to price depending  on price entering mode,
     * and recalculates price.
     *
     * @param float $dValue percent to add to price
     */
    public function addPercent($dValue): void
    {
        $this->subtractPercent(-$dValue);
    }

    /**
     * Adds another oxPrice object and recalculates current method.
     *
     * @param \OxidEsales\Eshop\Core\Price $oPrice object
     */
    public function addPrice(\OxidEsales\Eshop\Core\Price $oPrice): void
    {
        if ($this->isNettoMode()) {
            $this->add($oPrice->getNettoPrice());
        } else {
            $this->add($oPrice->getBruttoPrice());
        }
    }

    /**
     * Adds given value to price depending  on price entering mode,
     * and recalculates price.
     *
     * @param float $dValue value to add to price
     */
    public function add($dValue): void
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice + $dValue);
    }

    /**
     * Subtracts given value from price depending  on price entering mode,
     * and recalculates price.
     *
     * @param float $dValue value to subtracts from price
     */
    public function subtract($dValue): void
    {
        $this->add(-$dValue);
    }

    /**
     * Multiplies price by given value depending on price entering mode,
     * and recalculates price.
     *
     * @param float $dValue value for multiplying price
     */
    public function multiply($dValue): void
    {
        $dPrice = $this->getPrice();
        $this->setPrice($dPrice * $dValue);
    }

    /**
     * Divides price by given value depending on price entering mode,
     * and recalculates price.
     *
     * @param float $dValue value for dividing price
     */
    public function divide($dValue): void
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
     * @param \OxidEsales\Eshop\Core\Price $oPrice price object
     */
    public function compare(\OxidEsales\Eshop\Core\Price $oPrice)
    {
        $dBruttoPrice1 = $this->getBruttoPrice();
        $dBruttoPrice2 = $oPrice->getBruttoPrice();

        if ($dBruttoPrice1 === $dBruttoPrice2) {
            $iRes = 0;
        } elseif ($dBruttoPrice1 > $dBruttoPrice2) {
            $iRes = 1;
        } else {
            $iRes = -1;
        }

        return $iRes;
    }

    /**
     * Private function for percent value calculations.
     *
     * @param float $dValue   value
     * @param float $dPercent percent
     *
     * @return float
     */
    public static function percent($dValue, $dPercent)
    {
        return ((float)$dValue * (float)$dPercent) / 100.0;
    }

    /**
     * Converts Brutto price to Netto using formula:
     * X + $dVat% = $dBrutto
     * X/100 = $dBrutto/(100+$dVAT)
     * X= ($dBrutto/(100+$dVAT))/100
     * returns X.
     *
     * @param float $dBrutto brutto price
     * @param float $dVat    vat
     *
     * @return float
     */
    public static function brutto2Netto($dBrutto, $dVat)
    {
        // if VAT = -100% Return 0 because we subtract all what we have.
        // made to avoid division by zero in formula.
        if (-100 === $dVat) {
            return 0;
        }

        return (float)((float)$dBrutto * 100.0) / (100.0 + (float)$dVat);
    }

    /**
     * Converts Netto price to Brutto using formula:
     * X = $dNetto + $dVat%
     * returns X.
     *
     * @param float $dNetto netto price
     * @param float $dVat   vat
     *
     * @return float
     */
    public static function netto2Brutto($dNetto, $dVat)
    {
        return (float)$dNetto + self::percent($dNetto, $dVat);
    }

    /**
     * Returns price multiplied by current currency.
     *
     * @param string $dPrice price value
     *
     * @return float
     */
    public static function getPriceInActCurrency($dPrice)
    {
        $oCur = \OxidEsales\Eshop\Core\Registry::getConfig()->getActShopCurrencyObject();

        return ((float)$dPrice) * $oCur->rate;
    }

    /**
     * Sets discount to price.
     *
     * @param float  $dValue discount value
     * @param string $sType  discount type: abs or %
     */
    public function setDiscount($dValue, $sType): void
    {
        $this->_aDiscounts[] = [
            'value' => $dValue,
            'type' => $sType,
        ];
    }

    /**
     * Returns assigned discounts.
     *
     * @return array
     */
    public function getDiscounts()
    {
        return $this->_aDiscounts;
    }

    /**
     * Flush assigned discounts.
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "flushDiscounts" in next major
     */
    protected function _flushDiscounts(): void // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_aDiscounts = null;
    }

    /**
     * Calculates price: affects discounts.
     */
    public function calculateDiscount(): void
    {
        $dPrice = $this->getPrice();
        $aDiscounts = $this->getDiscounts();

        if ($aDiscounts) {
            foreach ($aDiscounts as $aDiscount) {
                if ('abs' === $aDiscount['type']) {
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
