<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxWrapping;
use \oxField;
use \oxRegistry;

class testOxWrapping extends oxWrapping
{
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return null;
    }

    public function __set($name, $val)
    {
        $this->$name = $val;
    }
}

/**
 * Testing oxwrapping class
 */
class RappingTest extends \OxidTestCase
{
    protected $_sCardOxid = null;
    protected $_sWrapOxid = null;
    protected $_dDefaultVAT;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->sTableName = getViewName("oxwrapping");

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
     *
     * @return null
     */
    protected function tearDown()
    {
        $myConfig = $this->getConfig();
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
        $this->assertEquals(4, $oWrap->getWrappingCount('WRAP'));
        $this->assertEquals(4, $oWrap->getWrappingCount('CARD'));
        $this->assertEquals(0, $oWrap->getWrappingCount('xxx'));
    }

    /**
     * Testing what code is executed by GetNoSslDynImageDir getter
     */
    public function testGetNoSslDynImageDir()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getPictureUrl'));
        $oConfig->expects($this->once())->method('getPictureUrl')
            ->with(
                $this->equalTo(null),
                $this->equalTo(false),
                $this->equalTo(false),
                $this->equalTo(null),
                $this->equalTo('123')
            )
            ->will($this->returnValue('testDynPath'));

        $oWrapping = $this->getMock(\OxidEsales\Eshop\Application\Model\Wrapping::class, array('getConfig'), array(), '', false);
        $oWrapping->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oWrapping->oxwrapping__oxshopid = new oxField('123');

        $this->assertEquals('testDynPath', $oWrapping->getNoSslDynImageDir());
    }

    /**
     * Checking if object is loaded and if type is valid
     */
    public function testLoadWrap()
    {
        $oWrapping = oxNew('oxwrapping');
        $oWrapping->Load($this->_sWrapOxid);

        $this->assertEquals('WRAP', $oWrapping->oxwrapping__oxtype->value);
    }

    public function testLoadCard()
    {
        $oCard = oxNew('oxwrapping');
        $oCard->Load($this->_sCardOxid);

        $this->assertEquals('CARD', $oCard->oxwrapping__oxtype->value);
    }

    public function testGetCardPrice()
    {
        $myUtils = oxRegistry::getUtils();
        $oCard = oxNew('oxwrapping');
        if (!$oCard->Load($this->_sCardOxid)) {
            $this->fail('can not load wrapping');
        }

        $oCard->setWrappingVat($this->_dDefaultVAT);
        $oCardPrice = $oCard->getWrappingPrice();

        $this->assertEquals(2.5, $oCardPrice->getBruttoPrice());
        $this->assertEquals('2,10', oxRegistry::getLang()->formatCurrency($oCardPrice->getNettoPrice()));
        $this->assertEquals('0,40', oxRegistry::getLang()->formatCurrency($oCardPrice->getVATValue()));
    }

    public function testGetWrapPrice()
    {
        $myUtils = oxRegistry::getUtils();

        $oWrap = oxNew('oxwrapping');
        if (!$oWrap->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        $oWrap->setWrappingVat($this->_dDefaultVAT);
        $oWrapPrice = $oWrap->getWrappingPrice(2);

        $this->assertEquals(5.9, $oWrapPrice->getBruttoPrice());
        $this->assertEquals('4,96', oxRegistry::getLang()->formatCurrency($oWrapPrice->getNettoPrice()));
        $this->assertEquals('0,94', oxRegistry::getLang()->formatCurrency($oWrapPrice->getVATValue()));
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
        $this->assertEquals(5.9 * $dVat, $oWrapPrice->getBruttoPrice(), '', 2);
        $this->assertEquals(5.9, $oWrapPrice->getNettoPrice());
        $this->assertEquals('7,02', oxRegistry::getLang()->formatCurrency($oWrapPrice->getBruttoPrice()));
        $this->assertEquals('5,90', oxRegistry::getLang()->formatCurrency($oWrapPrice->getNettoPrice()));
        $this->assertEquals('1,12', oxRegistry::getLang()->formatCurrency($oWrapPrice->getVATValue()));
    }

    /**
     * Calculates prices in EUR
     */
    public function testCalcDPriceInEUR()
    {
        $myUtils = oxRegistry::getUtils();

        $oWrapping = oxNew('oxwrapping');
        if (!$oWrapping->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        // validating
        $oWrapping->setWrappingVat($this->_dDefaultVAT);
        $this->assertEquals(2.95, $oWrapping->getWrappingPrice()->getBruttoPrice());
    }

    public function testCalcFPriceInEUR()
    {
        $myUtils = oxRegistry::getUtils();
        $myConfig = $this->getConfig();

        $iTempCur = $myConfig->getActShopCurrencyObject()->id;

        // setting active currency to EUR
        $myConfig->setActShopCurrency(0);

        $oWrapping = oxNew('oxwrapping');
        if (!$oWrapping->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        // validating
        $oWrapping->setWrappingVat($this->_dDefaultVAT);
        $this->assertEquals('2,95', oxRegistry::getLang()->formatCurrency($oWrapping->getWrappingPrice()->getBruttoPrice()));
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
        $this->assertEquals('2.53', $sPrice);
    }

    public function testCalcFPriceInCHF()
    {
        $myConfig = $this->getConfig();
        $myUtils = oxRegistry::getUtils();

        $iTempCur = $myConfig->getActShopCurrencyObject()->id;

        // setting active currency to GHF
        $myConfig->setActShopCurrency(2);

        $oWrapping = oxNew('oxwrapping');
        if (!$oWrapping->Load($this->_sWrapOxid)) {
            $this->fail('can not load wrapping');
        }

        $oWrapping->setWrappingVat($this->_dDefaultVAT);
        $sPrice = oxRegistry::getLang()->formatCurrency($oWrapping->getWrappingPrice()->getBruttoPrice());

        // validating
        $this->assertEquals('4,23', $sPrice);
    }

    public function testGetWrappingListIfNotAllActive()
    {
        $myUtils = oxRegistry::getUtils();

        $oWrap = oxNew('oxwrapping');
        $oWrapList = $oWrap->getWrappingList('WRAP');

        $this->assertEquals(4, $oWrapList->count());
        foreach ($oWrapList as $oWrapping) {
            if ($oWrapping->getId() == '_testWrap3') {
                $this->assertEquals('0,00', $oWrapping->getFPrice());
            } else {
                $this->assertEquals('2,95', $oWrapping->getFPrice());
            }
        }

        $oCardList = $oWrap->getWrappingList('CARD');

        foreach ($oCardList as $oCard) {
            if ($oCard->getId() == '_testCard3') {
                $this->assertEquals('0,00', $oCard->getFPrice());
            } elseif ($oCard->getId() == '81b40cf0cd383d3a9.70988998') {
                $this->assertEquals('3,00', $oCard->getFPrice());
            } else {
                $this->assertEquals('2,50', $oCard->getFPrice());
            }
        }

        $this->assertEquals(4, $oCardList->count());
    }

    public function testGetWrappingList()
    {
        $myUtils = oxRegistry::getUtils();

        $oWrap = oxNew('oxwrapping');
        $oWrap->load($this->_sWrapOxid);
        $oWrap->oxwrapping__oxactive = new oxField(1, oxField::T_RAW);
        $oWrap->save();

        $oWrapList = $oWrap->getWrappingList('WRAP');

        $this->assertEquals(4, $oWrapList->count());
    }

    /**
     * Testing formatted basket total price
     */
    public function testGetFPrice()
    {
        $oPrice = $this->getMock(\OxidEsales\Eshop\Core\Price::class, array('getBruttoPrice'));
        $oPrice->expects($this->once())->method('getBruttoPrice')->will($this->returnValue(11.588));
        $oWrap = $this->getProxyClass("oxWrapping");
        $oWrap->setNonPublicVar('_oPrice', $oPrice);
        $this->assertEquals("11,59", $oWrap->getFPrice());
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
