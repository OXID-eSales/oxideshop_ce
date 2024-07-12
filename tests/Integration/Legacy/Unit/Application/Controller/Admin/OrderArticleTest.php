<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\Order;
use OxidEsales\Facts\Facts;
use \oxOrder;
use \oxField;
use \oxTestModules;

/**
 * Module for oxorder testing
 */
class OrderArticleHelper extends oxOrder
{
    /**
     * Modify recalculateOrder method.
     *
     * @param array $aNewOrderArticles article list of new order
     * @param bool  $blChangeDelivery  change deliveryr
     * @param bool  $blChangeDiscount  change discount
     */
    public function recalculateOrder($aNewOrderArticles = [], $blChangeDelivery = false, $blChangeDiscount = false)
    {
        $this->oxorder__oxtotalbrutsum = new oxField(1);
        $this->save();
    }

    /**
     * Modify validateStock method.
     *
     * @param object $oBasket basket object
     *
     * @return boolean
     */
    public function validateStock($oBasket)
    {
        return true;
    }
}

class OrderArticleTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $myConfig = $this->getConfig();

        $this->getConfig()->setConfigParam('blPerfNoBasketSaving', true);

        // adding test order
        $oOrder = oxNew('oxbase');
        $oOrder->init('oxorder');
        $oOrder->setId('_testOrder');

        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->save();

        // adding test article
        $oArticle = oxNew('oxbase');
        $oArticle->init('oxarticles');
        $oArticle->load('1126');
        $oArticle->setId('_testArticle');

        $oArticle->oxarticles__oxartnum = new oxField('_testArticle');
        $oArticle->oxarticles__oxstock = new oxField(100);
        $oArticle->save();

        //set order
        $oOrder = oxNew("oxOrder");
        $oOrder->setId('_testOrderId1');

        $oOrder->oxorder__oxshopid = new oxField($myConfig->getShopId(), oxField::T_RAW);
        $oOrder->oxorder__oxuserid = new oxField("_testUserId", oxField::T_RAW);
        $oOrder->oxorder__oxbillcountryid = new oxField('10', oxField::T_RAW);
        $oOrder->oxorder__oxdelcountryid = new oxField('11', oxField::T_RAW);
        $oOrder->oxorder__oxdeltype = new oxField('_testDeliverySetId', oxField::T_RAW);
        $oOrder->oxorder__oxpaymentid = new oxField('_testPaymentId', oxField::T_RAW);
        $oOrder->oxorder__oxpaymenttype = new oxField('_testPaymentId', oxField::T_RAW);
        $oOrder->oxorder__oxcardid = new oxField('_testWrappingId', oxField::T_RAW);

        $oOrder->save();
    }

    protected function tearDown(): void
    {
        $oOrder = oxNew('oxorder');
        $oOrder->delete('_testOrder');

        $oArticle = oxNew('oxArticle');
        $oArticle->delete('_testArticle');

        $this->cleanUpTable('oxorder');

        parent::tearDown();
    }

    /**
     * Test add article.
     */
    public function testAddThisArticle()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $this->setRequestParameter("aid", '_testArticle');
        $this->setRequestParameter("am", 4);
        $this->setRequestParameter("oxid", '_testOrder');

        $oObj = oxNew('order_article');
        $oObj->addThisArticle();

        // now reading order articles table
        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrder');

        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals(1, $oOrderArticles->count());
        $this->assertEquals('_testArticle', $oOrderArticles->current()->oxorderarticles__oxartnum->value);
        $this->assertEquals(4, $oOrderArticles->current()->oxorderarticles__oxamount->value);
    }

    /**
     * Test add another article.
     */
    public function testAddThisArticle2()
    {
        oxTestModules::addFunction("oxUtilsServer", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\OrderArticleHelper::class, 'oxorder');

        $this->setRequestParameter('aid', '2000');
        $this->setRequestParameter('am', 1);
        $this->setRequestParameter('oxid', '_testOrderId1');

        $oOrderArticle = oxNew('order_article');
        $oOrderArticle->addThisArticle();

        $oOrder = oxNew("oxOrder");
        $oOrder->load('_testOrderId1');
        $this->assertEquals(1, $oOrder->oxorder__oxtotalbrutsum->value);
    }

    /**
     * Test delete article.
     */
    public function testDeleteThisArticle()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $this->setRequestParameter("aid", '_testArticle');
        $this->setRequestParameter("am", 4);
        $this->setRequestParameter("oxid", '_testOrder');

        $oObj = oxNew('order_article');
        $oObj->addThisArticle();

        // now reading order articles table
        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrder');

        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals(1, $oOrderArticles->count());
        $this->assertEquals('_testArticle', $oOrderArticles->current()->oxorderarticles__oxartnum->value);
        $this->assertEquals(4, $oOrderArticles->current()->oxorderarticles__oxamount->value);

        $this->setRequestParameter("sArtID", $oOrderArticles->current()->getId());

        $oObj->deleteThisArticle();

        // now reading order articles table
        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrder');

        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals(0, $oOrderArticles->count());
    }

    /**
     * Test cancel article.
     */
    public function testStorno()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $this->setRequestParameter("aid", '_testArticle');
        $this->setRequestParameter("am", 4);
        $this->setRequestParameter("oxid", '_testOrder');

        $oObj = oxNew('order_article');
        $oObj->addThisArticle();

        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrder');

        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals(1, $oOrderArticles->count());
        $this->assertEquals('_testArticle', $oOrderArticles->current()->oxorderarticles__oxartnum->value);
        $this->assertEquals(4, $oOrderArticles->current()->oxorderarticles__oxamount->value);
        $this->assertEquals(0, $oOrderArticles->current()->oxorderarticles__oxstorno->value);

        $this->setRequestParameter("sArtID", $oOrderArticles->current()->getId());

        // canceling
        $oObj->storno();
        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrder');

        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals(1, $oOrderArticles->count());
        $this->assertEquals(1, $oOrderArticles->current()->oxorderarticles__oxstorno->value);

        $this->setRequestParameter("sArtID", $oOrderArticles->current()->getId());

        // "un"-canceling
        $oObj->storno();
        $oOrder = oxNew('oxorder');
        $oOrder->load('_testOrder');

        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals(1, $oOrderArticles->count());
        $this->assertEquals(0, $oOrderArticles->current()->oxorderarticles__oxstorno->value);
    }


    /**
     * Test get edit object.
     */
    public function testGetEditObject()
    {
        $this->setRequestParameter("oxid", null);

        $oView = oxNew('Order_Article');
        $this->assertNull($oView->getEditObject());

        $this->setRequestParameter("oxid", "_testOrderId1");

        $oView = oxNew('Order_Article');
        $oOrder = $oView->getEditObject();
        $this->assertTrue($oOrder instanceof order);
    }

    /**
     * Test get search protuct article number.
     */
    public function testGetSearchProductArtNr()
    {
        $this->setRequestParameter("sSearchArtNum", null);
        $oView = oxNew('Order_Article');
        $this->assertNull($oView->getSearchProductArtNr());

        $this->setRequestParameter("sSearchArtNum", 123);
        $oView = oxNew('Order_Article');
        $this->assertEquals(123, $oView->getSearchProductArtNr());
    }

    /**
     * Test get search protuct.
     */
    public function testGetSearchProduct()
    {
        $sProdArtNr = "xxx";
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxartnum = new oxField($sProdArtNr);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\OrderArticle::class, ["getSearchProductArtNr", "getProductList"]);
        $oView->expects($this->once())->method('getSearchProductArtNr')->will($this->returnValue($sProdArtNr));
        $oView->expects($this->once())->method('getProductList')->will($this->returnValue([$oProduct]));

        $this->assertEquals($oProduct, $oView->getSearchProduct());
    }

    /**
     * Test get main protuct.
     */
    public function testGetMainProduct()
    {
        // product number not set
        $this->setRequestParameter("sSearchArtNum", null);

        $oView = oxNew('Order_Article');
        $this->assertNull($oView->getMainProduct());

        // not existing product number
        $this->setRequestParameter("sSearchArtNum", "xxx");

        $oView = oxNew('Order_Article');
        $this->assertFalse($oView->getMainProduct());

        // existing product
        $this->setRequestParameter("sSearchArtNum", "1126");

        $oView = oxNew('Order_Article');
        $oProduct = $oView->getMainProduct();
        $this->assertTrue($oProduct instanceof article);
    }

    /**
     * Test get protuct list.
     */
    public function testGetProductList()
    {
        // empty list
        $this->setRequestParameter("sSearchArtNum", null);

        $oView = oxNew('Order_Article');
        $oList = $oView->getProductList();
        $this->assertEquals(0, $oList->count());

        $iCnt = 4;
        $searchArticleNumber = "2077";
        if ((new Facts())->getEdition() === 'EE') {
            $iCnt = 3;
            $searchArticleNumber = "1661";
        }

        $this->setRequestParameter("sSearchArtNum", $searchArticleNumber);

        $oView = oxNew('Order_Article');
        $oList = $oView->getProductList();
        $this->assertEquals($iCnt, $oList->count());
    }

    /**
     * Test add non existing artiicle.
     */
    public function testAddThisArticleWrongArticle()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\OrderArticleHelper::class, 'oxorder');

        $this->setRequestParameter('sArtNum', 'sdasda');
        $this->setRequestParameter('am', 1);
        $this->setRequestParameter('oxid', '_testOrderId1');

        $oOrderArticle = oxNew('order_article');
        $oOrderArticle->addThisArticle();

        $oOrder = oxNew("oxOrder");
        $oOrder->load('_testOrderId1');
        $this->assertEquals(0, $oOrder->oxorder__oxtotalbrutsum->value);
    }

    /**
     * Test add artiicle with wrong ammount.
     */
    public function testAddThisArticleWrongAmount()
    {
        $this->addClassExtension(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\OrderArticleHelper::class, 'oxorder');

        $this->setRequestParameter('sArtNum', '2000');
        $this->setRequestParameter('am', 'test');
        $this->setRequestParameter('oxid', '_testOrderId1');

        $oOrderArticle = oxNew('order_article');
        $oOrderArticle->addThisArticle();

        $oOrder = oxNew("oxOrder");
        $oOrder->load('_testOrderId1');
        $this->assertEquals(0, $oOrder->oxorder__oxtotalbrutsum->value);
    }
}
