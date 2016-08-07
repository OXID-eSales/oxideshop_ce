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

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxpriceTest extends OxidTestCase
{
    protected $_oPrice;
    protected $_oConfig;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oConfig = oxConfig::getInstance();
        //$this->_oConfig = oxConfig::getInstance();
        $this->_oPrice = new oxPrice();
        $this->_oPrice->setBruttoPriceMode();

        //Default price calculation mode
        $this->_oConfig->blEnterNetPrice = false;
    }

    public function testGetPriceInActCurrency()
    {
        $oSessCurr = oxConfig::getInstance()->getActShopCurrencyObject();
        $dPrice = 99.66;

        $this->assertEquals( $dPrice * $oSessCurr->rate, oxPrice::getPriceInActCurrency( $dPrice ) );
    }

    public function testVatSetterAndGetter()
    {
        $this->_oPrice->setVat(18);
        $this->assertEquals(18, $this->_oPrice->getVat());

        $this->_oPrice->setVat(4.5);
        $this->assertEquals(4.5, $this->_oPrice->getVat());

        $this->_oPrice->setVat(-4.5);
        $this->assertEquals(-4.5, $this->_oPrice->getVat());

        $this->_oPrice->setVat(-4.4);
        $this->assertNotEquals(-4, $this->_oPrice->getVat());
    }

    public function testVatPriceGetter()
    {
        $this->_oPrice->setPrice(18, 0);
        $this->assertEquals(0.00, $this->_oPrice->getVatValue());

        $this->_oPrice->setPrice(118, 18);
        $this->assertEquals(18.00, $this->_oPrice->getVatValue());
    }

    public function testVatPriceGetterWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(18, 0);
        $this->assertEquals(0.00, $this->_oPrice->getVatValue());

        $this->_oPrice->setPrice(100, 18);
        $this->assertEquals(18.00, $this->_oPrice->getVatValue());
    }

    public function testSetUserVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->setUserVat(19);
        $this->assertEquals(119.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(100.00, $this->_oPrice->getNettoPrice());
    }

    public function testSetUserVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 18);
        $this->_oPrice->setUserVat(19);
        $this->assertEquals(119.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(100.00, $this->_oPrice->getNettoPrice());
    }

    public function testPriceSetterAndGetter()
    {
        $this->_oPrice->setPrice(18, 0);
        $this->assertEquals(18.00, $this->_oPrice->getBruttoPrice());
        $this->_oPrice->setPrice(97.58, 18);
        $this->assertEquals(97.58, $this->_oPrice->getBruttoPrice());
    }

    public function testPriceSetterAndGetterWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(99, 0);
        $this->assertEquals(99.00, $this->_oPrice->getNettoPrice());
        $this->_oPrice->setPrice(873.57, 16);
        $this->assertEquals(873.57, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithZeroVat()
    {
        $this->_oPrice->setVat(0);
        $this->_oPrice->setPrice(18);
        $this->assertEquals(18.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(18.00, $this->_oPrice->getNettoPrice());

        $this->_oPrice->setPrice(7.58);
        $this->assertEquals(7.58, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(7.58, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithZeroVat()
    {
        $this->_oPrice->setVat(0);
        $this->_oPrice->setPrice(-18);
        $this->assertEquals(-18.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-18.00, $this->_oPrice->getNettoPrice());

        $this->_oPrice->setPrice(-7.58);
        $this->assertEquals(-7.58, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-7.58, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithPositiveVat()
    {
        $this->_oPrice->setVat(18);
        $this->_oPrice->setPrice(118);
        $this->assertEquals(118.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(100.00, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithNegativeVat()
    {
        $this->_oPrice->setVat(-99.00);
        $this->_oPrice->setPrice(118);
        $this->assertEquals(118.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(11800.00, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithPositiveVat()
    {
        $this->_oPrice->setVat(19);
        $this->_oPrice->setPrice(-119);
        $this->assertEquals(-119, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-100, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithNegativeVat()
    {
        $this->_oPrice->setVat(-16.00);
        $this->_oPrice->setPrice(-84);
        $this->assertEquals(-84.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-100, $this->_oPrice->getNettoPrice());
    }

    // --------------------------------------------------------------------------------------
    public function testPositivePriceCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(18, 0);
        $this->assertEquals(18.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(18.00, $this->_oPrice->getNettoPrice());

        $this->_oPrice->setPrice(7.58, 0);
        $this->assertEquals(7.58, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(7.58, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithZeroVatWithNetPriceMode()
    {

        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(-18, 0);
        $this->assertEquals(-18.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-18.00, $this->_oPrice->getNettoPrice());

        $this->_oPrice->setPrice(-7.58);
        $this->assertEquals(-7.58, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-7.58, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithPositiveVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(118, 18);
        $this->assertEquals(139.24, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(118.00, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithNegativeVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, -99.00);
        $this->assertEquals(1.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(100.00, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithPositiveVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(-119, 19);
        $this->assertEquals(-141.61, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-119, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithNegativeVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(-84, -16.00);
        $this->assertEquals(-70.56, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-84.00, $this->_oPrice->getNettoPrice());
    }
// ------------------------------------------------------------------------------------------------------

    public function testAddPercentCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->addPercent(50);

        $this->assertEquals(150.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(150.00, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractPercentCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->subtractPercent(50);

        $this->assertEquals(50, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(50, $this->_oPrice->getNettoPrice());
    }

    public function testAddFixedCalculationZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->add(25);

        $this->assertEquals(125, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(125, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractFixedCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->subtract(25);

        $this->assertEquals(75, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(75, $this->_oPrice->getNettoPrice());
    }


    public function testAddPercentCalculationWithVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->addPercent(50);

        $this->assertEquals(177.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(150.00, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractPercentCalculationWithVat()
    {
        $this->_oPrice->setPrice(177, 18);
        $this->_oPrice->subtractPercent(50);

        $this->assertEquals(88.50, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(75.00, $this->_oPrice->getNettoPrice());
    }

    public function testAddFixedCalculationWithVat()
    {
        $this->_oPrice->setPrice(236, 18);
        $this->_oPrice->add(118);

        $this->assertEquals(354.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(300.00, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractFixedCalculationWithVat()
    {
        $this->_oPrice->setPrice(354, 18);
        $this->_oPrice->subtract(118);

        $this->assertEquals(236.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(200.00, $this->_oPrice->getNettoPrice());
    }


    public function testAddPercentCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 18);
        $this->_oPrice->addPercent(50);

        $this->assertEquals(177.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(150.00, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractPercentCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(150, 18);
        $this->_oPrice->subtractPercent(50);

        $this->assertEquals(88.50, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(75.00, $this->_oPrice->getNettoPrice());
    }

    public function testAddFixedCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(200, 18);
        $this->_oPrice->add(100);

        $this->assertEquals(354.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(300.00, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractFixedCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(300, 18);
        $this->_oPrice->subtract(100);

        $this->assertEquals(236.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(200.00, $this->_oPrice->getNettoPrice());
    }

// multiply

    public function testMultiplyCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(300, 0);
        $this->_oPrice->multiply(10);

        $this->assertEquals(3000.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(3000.00, $this->_oPrice->getNettoPrice());
    }

    public function testMultiplyCalculationWithVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->multiply(-2);

        $this->assertEquals(-236.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-200.00, $this->_oPrice->getNettoPrice());
    }

    public function testMultiplyCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(278, 0);
        $this->_oPrice->multiply(-10);

        $this->assertEquals(-2780.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-2780.00, $this->_oPrice->getNettoPrice());
    }

    public function testMultiplyCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 20);
        $this->_oPrice->multiply(3.2);

        $this->assertEquals(384.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(320.00, $this->_oPrice->getNettoPrice());
    }
// --------------

    public function testDivideCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(300, 0);
        $this->_oPrice->divide(10);

        $this->assertEquals(30.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(30.00, $this->_oPrice->getNettoPrice());
    }

    public function testDivideCalculationWithVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->divide(-2);

        $this->assertEquals(-59.00, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-50.00, $this->_oPrice->getNettoPrice());
    }

    public function testDivideCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(278, 0);
        $this->_oPrice->divide(-10);

        $this->assertEquals(-27.80, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(-27.80, $this->_oPrice->getNettoPrice());
    }

    public function testDivideCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 20);
        $this->_oPrice->divide(3.2);

        $this->assertEquals(37.50, $this->_oPrice->getBruttoPrice());
        $this->assertEquals(31.25, $this->_oPrice->getNettoPrice());
    }

    public function testAddWithPriceObject()
    {
        $oPrice = new oxPrice();
        $oPrice->setPrice(236, 18);

        $oPrice2 = new oxPrice();
        $oPrice2->setPrice(118, 18);

        $oPrice->addPrice($oPrice2);
        $this->assertEquals(354.00, $oPrice->getBruttoPrice());
        $this->assertEquals(300.00, $oPrice->getNettoPrice());
    }

    public function testAddWithPriceObjectIfNetPriceMode()
    {
        $oPrice = new oxPrice();
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(200, 18);

        $oPrice2 = new oxPrice();
        $oPrice2->setNettoPriceMode();
        $oPrice2->setPrice(100, 18);

        $oPrice->addPrice($oPrice2);
        $this->assertEquals(354.00, $oPrice->getBruttoPrice());
        $this->assertEquals(300.00, $oPrice->getNettoPrice());
    }

    public function testBrutto2Netto()
    {
        $this->assertEquals( 200.00, $this->_oPrice->brutto2Netto(236, 18) );
        $this->assertLessThan(0.00001, abs(200.10462372881 - $this->_oPrice->brutto2Netto(236.123456, 18) ) );
    }

    public function testBrutto2NettoMinusVat()
    {
        $this->assertEquals( 0, $this->_oPrice->brutto2Netto(236, -100) );
    }

    public function testNetto2Brutto()
    {
        $this->assertEquals( 120.00, $this->_oPrice->netto2Brutto(100, 20) );
        $this->assertLessThan(0.00001, abs(120.1481472 - $this->_oPrice->netto2Brutto(100.123456, 20) ) );
    }

    public function testCompare()
    {
        $oPrice1 = new oxPrice();
        $oPrice1->setPrice(100, 20);

        $oPrice2 = new oxPrice();
        $oPrice2->setPrice(101, 0);

        $oPrice3 = new oxPrice();
        $oPrice3->setPrice(99, 0);

        $oPrice4 = new oxPrice();
        $oPrice4->setPrice(100, 0);

        $this->assertEquals( 0, $oPrice1->compare($oPrice4) );
        $this->assertEquals( 1, $oPrice1->compare($oPrice3) );
        $this->assertEquals( -1, $oPrice1->compare($oPrice2) );
    }

    public function testInitWithParams()
    {
        $oPrice = new oxPrice(15);
        $this->assertEquals(15, $oPrice->getBruttoPrice());
    }

    /**
     * Test netto price calculation then in brutto mode.
     *
     * @return null
     */
    /*public function testRecalculate_bruttomode()
    {
        $oPriceProxy = $this->getProxyClass('oxPrice');
        $oPriceProxy->setNonPublicVar('_blNetPriceMode', false);
        $oPriceProxy->setNonPublicVar('_dVat', 7);
        $oPriceProxy->setNonPublicVar('_dBrutto', 41.5);

        $oPriceProxy->UNITrecalculate();

        $this->assertLessThanOrEqual(0.00001, abs($oPriceProxy->getNonPublicVar('_dNetto') - 38.785046728972));
    }*/

    /**
     * Test brutto price calculation then in netto mode.
     *
     * @return null
     */
    /*public function testRecalculate_nettomode()
    {
        $oPriceProxy = $this->getProxyClass('oxPrice');
        $oPriceProxy->setNonPublicVar('_blNetPriceMode', true);
        $oPriceProxy->setNonPublicVar('_dVat', 7);
        $oPriceProxy->setNonPublicVar('_dNetto', 38.785046728972);

        $oPriceProxy->UNITrecalculate();

        $this->assertLessThanOrEqual(0.00001, abs($oPriceProxy->getNonPublicVar('_dBrutto') - 41.5));
    }*/


    /**
     * Test getPrice
     *
     * @return null
     */
    public function testGetPrice()
    {
        $oPrice = new oxPrice();
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertEquals( 10, $oPrice->getPrice() );

        $oPrice = new oxPrice();
        $oPrice->setBruttoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertEquals( 10, $oPrice->getPrice() );

    }

    /**
     * Test getPrice
     *
     * @return null
     */
    public function testNettoBruttoCalculation()
    {
        $oPrice = new oxPrice();
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertEquals( 10, $oPrice->getNettoPrice() );
        $this->assertEquals( 11.9, $oPrice->getBruttoPrice() );
        $this->assertEquals( 1.9, $oPrice->getVatValue() );

        $oPrice = new oxPrice();
        $oPrice->setBruttoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertEquals( 8.40, $oPrice->getNettoPrice() );
        $this->assertEquals( 10, $oPrice->getBruttoPrice() );
        $this->assertEquals( 1.60, $oPrice->getVatValue() );

    }


    /**
     * Test getPrice
     *
     * @return null
     */
    public function testApplyDiscount()
    {
        $oPrice = new oxPrice();
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(10, 19);

        $oPrice->setDiscount( 50, '%');
        $oPrice->calculateDiscount();
        $this->assertEquals( 5, $oPrice->getNettoPrice() );
        $this->assertEquals( 5.95, $oPrice->getBruttoPrice() );
        $this->assertEquals( 0.95, $oPrice->getVatValue() );

        $oPrice->setDiscount( 1, 'abs');
        $oPrice->calculateDiscount();
        $this->assertEquals( 4, $oPrice->getNettoPrice() );
        $this->assertEquals( 4.76, $oPrice->getBruttoPrice() );
        $this->assertEquals( 0.76, $oPrice->getVatValue() );

        $oPrice->setDiscount( -1, 'abs');
        $oPrice->calculateDiscount();
        $this->assertEquals( 5, $oPrice->getNettoPrice() );
        $this->assertEquals( 5.95, $oPrice->getBruttoPrice() );
        $this->assertEquals( 0.95, $oPrice->getVatValue() );

        $oPrice->setDiscount( -20, '%');
        $oPrice->calculateDiscount();
        $this->assertEquals( 6, $oPrice->getNettoPrice() );
        $this->assertEquals( 7.14, $oPrice->getBruttoPrice() );
        $this->assertEquals( 1.14, $oPrice->getVatValue() );


        $oPrice->setDiscount( 7, 'abs');
        $oPrice->calculateDiscount();
        $this->assertEquals( 0, $oPrice->getNettoPrice() );
        $this->assertEquals( 0, $oPrice->getBruttoPrice() );
        $this->assertEquals( 0, $oPrice->getVatValue() );

    }

    /**
     * `roundCents` should not perform any price changes if argument type is not integer.
     */
    public function testRoundCents_wrongArgumentType_priceRemainsUnchanged()
    {
        $aWrongArguments = array('', ' ', '5', 'two', true, false, null, 2.1, array(5), new oxStdClass(), function () {
        });

        foreach ( $aWrongArguments as $mRoundPrecision ) {
            $this->SUT->roundCents( $mRoundPrecision );
            $this->assertSame( 17.93, $this->SUT->getPrice() );
        }
    }

    /**
     * `roundCents` should not perform any price changes if argument type is one cent of zero or negative.
     */
    public function testRoundCents_argumentLessThanTwo_priceRemainsUnchanged()
    {
        $aWrongArguments = array(-10, -1, 0, 1);

        foreach ( $aWrongArguments as $iRoundPrecision ) {
            $this->SUT->roundCents( $iRoundPrecision );
            $this->assertSame( 17.93, $this->SUT->getPrice() );
        }
    }

    /**
     * `roundCents` should not perform any price changes if argument is more that 100 cents.
     */
    public function testRoundCents_argumentMoreThanHundred_priceRemainsUnchanged()
    {
        $aWrongArguments = array(101, 1000);

        foreach ( $aWrongArguments as $iRoundPrecision ) {
            $this->SUT->roundCents( $iRoundPrecision );
            $this->assertSame( 17.93, $this->SUT->getPrice() );
        }
    }

    /**
     * `roundCents` should round internal price value with given precision in cents, when argument is valid.
     */
    public function testRoundCents_validArgument_priceIsRounded()
    {
        $aValidArguments = array(
            2   => 17.94,
            3   => 17.93,
            5   => 17.95,
            6   => 17.96,
            7   => 17.91,
            8   => 17.96,
            10  => 17.90,
            12  => 17.96,
            17  => 17.85,
            20  => 18.00,
            100 => 18.00,
        );

        foreach ( $aValidArguments as $iRoundPrecision => $dExpectedPriceValue ) {
            $this->SUT->setPrice( 17.93 );
            $this->SUT->roundCents( $iRoundPrecision );

            $this->assertSame( $dExpectedPriceValue, $this->SUT->getPrice() );
        }
    }
}
