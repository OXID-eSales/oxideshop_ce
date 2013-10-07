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
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';
require_once oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'core/smarty/plugins/function.oxprice.php';

class Unit_Maintenance_pluginSmartyOxPriceTest extends OxidTestCase
{
    /**
     * Data provider
     * @return array
     */
    public function pricesAsObjects()
    {
        $oEURCurrency = $this->_getEurCurrency();
        $oUSDCurrency = $this->_getUsdCurrency();
        $oEmptyCurrency = new stdClass();

        return array(
            array( new oxPrice( 12.12 ), $oEURCurrency, '12,12 EUR' ),
            array( new oxPrice( 0.12 ), $oEURCurrency, '0,12 EUR' ),
            array( new oxPrice( 120012.1 ), $oUSDCurrency, 'USD 120,012.100' ),
            array( new oxPrice( 1278 ), $oEURCurrency, '1.278,00 EUR' ),
            array( new oxPrice( 1992.45 ), $oEmptyCurrency, '1.992,45' ),
            array( new oxPrice( 1992.45 ), null, '1.992,45' ),
        );
    }

    /**
     * Test using price as oxPrice object
     *
     * @dataProvider pricesAsObjects
     *
     * @param oxPrice $oPrice price
     * @param stdClass $oCurrency currency object
     * @param string $sExpectedOutput expected output
     */
    public function testFormatPrice_usingPriceAsObject( $oPrice, $oCurrency, $sExpectedOutput )
    {
        $oSmarty = new Smarty();
        $aParams['price'] = $oPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals( $sExpectedOutput, smarty_function_oxprice( $aParams, $oSmarty ) );
    }

    /**
     * Data provider
     * @return array
     */
    public function pricesAsFloats()
    {
        $oEURCurrency = $this->_getEurCurrency();
        $oUSDCurrency = $this->_getUsdCurrency();
        $oEmptyCurrency = new stdClass();

        return array(
            array( 12.12, $oEURCurrency, '12,12 EUR' ),
            array( 0.12, $oEURCurrency, '0,12 EUR' ),
            array( 120012.1, $oUSDCurrency, 'USD 120,012.100' ),
            array( 1278, $oEURCurrency, '1.278,00 EUR' ),
            array( 1992.45, $oEmptyCurrency, '1.992,45' ),
            array( 1992.45, null, '1.992,45' ),
        );
    }

    /**
     * Test using price as float
     *
     * @dataProvider pricesAsFloats
     *
     * @param float $fPrice price
     * @param stdClass $oCurrency currency object
     * @param string $sExpectedOutput expected output
     */
    public function testFormatPrice_usingPriceAsFlout( $fPrice, $oCurrency, $sExpectedOutput )
    {
        $oSmarty = new Smarty();
        $aParams['price'] = $fPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals( $sExpectedOutput, smarty_function_oxprice( $aParams, $oSmarty ) );
    }

    /**
     * Data provider
     * @return array
     */
    public function pricesNullPrices()
    {
        $oEURCurrency = $this->_getEurCurrency();
        $oUSDCurrency = $this->_getUsdCurrency();
        $oEmptyCurrency = new stdClass();

        return array(
            array( '', $oEURCurrency, '' ),
            array( null, $oUSDCurrency, '' ),
            array( 0, $oEURCurrency, '0,00 EUR' ),
            array( 0, $oUSDCurrency, 'USD 0.000' ),
            array( 0, $oEmptyCurrency, '' ),
            array( 0, null, '' ),
        );
    }

    /**
     * Test using price as null or zero
     *
     * @dataProvider pricesNullPrices
     *
     * @param float $fPrice price
     * @param stdClass $oCurrency currency object
     * @param string $sExpectedOutput expected output
     */
    public function testFormatPrice_badPriceOrCurrency( $fPrice, $oCurrency, $sExpectedOutput )
    {
        $oSmarty = new Smarty();
        $aParams['price'] = $fPrice;
        $aParams['currency'] = $oCurrency;

        $this->assertEquals( $sExpectedOutput, smarty_function_oxprice( $aParams, $oSmarty ) );
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
}
