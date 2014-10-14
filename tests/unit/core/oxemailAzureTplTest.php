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


class Unit_Core_oxemailAzureTplTest extends OxidTestCase
{

    protected $_oEmail     = null;
    protected $_oUser      = null;
    protected $_oShop      = null;
    protected $_oArticle   = null;
    protected $_sOrigTheme = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        // reload smarty
        oxUtilsView::getInstance()->getSmarty(true);

        $this->_oEmail = oxNew( "oxEmail");

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxorderarticles');

        //set default user
        $this->_oUser = oxNew( "oxuser" );
        $this->_oUser->setId('_testUserId');
        $this->_oUser->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $this->_oUser->oxuser__oxusername = new oxField('username@useremail.nl', oxField::T_RAW);
        $this->_oUser->oxuser__oxcustnr = new oxField('998', oxField::T_RAW);
        $this->_oUser->oxuser__oxfname = new oxField('testUserFName', oxField::T_RAW);
        $this->_oUser->oxuser__oxlname = new oxField('testUserLName', oxField::T_RAW);
        $this->_oUser->oxuser__oxpassword = new oxField('ox_BBpaRCslUU8u', oxField::T_RAW); //pass = admin
        $this->_oUser->oxuser__oxregister = new oxField(date("Y-m-d H:i:s"), oxField::T_RAW);
        $this->_oUser->save();

        // set shop params for testing
        $this->_oShop = oxNew( "oxshop" );
        $this->_oShop->load( oxConfig::getInstance()->getShopId() );
        $this->_oShop->oxshops__oxorderemail = new oxField('orderemail@orderemail.nl', oxField::T_RAW);
        $this->_oShop->oxshops__oxordersubject = new oxField('testOrderSubject', oxField::T_RAW);
        $this->_oShop->oxshops__oxsendednowsubject = new oxField('testSendedNowSubject', oxField::T_RAW);
        $this->_oShop->oxshops__oxname = new oxField('testShopName', oxField::T_RAW);
        $this->_oShop->oxshops__oxowneremail = new oxField('shopOwner@shopOwnerEmail.nl', oxField::T_RAW);
        $this->_oShop->oxshops__oxinfoemail = new oxField('shopInfoEmail@shopOwnerEmail.nl', oxField::T_RAW);
        //$this->_oShop->oxshops__oxsmtp = new oxField('localhost', oxField::T_RAW);
        $this->_oShop->oxshops__oxsmtp = new oxField('127.0.0.1', oxField::T_RAW);
        $this->_oShop->oxshops__oxsmtpuser = new oxField('testSmtpUser', oxField::T_RAW);
        $this->_oShop->oxshops__oxsmtppwd = new oxField('testSmtpPassword', oxField::T_RAW);
        $this->_oShop->oxshops__oxregistersubject = new oxField('testUserRegistrationSubject', oxField::T_RAW);
        $this->_oShop->oxshops__oxforgotpwdsubject = new oxField('testUserFogotPwdSubject', oxField::T_RAW);

        // replace default shop
        //$this->_oEmail->setShop( $this->_oShop );

        // insert test article
        $this->_oArticle = oxNew( "oxarticle" );
        $this->_oArticle->setId('_testArticleId');
        $this->_oArticle->oxarticles__oxtitle = new oxField('testArticle', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxartnum = new oxField('123456789', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxshopid = new oxField(oxConfig::getInstance()->getShopId(), oxField::T_RAW);
        //$this->_oArticle->oxarticles__oxamount = new oxField('12', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxshortdesc = new oxField('testArticleDescription', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindactive = new oxField('1', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);


        $this->_oArticle->save();

        oxDb::getDb()->Execute( "Insert into oxorderarticles (`oxid`, `oxartid`, `oxamount`, `oxtitle`, `oxartnum`)
                  values ('_testOrderArtId', '_testArticleId' , '7' , 'testArticleTitle', '5')" );
        oxDb::getDb()->Execute( "Update oxarticles set `oxtitle_1`='testArticle_EN' where `oxid`='_testArticleId'" );

    }

   /**
    * Tear down the fixture.
    *
    * @return null
    */
    protected function tearDown()
    {
            // reload smarty
            oxUtilsView::getInstance()->getSmarty(true);

            $oActShop = oxConfig::getInstance()->getActiveShop();
            $oActShop->setLanguage(0);
            oxLang::getInstance()->setBaseLanguage(0);
            $this->cleanUpTable('oxuser');
            $this->cleanUpTable('oxorderarticles');
            $this->cleanUpTable('oxarticles');

            $this->cleanUpTable('oxremark', 'oxparentid');

            parent::tearDown();
    }

    protected function checkMailFields( $aFields = array(), $oEmail = null )
    {
        if ( !$oEmail ) {
            $oEmail = $this->_oEmail;
        }

        if ( $aFields['sRecipient'] ) {
            $aRecipient = $oEmail->getRecipient();
            $this->assertEquals( $aFields['sRecipient'], $aRecipient[0][0], 'Incorect mail recipient' );
        }

        if ( $aFields['sRecipientName'] ) {
            $aRecipient = $oEmail->getRecipient();
            $this->assertEquals( $aFields['sRecipientName'], $aRecipient[0][1], 'Incorect mail recipient name' );
        }

        if ( $aFields['sSubject'] ) {
            $this->assertEquals( $aFields['sSubject'], $oEmail->getSubject(), 'Incorect mail subject' );
        }

        if ( $aFields['sFrom'] ) {
            $sFrom = $oEmail->getFrom();
            $this->assertEquals( $aFields['sFrom'], $sFrom, 'Incorect mail from address' );
        }

        if ( $aFields['sFromName'] ) {
            $sFromName = $oEmail->getFromName();
            $this->assertEquals( $aFields['sFromName'], $sFromName, 'Incorect mail from name' );
        }

        if ( $aFields['sReplyTo'] ) {
            $aReplyTo = $oEmail->getReplyTo();
            $this->assertEquals( $aFields['sReplyTo'], $aReplyTo[0][0], 'Incorect mail reply to address' );
        }

        if ( $aFields['sReplyToName'] ) {
            $aReplyTo = $oEmail->getReplyTo();
            $this->assertEquals( $aFields['sReplyToName'], $aReplyTo[0][1], 'Incorect mail reply to name' );
        }

        if ( $aFields['sBody'] ) {
            $this->assertEquals( $aFields['sBody'], $oEmail->getBody(), 'Incorect mail body' );
        }

        return true;
    }

    protected function checkMailBody( $sFuncName, $sBody, $blWriteToTestFile = false )
    {
        $sUtf = ( oxConfig::getInstance()->isUtf() ) ? '_utf8' : '';

        $sPath = getTestsBasePath().'/unit/email_templates/azure/'.$sFuncName.$sUtf.'.html';
        if ( !($sExpectedBody = file_get_contents($sPath)) ) {
            return false;
        }

        //remove <img src="cid:1192193298470f6d12383b8" ... from body, because it is everytime different
        $sExpectedBody = preg_replace("/cid:[0-9a-zA-Z]+\"/", "cid:\"", $sExpectedBody);

        //replacing test shop id to good one
        $sExpectedBody = preg_replace("/shp\=testShopId/", "shp=".$this->_oShop->getId(), $sExpectedBody);

        $sBody = preg_replace("/cid:[0-9a-zA-Z]+\"/", "cid:\"", $sBody);

        // A. very special case for user password reminder
        if ( $sFuncName == 'testSendForgotPwdEmail' ) {
            $sExpectedBody = preg_replace("/uid=[0-9a-zA-Z]+\&amp;/", "", $sExpectedBody);
            $sBody = preg_replace("/uid=[0-9a-zA-Z]+\&amp;/", "", $sBody);
        }

        $sExpectedBody = preg_replace("/\s+/", " ", $sExpectedBody);
        $sBody = preg_replace("/\s+/", " ", $sBody);

        $sExpectedBody = str_replace("> <", "><", $sExpectedBody);
        $sBody = str_replace("> <", "><", $sBody);

        $sExpectedShopUrl = "http://eshop/";
        $sShopUrl = oxConfig::getInstance()->getConfigParam( 'sShopURL' );

        //remove shop url base path from links
        $sBody = str_replace($sShopUrl, $sExpectedShopUrl, $sBody);

        if ($blWriteToTestFile) {
            file_put_contents ( getTestsBasePath().'/unit/email_templates/azure/'.$sFuncName.'_test_expecting.html', $sExpectedBody );
            file_put_contents ( getTestsBasePath().'/unit/email_templates/azure/'.$sFuncName.'_test_result.html', $sBody );
        }


        $this->assertEquals( strtolower(trim($sExpectedBody)), strtolower(trim($sBody)), 'Incorect mail body' );

        return true;
    }

    /*-------------------------------------------------------------*/

    /*
     * Test sending mail
     */
    public function testSendEmail()
    {
        $sTo = 'username@useremail.nl';
        $sSubject = 'testSubject';
        $sBody = 'testBody';

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->once() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));

        $blRet = $oEmail->sendEmail( $sTo, $sSubject, $sBody );
        $this->assertTrue( $blRet, 'Mail was not sent' );

        // check mail fields
        $aFields['sRecipient']     = $sTo;
        $aFields['sBody']          = $sBody;
        $aFields['sSubject']       = $sSubject;
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');
    }

    /**
     * Test sending ordering mail to user
     */
    public function testSendOrderEmailToUser()
    {
        modConfig::getInstance()->setConfigParam( 'blSkipEuroReplace', true );
        modConfig::getInstance()->setConfigParam( 'blShowVATForDelivery', false );

        $oPrice = oxNew ( 'oxprice' );
        $oPrice->setPrice( 256 );

        $oBasketItem = $this->getMock( 'oxbasketitem',
            array( 'getRegularUnitPrice', 'getVatPercent', 'getAmount', 'getTitle', 'getProductId' ) );

        $oBasketItem->expects( $this->any() )->method( 'getRegularUnitPrice' )->will($this->returnValue( $oPrice ) );
        $oBasketItem->expects( $this->any() )->method( 'getVatPercent' )->will($this->returnValue( 19 ) );
        $oBasketItem->expects( $this->any() )->method( 'getAmount' )->will($this->returnValue( 1 ) );
        $oBasketItem->expects( $this->any() )->method( 'getTitle' )->will($this->returnValue( "testArticle" ) );
        $oBasketItem->expects( $this->any() )->method( 'getProductId' )->will($this->returnValue( "_testArticleId" ) );

        $oBasketItem->oxarticles__oxtitle     = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();

        $oBasketItem->setPrice( $oPrice );

        $aBasketContents[] = $oBasketItem;
        $aBasketArticles[] = $this->_oArticle;

        $oPriceTotal = $this->getMock( 'oxprice' );
        $oPriceTotal->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 999 ) );
        $oPriceTotal->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 999 ) );

        $oBasket = $this->getMock( 'oxBasket',
            array( "getBasketArticles", "getContents", "getPrice", "getBruttoSum", "getNettoSum", "getProductVats" ) );

        $oBasket->expects( $this->any() )->method( 'getBasketArticles')->will( $this->returnValue( $aBasketArticles ));
        $oBasket->expects( $this->any() )->method( 'getContents')->will( $this->returnValue( $aBasketContents ));
        $oBasket->expects( $this->any() )->method( 'getPrice')->will( $this->returnValue( $oPriceTotal ));
        $oBasket->expects( $this->any() )->method( 'getBruttoSum')->will( $this->returnValue( 888 ));
        $oBasket->expects( $this->any() )->method( 'getNettoSum')->will( $this->returnValue( 777 ));
        $oBasket->expects( $this->any() )->method( 'getProductVats')->will( $this->returnValue( array('19'=>14.35, '5' => 0.38 ) ));


        $oPrice1 = $this->getMock( 'oxprice' );
        $oPrice1->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice1->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 666 ) );
        $oBasket->setCost( 'oxdelivery', $oPrice1 );

        $oPrice2 = $this->getMock( 'oxprice' );
        $oPrice2->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice2->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 5 ) );
        $oBasket->setCost( 'oxwrapping', $oPrice2 );

        $oPrice3 = $this->getMock( 'oxprice' );
        $oPrice3->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice3->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 6 ) );
        $oBasket->setCost( 'oxgiftcard', $oPrice3 );

        $oPrice4 = $this->getMock( 'oxprice' );
        $oPrice4->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice4->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( true ) );
        $oPrice4->expects( $this->any() )->method( 'getNettoPrice' )->will($this->returnValue( 7 ) );
        $oBasket->setCost( 'oxtsprotection', $oPrice4 );

        $oPayment = new oxPayment();
        $oPayment->oxpayments__oxdesc = new oxField( "testPaymentDesc" );

        $oOrder = $this->getMock( 'oxOrder', array( "getOrderUser", "getBasket", "getPayment" ) );
        $oOrder->expects( $this->any() )->method( 'getOrderUser')->will( $this->returnValue( $this->_oUser ));
        $oOrder->expects( $this->any() )->method( 'getBasket')->will( $this->returnValue( $oBasket ));
        $oOrder->expects( $this->any() )->method( 'getPayment')->will( $this->returnValue( $oPayment ));

        $oOrder->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $oOrder->oxorder__oxbillcompany = new oxField( '' );
        $oOrder->oxorder__oxbillfname = new oxField( '' );
        $oOrder->oxorder__oxbilllname = new oxField( '' );
        $oOrder->oxorder__oxbilladdinfo = new oxField( '' );
        $oOrder->oxorder__oxbillstreet = new oxField( '' );
        $oOrder->oxorder__oxbillcity = new oxField( '' );
        $oOrder->oxorder__oxbillcountry = new oxField( '' );
        $oOrder->oxorder__oxbillcompany = new oxField( '' );
        $oOrder->oxorder__oxtsprotectcosts = new oxField( '12' );
        $oOrder->oxorder__oxdeltype = new oxField( "oxidstandard" );

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages", 'getOrderFileList' ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( 'getOrderFileList')->will( $this->returnValue( false ));

        $blRet = $oEmail->sendOrderEmailToUser( $oOrder );
        $this->assertTrue( $blRet, 'Order email was not sent to customer');

        // check mail fields
        $aFields['sRecipient']     = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject']       = 'testOrderSubject (#987654321)';
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendOrderEmailToUser', $oEmail->getBody() ) ) {
            $this->fail('Incorect mail body');
        }
    }

    /**
     * Test sending ordering mail to shop owner
     */
    public function testSendOrderEmailToOwner()
    {

        oxConfig::getInstance()->setConfigParam( 'blSkipEuroReplace', true );
        modConfig::getInstance()->setConfigParam( 'blShowVATForDelivery', false );

        $oPrice = oxNew ( 'oxprice' );
        $oPrice->setPrice( 256 );

        $oBasketItem = $this->getMock( 'oxbasketitem',
            array( 'getRegularUnitPrice', 'getVatPercent', 'getAmount', 'getTitle', 'getProductId' ) );

        $oBasketItem->expects( $this->any() )->method( 'getRegularUnitPrice' )->will($this->returnValue( $oPrice ) );
        $oBasketItem->expects( $this->any() )->method( 'getVatPercent' )->will($this->returnValue( 19 ) );
        $oBasketItem->expects( $this->any() )->method( 'getAmount' )->will($this->returnValue( 1 ) );
        $oBasketItem->expects( $this->any() )->method( 'getTitle' )->will($this->returnValue( "testArticle" ) );
        $oBasketItem->expects( $this->any() )->method( 'getProductId' )->will($this->returnValue( "_testArticleId" ) );

        $oBasketItem->oxarticles__oxtitle     = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();

        $oBasketItem->setPrice( $oPrice );

        $aBasketContents[] = $oBasketItem;
        $aBasketArticles[] = $this->_oArticle;

        $oPriceTotal = $this->getMock( 'oxprice' );
        $oPriceTotal->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 999 ) );
        $oPriceTotal->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 999 ) );

        $oBasket = $this->getMock( 'oxBasket',
            array( "getBasketArticles", "getContents", "getPrice", "getBruttoSum", "getNettoSum", "getProductVats" ) );

        $oBasket->expects( $this->any() )->method( 'getBasketArticles')->will( $this->returnValue( $aBasketArticles ));
        $oBasket->expects( $this->any() )->method( 'getContents')->will( $this->returnValue( $aBasketContents ));
        $oBasket->expects( $this->any() )->method( 'getPrice')->will( $this->returnValue( $oPriceTotal ));
        $oBasket->expects( $this->any() )->method( 'getBruttoSum')->will( $this->returnValue( 888 ));
        $oBasket->expects( $this->any() )->method( 'getNettoSum')->will( $this->returnValue( 777 ));
        $oBasket->expects( $this->any() )->method( 'getProductVats')->will( $this->returnValue( array('19'=>14.35, '5' => 0.38 ) ));


        $oPrice1 = $this->getMock( 'oxprice' );
        $oPrice1->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice1->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 666 ) );
        $oBasket->setCost( 'oxdelivery', $oPrice1 );

        $oPrice2 = $this->getMock( 'oxprice' );
        $oPrice2->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice2->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 5 ) );
        $oBasket->setCost( 'oxwrapping', $oPrice2 );

        $oPrice3 = $this->getMock( 'oxprice' );
        $oPrice3->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice3->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( 6 ) );
        $oBasket->setCost( 'oxgiftcard', $oPrice3 );

        $oPrice4 = $this->getMock( 'oxprice' );
        $oPrice4->expects( $this->any() )->method( 'getPrice' )->will($this->returnValue( 256 ) );
        $oPrice4->expects( $this->any() )->method( 'getBruttoPrice' )->will($this->returnValue( true ) );
        $oPrice4->expects( $this->any() )->method( 'getNettoPrice' )->will($this->returnValue( 7 ) );
        $oBasket->setCost( 'oxtsprotection', $oPrice4 );

        $oPayment = new oxPayment();
        $oPayment->oxpayments__oxdesc = new oxField( "testPaymentDesc" );

        $oOrder = $this->getMock( 'oxOrder', array( "getOrderUser", "getBasket", "getPayment" ) );
        $oOrder->expects( $this->any() )->method( 'getOrderUser')->will( $this->returnValue( $this->_oUser ));
        $oOrder->expects( $this->any() )->method( 'getBasket')->will( $this->returnValue( $oBasket ));
        $oOrder->expects( $this->any() )->method( 'getPayment')->will( $this->returnValue( $oPayment ));

        $oOrder->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $oOrder->oxorder__oxbillcompany = new oxField( '' );
        $oOrder->oxorder__oxbillfname = new oxField( '' );
        $oOrder->oxorder__oxbilllname = new oxField( '' );
        $oOrder->oxorder__oxbilladdinfo = new oxField( '' );
        $oOrder->oxorder__oxbillstreet = new oxField( '' );
        $oOrder->oxorder__oxbillcity = new oxField( '' );
        $oOrder->oxorder__oxbillcountry = new oxField( '' );
        $oOrder->oxorder__oxtsprotectcosts = new oxField( '12' );
        $oOrder->oxorder__oxdeltype = new oxField( "oxidstandard" );

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendOrderEmailToOwner( $oOrder );
        $this->assertTrue( $blRet, 'Order email was not sent to shop owner' );

        // check mail fields
        $aFields['sRecipient']     = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = 'order';
        $aFields['sSubject']       = 'testOrderSubject (#987654321)';
        $aFields['sFrom']          = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sFromName']      = '';
        $aFields['sReplyTo']       = 'username@useremail.nl';
        $aFields['sReplyToName']   = 'testUserFName testUserLName';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendOrderEMailToOwner', $oEmail->getBody()) )
            $this->fail('Incorect mail body');
    }

    /**
     * Test sending ordering mail to shop owner when shop language is different from admin language.
     * Shop language must be same as admin language.
     */
    public function testSendOrderEMailToOwnerWhenShopLangIsDifferentFromAdminLang()
    {
        $myConfig = oxConfig::getInstance();
        oxLang::getInstance()->setTplLanguage( 1 );
        oxLang::getInstance()->setBaseLanguage( 1 );

        $oPayment = new oxPayment();
        $oPayment->oxpayments__oxdesc = new oxField( "testPaymentDesc" );

        $oBasket = new oxBasket();
        $oBasket->setCost('oxpayment', new oxPrice(0) );
        $oBasket->setCost('oxdelivery', new oxPrice(6626) );

        $oOrder = $this->getMock( 'oxOrder', array( "getOrderUser", "getBasket", "getPayment" ) );
        $oOrder->expects( $this->any() )->method( 'getOrderUser')->will( $this->returnValue( $this->_oUser ));
        $oOrder->expects( $this->any() )->method( 'getBasket')->will( $this->returnValue( $oBasket ));
        $oOrder->expects( $this->any() )->method( 'getPayment')->will( $this->returnValue( $oPayment ));

        $oOrder->oxorder__oxbillcompany = new oxField( '' );
        $oOrder->oxorder__oxbillfname = new oxField( '' );
        $oOrder->oxorder__oxbilllname = new oxField( '' );
        $oOrder->oxorder__oxbilladdinfo = new oxField( '' );
        $oOrder->oxorder__oxbillstreet = new oxField( '' );
        $oOrder->oxorder__oxbillcity = new oxField( '' );
        $oOrder->oxorder__oxbillcountry = new oxField( '' );
        $oOrder->oxorder__oxdeltype = new oxField( "oxidstandard" );

        $oShop_en = clone $this->_oShop;
        $oShop_en->oxshops__oxordersubject = new oxField('testOrderSubject_en', oxField::T_RAW);

        $oEmail = $this->getMock( 'oxEmail', array( "_getShop", "_sendMail" ) );
        $oEmail->expects( $this->at(0) )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->at(1) )->method( '_getShop')->with( $this->equalTo(1) )->will( $this->returnValue( $oShop_en ));
        $oEmail->expects( $this->at(2) )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->at(3) )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_sendMail')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendOrderEmailToOwner( $oOrder );

        $this->assertTrue( $blRet, 'Order email was not sent to shop owner' );

        // check mail fields
        $aFields['sRecipient']     = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = 'order';
        $aFields['sSubject']       = 'testOrderSubject_en (#)';
        $aFields['sFrom']          = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sFromName']      = '';
        $aFields['sReplyTo']       = 'username@useremail.nl';
        $aFields['sReplyToName']   = 'testUserFName testUserLName';

        if ( !$this->checkMailFields($aFields, $oEmail) ) {
            $this->fail('Incorect mail fields');
        }

        //checking if mail body is in english
        $this->assertContains( 'The following products have been ordered in testShopName right now:', $oEmail->getBody() );

    }

    /*
     * Test sending registration mail to user
     */
    public function testSendRegisterEMail()
    {
        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));


        $blRet = $oEmail->sendRegisterEMail( $this->_oUser );
        $this->assertTrue( $blRet, 'Registration mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject']       = 'testUserRegistrationSubject';
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendRegisterEMail', $oEmail->getBody()) )
            $this->fail('Incorect mail body');
    }


    /*
     * Test sending forgot password to user
     */
    public function testSendForgotPwdEmail()
    {
        $myConfig = oxConfig::getInstance();

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendForgotPwdEmail( 'username@useremail.nl' );
        $this->assertTrue( $blRet, 'Forgot password email was not sent' );

        // check mail fields
        $aFields['sRecipient']     = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject']       = 'testUserFogotPwdSubject';
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendForgotPwdEmail', $oEmail->getBody()) )
            $this->fail('Incorect mail body');
    }

    /*
     * Test sending forgot password to not existing user
     */
    public function testSendForgotPwdEmailToNotExistingUser()
    {
        $myConfig = oxConfig::getInstance();

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop" ) );
        $oEmail->expects( $this->never() )->method( '_sendMail');
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));

        $blRet = $oEmail->SendForgotPwdEmail( 'nosuchuser@useremail.nl' );
        $this->assertFalse( $blRet, 'Mail was sent to not existing user' );
    }


    /*
     * Test sending contact info mail from user to shop owner
     */
    public function testSendContactMail()
    {
        $myConfig = oxConfig::getInstance();

        $sSubject   = 'testSubject';
        $sBody      = 'testBodyMessage';
        $sUserMail  = 'username@useremail.nl';
        $sShopOwnerMail  = 'shopOwner@shopOwnerEmail.nl';

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->once() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));

        $blRet = $oEmail->sendContactMail( $sUserMail, $sSubject, $sBody );
        $this->assertTrue( $blRet, 'Contact user mail was not sent to shop owner' );

        // check mail fields
        $aFields['sRecipient']     = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = '';
        $aFields['sSubject']       = $sSubject;
        $aFields['sBody']          = $sBody;
        $aFields['sFrom']          = $sShopOwnerMail;
        $aFields['sFromName']      = '';
        $aFields['sReplyTo']       = $sUserMail;
        $aFields['sReplyToName']   = '';

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');
    }

    /*
     * Test sending newsletter cofirmation mail to user
     */
    public function testSendNewsletterDBOptInMail()
    {
        modSession::getInstance()->setId('xsessx');

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendNewsletterDbOptInMail( $this->_oUser );
        $this->assertTrue( $blRet, 'Newsletter confirmation mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject']       = 'Newsletter testShopName';
        $aFields['sFrom']          = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendNewsletterDBOptInMail', $oEmail->getBody()) )
            $this->fail('Incorect mail body');
    }

    /*
     * Test sending newsletter mail to user
     */
    public function testSendNewsletterMail()
    {

        $oNewsletter = $this->getMock( 'oxNewsletter', array( "getHtmlText" ) );
        $oNewsletter->expects( $this->once() )->method( "getHtmlText" )->will( $this->returnValue( "testNewsletterHtmlText" ));
        $oNewsletter->oxnewsletter__oxtitle = new oxField('testNewsletterTitle', oxField::T_RAW);

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->once() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));

        $blRet = $oEmail->sendNewsletterMail( $oNewsletter, $this->_oUser );
        $this->assertTrue( $blRet, 'Newsletter mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject']       = 'testNewsletterTitle';
        $aFields['sBody']          = 'testNewsletterHtmlText';
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');
    }

    /*
     * Test sending suggest email
     */
    public function testSendSuggestMail()
    {
        $oParams = new stdClass();
        $oParams->rec_email    = 'username@useremail.nl';
        $oParams->rec_name     = 'testUserFName testUserLName';
        $oParams->send_subject = 'testSuggestSubject';
        $oParams->send_email   = 'orderemail@orderemail.nl';
        $oParams->send_name    = 'testShopName';

        $oProduct = new oxArticle();
        $oProduct->load('_testArticleId');

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendSuggestMail( $oParams, $oProduct );
        $this->assertTrue( $blRet, 'Suggest mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = $oParams->rec_email;
        $aFields['sRecipientName'] = $oParams->rec_name;
        $aFields['sSubject']       = $oParams->send_subject;
        $aFields['sFrom']          = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sFromName']      = '';
        $aFields['sReplyTo']       = $oParams->send_email;
        $aFields['sReplyToName']   = $oParams->send_name;

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendSuggestMail', $oEmail->getBody()) )
            $this->fail('Incorect mail body');
    }

    /*
     * Test sending order
     */
    public function testSendSendedNowMail()
    {
        $myConfig = oxConfig::getInstance();
        $myConfig->setConfigParam( 'blAdmin', true );
        $myConfig->setAdminMode( true );

        $oOrderArticle = oxNew( "oxorderarticle" );
        $oOrderArticle->load('_testOrderArtId');
        $aOrderArticles[] = $oOrderArticle;

        $oArticles = new oxList();
        $oArticles->assign( $aOrderArticles );

        $oPayment = new oxPayment();
        $oPayment->oxpayments__oxdesc = new oxField( "testPaymentDesc" );

        $oOrder = $this->getMock( 'oxOrder', array( "getOrderUser", "getOrderArticles", "getPayment" ) );
        $oOrder->expects( $this->any() )->method( 'getOrderUser')->will( $this->returnValue( $this->_oUser ));
        $oOrder->expects( $this->any() )->method( 'getOrderArticles')->will( $this->returnValue( $oArticles ));
        $oOrder->expects( $this->any() )->method( 'getPayment')->will( $this->returnValue( $oPayment ));

        $oOrder->oxorder__oxbillcompany = new oxField( '' );
        $oOrder->oxorder__oxbillfname = new oxField( '' );
        $oOrder->oxorder__oxbilllname = new oxField( '' );
        $oOrder->oxorder__oxbilladdinfo = new oxField( '' );
        $oOrder->oxorder__oxbillstreet = new oxField( '' );
        $oOrder->oxorder__oxbillcity = new oxField( '' );
        $oOrder->oxorder__oxbillcountry = new oxField( '' );
        $oOrder->oxorder__oxdeltype = new oxField( "oxidstandard" );
        $oOrder->oxorder__oxordernr = new oxField('123456789', oxField::T_RAW);
        $oOrder->oxorder__oxbillemail = new oxField('testOrderEmail@testuser.eu', oxField::T_RAW);
        $oOrder->oxorder__oxbillfname = new oxField('testOrderBillFName', oxField::T_RAW);
        $oOrder->oxorder__oxbilllname = new oxField('testOrderBillLName', oxField::T_RAW);
        $oOrder->oxorder__oxuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages", 'getOrderFileList' ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( 'getOrderFileList')->will( $this->returnValue( false ));

        $blRet = $oEmail->sendSendedNowMail( $oOrder );
        $this->assertTrue( $blRet, 'Suggest mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = 'testOrderEmail@testuser.eu';
        $aFields['sRecipientName'] = 'testOrderBillFName testOrderBillLName';
        $aFields['sSubject']       = 'testSendedNowSubject';
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendNowMailSent', $oEmail->getBody() ) ) {
            $this->fail('Incorect mail body');
        }
    }

    /*
     * Test sending download links
     */
    public function testSendDownloadLinksMail()
    {
        $myConfig = oxConfig::getInstance();
        $myConfig->setConfigParam( 'blAdmin', true );
        $myConfig->setAdminMode( true );

        $oOrder = $this->getMock( 'oxOrder', array( "getId" ) );
        $oOrder->expects( $this->any() )->method( 'getId')->will( $this->returnValue( '_testOrder' ));

        $oOrder->oxorder__oxordernr = new oxField('123456789', oxField::T_RAW);
        $oOrder->oxorder__oxpaid = new oxField( true );
        $oOrder->oxorder__oxbillemail = new oxField('testOrderEmail@testuser.eu', oxField::T_RAW);
        $oOrder->oxorder__oxbillfname = new oxField('testOrderBillFName', oxField::T_RAW);
        $oOrder->oxorder__oxbilllname = new oxField('testOrderBillLName', oxField::T_RAW);
        $oOrder->oxorder__oxuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);

        $oOrderFile = $this->getMock( 'oxOrderFile', array( "getId", "getFileSize" ) );
        $oOrderFile->expects( $this->any() )->method( 'getId')->will( $this->returnValue( '_testOrder' ));
        $oOrderFile->expects( $this->any() )->method( 'getFileSize')->will( $this->returnValue( '5000' ));
        $oOrderFile->oxorderfiles__oxfilename = new oxField('testFileName', oxField::T_RAW);

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages", 'getOrderFileList' ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( 'getOrderFileList')->will( $this->returnValue( array($oOrderFile) ));

        $blRet = $oEmail->sendDownloadLinksMail( $oOrder, 'testDownloadLinksSubject' );
        $this->assertTrue( $blRet, 'SendDownloadLinks mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = 'testOrderEmail@testuser.eu';
        $aFields['sRecipientName'] = 'testOrderBillFName testOrderBillLName';
        $aFields['sSubject']       = 'testDownloadLinksSubject';
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sFromName']      = 'testShopName';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';
        $aFields['sReplyToName']   = 'testShopName';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendDownloadLinksMail', $oEmail->getBody()) ) {
            $this->fail('Incorect mail body');
        }
    }

    /*
     * Test sending backup mail to shop owner
     */
    public function testSendBackupMail()
    {
        $myConfig = oxConfig::getInstance();

        $aAttFiles    = array();
        $sAttPath     = null;
        $sEmailAddress = 'username@useremail.nl';
        $sSubject     = 'testBackupMailSubject';
        $sMessage     = 'testBackupMailMessage';
        $aStatus      = array();
        $aError       = array();

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->once() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));

        $blRet = $oEmail->sendBackupMail( $aAttFiles, $sAttPath, $sEmailAddress, $sSubject, $sMessage, $aStatus, $aError );
        $this->assertTrue( $blRet, 'Backup mail was not sent to shop owner' );

        // check mail fields
        $aFields['sRecipient']     = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = '';
        $aFields['sSubject']       = $sSubject;
        $aFields['sBody']          = $sMessage;
        $aFields['sFrom']          = $sEmailAddress;
        $aFields['sFromName']      = '';
        $aFields['sReplyTo']       = $sEmailAddress;
        $aFields['sReplyToName']   = '';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

    }

    /*
     * Test sends reminder email to shop owner
     */
    public function testSendStockReminder()
    {
        //set params for stock reminder
        $this->_oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindamount = new oxField('9', oxField::T_RAW);
        $this->_oArticle->save();

        $oBasketItem = $this->getMock( 'oxbasketitem', array( 'getArticle', 'getProductId' ) );
        $oBasketItem->expects( $this->any() )->method( 'getArticle' )->will($this->returnValue( $this->_oArticle ) );
        $oBasketItem->expects( $this->any() )->method( 'getProductId' )->will($this->returnValue( '_testArticleId' ) );

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendStockReminder( $aBasketContents );
        $this->assertTrue( $blRet, 'Stock remind mail was not sent' );

        // check mail fields
        $aFields['sRecipient']     = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = 'testShopName';
        $aFields['sSubject']       = oxLang::getInstance()->translateString('STOCK_LOW', 0 );
        $aFields['sFrom']          = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sFromName']      = 'testShopName';

        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendStockReminder', $oEmail->getBody()) )
            $this->fail('Incorect mail body');
    }

    /*
     * Test sending whishlist mail to user
     */
    public function testSendWishlistMail()
    {
        $oParams = new stdClass();

        $oParams->rec_email    = 'username@useremail.nl';
        $oParams->rec_name     = 'testUserFName testUserLName';
        $oParams->send_subject = 'testSuggestSubject';
        $oParams->send_email   = 'orderemail@orderemail.nl';
        $oParams->send_name    = 'testShopName';
        $oParams->send_id      = '123456789';

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

        $blRet = $oEmail->sendWishlistMail( $oParams );
        $this->assertTrue( $blRet, 'Whishlist mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = $oParams->rec_email;
        $aFields['sRecipientName'] = $oParams->rec_name;
        $aFields['sSubject']       = $oParams->send_subject;
        $aFields['sFrom']          = $oParams->send_email;
        $aFields['sFromName']      = $oParams->send_name;
        $aFields['sReplyTo']       = $oParams->send_email;
        $aFields['sReplyToName']   = $oParams->send_name;

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) )
            $this->fail('Incorect mail fields');

        //uncoment line to generate template for checking mail body
        //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

        if ( !$this->checkMailBody('testSendWishlistMail', $oEmail->getBody()) )
        $this->fail('Incorect mail body');
    }


    /*
     * Test sending a notification to the shop owner that pricealarm was subscribed
     */
    public function testSendPriceAlarmNotification()
    {
        $iErrorReporting = error_reporting( E_ALL ^ E_NOTICE );
        $e = null;

        try {
            $oParams = new stdclass();

            $aParams['email'] = 'username@useremail.nl';
            $aParams['aid']   = '_testArticleId';

            $oAlarm = oxNew( "oxpricealarm");
            $oAlarm->oxpricealarm__oxprice = new oxField('123', oxField::T_RAW);

            $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages" ) );
            $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
            $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
            $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));

            $blRet = $oEmail->sendPriceAlarmNotification( $aParams, $oAlarm );
            $this->assertTrue( $blRet, 'Price alarm mail was not sent to user' );

            // check mail fields
            $aFields['sRecipient']     = 'orderemail@orderemail.nl';
            $aFields['sRecipientName'] = 'testShopName';
            $aFields['sSubject']       = oxLang::getInstance()->translateString('PRICE_ALERT_FOR_PRODUCT', 0 ) . " testArticle";
            $aFields['sFrom']          = 'username@useremail.nl';
            $aFields['sReplyTo']       = 'username@useremail.nl';

            // check mail fields
            if ( !$this->checkMailFields($aFields, $oEmail) ) {
                $this->fail('Incorect mail fields');
            }

            //uncoment line to generate template for checking mail body
            //file_put_contents ('unit/email_templates/azure/'.__FUNCTION__.'_.html', $oEmail->getBody() );

            if ( !$this->checkMailBody('testSendPriceAlarmNotification', $oEmail->getBody()) ) {
                $this->fail('Incorect mail body');
            }
        }
        catch (Exception $e) {
        }

        error_reporting( $iErrorReporting );
        if ($e) {
            throw $e;
        }
    }

    /*
     * Test sending a notification to the customer that pricealarm was subscribed
     */
    public function testSendPriceAlarmToCustomer()
    {
        $myConfig = oxConfig::getInstance();
        $myConfig->setConfigParam( 'blAdmin', true );
        $myConfig->setAdminMode( true );
        $oAlarm = oxNew( "oxpricealarm");
        $oAlarm->oxpricealarm__oxprice = new oxField('123', oxField::T_RAW);
        $oAlarm->oxpricealarm__oxcurrency = new oxField('EUR');

        oxTestModules::addModuleObject( "oxShop", $this->_oShop );

        $oSmarty = $this->getMock( 'Smarty', array( "fetch" ) );
        $oSmarty->expects( $this->once() )->method( 'fetch')->will( $this->returnValue( "body" ));

        $oEmail = $this->getMock( 'oxEmail', array( "_sendMail", "_getShop", "_getUseInlineImages", "_getSmarty" ) );
        $oEmail->expects( $this->once() )->method( '_sendMail')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getShop')->will( $this->returnValue( $this->_oShop ));
        $oEmail->expects( $this->any() )->method( '_getUseInlineImages')->will( $this->returnValue( true ));
        $oEmail->expects( $this->any() )->method( '_getSmarty')->will( $this->returnValue( $oSmarty ));

        $blRet = $oEmail->sendPriceAlarmToCustomer( 'username@useremail.nl', $oAlarm );
        $myConfig->setConfigParam( 'blAdmin', false );
        $myConfig->setAdminMode( false );
        $this->assertTrue( $blRet, 'Price alarm mail was not sent to user' );

        // check mail fields
        $aFields['sRecipient']     = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'username@useremail.nl';
        $aFields['sSubject']       = $this->_oShop->oxshops__oxname->value;
        $aFields['sFrom']          = 'orderemail@orderemail.nl';
        $aFields['sReplyTo']       = 'orderemail@orderemail.nl';

        // check mail fields
        if ( !$this->checkMailFields($aFields, $oEmail) ) {
            $this->fail('Incorect mail fields');
        }
    }

    /*
     * Test sending a notification to the shop owner that pricealarm was subscribed in other language
     */
    public function testSendPriceAlarmNotificationInEN()
    {
        $aParams['aid']   = $this->_oArticle->getId();
        $aParams['email'] = 'info@oxid-esales.com';

        $oShop = $this->getMock( 'oxShop', array( 'getImageUrl' ) );
        $oShop->expects( $this->any() )->method( 'getImageUrl' );
        //$oShop->loadInLang( 1, oxConfig::getinstance()->getBaseShopId() );
        $oShop->oxshops__oxorderemail = new oxField( 'order@oxid-esales.com' );
        $oShop->oxshops__oxname =  new oxField( 'test shop' );

        $oEmail = $this->getMock( 'oxemail', array( '_clearMailer', '_getShop', '_setMailParams',
                                                    'setRecipient', 'setSubject', 'setBody',
                                                    'setFrom', 'setReplyTo', 'send' ) );

        $oEmail->expects( $this->once() )->method( '_clearMailer' );
        $oEmail->expects( $this->any() )->method( '_getShop' )->will( $this->returnValue( $oShop ) );
        $oEmail->expects( $this->once() )->method( '_setMailParams' )->with( $this->equalTo( $oShop ) );
        $oEmail->expects( $this->once() )->method( 'setRecipient' )->with( $this->equalTo( $oShop->oxshops__oxorderemail->value ), $this->equalTo( $oShop->oxshops__oxname->value ) );
        $oEmail->expects( $this->once() )->method( 'setSubject' )->with( $this->equalTo( "Price alert for article testArticle_EN" ) );
        $oEmail->expects( $this->once() )->method( 'setBody' );
        $oEmail->expects( $this->once() )->method( 'setFrom' )->with( $this->equalto( $aParams['email'] ), $this->equalto( '' ) );
        $oEmail->expects( $this->once() )->method( 'setReplyTo' )->with( $this->equalto( $aParams['email'] ), $this->equalto( '' ) );
        $oEmail->expects( $this->once() )->method( 'send' )->will( $this->returnValue( 'zzz' ) );

        $oAlarm = new stdClass();
        $oAlarm->oxpricealarm__oxprice = new oxField( '100' );
        $oAlarm->oxpricealarm__oxlang = new oxField( '1' );

        $this->assertEquals( 'zzz', $oEmail->sendPriceAlarmNotification( $aParams, $oAlarm ) );
    }



}

