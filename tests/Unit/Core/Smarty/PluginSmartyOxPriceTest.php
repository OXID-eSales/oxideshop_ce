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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core\Smarty;

use \stdClass;
use \oxPrice;
use \Smarty;
use \oxRegistry;

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/smarty/plugins/function.oxprice.php';

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
            //array(new oxPrice(1992.45), null, '1.992,45 EUR', null), // @todo: this leads to a bug in the oxprice->getPrice
            array(new oxPrice(1992.45), null, '1#992,45 EUR', array('EUR@ 1.00@ ,@ #@ EUR@ 2')),
        );
    }

    /**
     * Test using price as oxPrice object
     *
     * @dataProvider pricesAsObjects
     *
     * @param oxPrice  $oPrice            price
     * @param stdClass $oCurrency         currency object
     * @param string   $sExpectedOutput   expected output
     * @param array    $aCurrencies       The currencies we want to set for this run.
     */
    public function testFormatPrice_usingPriceAsObject($oPrice, $oCurrency, $sExpectedOutput, $aCurrencies = array())
    {
        $this->_setCurrencies($aCurrencies);

        $oSmarty = new Smarty();
        $aParams['price'] = $oPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals($sExpectedOutput, smarty_function_oxprice($aParams, $oSmarty));
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
            array(1992.45, null, '1#992,45 EUR', array('EUR@ 1.00@ ,@ #@ EUR@ 2')),
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
     * @param array    $aCurrencies       The currencies we want to set
     */
    public function testFormatPrice_usingPriceAsFloat($fPrice, $oCurrency, $sExpectedOutput, $aCurrencies = array())
    {
        $this->_setCurrencies($aCurrencies);

        $oSmarty = new Smarty();
        $aParams['price'] = $fPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals($sExpectedOutput, smarty_function_oxprice($aParams, $oSmarty));
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
            array('', $oEURCurrency, ''),
            array(null, $oUSDCurrency, ''),
            array(0, $oEURCurrency, '0,00 EUR'),
            array(0, $oEURCurrencyZero, '0 EUR'),
            array(0, $oUSDCurrency, 'USD0.000'),
            array(0, $oEmptyCurrency, ''),
            array(0, null, '0#00 EUR', array('EUR@ 1.00@ #@ .@ EUR@ 2')),
        );
    }

    /**
     * Test using price as null or zero
     *
     * @dataProvider pricesNullPrices
     *
     * @param float      $fPrice          price
     * @param stdClass   $oCurrency       currency object
     * @param string     $sExpectedOutput expected output
     * @param array      $aCurrencies     The currencies we want to set
     */
    public function testFormatPrice_badPriceOrCurrency($fPrice, $oCurrency, $sExpectedOutput, $aCurrencies = array())
    {
        $this->_setCurrencies($aCurrencies);

        $oSmarty = new Smarty();
        $aParams['price'] = $fPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals($sExpectedOutput, smarty_function_oxprice($aParams, $oSmarty));
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

    /**
     * Helper method to set the given currencies.
     *
     * @param array $aCurrencies The currencies we want to set.
     */
    protected function _setCurrencies($aCurrencies)
    {
        if ( !empty( $aCurrencies ) || is_null($aCurrencies) ) {
            $oConfig = oxRegistry::getConfig();

            $oConfig->setConfigParam('aCurrencies', $aCurrencies);
        }
    }
}
