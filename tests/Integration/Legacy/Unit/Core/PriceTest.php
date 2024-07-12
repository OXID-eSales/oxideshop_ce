<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxPrice;

class PriceTest extends \PHPUnit\Framework\TestCase
{
    protected $_oPrice;

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_oPrice = oxNew('oxPrice');
        $this->_oPrice->setBruttoPriceMode();
    }

    public function testGetPriceInActCurrency()
    {
        $oCurrency = $this->getConfig()->getActShopCurrencyObject();
        $dPrice = 99.66;

        $this->assertSame($dPrice * $oCurrency->rate, oxPrice::getPriceInActCurrency($dPrice));
    }

    public function testVatSetterAndGetter()
    {
        $this->_oPrice->setVat(18);
        $this->assertSame(18, $this->_oPrice->getVat());

        $this->_oPrice->setVat(4.5);
        $this->assertEqualsWithDelta(4.5, $this->_oPrice->getVat(), PHP_FLOAT_EPSILON);

        $this->_oPrice->setVat(-4.5);
        $this->assertSame(-4.5, $this->_oPrice->getVat());

        $this->_oPrice->setVat(-4.4);
        $this->assertNotSame(-4, $this->_oPrice->getVat());
    }

    public function testVatPriceGetter()
    {
        $this->_oPrice->setPrice(18, 0);
        $this->assertEqualsWithDelta(0.00, $this->_oPrice->getVatValue(), PHP_FLOAT_EPSILON);

        $this->_oPrice->setPrice(118, 18);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getVatValue(), PHP_FLOAT_EPSILON);
    }

    public function testVatPriceGetterWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(18, 0);
        $this->assertEqualsWithDelta(0.00, $this->_oPrice->getVatValue(), PHP_FLOAT_EPSILON);

        $this->_oPrice->setPrice(100, 18);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getVatValue(), PHP_FLOAT_EPSILON);
    }

    public function testSetUserVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->setUserVat(19);
        $this->assertEqualsWithDelta(119.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(100.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testSetUserVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 18);
        $this->_oPrice->setUserVat(19);
        $this->assertEqualsWithDelta(119.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(100.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testPriceSetterAndGetter()
    {
        $this->_oPrice->setPrice(18, 0);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->_oPrice->setPrice(97.58, 18);
        $this->assertEqualsWithDelta(97.58, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testPriceSetterAndGetterWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(99, 0);
        $this->assertEqualsWithDelta(99.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
        $this->_oPrice->setPrice(873.57, 16);
        $this->assertEqualsWithDelta(873.57, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testPositivePriceCalculationWithZeroVat()
    {
        $this->_oPrice->setVat(0);
        $this->_oPrice->setPrice(18);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);

        $this->_oPrice->setPrice(7.58);
        $this->assertEqualsWithDelta(7.58, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(7.58, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testNegativePriceCalculationWithZeroVat()
    {
        $this->_oPrice->setVat(0);
        $this->_oPrice->setPrice(-18);
        $this->assertSame(-18.00, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-18.00, $this->_oPrice->getNettoPrice());

        $this->_oPrice->setPrice(-7.58);
        $this->assertSame(-7.58, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-7.58, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithPositiveVat()
    {
        $this->_oPrice->setVat(18);
        $this->_oPrice->setPrice(118);
        $this->assertEqualsWithDelta(118.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(100.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testPositivePriceCalculationWithNegativeVat()
    {
        $this->_oPrice->setVat(-99.00);
        $this->_oPrice->setPrice(118);
        $this->assertEqualsWithDelta(118.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(11800.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testNegativePriceCalculationWithPositiveVat()
    {
        $this->_oPrice->setVat(19);
        $this->_oPrice->setPrice(-119);
        $this->assertSame(-119, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-100, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithNegativeVat()
    {
        $this->_oPrice->setVat(-16.00);
        $this->_oPrice->setPrice(-84);
        $this->assertSame(-84.00, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-100, $this->_oPrice->getNettoPrice());
    }

    // --------------------------------------------------------------------------------------
    public function testPositivePriceCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(18, 0);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(18.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);

        $this->_oPrice->setPrice(7.58, 0);
        $this->assertEqualsWithDelta(7.58, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(7.58, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testNegativePriceCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(-18, 0);
        $this->assertSame(-18.00, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-18.00, $this->_oPrice->getNettoPrice());

        $this->_oPrice->setPrice(-7.58);
        $this->assertSame(-7.58, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-7.58, $this->_oPrice->getNettoPrice());
    }

    public function testPositivePriceCalculationWithPositiveVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(118, 18);
        $this->assertEqualsWithDelta(139.24, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(118.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testPositivePriceCalculationWithNegativeVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, -99.00);
        $this->assertEqualsWithDelta(1.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(100.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testNegativePriceCalculationWithPositiveVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(-119, 19);
        $this->assertSame(-141.61, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-119, $this->_oPrice->getNettoPrice());
    }

    public function testNegativePriceCalculationWithNegativeVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(-84, -16.00);
        $this->assertSame(-70.56, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-84.00, $this->_oPrice->getNettoPrice());
    }

    // ------------------------------------------------------------------------------------------------------

    public function testAddPercentCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->addPercent(50);

        $this->assertEqualsWithDelta(150.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(150.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testSubtractPercentCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->subtractPercent(50);

        $this->assertSame(50, $this->_oPrice->getBruttoPrice());
        $this->assertSame(50, $this->_oPrice->getNettoPrice());
    }

    public function testAddFixedCalculationZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->add(25);

        $this->assertSame(125, $this->_oPrice->getBruttoPrice());
        $this->assertSame(125, $this->_oPrice->getNettoPrice());
    }

    public function testSubtractFixedCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(100, 0);
        $this->_oPrice->subtract(25);

        $this->assertSame(75, $this->_oPrice->getBruttoPrice());
        $this->assertSame(75, $this->_oPrice->getNettoPrice());
    }


    public function testAddPercentCalculationWithVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->addPercent(50);

        $this->assertEqualsWithDelta(177.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(150.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testSubtractPercentCalculationWithVat()
    {
        $this->_oPrice->setPrice(177, 18);
        $this->_oPrice->subtractPercent(50);

        $this->assertEqualsWithDelta(88.50, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(75.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testAddFixedCalculationWithVat()
    {
        $this->_oPrice->setPrice(236, 18);
        $this->_oPrice->add(118);

        $this->assertEqualsWithDelta(354.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(300.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testSubtractFixedCalculationWithVat()
    {
        $this->_oPrice->setPrice(354, 18);
        $this->_oPrice->subtract(118);

        $this->assertEqualsWithDelta(236.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(200.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }


    public function testAddPercentCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 18);
        $this->_oPrice->addPercent(50);

        $this->assertEqualsWithDelta(177.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(150.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testSubtractPercentCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(150, 18);
        $this->_oPrice->subtractPercent(50);

        $this->assertEqualsWithDelta(88.50, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(75.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testAddFixedCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(200, 18);
        $this->_oPrice->add(100);

        $this->assertEqualsWithDelta(354.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(300.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testSubtractFixedCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(300, 18);
        $this->_oPrice->subtract(100);

        $this->assertEqualsWithDelta(236.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(200.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    // multiply

    public function testMultiplyCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(300, 0);
        $this->_oPrice->multiply(10);

        $this->assertEqualsWithDelta(3000.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(3000.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testMultiplyCalculationWithVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->multiply(-2);

        $this->assertSame(-236.00, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-200.00, $this->_oPrice->getNettoPrice());
    }

    public function testMultiplyCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(278, 0);
        $this->_oPrice->multiply(-10);

        $this->assertSame(-2780.00, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-2780.00, $this->_oPrice->getNettoPrice());
    }

    public function testMultiplyCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 20);
        $this->_oPrice->multiply(3.2);

        $this->assertEqualsWithDelta(384.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(320.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    // --------------

    public function testDivideCalculationWithZeroVat()
    {
        $this->_oPrice->setPrice(300, 0);
        $this->_oPrice->divide(10);

        $this->assertEqualsWithDelta(30.00, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(30.00, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testDivideCalculationWithVat()
    {
        $this->_oPrice->setPrice(118, 18);
        $this->_oPrice->divide(-2);

        $this->assertSame(-59.00, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-50.00, $this->_oPrice->getNettoPrice());
    }

    public function testDivideCalculationWithZeroVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(278, 0);
        $this->_oPrice->divide(-10);

        $this->assertSame(-27.80, $this->_oPrice->getBruttoPrice());
        $this->assertSame(-27.80, $this->_oPrice->getNettoPrice());
    }

    public function testDivideCalculationWithVatWithNetPriceMode()
    {
        $this->_oPrice->setNettoPriceMode();
        $this->_oPrice->setPrice(100, 20);
        $this->_oPrice->divide(3.2);

        $this->assertEqualsWithDelta(37.50, $this->_oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(31.25, $this->_oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testAddWithPriceObject()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(236, 18);

        $oPrice2 = oxNew('oxPrice');
        $oPrice2->setPrice(118, 18);

        $oPrice->addPrice($oPrice2);
        $this->assertEqualsWithDelta(354.00, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(300.00, $oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testAddWithPriceObjectIfNetPriceMode()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(200, 18);

        $oPrice2 = oxNew('oxPrice');
        $oPrice2->setNettoPriceMode();
        $oPrice2->setPrice(100, 18);

        $oPrice->addPrice($oPrice2);
        $this->assertEqualsWithDelta(354.00, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(300.00, $oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testBrutto2Netto()
    {
        $this->assertEqualsWithDelta(200.00, $this->_oPrice->brutto2Netto(236, 18), PHP_FLOAT_EPSILON);
        $this->assertLessThan(0.00001, abs(200.10462372881 - $this->_oPrice->brutto2Netto(236.123456, 18)));
    }

    public function testBrutto2NettoMinusVat()
    {
        $this->assertSame(0, $this->_oPrice->brutto2Netto(236, -100));
    }

    public function testNetto2Brutto()
    {
        $this->assertEqualsWithDelta(120.00, $this->_oPrice->netto2Brutto(100, 20), PHP_FLOAT_EPSILON);
        $this->assertLessThan(0.00001, abs(120.1481472 - $this->_oPrice->netto2Brutto(100.123456, 20)));
    }

    public function testCompare()
    {
        $oPrice1 = oxNew('oxPrice');
        $oPrice1->setPrice(100, 20);

        $oPrice2 = oxNew('oxPrice');
        $oPrice2->setPrice(101, 0);

        $oPrice3 = oxNew('oxPrice');
        $oPrice3->setPrice(99, 0);

        $oPrice4 = oxNew('oxPrice');
        $oPrice4->setPrice(100, 0);

        $this->assertSame(0, $oPrice1->compare($oPrice4));
        $this->assertSame(1, $oPrice1->compare($oPrice3));
        $this->assertSame(-1, $oPrice1->compare($oPrice2));
    }

    public function testInitWithParams()
    {
        $oPrice = new oxPrice(15);
        $this->assertSame(15, $oPrice->getBruttoPrice());
    }

    /**
     * Test getPrice
     */
    public function testGetPrice()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertSame(10, $oPrice->getPrice());

        $oPrice = oxNew('oxPrice');
        $oPrice->setBruttoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertSame(10, $oPrice->getPrice());
    }

    /**
     * Test getPrice
     */
    public function testNettoBruttoCalculation()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertSame(10, $oPrice->getNettoPrice());
        $this->assertEqualsWithDelta(11.9, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1.9, $oPrice->getVatValue(), PHP_FLOAT_EPSILON);

        $oPrice = oxNew('oxPrice');
        $oPrice->setBruttoPriceMode();
        $oPrice->setPrice(10, 19);
        $this->assertEqualsWithDelta(8.40, $oPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
        $this->assertSame(10, $oPrice->getBruttoPrice());
        $this->assertEqualsWithDelta(1.60, $oPrice->getVatValue(), PHP_FLOAT_EPSILON);
    }


    /**
     * Test getPrice
     */
    public function testApplyDiscount()
    {
        $oPrice = oxNew('oxPrice');
        $oPrice->setNettoPriceMode();
        $oPrice->setPrice(10, 19);

        $oPrice->setDiscount(50, '%');
        $oPrice->calculateDiscount();
        $this->assertSame(5, $oPrice->getNettoPrice());
        $this->assertEqualsWithDelta(5.95, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(0.95, $oPrice->getVatValue(), PHP_FLOAT_EPSILON);

        $oPrice->setDiscount(1, 'abs');
        $oPrice->calculateDiscount();
        $this->assertSame(4, $oPrice->getNettoPrice());
        $this->assertEqualsWithDelta(4.76, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(0.76, $oPrice->getVatValue(), PHP_FLOAT_EPSILON);

        $oPrice->setDiscount(-1, 'abs');
        $oPrice->calculateDiscount();
        $this->assertSame(5, $oPrice->getNettoPrice());
        $this->assertEqualsWithDelta(5.95, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(0.95, $oPrice->getVatValue(), PHP_FLOAT_EPSILON);

        $oPrice->setDiscount(-20, '%');
        $oPrice->calculateDiscount();
        $this->assertSame(6, $oPrice->getNettoPrice());
        $this->assertEqualsWithDelta(7.14, $oPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertEqualsWithDelta(1.14, $oPrice->getVatValue(), PHP_FLOAT_EPSILON);


        $oPrice->setDiscount(7, 'abs');
        $oPrice->calculateDiscount();
        $this->assertSame(0, $oPrice->getNettoPrice());
        $this->assertSame(0, $oPrice->getBruttoPrice());
        $this->assertSame(0, $oPrice->getVatValue());
    }

    public function testSetModeNetto_defaultParam_NettoMode()
    {
        $oPrice = oxNew('oxPrice');

        $oPrice->setNettoMode();
        $this->assertTrue($oPrice->isNettoMode());
    }

    public function testSetModeNetto_ParamTrue_NettoMode()
    {
        $oPrice = oxNew('oxPrice');

        $oPrice->setNettoMode(true);
        $this->assertTrue($oPrice->isNettoMode());
    }

    public function testSetModeNetto_ParamFalse_BruttoMode()
    {
        $oPrice = oxNew('oxPrice');

        $oPrice->setNettoMode(false);
        $this->assertFalse($oPrice->isNettoMode());
    }
}
