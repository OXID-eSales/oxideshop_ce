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
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Module for oxorder testing
 */
class modOxOrder_orderArticle extends oxOrder
{
    /**
     * Modify recalculateOrder method.
     *
     * @param array $aNewOrderArticles article list of new order
     * @param bool  $blChangeDelivery  change deliveryr
     * @param bool  $blChangeDiscount  change discount
     *
     * @return null
     */
    public function recalculateOrder( $aNewOrderArticles = array(), $blChangeDelivery = false, $blChangeDiscount = false )
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
    public function validateStock( $oBasket )
    {
        return true;
    }
}

/**
 * Tests for Order_Article class
 */
class Unit_Admin_OrderArticleTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myConfig = oxConfig::getInstance();

        modConfig::getInstance()->setConfigParam( 'blPerfNoBasketSaving', true );

        // adding test order
        $oOrder = new oxbase();
        $oOrder->init( 'oxorder' );
        $oOrder->setId( '_testOrder' );
        $oOrder->oxorder__oxuserid = new oxField( 'oxdefaultadmin' );
        $oOrder->save();

        // adding test article
        $oArticle = new oxbase();
        $oArticle->init( 'oxarticles' );
        $oArticle->load( '1126' );
        $oArticle->setId( '_testArticle' );
        $oArticle->oxarticles__oxartnum = new oxField( '_testArticle' );
        $oArticle->oxarticles__oxstock  = new oxField( 100 );
        $oArticle->save();

        //set order
        $oOrder = oxNew( "oxOrder" );
        $oOrder->setId( '_testOrderId1' );
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

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oOrder = new oxorder();
        $oOrder->delete( '_testOrder' );

        $oArticle = new oxarticle();
        $oArticle->delete( '_testArticle' );

        $this->cleanUpTable( 'oxorder' );
        oxRemClassModule( 'modOxOrder_orderArticle' );

        parent::tearDown();
    }

    /**
     * Test add article.
     *
     * @return null
     */
    public function testAddThisArticle()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        modConfig::setParameter( "aid", '_testArticle' );
        modConfig::setParameter( "am", 4 );
        modConfig::setParameter( "oxid", '_testOrder');

        $oObj = new order_article();
        $oObj->addThisArticle();

        // now reading order articles table
        $oOrder = oxNew( "oxOrder" );
        $oOrder->load( '_testOrder' );
        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals( 1, $oOrderArticles->count() );
        $this->assertEquals( '_testArticle', $oOrderArticles->current()->oxorderarticles__oxartnum->value );
        $this->assertEquals( 4, $oOrderArticles->current()->oxorderarticles__oxamount->value );
    }

    /**
     * Test add another article.
     *
     * @return null
     */
    public function testAddThisArticle2()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxAddClassModule( 'modOxOrder_orderArticle', 'oxorder' );

        modConfig::setParameter( 'aid', '2000' );
        modConfig::setParameter( 'am', 1 );
        modConfig::setParameter( 'oxid', '_testOrderId1' );

        $oOrderArticle = new order_article();
        $oOrderArticle->addThisArticle();

        $oOrder = oxNew( "oxOrder" );
        $oOrder->load('_testOrderId1');
        $this->assertEquals( 1, $oOrder->oxorder__oxtotalbrutsum->value  );
    }

    /**
     * Test delete article.
     *
     * @return null
     */
    public function testDeleteThisArticle()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        modConfig::setParameter( "aid", '_testArticle' );
        modConfig::setParameter( "am", 4 );
        modConfig::setParameter( "oxid", '_testOrder');

        $oObj = new order_article();
        $oObj->addThisArticle();

        // now reading order articles table
        $oOrder = new oxorder();
        $oOrder->load( '_testOrder' );
        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals( 1, $oOrderArticles->count() );
        $this->assertEquals( '_testArticle', $oOrderArticles->current()->oxorderarticles__oxartnum->value );
        $this->assertEquals( 4, $oOrderArticles->current()->oxorderarticles__oxamount->value );

        modConfig::setParameter( "sArtID", $oOrderArticles->current()->getId() );

        $oObj->deleteThisArticle();

        // now reading order articles table
        $oOrder = new oxorder();
        $oOrder->load( '_testOrder' );
        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals( 0, $oOrderArticles->count() );
    }

    /**
     * Test cancel article.
     *
     * @return null
     */
    public function testStorno()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '".oxConfig::getInstance()->getShopUrl()."'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        modConfig::setParameter( "aid", '_testArticle' );
        modConfig::setParameter( "am", 4 );
        modConfig::setParameter( "oxid", '_testOrder');

        $oObj = new order_article();
        $oObj->addThisArticle();

        $oOrder = new oxorder();
        $oOrder->load( '_testOrder' );
        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals( 1, $oOrderArticles->count() );
        $this->assertEquals( '_testArticle', $oOrderArticles->current()->oxorderarticles__oxartnum->value );
        $this->assertEquals( 4, $oOrderArticles->current()->oxorderarticles__oxamount->value );
        $this->assertEquals( 0, $oOrderArticles->current()->oxorderarticles__oxstorno->value );

        modConfig::setParameter( "sArtID", $oOrderArticles->current()->getId() );

        // canceling
        $oObj->storno();
        $oOrder = new oxorder();
        $oOrder->load( '_testOrder' );
        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals( 1, $oOrderArticles->count() );
        $this->assertEquals( 1, $oOrderArticles->current()->oxorderarticles__oxstorno->value );

        modConfig::setParameter( "sArtID", $oOrderArticles->current()->getId() );

        // "un"-canceling
        $oObj->storno();
        $oOrder = new oxorder();
        $oOrder->load( '_testOrder' );
        $oOrderArticles = $oOrder->getOrderArticles();

        $this->assertEquals( 1, $oOrderArticles->count() );
        $this->assertEquals( 0, $oOrderArticles->current()->oxorderarticles__oxstorno->value );
    }


    /**
     * Test get edit object.
     *
     * @return null
     */
    public function testGetEditObject()
    {
        modConfig::setParameter( "oxid", null );

        $oView = new Order_Article();
        $this->assertNull( $oView->getEditObject() );

        modConfig::setParameter( "oxid", "_testOrderId1" );

        $oView = new Order_Article();
        $oOrder = $oView->getEditObject();
        $this->assertTrue( $oOrder instanceof oxorder );
    }

    /**
     * Test get search protuct article number.
     *
     * @return null
     */
    public function testGetSearchProductArtNr()
    {
        modConfig::setParameter( "sSearchArtNum", null );
        $oView = new Order_Article();
        $this->assertNull( $oView->getSearchProductArtNr() );

        modConfig::setParameter( "sSearchArtNum", 123 );
        $oView = new Order_Article();
        $this->assertEquals( 123, $oView->getSearchProductArtNr() );
    }

    /**
     * Test get search protuct.
     *
     * @return null
     */
    public function testGetSearchProduct()
    {
        $sProdArtNr = "xxx";
        $oProduct   = new oxArticle();
        $oProduct->oxarticles__oxartnum = new oxField( $sProdArtNr );

        $oView = $this->getMock( "Order_Article", array( "getSearchProductArtNr", "getProductList" ) );
        $oView->expects( $this->once() )->method( 'getSearchProductArtNr' )->will( $this->returnValue( $sProdArtNr ) );
        $oView->expects( $this->once() )->method( 'getProductList' )->will( $this->returnValue( array( $oProduct ) ) );

        $this->assertEquals( $oProduct, $oView->getSearchProduct() );
    }

    /**
     * Test get main protuct.
     *
     * @return null
     */
    public function testGetMainProduct()
    {
        // product number not set
        modConfig::setParameter( "sSearchArtNum", null );

        $oView = new Order_Article();
        $this->assertNull( $oView->getMainProduct() );

        // not existing product number
        modConfig::setParameter( "sSearchArtNum", "xxx" );

        $oView = new Order_Article();
        $this->assertFalse( $oView->getMainProduct() );

        // existing product
        modConfig::setParameter( "sSearchArtNum", "1126" );

        $oView = new Order_Article();
        $oProduct = $oView->getMainProduct();
        $this->assertTrue( $oProduct instanceof oxarticle );
    }

    /**
     * Test get protuct list.
     *
     * @return null
     */
    public function testGetProductList()
    {
        // empty list
        modConfig::setParameter( "sSearchArtNum", null );

        $oView = new Order_Article();
        $oList = $oView->getProductList();
        $this->assertEquals( 0, $oList->count() );

        // existing product
            modConfig::setParameter( "sSearchArtNum", "2077" );
            $iCnt = 4;

        $oView = new Order_Article();
        $oList = $oView->getProductList();
        $this->assertEquals( $iCnt, $oList->count() );
    }

    /**
     * Test add non existing artiicle.
     *
     * @return null
     */
    public function testAddThisArticleWrongArticle()
    {
        oxAddClassModule( 'modOxOrder_orderArticle', 'oxorder' );

        modConfig::setParameter( 'sArtNum', 'sdasda' );
        modConfig::setParameter( 'am', 1 );
        modConfig::setParameter( 'oxid', '_testOrderId1' );

        $oOrderArticle = new order_article();
        $oOrderArticle->addThisArticle();

        $oOrder = oxNew( "oxOrder" );
        $oOrder->load('_testOrderId1');
        $this->assertEquals( 0, $oOrder->oxorder__oxtotalbrutsum->value  );
    }

    /**
     * Test add artiicle with wrong ammount.
     *
     * @return null
     */
    public function testAddThisArticleWrongAmount()
    {
        oxAddClassModule( 'modOxOrder_orderArticle', 'oxorder' );

        modConfig::setParameter( 'sArtNum', '2000' );
        modConfig::setParameter( 'am', 'test' );
        modConfig::setParameter( 'oxid', '_testOrderId1' );

        $oOrderArticle = new order_article();
        $oOrderArticle->addThisArticle();

        $oOrder = oxNew( "oxOrder" );
        $oOrder->load('_testOrderId1');
        $this->assertEquals( 0, $oOrder->oxorder__oxtotalbrutsum->value  );
    }
}
