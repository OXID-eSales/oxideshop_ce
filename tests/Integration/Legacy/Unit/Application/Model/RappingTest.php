<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \oxWrapping;
use \oxField;
use \oxRegistry;

class testOxWrapping extends oxWrapping
{
    public function __get($name)
    {
        return $this->$name ?? null;
    }

    public function __set($name, $val)
    {
        $this->$name = $val;
    }
}

/**
 * Testing oxwrapping class
 */
class RappingTest extends \PHPUnit\Framework\TestCase
{
    public $sTableName;
    protected $_sCardOxid;

    protected $_sWrapOxid;

    protected $_dDefaultVAT;

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $this->sTableName = $tableViewNameGenerator->getViewName("oxwrapping");

        // card
        // not active
        $oCard = oxNew('oxBase');
        $oCard->init('oxwrapping');
        $oCard->setId('_testCard');

        $oCard->oxwrapping__oxname = new oxField('Test Card 1 DE', oxField::T_RAW);
        $oCard->oxwrapping__oxname_1 = new oxField('Test Card 1 ENG', oxField::T_RAW);
        $oCard->oxwrapping__oxtype = new oxField('CARD', oxField::T_RAW);
        $oCard->oxwrapping__oxprice = new oxField(2.5, oxField::T_RAW);
        $oCard->oxwrapping__oxactive = new oxField(0);
        $oCard->oxwrapping__oxactive_1 = new oxField(0);
        $oCard->save();

        // active
        $oCard = oxNew('oxBase');
        $oCard->init('oxwrapping');
        $oCard->setId('_testCard2');

        $oCard->oxwrapping__oxname = new oxField('Test Card 1 DE', oxField::T_RAW);
        $oCard->oxwrapping__oxname_1 = new oxField('Test Card 1 ENG', oxField::T_RAW);
        $oCard->oxwrapping__oxtype = new oxField('CARD', oxField::T_RAW);
        $oCard->oxwrapping__oxprice = new oxField(2.5, oxField::T_RAW);
        $oCard->oxwrapping__oxactive = new oxField(1);
        $oCard->oxwrapping__oxactive_1 = new oxField(1);
        $oCard->save();
        $this->_sCardOxid = $oCard->getId();

        // active and free
        $oCard = oxNew('oxBase');
        $oCard->init('oxwrapping');
        $oCard->setId('_testCard3');

        $oCard->oxwrapping__oxname = new oxField('Test Card 1 DE', oxField::T_RAW);
        $oCard->oxwrapping__oxname_1 = new oxField('Test Card 1 ENG', oxField::T_RAW);
        $oCard->oxwrapping__oxtype = new oxField('CARD', oxField::T_RAW);
        $oCard->oxwrapping__oxactive = new oxField(1);
        $oCard->oxwrapping__oxactive_1 = new oxField(1);
        $oCard->save();

        // wrapping
        // not active
        $oWrapping = oxNew('oxBase');
        $oWrapping->init('oxwrapping');
        $oWrapping->setId('_testWrap');

        $oWrapping->oxwrapping__oxname = new oxField('Test Wrap 1 DE', oxField::T_RAW);
        $oWrapping->oxwrapping__oxname_1 = new oxField('Test Wrap 1 ENG', oxField::T_RAW);
        $oWrapping->oxwrapping__oxtype = new oxField('WRAP', oxField::T_RAW);
        $oWrapping->oxwrapping__oxprice = new oxField(2.95, oxField::T_RAW);
        $oWrapping->oxwrapping__oxactive = new oxField(0);
        $oWrapping->oxwrapping__oxactive_1 = new oxField(0);
        $oWrapping->save();

        // active
        $oWrapping = oxNew('oxBase');
        $oWrapping->init('oxwrapping');
        $oWrapping->setId('_testWrap2');

        $oWrapping->oxwrapping__oxname = new oxField('Test Wrap 1 DE', oxField::T_RAW);
        $oWrapping->oxwrapping__oxname_1 = new oxField('Test Wrap 1 ENG', oxField::T_RAW);
        $oWrapping->oxwrapping__oxtype = new oxField('WRAP', oxField::T_RAW);
        $oWrapping->oxwrapping__oxprice = new oxField(2.95, oxField::T_RAW);
        $oWrapping->oxwrapping__oxactive = new oxField(1);
        $oWrapping->oxwrapping__oxactive_1 = new oxField(1);
        $oWrapping->save();

        $this->_sWrapOxid = $oWrapping->getId();

        // active and free
        $oWrapping = oxNew('oxBase');
        $oWrapping->init('oxwrapping');
        $oWrapping->setId('_testWrap3');

        $oWrapping->oxwrapping__oxname = new oxField('Test Wrap 1 DE', oxField::T_RAW);
        $oWrapping->oxwrapping__oxname_1 = new oxField('Test Wrap 1 ENG', oxField::T_RAW);
        $oWrapping->oxwrapping__oxtype = new oxField('WRAP', oxField::T_RAW);
        $oWrapping->oxwrapping__oxactive = new oxField(1);
        $oWrapping->oxwrapping__oxactive_1 = new oxField(1);
        $oWrapping->save();

        $this->_dDefaultVAT = $this->getConfig()->getConfigParam('dDefaultVAT');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->getConfig();
        $this->getConfig()->setConfigParam('blEnterNetPrice', false);

        // card
        $oCard = oxNew('oxwrapping');
        $oCard->delete('_testCard');
        $oCard->delete('_testCard2');
        $oCard->delete('_testCard3');

        // wrapping
        $oWrapping = oxNew('oxwrapping');
        $oWrapping->delete('_testWrap');
        $oWrapping->delete('_testWrap2');
        $oWrapping->delete('_testWrap3');

        parent::tearDown();
    }

    public function testGetWrappingCount()
    {
        $oWrap = oxNew('oxwrapping');
        $this->assertSame(4, $oWrap->getWrappingCount('WRAP'));
        $this->assertSame(4, $oWrap->getWrappingCount('CARD'));
        $this->assertSame(0, $oWrap->getWrappingCount('xxx'));
    }

    /**
     * Testing what code is executed by GetNoSslDynImageDir getter
     */
    public function testGetNoSslDynImageDir()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getPictureUrl']);
        $oConfig->expects($this->once())->method('getPictureUrl')
            ->with(
                null,
                false,
                false,
                null,
                '123'
            )
            ->willReturn('testDynPath');

        $oWrapping = $this->getMock(\OxidEsales\Eshop\Application\Model\Wrapping::class, ['getConfig'], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oWrapping->oxwrapping__oxshopid = new oxField('123');

        $this->assertSame('testDynPath', $oWrapping->getNoSslDynImageDir());
    }

    /**
     * Checking if object is loaded and if type is valid
     */
    public function testLoadWrap()
    {
        $oWrapping = oxNew('oxwrapping');
        $oWrapping->Load($this->_sWrapOxid);

        $this->assertSame('WRAP', $oWrapping->oxwrapping__oxtype->value);
    }

    public function testLoadCard()
    {
        $oCard = oxNew('oxwrapping');
        $oCard->Load($this->_sCardOxid);

        $this->assertSame('CARD', $oCard->oxwrapping__oxtype->value);
    }

    public function testGetCardPrice()
    {
        oxRegistry::getUtils();
        $oCard = oxNew('oxwrapping');
        if (!$oCard->Load($this->_sCardOxid)) {
            $this->fail('can not load wrapping');
        }

        $oCard->setWrappingVat($this->_dDefaultVAT);
        $oCardPrice = $oCard->getWrappingPrice();

        $this->assertEqualsWithDelta(2.5, $oCardPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertSame('2,10', oxRegistry::getLang()->formatCurrency($oCardPrice->getNettoPrice()));
        $this->assertSame('0,40', oxRegistry::getLang()->formatCurrency($oCardPrice->getVATValue()));
    }

    public function testGetWrapPrice()
    {
        oxRegistry::getUtils();

        $oWrap = oxNew('oxwrapping');
        if (!$oWrap->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        $oWrap->setWrappingVat($this->_dDefaultVAT);
        $oWrapPrice = $oWrap->getWrappingPrice(2);

        $this->assertEqualsWithDelta(5.9, $oWrapPrice->getBruttoPrice(), PHP_FLOAT_EPSILON);
        $this->assertSame('4,96', oxRegistry::getLang()->formatCurrency($oWrapPrice->getNettoPrice()));
        $this->assertSame('0,94', oxRegistry::getLang()->formatCurrency($oWrapPrice->getVATValue()));
    }

    public function testGetWrapPriceVatOnTop()
    {
        $this->getConfig()->setConfigParam('blWrappingVatOnTop', true);
        $oWrap = oxNew('oxwrapping');
        if (!$oWrap->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        $oWrap->setWrappingVat($this->_dDefaultVAT);
        $oWrapPrice = $oWrap->getWrappingPrice(2);

        $dVat = 1 + $this->getConfig()->getConfigParam('dDefaultVAT') / 100;
        $this->assertEqualsWithDelta(5.9 * $dVat, $oWrapPrice->getBruttoPrice(), 2);
        $this->assertEqualsWithDelta(5.9, $oWrapPrice->getNettoPrice(), PHP_FLOAT_EPSILON);
        $this->assertSame('7,02', oxRegistry::getLang()->formatCurrency($oWrapPrice->getBruttoPrice()));
        $this->assertSame('5,90', oxRegistry::getLang()->formatCurrency($oWrapPrice->getNettoPrice()));
        $this->assertSame('1,12', oxRegistry::getLang()->formatCurrency($oWrapPrice->getVATValue()));
    }

    /**
     * Calculates prices in EUR
     */
    public function testCalcDPriceInEUR()
    {
        oxRegistry::getUtils();

        $oWrapping = oxNew('oxwrapping');
        if (!$oWrapping->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        // validating
        $oWrapping->setWrappingVat($this->_dDefaultVAT);
        $this->assertEqualsWithDelta(2.95, $oWrapping->getWrappingPrice()->getBruttoPrice(), PHP_FLOAT_EPSILON);
    }

    public function testCalcFPriceInEUR()
    {
        oxRegistry::getUtils();
        $myConfig = $this->getConfig();

        $myConfig->getActShopCurrencyObject()->id;

        // setting active currency to EUR
        $myConfig->setActShopCurrency(0);

        $oWrapping = oxNew('oxwrapping');
        if (!$oWrapping->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        // validating
        $oWrapping->setWrappingVat($this->_dDefaultVAT);
        $this->assertSame('2,95', oxRegistry::getLang()->formatCurrency($oWrapping->getWrappingPrice()->getBruttoPrice()));
    }

    public function testCalcFPriceInGBP()
    {
        $myConfig = $this->getConfig();

        // setting active currency to GBP
        $myConfig->setActShopCurrency(1);

        $oWrapping = oxNew('oxwrapping');
        $oWrapping->Load($this->_sWrapOxid);
        $oWrapping->setWrappingVat($this->_dDefaultVAT);

        $sPrice = oxRegistry::getLang()->formatCurrency($oWrapping->getWrappingPrice()->getBruttoPrice());

        // validating
        $this->assertSame('2.53', $sPrice);
    }

    public function testCalcFPriceInCHF()
    {
        $myConfig = $this->getConfig();
        oxRegistry::getUtils();

        $myConfig->getActShopCurrencyObject()->id;

        // setting active currency to GHF
        $myConfig->setActShopCurrency(2);

        $oWrapping = oxNew('oxwrapping');
        if (!$oWrapping->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        $oWrapping->setWrappingVat($this->_dDefaultVAT);
        $sPrice = oxRegistry::getLang()->formatCurrency($oWrapping->getWrappingPrice()->getBruttoPrice());

        // validating
        $this->assertSame('4,23', $sPrice);
    }

    public function testGetWrappingListIfNotAllActive()
    {
        oxRegistry::getUtils();

        $oWrap = oxNew('oxwrapping');
        $oWrapList = $oWrap->getWrappingList('WRAP');

        $this->assertSame(4, $oWrapList->count());
        foreach ($oWrapList as $oWrapping) {
            if ($oWrapping->getId() == '_testWrap3') {
                $this->assertSame('0,00', $oWrapping->getFPrice());
            } else {
                $this->assertSame('2,95', $oWrapping->getFPrice());
            }
        }

        $oCardList = $oWrap->getWrappingList('CARD');

        foreach ($oCardList as $oCard) {
            if ($oCard->getId() == '_testCard3') {
                $this->assertSame('0,00', $oCard->getFPrice());
            } elseif ($oCard->getId() == '81b40cf0cd383d3a9.70988998') {
                $this->assertSame('3,00', $oCard->getFPrice());
            } else {
                $this->assertSame('2,50', $oCard->getFPrice());
            }
        }

        $this->assertSame(4, $oCardList->count());
    }

    public function testGetWrappingList()
    {
        oxRegistry::getUtils();

        $oWrap = oxNew('oxwrapping');
        $oWrap->load($this->_sWrapOxid);

        $oWrap->oxwrapping__oxactive = new oxField(1, oxField::T_RAW);
        $oWrap->save();

        $oWrapList = $oWrap->getWrappingList('WRAP');

        $this->assertSame(4, $oWrapList->count());
    }

    /**
     * Testing formatted basket total price
     */
    public function testGetFPrice()
    {
        $oPrice = $this->getMock(\OxidEsales\Eshop\Core\Price::class, ['getBruttoPrice']);
        $oPrice->expects($this->once())->method('getBruttoPrice')->willReturn(11.588);
        $oWrap = $this->getProxyClass("oxWrapping");
        $oWrap->setNonPublicVar('_oPrice', $oPrice);
        $this->assertSame("11,59", $oWrap->getFPrice());
    }

    public function testGetPictureUrl()
    {
        $oWrap = oxNew('oxwrapping');
        $this->assertNull($oWrap->getPictureUrl());

        $oWrap->load("a6840cc0ec80b3991.74884864");
        $this->assertEquals($this->getConfig()->getPictureUrl("master/wrapping/img_geschenkpapier_1_wp.gif", false, null, null, 1), $oWrap->getPictureUrl());
    }

    /**
     * Test wrapping config setter
     */
    public function testSetWrappingVatOnTop()
    {
        $oWrapping = $this->getProxyClass("oxwrapping");
        $oWrapping->setWrappingVatOnTop(true);
        $this->assertTrue($oWrapping->getNonPublicVar("_blWrappingVatOnTop"));
    }
}
