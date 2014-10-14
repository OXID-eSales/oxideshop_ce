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

/**
 * Testing thankyou class
 */
class Unit_Views_thankyouTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxarticles');
        parent::tearDown();
    }

    public function testGetBasket()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $mySession = oxSession::getInstance();
        $oBasket = oxNew ('oxBasket');
        //oxSession::setVar( 'basket', $oBasket );
        $mySession->setBasket( $oBasket );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $oThankyou->init();
        $this->assertEquals( $oBasket, $oThankyou->getBasket());
    }

    /* removing as this function is deprecated as of #0003283
    public function testShowFinalStep()
    {
        modConfig::getInstance()->setConfigParam( 'blShowFinalStep', true );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertTrue( $oThankyou->showFinalStep());
    }
    */

    public function testGetCurrencyCovIndex()
    {
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 1, $oThankyou->getCurrencyCovIndex());
    }

    public function testGetIPaymentBasket()
    {
        $oPrice = oxNew ('oxPrice');
        $oPrice->setPrice(10.12);
        $oBasket = $this->getMock( 'oxBasket', array( 'getPrice' ) );
        $oBasket->expects( $this->once() )->method( 'getPrice')->will( $this->returnValue( $oPrice ) );

        $oThankyou = $this->getProxyClass( 'thankyou' );
        $oThankyou->setNonPublicVar( '_oBasket', $oBasket );

        $this->assertEquals( 10.12*100, $oThankyou->getIPaymentBasket());
    }

    public function testGetIPaymentAccount()
    {
        modConfig::getInstance()->setConfigParam( 'iShopID_iPayment_Account', 'testAccount' );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 'testAccount', $oThankyou->getIPaymentAccount());
    }

    public function testGetIPaymentUser()
    {
        modConfig::getInstance()->setConfigParam( 'iShopID_iPayment_User', 'testUser' );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 'testUser', $oThankyou->getIPaymentUser());
    }

    public function testGetIPaymentPassword()
    {
        modConfig::getInstance()->setConfigParam( 'iShopID_iPayment_Passwort', 'testPasswort' );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 'testPasswort', $oThankyou->getIPaymentPassword());
    }

    public function testGetTrustedShopId()
    {
        modConfig::getInstance()->setConfigParam( 'tsSealActive', 1);
        modConfig::getInstance()->setConfigParam( 'tsSealType', array( 0 => 'CLASSIC'));
        modConfig::getInstance()->setConfigParam( 'iShopID_TrustedShops', array ( 0 => 'XASASASASASASASASASASASASASASASAS' ) );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 'XASASASASASASASASASASASASASASASAS', $oThankyou->getTrustedShopId());
    }

    public function testGetMailError()
    {
        modConfig::setParameter( 'mailerror', 'testShop' );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 'testShop', $oThankyou->getMailError());
    }

    public function testGetOrder()
    {
        $myDB    = oxDb::getDb();
        $sInsert = "Insert into oxorder (`oxid`, `oxordernr`) values ('_test', '158')";
        $myDB->Execute($sInsert);

        $oBasket = $this->getMock( 'oxBasket', array( 'getOrderId' ) );
        $oBasket->expects( $this->once() )->method( 'getOrderId')->will( $this->returnValue( '_test' ) );

        $oThankyou = $this->getProxyClass( 'thankyou' );
        $oThankyou->setNonPublicVar( '_oBasket', $oBasket );

        $this->assertEquals( '_test', $oThankyou->getOrder()->getId());
    }

    public function testGetAlsoBoughtTheseProducts()
    {
        $this->oArticle = $this->getProxyClass('oxarticle');
        $this->oArticle->load('1126');
        $oBasketItem = $this->getMock( 'oxBasketItem', array( 'getArticle' ) );
        $oBasketItem->expects( $this->once() )->method( 'getArticle')->will( $this->returnValue( $this->oArticle ) );

        $oBasket = $this->getMock( 'oxBasket', array( 'getContents' ) );
        $oBasket->expects( $this->once() )->method( 'getContents')->will( $this->returnValue( array($oBasketItem) ) );

        $oThankyou = $this->getProxyClass( 'thankyou' );
        $oThankyou->setNonPublicVar( '_oBasket', $oBasket );
        $this->assertNull( $oThankyou->getAlsoBoughtTheseProducts());
    }

    /* removed as shopFinalStep function is deprecated as of #0003283
    public function testGetAlsoBoughtTheseProductsIfDontShowFinalStep()
    {
        $oThankyou = $this->getMock( 'thankyou', array( 'showFinalStep' ) );
        $oThankyou->expects( $this->any() )->method( 'showFinalStep')->will( $this->returnValue( false ) );

        $this->assertFalse( $oThankyou->getAlsoBoughtTheseProducts());
    }
    */

    // #1276: If product is "If out out stock, offline" and remaining stock is ordered, "Shp offline" error is shown in Order step 5
    public function testRender()
    {
        $this->oArticle = $this->getProxyClass('oxarticle');
        $this->oArticle->setId('_testArt');
        $this->oArticle->oxarticles__oxprice = new oxField(15.5, oxField::T_RAW);
        $this->oArticle->oxarticles__oxshopid = new oxField(oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW);
        $this->oArticle->oxarticles__oxshopincl = new oxField(oxConfig::getInstance()->getBaseShopId(), oxField::T_RAW);
        $this->oArticle->oxarticles__oxtitle = new oxField("test", oxField::T_RAW);
        $this->oArticle->oxarticles__oxstock = new oxField(0, oxField::T_RAW);
        $this->oArticle->oxarticles__oxstockflag = new oxField(2, oxField::T_RAW);
        $this->oArticle->save();


        $oBasketItem = $this->getProxyClass('oxbasketitem');
        $oBasketItem->setNonPublicVar( '_sProductId', '_testArt' );
        $oBasket = $this->getMock( 'oxBasket', array( 'getContents', 'getProductsCount', 'getOrderId' ) );
        $oBasket->expects( $this->once() )->method( 'getContents')->will( $this->returnValue( array($oBasketItem) ) );
        $oBasket->expects( $this->once() )->method( 'getProductsCount')->will( $this->returnValue( 1 ) );
        $oBasket->expects( $this->once() )->method( 'getOrderId')->will( $this->returnValue( 1 ) );
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $oThankyou->setNonPublicVar( '_oBasket', $oBasket );
        $oThankyou->render();
    }
    
    // #2580: Checking if after order unregistered user contact data were deleted
    public function testRender_resetUnregisteredUser()
    {
        $oUser = oxNew( "oxuser" );
        $oUser->oxuser__oxpassword = new oxField( "" );
        
        $oBasket = $this->getMock( 'oxBasket', array( 'getProductsCount' ) );
        $oBasket->expects( $this->once() )->method( 'getProductsCount')->will( $this->returnValue( 1 ) );
        
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $oThankyou->setNonPublicVar( '_oBasket', $oBasket );
        $oThankyou->setUser( $oUser );
        
        oxSession::setVar( "usr", "testValue1");
        oxSession::setVar( "dynvalue", "testValue2");
        
        $oThankyou->render();
        
        $this->assertFalse( oxSession::hasVar( "usr" ) );
        $this->assertFalse( oxSession::hasVar( "dynvalue" ) );
    }

    public function testGetActionClassName()
    {
        $oThankyou = $this->getProxyClass( 'thankyou' );
        $this->assertEquals( 'start', $oThankyou->getActionClassName());
    }

    /**
     * Testing Thankyou::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oTh = new Thankyou();

        $this->assertEquals(1, count($oTh->getBreadCrumb()));
    }
    
    /**
     * Testing Thankyou::getCountryISO3()
     *
     * @return null
     */
    public function testGetCountryISO3()
    {
        $oOrder = new oxOrder();
        $oOrder->oxorder__oxbillcountryid = new oxField( 'a7c40f631fc920687.20179984' );
        
        $oTh = $this->getMock( 'thankyou', array('getOrder') );
        $oTh->expects($this->any())->method('getOrder')->will( $this->returnValue( $oOrder ) );

        $this->assertEquals('DEU', $oTh->getCountryISO3());
    }

}
