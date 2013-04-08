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
 * @version   SVN: $Id: oxemailTest.php 32921 2011-02-04 13:42:04Z rimvydas.paskevicius $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';


class Unit_core_oxemailUtf8Test extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

   /**
    * Tear down the fixture.
    *
    * @return null
    */
    protected function tearDown()
    {
            parent::tearDown();
    }

    /**
     * Testing email charset
     *
     * @return null
     */
    public function testGetCharset()
    {
        $oEmail = new oxEmail();
        $this->assertEquals( "UTF-8", $oEmail->getCharset() );
    }

    /**
     * Testing email charset
     *
     * @return null
     */
    public function testGetCurrency()
    {
        $oEmail = new oxEmail();
        $this->assertEquals( "€", $oEmail->getCurrency()->sign );
    }

    /**
     * Test sending ordering mail to user
     */
    public function testSendOrderEmailToUser()
    {
        $oBasketItem = $this->getMock( 'oxbasketitem', array('getUnitPrice', 'getRegularUnitPrice', 'getFUnitPrice', 'getFTotalPrice', 'getVatPercent', 'getAmount', 'getTitle', 'getProductId') );
        $oBasketItem->expects( $this->any() )->method( 'getFUnitPrice' )->will($this->returnValue( '256,00' ) );
        $oBasketItem->expects( $this->any() )->method( 'getUnitPrice' )->will($this->returnValue( new oxPrice() ) );
        $oBasketItem->expects( $this->any() )->method( 'getRegularUnitPrice' )->will($this->returnValue( new oxPrice() ) );
        $oBasketItem->expects( $this->any() )->method( 'getTitle' )->will($this->returnValue( "testarticle" ) );

        // insert test article
        $oArticle = oxNew( "oxarticle" );
        $oArticle->setId('_testArticleId');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField();

        $aBasketContents[] = $oBasketItem;
        $aBasketArticles[] = $oArticle;

        $oBasket = $this->getMock( 'oxBasket', array( "getBasketArticles", "getContents", "getCosts", "getFPrice", "getFProductsPrice", "getShowGiftWrapping", "getFGiftCardCosts" ) );
        $oBasket->expects( $this->any() )->method( 'getBasketArticles')->will( $this->returnValue( $aBasketArticles ));
        $oBasket->expects( $this->any() )->method( 'getContents')->will( $this->returnValue( $aBasketContents ));
        $oBasket->expects( $this->any() )->method( 'getCosts')->will( $this->returnValue( new oxPrice(0) ));
        $oBasket->expects( $this->any() )->method( 'getFPrice')->will( $this->returnValue( 8 ));
        $oBasket->expects( $this->any() )->method( 'getFProductsPrice')->will( $this->returnValue( 7 ));
        $oBasket->expects( $this->any() )->method( 'getShowGiftWrapping')->will( $this->returnValue( true ));
        $oBasket->expects( $this->any() )->method( 'getFGiftCardCosts')->will( $this->returnValue( 3 ));

        $oPayment = new oxPayment();
        $oPayment->oxpayments__oxdesc = new oxField( "testPaymentDesc" );

        $oUser = oxNew( "oxuser" );
        $oUser->setId('_testUserId');
        $oUser->oxuser__oxusername = new oxField('username@useremail.nl', oxField::T_RAW);
        $oUser->oxuser__oxfname = new oxField('testUserFName', oxField::T_RAW);
        $oUser->oxuser__oxlname = new oxField('testUserLName', oxField::T_RAW);

        $oOrder = $this->getMock( 'oxOrder', array( "getOrderUser", "getBasket", "getPayment" ) );
        $oOrder->expects( $this->any() )->method( 'getOrderUser')->will( $this->returnValue( $oUser ));
        $oOrder->expects( $this->any() )->method( 'getBasket')->will( $this->returnValue( $oBasket ));
        $oOrder->expects( $this->any() )->method( 'getPayment')->will( $this->returnValue( $oPayment ));

        $oOrder->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $oOrder->oxorder__oxbillfname = new oxField( '' );
        $oOrder->oxorder__oxbilllname = new oxField( '' );
        $oOrder->oxorder__oxbilladdinfo = new oxField( '' );
        $oOrder->oxorder__oxbillstreet = new oxField( '' );
        $oOrder->oxorder__oxbillcity = new oxField( '' );
        $oOrder->oxorder__oxbillcountry = new oxField( '' );
        $oOrder->oxorder__oxbillcompany = new oxField( '' );
        $oOrder->oxorder__oxdeltype = new oxField( "oxidstandard" );

        $oShop = oxNew( "oxshop" );
        $oShop->load( oxConfig::getInstance()->getShopId() );

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $oShop ));
        $oEmail->expects( $this->any() )->method( 'getOrderFileList' )->will( $this->returnValue( false ));

        $blRet = $oEmail->sendOrderEmailToUser( $oOrder );
        $this->assertTrue( $blRet );

        $sBody = $oEmail->getBody();
        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        $oStr = oxStr::getStr();

        // checking if there are some utf-8 strings

        // translation check
        $this->assertTrue( $oStr->strpos($sBody, "Grußkarte:") > 0 );

        // euro sign check
        $this->assertTrue( $oStr->strpos($sBody, "256,00 €") > 0 );

        // strings, that comes from oxcontent
        $this->assertTrue( $oStr->strpos($sBody, "Vielen Dank für Ihre Bestellung!") > 0 );
        $this->assertTrue( $oStr->strpos($sBody, "Bitte fügen Sie hier Ihre vollständige Anbieterkennzeichnung ein.") > 0 );
    }
}

