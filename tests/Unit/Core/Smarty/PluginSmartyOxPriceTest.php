<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use oxRegistry;
use \stdClass;
use \oxPrice;
use \Smarty;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxprice.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/function.oxprice.php';
}

class PluginSmartyOxPriceTest extends \OxidTestCase
{

    /**
     * Data provider
     *
     * @return array
     */
    public function pricesAsObjects()
    {
        $oEURCurrency = $this->_getEurCurrency();
        $oUSDCurrency = $this->_getUsdCurrency();
        $oEmptyCurrency = new stdClass();

        return array(
            array(new oxPrice(12.12), $oEURCurrency, '12,12 EUR'),
            array(new oxPrice(0.12), $oEURCurrency, '0,12 EUR'),
            array(new oxPrice(120012.1), $oUSDCurrency, 'USD120,012.100'),
            array(new oxPrice(1278), $oEURCurrency, '1.278,00 EUR'),
            array(new oxPrice(1992.45), $oEmptyCurrency, '1.992,45'),
            array(new oxPrice(1992.45), null, '1.992,45 ?'),
        );
    }

    /**
     * Test using price as oxPrice object
     *
     * @dataProvider pricesAsObjects
     *
     * @param oxPrice  $oPrice          price
     * @param stdClass $oCurrency       currency object
     * @param string   $sExpectedOutput expected output
     */
    public function testFormatPrice_usingPriceAsObject($oPrice, $oCurrency, $sExpectedOutput)
    {
        $oSmarty = new Smarty();
        $aParams['price'] = $oPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals(utf8_decode($sExpectedOutput), utf8_decode(smarty_function_oxprice($aParams, $oSmarty)));
    }

    /**
     * Test, that the oxprice smarty plugin will use the admin setted currency, if we don't give some currency object in.
     */
    public function testNoCurrencyObjectAsParameterButInConfig()
    {
        $this->_setCurrencies(array('EUR@ 1.00@ ,@ #@ €@ 2'));

        $oSmarty = new Smarty();

        $aParams = array(
            'price' => new oxPrice(1992.45),
        );

        $this->assertEquals('1#992,45 €', smarty_function_oxprice($aParams, $oSmarty));
    }

    /**
     * Helper method to set the given currencies.
     *
     * @param array $aCurrencies The currencies we want to set.
     */
    protected function _setCurrencies($aCurrencies)
    {
        if (!empty($aCurrencies) || is_null($aCurrencies)) {
            $oConfig = Registry::getConfig();

            $oConfig->setConfigParam('aCurrencies', $aCurrencies);
        }
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function pricesAsFloats()
    {
        $oEURCurrency = $this->_getEurCurrency();
        $oUSDCurrency = $this->_getUsdCurrency();
        $oEURCurrencyZero = $this->_getEurCurrencyZeroDecimal();
        $oEmptyCurrency = new stdClass();

        return array(
            array(12.12, $oEURCurrency, '12,12 EUR'),
            array(12.12, $oEURCurrencyZero, '12 EUR'),
            array(0.12, $oEURCurrency, '0,12 EUR'),
            array(0.12, $oEURCurrencyZero, '0 EUR'),
            array(120012.1, $oUSDCurrency, 'USD120,012.100'),
            array(1278, $oEURCurrency, '1.278,00 EUR'),
            array(1278, $oEURCurrencyZero, '1.278 EUR'),
            array(1992.45, $oEmptyCurrency, '1.992,45'),
            array(1992.45, null, '1.992,45 ?'),
        );
    }

    /**
     * Test using price as float
     *
     * @dataProvider pricesAsFloats
     *
     * @param float    $fPrice          price
     * @param stdClass $oCurrency       currency object
     * @param string   $sExpectedOutput expected output
     */
    public function testFormatPrice_usingPriceAsFloat($fPrice, $oCurrency, $sExpectedOutput)
    {
        $oSmarty = new Smarty();
        $aParams['price'] = $fPrice;
        $aParams['currency'] = $oCurrency;

        // we utf8 decode here to make the test more robust against shop settings
        $this->assertEquals(utf8_decode($sExpectedOutput), utf8_decode(smarty_function_oxprice($aParams, $oSmarty)));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function pricesNullPrices()
    {
        $oEURCurrency = $this->_getEurCurrency();
        $oUSDCurrency = $this->_getUsdCurrency();
        $oEURCurrencyZero = $this->_getEurCurrencyZeroDecimal();
        $oEmptyCurrency = new stdClass();

        return array(
            array('', $oEURCurrency, '0,00 EUR'),
            array(null, $oUSDCurrency, ''),
            array(0, $oEURCurrency, '0,00 EUR'),
            array(0, $oEURCurrencyZero, '0 EUR'),
            array(0, $oUSDCurrency, 'USD0.000'),
            array(0, $oEmptyCurrency, ''),
            array(0, null, '0,00 ?'),
        );
    }

    /**
     * Test using price as null or zero
     *
     * @dataProvider pricesNullPrices
     *
     * @param float    $fPrice          price
     * @param stdClass $oCurrency       currency object
     * @param string   $sExpectedOutput expected output
     */
    public function testFormatPrice_badPriceOrCurrency($fPrice, $oCurrency, $sExpectedOutput)
    {
        $oSmarty = new Smarty();
        $aParams['price'] = $fPrice;
        $aParams['currency'] = $oCurrency;

        // we utf8 decode here to make the test more robust against shop settings
        $this->assertEquals(utf8_decode($sExpectedOutput), utf8_decode(smarty_function_oxprice($aParams, $oSmarty)));
    }

    /**
     * @return stdClass
     */
    protected function _getUsdCurrency()
    {
        $oUSDCurrency = new stdClass();
        $oUSDCurrency->dec = '.';
        $oUSDCurrency->thousand = ',';
        $oUSDCurrency->sign = 'USD';
        $oUSDCurrency->decimal = 3;
        $oUSDCurrency->side = 'Front';

        return $oUSDCurrency;
    }

    /**
     * @return stdClass
     */
    protected function _getEurCurrency()
    {
        $oEURCurrency = new stdClass();
        $oEURCurrency->dec = ',';
        $oEURCurrency->thousand = '.';
        $oEURCurrency->sign = 'EUR';
        $oEURCurrency->decimal = 2;

        return $oEURCurrency;
    }

    /**
     * @return stdClass
     */
    protected function _getEurCurrencyZeroDecimal()
    {
        $oEURCurrency = new stdClass();
        $oEURCurrency->dec = ',';
        $oEURCurrency->thousand = '.';
        $oEURCurrency->sign = 'EUR';
        $oEURCurrency->decimal = 0;

        return $oEURCurrency;
    }
}
