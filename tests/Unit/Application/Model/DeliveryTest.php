<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxArticleHelper;
use \oxDelivery;
use oxDeliveryHelper;
use \oxField;
use \oxDb;

class modOxDelivery extends oxDelivery
{
    public function getiItemCnt()
    {
        return $this->_iItemCnt;
    }

    public function getiProdCnt()
    {
        return $this->_iProdCnt;
    }

    public function getdPrice()
    {
        return $this->_dPrice;
    }

    public function setiItemCnt($iItemCnt)
    {
        $this->_iItemCnt = $iItemCnt;
    }

    public function setiProdCnt($iProdCnt)
    {
        $this->_iProdCnt = $iProdCnt;
    }

    public function setdPrice($dPrice)
    {
        $this->_dPrice = $dPrice;
    }

    public function setblFreeShipping($blFreeShipping)
    {
        $this->_blFreeShipping = $blFreeShipping;
    }

    public function getblFreeShipping()
    {
        return $this->_blFreeShipping;
    }
}

class DeliveryTest extends \OxidTestCase
{
    protected $_sOxId = null;

    /** @var oxBasketItem $_oBasketItem */
    protected $_oBasketItem = null;
    public $aArticleIds = array();
    public $aCategoryIds = array();

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modOxDelivery::class, 'oxDelivery');

        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxarticles');

        $this->aArticleIds = array();

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testDeliveryId');
        $oDelivery->oxdelivery__oxtitle = new oxField('test_oxDelivery', oxField::T_RAW);
        $oDelivery->save();
        $this->_sOxId = $oDelivery->getId();

        // insert test articles
        for ($i = 1; $i <= 3; $i++) {
            $oArticle = oxNew("oxArticle");
            $oArticle->setId('_testArticleId' . $i);
            $oArticle->oxarticles__oxtitle = new oxField('testArticle' . $i, oxField::T_RAW);
            $oArticle->oxarticles__oxartnum = new oxField(1000 + $i, oxField::T_RAW);
            $oArticle->oxarticles__oxshortdesc = new oxField('testArticle' . $i . 'Description', oxField::T_RAW);
            $oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
            $oArticle->oxarticles__oxremindactive = new oxField('1', oxField::T_RAW);
            $oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);
            $oArticle->oxarticles__oxlength = new oxField('2', oxField::T_RAW);
            $oArticle->oxarticles__oxwidth = new oxField('4', oxField::T_RAW);
            $oArticle->oxarticles__oxheight = new oxField('6', oxField::T_RAW);
            $oArticle->oxarticles__oxweight = new oxField('5', oxField::T_RAW);

            $oArticle->save();

            $this->aArticleIds[] = $oArticle->getId();
        }

        // some demo data
        $sQ = 'insert into oxobject2delivery (`OXID`, `OXDELIVERYID`, `OXOBJECTID`, `OXTYPE`) values ';
        $sQ .= '("_testId1", "' . $this->_sOxId . '", "_testArticleId1", "oxarticles" ), ';
        $sQ .= '("_testId2", "' . $this->_sOxId . '", "_testArticleId2", "oxarticles" ), ';
        $sQ .= '("_testId3", "' . $this->_sOxId . '", "_testArticleId3", "oxarticles" )';
        oxDb::getInstance()->getDb()->Execute($sQ);

        $sQ = 'insert into oxobject2delivery (`OXID`, `OXDELIVERYID`, `OXOBJECTID`, `OXTYPE`) values ';
        $sQ .= '("_testId4", "' . $this->_sOxId . '", "category_id1", "oxcategories" ), ';
        $sQ .= '("_testId5", "' . $this->_sOxId . '", "category_id2", "oxcategories" ), ';
        $sQ .= '("_testId6", "' . $this->_sOxId . '", "category_id3", "oxcategories" )';
        oxDb::getInstance()->getDb()->Execute($sQ);

        $this->aCategoryIds = array("category_id1", "category_id2", "category_id3");

        // preparing basket item


        $this->_oBasketItem = $this->getProxyClass("oxbasketitem");
        $this->_oBasketItem->init('_testArticleId1', 2);

        $oPrice = oxNew('oxprice');
        $oPrice->setPrice(256, 0);
        $this->_oBasketItem->setPrice($oPrice);

        oxArticleHelper::cleanup();
        oxDeliveryHelper::cleanup();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modOxDelivery::class);

        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxarticles');
        parent::tearDown();
    }

    public function testSetDeliveryPrice()
    {
        $oPrice = 'xxx';

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setDeliveryPrice($oPrice);
        $this->assertEquals('xxx', $oDelivery->getDeliveryPrice());
    }

    public function testGetDeliveryPriceCache()
    {
        $oDelivery = oxNew('oxDelivery');
        $oPrice = $oDelivery->getDeliveryPrice(50);
        $this->assertEquals($oPrice, $oDelivery->getDeliveryPrice(50));
    }

    /**
     * Testing how discount and basket checking works
     */
    public function testIsForBasketDeliverySetUpForArticle()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oO2D = oxNew('oxBase');
        $oO2D->init('oxobject2delivery');
        $oO2D->setId('_testoxobject2delivery');
        $oO2D->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId(), oxField::T_RAW);
        $oO2D->oxobject2delivery__oxobjectid = new oxField('1126', oxField::T_RAW);
        $oO2D->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oO2D->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1126', 5);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));

        $oBasket->addToBasket('1126', 10);
        $oBasket->calculateBasket();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    /**
     * Testing how discount and basket checking works
     */
    public function testIsForBasketDeliverySetUpForArticleOncePerArticle()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(50, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oO2D = oxNew('oxBase');
        $oO2D->init('oxobject2delivery');
        $oO2D->setId('_testoxobject2delivery');
        $oO2D->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId(), oxField::T_RAW);
        $oO2D->oxobject2delivery__oxobjectid = new oxField('1126', oxField::T_RAW);
        $oO2D->oxobject2delivery__oxtype = new oxField('oxarticles', oxField::T_RAW);
        $oO2D->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1126', 2);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));

        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->save();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    public function testIsForBasketDeliverySetUpForCategory()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $aCategoryIds = $oArticle->getCategoryIds();

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oO2D = oxNew('oxBase');
        $oO2D->init('oxobject2delivery');
        $oO2D->setId('_testoxobject2delivery');
        $oO2D->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId(), oxField::T_RAW);
        $oO2D->oxobject2delivery__oxobjectid = new oxField(current($aCategoryIds), oxField::T_RAW);
        $oO2D->oxobject2delivery__oxtype = new oxField('oxcategories', oxField::T_RAW);
        $oO2D->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1354', 15);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));

        $oBasket->addToBasket('1126', 15);
        $oBasket->calculateBasket();

        $this->assertTrue($oDelivery->isForBasket($oBasket));

        $oBasket->addToBasket('2000', 15);
        $oBasket->calculateBasket();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    public function testIsForBasketDeliverySetUpForCategoryOncePerArticle()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->load('1126');
        $aCategoryIds = $oArticle->getCategoryIds();

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(50, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oO2D = oxNew('oxBase');
        $oO2D->init('oxobject2delivery');
        $oO2D->setId('_testoxobject2delivery');
        $oO2D->oxobject2delivery__oxdeliveryid = new oxField($oDelivery->getId(), oxField::T_RAW);
        $oO2D->oxobject2delivery__oxobjectid = new oxField(current($aCategoryIds), oxField::T_RAW);
        $oO2D->oxobject2delivery__oxtype = new oxField('oxcategories', oxField::T_RAW);
        $oO2D->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1126', 2);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));

        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->save();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    public function testIsForBasketRegularDelivery()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1354', 5);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));

        $oBasket->addToBasket('1126', 15);
        $oBasket->calculateBasket();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    // #1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
    public function testIsForBasketPriceDeliveryIsFixed()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(0, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(0, oxField::T_RAW);
        $oDelivery->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1354', 5);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));
    }

    public function testIsForBasketRegularDeliveryOncePerArticle()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(50, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1126', 2);
        $oBasket->calculateBasket();

        $this->assertFalse($oDelivery->isForBasket($oBasket));

        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->save();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    // #M1504: Shipping Cost Rules by weight, calculation rules for each product - incorrectly calculated
    public function testIsForBasketTwoItemsAddedOncePerArticle()
    {
        $oDelivery = new modOxDelivery();
        $oDelivery->setId('_testdelivery');
        $oDelivery->oxdelivery__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oDelivery->oxdelivery__oxactive = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxtitle = new oxField('_testdelivery', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $oDelivery->oxdelivery__oxparam = new oxField(30, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(100, oxField::T_RAW);
        $oDelivery->save();

        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);
        $oBasket->addToBasket('1126', 2);
        $oBasket->addToBasket('2000', 2);
        $oBasket->calculateBasket();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
        $this->assertEquals(2, $oDelivery->getiItemCnt());
        $this->assertEquals(1, $oDelivery->getiProdCnt());

        $oDelivery->oxdelivery__oxparam = new oxField(10, oxField::T_RAW);
        $oDelivery->save();

        $oDelivery->setiItemCnt(0);
        $oDelivery->setiProdCnt(0);
        $this->assertTrue($oDelivery->isForBasket($oBasket));
        $this->assertEquals(4, $oDelivery->getiItemCnt());
        $this->assertEquals(2, $oDelivery->getiProdCnt());
    }

    //#M1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
    public function testIsForBasketFreeShipping()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load('_testDeliveryId');
        $oDelivery->oxdelivery__oxparam = new oxField(0.01, oxField::T_RAW);
        $oDelivery->oxdelivery__oxparamend = new oxField(99999999, oxField::T_RAW);
        $oDelivery->save();
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load('_testDeliveryId');
        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId1");
        $oArticle->oxarticles__oxfreeshipping = new oxField(true, oxField::T_RAW);
        $oArticle->save();
        $oBasket = oxNew('oxBasket');
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->getConfig()->setConfigParam('blExclNonMaterialFromDelivery', true);

        $oBasket->addToBasket('_testArticleId1', 15);
        $oBasket->calculateBasket();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    //#1115: Usability Problem during checkout with products without stock
    public function testIsForBasketIfArtOffline()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load('_testDeliveryId');
        $oBasketItem = $this->getProxyClass("oxbasketitem");
        $oBasketItem->init('_testArticleId1', 1);
        $oBasketItem->setNonPublicVar("_oArticle", null);
        $oBasket = $this->getProxyClass("oxbasket");
        $oBasket->setNonPublicVar("_aBasketContents", array($oBasketItem));

        $oArticle = oxNew("oxArticle");
        $oArticle->load("_testArticleId1");
        $oArticle->oxarticles__oxfreeshipping = new oxField(true, oxField::T_RAW);
        $oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $oArticle->save();

        $this->assertTrue($oDelivery->isForBasket($oBasket));
    }

    /*
     * Testing if blank (or the one which does not have any articles assigned) delivery returns 0
     */
    public function testGetArticlesWhenNoArticlesAreAssigned()
    {
        $oDelivery = oxNew('oxDelivery');
        $this->assertEquals(0, count($oDelivery->getArticles()));
    }

    /*
     * Testing if loaded delivery returns correct number (3) of assigned articles
     */
    public function testGetArticles()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load($this->_sOxId);

        // Main check
        $this->assertEquals(3, count($oDelivery->getArticles()));
        foreach ($oDelivery->getArticles() as $sId) {
            $this->assertTrue(in_array($sId, $this->aArticleIds));
        }
    }


    /*
     * Testing if blank (or the one which does not have any categories assigned) delivery returns 0
     */
    public function testGetCategoriesWhenNoCatgoeriesAreAssigned()
    {
        $oTestObject = oxNew('oxDelivery');
        $this->assertEquals(0, count($oTestObject->getCategories()));
    }

    /*
     * Testing if loaded delivery returns correct number (3) of assigned categories
     */
    public function testGetCategories()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load($this->_sOxId);

        $this->assertEquals(3, count($oDelivery->getCategories()));
        foreach ($oDelivery->getCategories() as $sId) {
            $this->assertTrue(in_array($sId, $this->aCategoryIds));
        }
    }


    /*
     * Testing if hasArticles() returns false, when no articles are assiged
     */
    public function testHasArticlesWhenNoArticlesAreAssigned()
    {
        $oDelivery = oxNew('oxDelivery');
        $this->assertFalse($oDelivery->hasArticles());
    }

    /*
     * Testing if hasArticles() returns true
     */
    public function testHasArticles()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load($this->_sOxId);
        $this->assertTrue($oDelivery->hasArticles());
    }

    /*
     * Testing if hasCategories() returns false, when no categories are assiged
     */
    public function testHasCategoriesWhenNoCategoriesAreAssigned()
    {
        $oDelivery = oxNew('oxDelivery');
        $this->assertFalse($oDelivery->hasCategories());
    }

    /*
     * Testing if hasArticles() returns true
     */
    public function testHasCategories()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load($this->_sOxId);
        $this->assertTrue($oDelivery->hasCategories());
    }

    /*
     * Testing getDeliveryAmount() - free shipping delivery
     */
    public function test_getDeliveryAmountFreeShipping()
    {
        $this->_oBasketItem->getArticle()->oxarticles__oxfreeshipping = new oxField(true);

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);

        $this->assertEquals(0, $oDelivery->getDeliveryAmount($this->_oBasketItem));
        $this->assertTrue($oDelivery->getblFreeShipping());

        // non free shipping
        $this->_oBasketItem->getArticle()->oxarticles__oxfreeshipping = new oxField(false);

        $this->assertEquals(512, $oDelivery->getDeliveryAmount($this->_oBasketItem));
        $this->assertFalse($oDelivery->getblFreeShipping());
    }

    /*
     * #1115: Usability Problem during checkout with products without stock
     */
    public function test_getDeliveryAmountIfArtOffline()
    {
        $oArt = $this->_oBasketItem->getArticle();
        $oArt->oxarticles__oxfreeshipping = new oxField(true);
        $oArt->oxarticles__oxstock = new oxField(0);
        $oArt->oxarticles__oxstockflag = new oxField(2);
        $oArt->save();

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p');

        $this->assertEquals(0, $oDelivery->getDeliveryAmount($this->_oBasketItem));
        $this->assertTrue($oDelivery->getblFreeShipping());

        // non free shiping
        $oArt->oxarticles__oxfreeshipping = new oxField(false);

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p');

        $this->assertEquals(512, $oDelivery->getDeliveryAmount($this->_oBasketItem));
        $this->assertFalse($oDelivery->getblFreeShipping());
    }

    /*
     * Testing getDeliveryAmount() - product price related
     */
    public function test_getDeliveryAmountCalcByPrice()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);

        // 256 x 2items
        $this->assertEquals(512, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - product price related Once per Product in Cart
     */
    public function test_getDeliveryAmountCalcByPricePerProduct()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $this->assertEquals(256, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - weight related
     */
    public function testGetDeliveryAmountCalcByWeight()
    {
        // test for delivery by weight
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('w', oxField::T_RAW);
        $this->assertEquals(10, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - weight related Once per Product in Cart
     */
    public function testGetDeliveryAmountCalcByWeightPerProduct()
    {
        // test for delivery by weight
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('w', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $this->assertEquals(5, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - weight related Once per Product in Order.
     * Test case for bug entry 0005942: Delivery cost rule with 'size' break tab in order administration.
     */
    public function testGetDeliveryAmountCalcByWeightPerOrderProduct()
    {
        /** @var oxDelivery $oDelivery delivery by weight. */
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('w', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);

        /** @var oxOrderArticle|PHPUnit\Framework\MockObject\MockObject $oOrderArticle */
        $oOrderArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderArticle::class, array(), array(), '', false);
        $oOrderArticle->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oBasketItem->getArticle()));
        $oOrderArticle->expects($this->any())->method('isOrderArticle')->will($this->returnValue(true));

        /** @var oxBasketItem $oBasketItem|PHPUnit\Framework\MockObject\MockObject */
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array(), array(), '', false);
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($oOrderArticle));

        $this->assertEquals(5, $oDelivery->getDeliveryAmount($oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - size related
     */
    public function testGetDeliveryAmountCalcBySize()
    {
        // test for delivery by size
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('s', oxField::T_RAW);

        // 2*4*6 x 2items (length * width * height * items)
        $this->assertEquals(96, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - size related Once per Product in Cart
     */
    public function testGetDeliveryAmountCalcBySizePerProduct()
    {
        // test for delivery by size
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('s', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $this->assertEquals(48, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - size related Once per Product in Order.
     * Test case for bug entry 0005942: Delivery cost rule with 'size' break tab in order administration.
     */
    public function testGetDeliveryAmountCalcBySizePerOrderProduct()
    {
        /** @var oxDelivery $oDelivery delivery by size. */
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('s', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);

        /** @var oxOrderArticle|PHPUnit\Framework\MockObject\MockObject $oOrderArticle */
        $oOrderArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderArticle::class, array(), array(), '', false);
        $oOrderArticle->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oBasketItem->getArticle()));
        $oOrderArticle->expects($this->any())->method('isOrderArticle')->will($this->returnValue(true));

        /** @var oxBasketItem $oBasketItem|PHPUnit\Framework\MockObject\MockObject */
        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array(), array(), '', false);
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($oOrderArticle));

        $this->assertEquals(48, $oDelivery->getDeliveryAmount($oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - amount related
     */
    public function testGetDeliveryAmountCalcByAmount()
    {
        // test for delivery by amount
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);

        $this->assertEquals(2, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - amount related Once per Product in Cart
     */
    public function testGetDeliveryAmountCalcByAmountPerProduct()
    {
        // test for delivery by amount
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('a', oxField::T_RAW);
        $oDelivery->oxdelivery__oxfixed = new oxField('2', oxField::T_RAW);
        $this->assertEquals(2, $oDelivery->getDeliveryAmount($this->_oBasketItem));
    }

    /*
     * Testing getDeliveryAmount() - setting _dPrice
     */
    public function testGetDeliveryAmountSetsAdditionalParams()
    {
        // test for delivery by amount
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);

        $oDelivery->getDeliveryAmount($this->_oBasketItem);
        $this->assertEquals(512, $oDelivery->getdPrice());
        $this->assertFalse($oDelivery->getblFreeShipping());
    }


    /*
     * Testing getDeliveryAmount() - _dPrice sums on every basket item
     */
    public function testGetDeliveryAmountSumsAdditionalParams()
    {
        // test for delivery by amount
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p', oxField::T_RAW);

        $aBasketContents[0] = $this->_oBasketItem;
        $aBasketContents[1] = $this->_oBasketItem;

        // 2 basket items
        foreach ($aBasketContents as $oBasketItem) {
            $oDelivery->getDeliveryAmount($oBasketItem);
        }

        $this->assertEquals(1024, $oDelivery->getdPrice());
    }

    /*
     * Testing getDeliveryAmount() - if item is free shipped
     */
    public function testGetDeliveryAmountIfOneArtFreeShipped()
    {
        $oPrice = oxNew('oxprice');
        $oPrice->setPrice(256, 0);

        $oBasketItem1 = oxNew("oxBasketItem");
        $oBasketItem1->init('_testArticleId2', 2);
        $oBasketItem1->setPrice($oPrice);
        $oBasketItem1->getArticle()->oxarticles__oxfreeshipping = new oxField(true);

        $oBasketItem2 = oxNew("oxBasketItem");
        $oBasketItem2->init('_testArticleId2', 2);
        $oBasketItem2->setPrice($oPrice);
        $oBasketItem2->getArticle()->oxarticles__oxfreeshipping = new oxField(false);

        // 2 basket items
        $aBasketContents = array($this->_oBasketItem, $oBasketItem1, $oBasketItem2);

        // test for delivery by amount
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p');

        foreach ($aBasketContents as $oBasketItem) {
            $oDelivery->getDeliveryAmount($oBasketItem);
        }

        $this->assertEquals(1024, $oDelivery->getdPrice());
    }

    /*
     * Testing getDeliveryPrice() - if item is free shipped
     */
    public function testGetDeliveryPriceIfFreeShipped()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(0, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->setblFreeShipping(true);
        $oPrice = $oDelivery->getDeliveryPrice();
        $oDelivery->setblFreeShipping(false);

        $this->assertEquals(0, $oPrice->getBruttoPrice());
    }

    /*
     * Testing getDeliveryPrice() - once per cart
     */
    public function testGetDeliveryPriceOncePerCart()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(0, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->setblFreeShipping(false);
        $oPrice = $oDelivery->getDeliveryPrice();

        $this->assertEquals(10, $oPrice->getBruttoPrice());
    }

    /*
     * Testing getDeliveryPrice() - once per product overall
     */
    public function testGetDeliveryPriceOncePerProductOverall()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->setblFreeShipping(false);
        $oDelivery->setiProdCnt(5);

        $oPrice = $oDelivery->getDeliveryPrice();

        $this->assertEquals(50, $oPrice->getBruttoPrice());
    }

    /*
     * Testing getDeliveryPrice() - once per product in cart
     */
    public function testGetDeliveryPriceOncePerProductInCart()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(2, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->setiItemCnt(7);
        $oDelivery->setblFreeShipping(false);

        $oPrice = $oDelivery->getDeliveryPrice();

        $this->assertEquals(70, $oPrice->getBruttoPrice());
    }

    /*
     * Testing getDeliveryPrice() - percental cost
     */
    public function testGetDeliveryPriceOncePercentalCost()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(2, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('20', oxField::T_RAW);
        $oDelivery->setdPrice(60);
        $oDelivery->setblFreeShipping(false);

        $oPrice = $oDelivery->getDeliveryPrice();

        $this->assertEquals(12, $oPrice->getBruttoPrice());
    }

    /*
     * Testing getDeliveryPrice() - setting VAT
     */
    public function testGetDeliveryPriceSettingVat()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->setiProdCnt(5);
        $oDelivery->setblFreeShipping(false);

        $oPrice = $oDelivery->getDeliveryPrice(18);

        $this->assertEquals(50, $oPrice->getBruttoPrice());
        $this->assertEquals(18, $oPrice->getVat());
    }

    /*
     * Testing getDeliveryPrice() - with DeliveryVatOnTop
     */
    public function testGetDeliveryWithDeliveryVatOnTop()
    {
        $this->getConfig()->setConfigParam('blDeliveryVatOnTop', true);

        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(1, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('50', oxField::T_RAW);
        $oDelivery->setiProdCnt(1);
        $oDelivery->setblFreeShipping(false);

        $oPrice = $oDelivery->getDeliveryPrice(20);

        $this->assertEquals(50 * 1.2, $oPrice->getBruttoPrice());
        $this->assertEquals(50, $oPrice->getNettoPrice(), '', 0.0001);
        $this->assertEquals(50 * 1.2 - 50, $oPrice->getVatValue(), '', 0.0001);
        $this->assertEquals(20, $oPrice->getVat(), '', 0.0001);
    }

    /*
     * Testing getDeliveryPrice() - once per cart in spec currency
     */
    public function testGetDeliveryPriceInSpecCurrency()
    {
        $this->getConfig()->setActShopCurrency(2);
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->oxdelivery__oxfixed = new oxField(0, oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDelivery->oxdelivery__oxaddsum = new oxField('10', oxField::T_RAW);
        $oDelivery->setblFreeShipping(false);
        $oPrice = $oDelivery->getDeliveryPrice();

        $this->assertEquals(14.33, $oPrice->getBruttoPrice());
    }

    /*
     * Test deleting
     */
    public function testDelete()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->delete($this->_sOxId);

        $sQ = "select oxdeliveryid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $this->_sOxId . "' ";
        $sDeliveryId = oxDb::getInstance()->getDb()->getOne($sQ);

        $this->assertFalse($sDeliveryId);
    }

    public function testDeleteWithoutId()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->load($this->_sOxId);
        $oDelivery->delete($this->_sOxId);

        $sQ = "select oxdeliveryid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $this->_sOxId . "' ";
        $sDeliveryId = oxDb::getInstance()->getDb()->getOne($sQ);

        $this->assertFalse($sDeliveryId);
    }

    public function testDeleteNotLoaded()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->delete();

        $sQ = "select oxdeliveryid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $this->_sOxId . "' ";
        $sDeliveryId = oxDb::getInstance()->getDb()->getOne($sQ);

        $this->assertEquals($this->_sOxId, $sDeliveryId);
    }

    // 3. trying to delete denied action by RR (EE only)
    public function testCheckDeliveryAmount()
    {
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testDeliveryId');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p');
        $oDelivery->oxdelivery__oxparam = new oxField(80);
        $oDelivery->oxdelivery__oxparamend = new oxField(100);
        $this->assertFalse($oDelivery->UNITcheckDeliveryAmount(50));
        $this->assertTrue($oDelivery->UNITcheckDeliveryAmount(81));
        $this->assertFalse($oDelivery->UNITcheckDeliveryAmount(110));

        $this->getConfig()->setActShopCurrency(2);
        $oDelivery = oxNew('oxDelivery');
        $oDelivery->setId('_testDeliveryId');
        $oDelivery->oxdelivery__oxdeltype = new oxField('p');
        $oDelivery->oxdelivery__oxparam = new oxField(80); // eur
        $oDelivery->oxdelivery__oxparamend = new oxField(100); // eur
        $this->assertFalse($oDelivery->UNITcheckDeliveryAmount(81)); // chf -> 55.1 eur
        $this->assertTrue($oDelivery->UNITcheckDeliveryAmount(120)); // chf -> 81 eur
        $this->assertFalse($oDelivery->UNITcheckDeliveryAmount(161)); // chf -> 110 eur
    }

    public function testIsForArticle()
    {
        $oDelivery = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modOxDelivery::class, array('_checkDeliveryAmount', 'getCalculationRule'));
        $oDelivery->expects($this->once())->method('_checkDeliveryAmount')->will($this->returnValue(true));
        $calculateMoreThanOncePerCartRule = 123;
        $oDelivery->expects($this->any())->method('getCalculationRule')->will($this->returnValue($calculateMoreThanOncePerCartRule));
        $oDelivery->load('_testDeliveryId');
        $oDelivery->setblFreeShipping(false);
        $blReturn = $oDelivery->UNITisForArticle($this->_oBasketItem, 2);

        $this->assertTrue($blReturn);
    }

    public function testIsForArticleIfArticleIsFreeShipped()
    {
        $oDelivery = $this->getProxyClass("oxdelivery");
        $oDelivery->setNonPublicVar("_blFreeShipping", true);
        $oDelivery->load('_testDeliveryId');
        $blReturn = $oDelivery->UNITisForArticle($this->_oBasketItem, 2);
        $this->assertFalse($blReturn);
    }

    public function testGetIdByName()
    {
        $oD = oxNew('oxDelivery');
        $this->assertEquals('_testDeliveryId', $oD->getIdByName('test_oxDelivery'));
    }

    /**
     * Test get payment countries
     */
    public function testGetCountriesISO()
    {
        $oD = oxNew('oxDelivery');
        //standard delivery id for rest EU
        $oD->load('1b842e7352422a708.01472527');
        $aCountries = $oD->getCountriesISO();
        $this->assertEquals(2, count($aCountries), "Failed getting countries code");
        $this->assertEquals(array("AT", "CH"), $aCountries);
    }

    public function testSetDelVatOnTop()
    {
        $oDelivery = $this->getProxyClass("oxdelivery");
        $oDelivery->setDelVatOnTop(true);
        $this->assertTrue($oDelivery->getNonPublicVar("_blDelVatOnTop"));
    }
}
