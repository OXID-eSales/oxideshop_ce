<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxField;
use \oxPrice;
use \stdClass;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class EmailAzureTplTest extends \OxidTestCase
{
    protected $_oEmail = null;
    protected $_oUser = null;
    protected $_oShop = null;
    protected $_oArticle = null;
    protected $_sOrigTheme = null;

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('sTheme', 'azure');

        // reload smarty
        \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty(true);

        $this->_oEmail = oxNew("oxEmail");

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxorderarticles');

        //set default user
        $this->_oUser = oxNew("oxUser");
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
        $this->_oShop = oxNew("oxShop");
        $this->_oShop->load($this->getConfig()->getShopId());
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
        $this->_oArticle = oxNew("oxArticle");
        $this->_oArticle->setId('_testArticleId');
        $this->_oArticle->oxarticles__oxtitle = new oxField('testArticle', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxartnum = new oxField('123456789', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        //$this->_oArticle->oxarticles__oxamount = new oxField('12', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxshortdesc = new oxField('testArticleDescription', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindactive = new oxField('1', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);

        $this->_oArticle->save();

        oxDb::getDb()->execute(
            "Insert into oxorderarticles (`oxid`, `oxartid`, `oxamount`, `oxtitle`, `oxartnum`)
                             values ('_testOrderArtId', '_testArticleId' , '7' , 'testArticleTitle', '5')"
        );
        oxDb::getDb()->execute("Update oxarticles set `oxtitle_1`='testArticle_EN' where `oxid`='_testArticleId'");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        // reload smarty
        \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty(true);

        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(0);
        oxRegistry::getLang()->setBaseLanguage(0);
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxarticles');

        $this->cleanUpTable('oxremark', 'oxparentid');

        parent::tearDown();
    }

    /**
     * Test sending mail
     */
    public function testSendEmail()
    {
        $sTo = 'username@useremail.nl';
        $sSubject = 'testSubject';

        $sBody = 'testBody';
        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendEmail($sTo, $sSubject, $sBody);
        $this->assertTrue($blRet, 'Mail was not sent');

        // check mail fields
        $aFields['sRecipient'] = $sTo;
        $aFields['sBody'] = $sBody;
        $aFields['sSubject'] = $sSubject;
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
    }

    /**
     * Test sending ordering mail to user
     */
    public function testSendOrderEmailToUser()
    {
        $this->getConfig()->setConfigParam('blShowVATForDelivery', false);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);

        /** @var oxBasketItem|PHPUnit\Framework\MockObject\MockObject $oBasketItem */
        $oBasketItem = $this->getMock(
            'oxBasketItem',
            array('getRegularUnitPrice', 'getVatPercent', 'getAmount', 'getTitle', 'getProductId')
        );

        $oBasketItem->expects($this->any())->method('getRegularUnitPrice')->will($this->returnValue($oPrice));
        $oBasketItem->expects($this->any())->method('getVatPercent')->will($this->returnValue(19));
        $oBasketItem->expects($this->any())->method('getAmount')->will($this->returnValue(1));
        $oBasketItem->expects($this->any())->method('getTitle')->will($this->returnValue("testArticle"));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue("_testArticleId"));

        $oBasketItem->oxarticles__oxtitle = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();

        $oBasketItem->setPrice($oPrice);

        $aBasketContents[] = $oBasketItem;
        $aBasketArticles[] = $this->_oArticle;

        $oPriceTotal = $this->getMock('oxPrice');
        $oPriceTotal->expects($this->any())->method('getPrice')->will($this->returnValue(999));
        $oPriceTotal->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(999));

        /** @var oxBasket|PHPUnit\Framework\MockObject\MockObject $oBasket */
        $oBasket = $this->getMock(
            'oxBasket',
            array("getBasketArticles", "getContents", "getPrice", "getBruttoSum", "getNettoSum", "getProductVats")
        );

        $oBasket->expects($this->any())->method('getBasketArticles')->will($this->returnValue($aBasketArticles));
        $oBasket->expects($this->any())->method('getContents')->will($this->returnValue($aBasketContents));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue($oPriceTotal));
        $oBasket->expects($this->any())->method('getBruttoSum')->will($this->returnValue(888));
        $oBasket->expects($this->any())->method('getNettoSum')->will($this->returnValue(777));
        $oBasket->expects($this->any())->method('getProductVats')->will($this->returnValue(array('19' => 14.35, '5' => 0.38)));


        /** @var oxPrice|PHPUnit\Framework\MockObject\MockObject $oPrice1 */
        $oPrice1 = $this->getMock('oxPrice');
        $oPrice1->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice1->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(666));
        $oBasket->setCost('oxdelivery', $oPrice1);

        /** @var oxPrice|PHPUnit\Framework\MockObject\MockObject $oPrice2 */
        $oPrice2 = $this->getMock('oxPrice');
        $oPrice2->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice2->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(5));
        $oBasket->setCost('oxwrapping', $oPrice2);

        /** @var oxPrice|PHPUnit\Framework\MockObject\MockObject $oPrice3 */
        $oPrice3 = $this->getMock('oxPrice');
        $oPrice3->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice3->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(6));
        $oBasket->setCost('oxgiftcard', $oPrice3);

        /** @var oxPrice|PHPUnit\Framework\MockObject\MockObject $oPrice4 */
        $oPrice4 = $this->getMock('oxPrice');
        $oPrice4->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice4->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(true));
        $oPrice4->expects($this->any())->method('getNettoPrice')->will($this->returnValue(7));

        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
        $oPayment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        /** @var oxOrder|PHPUnit\Framework\MockObject\MockObject $oOrder */
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser", "getBasket", "getPayment"));
        $oOrder->expects($this->any())->method('getOrderUser')->will($this->returnValue($this->_oUser));
        $oOrder->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));
        $oOrder->expects($this->any())->method('getPayment')->will($this->returnValue($oPayment));

        $oOrder->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $oOrder->oxorder__oxbillcompany = new oxField('');
        $oOrder->oxorder__oxbillfname = new oxField('');
        $oOrder->oxorder__oxbilllname = new oxField('');
        $oOrder->oxorder__oxbilladdinfo = new oxField('');
        $oOrder->oxorder__oxbillstreet = new oxField('');
        $oOrder->oxorder__oxbillcity = new oxField('');
        $oOrder->oxorder__oxbillcountry = new oxField('');
        $oOrder->oxorder__oxbillcompany = new oxField('');
        $oOrder->oxorder__oxdeltype = new oxField("oxidstandard");

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages", 'getOrderFileList'));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('getOrderFileList')->will($this->returnValue(false));

        $blRet = $oEmail->sendOrderEmailToUser($oOrder);
        $this->assertTrue($blRet, 'Order email was not sent to customer');

        // check mail fields
        $aFields['sRecipient'] = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject'] = 'testOrderSubject (#987654321)';
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendOrderEmailToUser', $oEmail->getBody());
    }

    /**
     * Test sending ordering mail to shop owner
     */
    public function testSendOrderEmailToOwner()
    {
        $this->getConfig()->setConfigParam('blShowVATForDelivery', false);

        $oPrice = oxNew('oxPrice');
        $oPrice->setPrice(256);

        $oBasketItem = $this->getMock(
            'oxBasketItem',
            array('getRegularUnitPrice', 'getVatPercent', 'getAmount', 'getTitle', 'getProductId')
        );

        $oBasketItem->expects($this->any())->method('getRegularUnitPrice')->will($this->returnValue($oPrice));
        $oBasketItem->expects($this->any())->method('getVatPercent')->will($this->returnValue(19));
        $oBasketItem->expects($this->any())->method('getAmount')->will($this->returnValue(1));
        $oBasketItem->expects($this->any())->method('getTitle')->will($this->returnValue("testArticle"));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue("_testArticleId"));

        $oBasketItem->oxarticles__oxtitle = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();
        $oBasketItem->oxarticles__oxvarselect = new oxField();

        $oBasketItem->setPrice($oPrice);

        $aBasketContents[] = $oBasketItem;
        $aBasketArticles[] = $this->_oArticle;

        $oPriceTotal = $this->getMock('oxprice');
        $oPriceTotal->expects($this->any())->method('getPrice')->will($this->returnValue(999));
        $oPriceTotal->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(999));

        $oBasket = $this->getMock(
            'oxBasket',
            array("getBasketArticles", "getContents", "getPrice", "getBruttoSum", "getNettoSum", "getProductVats")
        );

        $oBasket->expects($this->any())->method('getBasketArticles')->will($this->returnValue($aBasketArticles));
        $oBasket->expects($this->any())->method('getContents')->will($this->returnValue($aBasketContents));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue($oPriceTotal));
        $oBasket->expects($this->any())->method('getBruttoSum')->will($this->returnValue(888));
        $oBasket->expects($this->any())->method('getNettoSum')->will($this->returnValue(777));
        $oBasket->expects($this->any())->method('getProductVats')->will($this->returnValue(array('19' => 14.35, '5' => 0.38)));


        $oPrice1 = $this->getMock('oxprice');
        $oPrice1->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice1->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(666));
        $oBasket->setCost('oxdelivery', $oPrice1);

        $oPrice2 = $this->getMock('oxprice');
        $oPrice2->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice2->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(5));
        $oBasket->setCost('oxwrapping', $oPrice2);

        $oPrice3 = $this->getMock('oxprice');
        $oPrice3->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice3->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(6));
        $oBasket->setCost('oxgiftcard', $oPrice3);

        $oPrice4 = $this->getMock('oxprice');
        $oPrice4->expects($this->any())->method('getPrice')->will($this->returnValue(256));
        $oPrice4->expects($this->any())->method('getBruttoPrice')->will($this->returnValue(true));
        $oPrice4->expects($this->any())->method('getNettoPrice')->will($this->returnValue(7));

        $oPayment = oxNew('oxPayment');
        $oPayment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser", "getBasket", "getPayment"));
        $oOrder->expects($this->any())->method('getOrderUser')->will($this->returnValue($this->_oUser));
        $oOrder->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));
        $oOrder->expects($this->any())->method('getPayment')->will($this->returnValue($oPayment));

        $oOrder->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $oOrder->oxorder__oxbillcompany = new oxField('');
        $oOrder->oxorder__oxbillfname = new oxField('');
        $oOrder->oxorder__oxbilllname = new oxField('');
        $oOrder->oxorder__oxbilladdinfo = new oxField('');
        $oOrder->oxorder__oxbillstreet = new oxField('');
        $oOrder->oxorder__oxbillcity = new oxField('');
        $oOrder->oxorder__oxbillcountry = new oxField('');
        $oOrder->oxorder__oxdeltype = new oxField("oxidstandard");

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));

        $blRet = $oEmail->sendOrderEmailToOwner($oOrder);
        $this->assertTrue($blRet, 'Order email was not sent to shop owner');

        // check mail fields
        $aFields['sRecipient'] = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = 'order';
        $aFields['sSubject'] = 'testOrderSubject (#987654321)';
        $aFields['sFrom'] = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sFromName'] = '';
        $aFields['sReplyTo'] = 'username@useremail.nl';
        $aFields['sReplyToName'] = 'testUserFName testUserLName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendOrderEMailToOwner', $oEmail->getBody());
    }

    /**
     * Test sending ordering mail to shop owner when shop language is different from admin language.
     * Shop language must be same as admin language.
     */
    public function testSendOrderEMailToOwnerWhenShopLangIsDifferentFromAdminLang()
    {
        oxRegistry::getLang()->setTplLanguage(1);
        oxRegistry::getLang()->setBaseLanguage(1);

        $oPayment = oxNew('oxPayment');
        $oPayment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $oBasket = oxNew('oxBasket');
        $oBasket->setCost('oxpayment', new oxPrice(0));
        $oBasket->setCost('oxdelivery', new oxPrice(6626));

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser", "getBasket", "getPayment"));
        $oOrder->expects($this->any())->method('getOrderUser')->will($this->returnValue($this->_oUser));
        $oOrder->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));
        $oOrder->expects($this->any())->method('getPayment')->will($this->returnValue($oPayment));

        $oOrder->oxorder__oxbillcompany = new oxField('');
        $oOrder->oxorder__oxbillfname = new oxField('');
        $oOrder->oxorder__oxbilllname = new oxField('');
        $oOrder->oxorder__oxbilladdinfo = new oxField('');
        $oOrder->oxorder__oxbillstreet = new oxField('');
        $oOrder->oxorder__oxbillcity = new oxField('');
        $oOrder->oxorder__oxbillcountry = new oxField('');
        $oOrder->oxorder__oxdeltype = new oxField("oxidstandard");

        $oShop_en = clone $this->_oShop;
        $oShop_en->oxshops__oxordersubject = new oxField('testOrderSubject_en', oxField::T_RAW);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_getShop", "_sendMail"));
        $oEmail->expects($this->at(0))->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->at(1))->method('_getShop')->with($this->equalTo(1))->will($this->returnValue($oShop_en));
        $oEmail->expects($this->at(2))->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->at(3))->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_sendMail')->will($this->returnValue(true));

        $blRet = $oEmail->sendOrderEmailToOwner($oOrder);

        $this->assertTrue($blRet, 'Order email was not sent to shop owner');

        // check mail fields
        $aFields['sRecipient'] = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = 'order';
        $aFields['sSubject'] = 'testOrderSubject_en (#)';
        $aFields['sFrom'] = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sFromName'] = '';
        $aFields['sReplyTo'] = 'username@useremail.nl';
        $aFields['sReplyToName'] = 'testUserFName testUserLName';

        $this->checkMailFields($aFields, $oEmail);

        //checking if mail body is in english
        $this->assertContains('The following products have been ordered in testShopName right now:', $oEmail->getBody());
    }

    /**
     * Test sending registration mail to user
     */
    public function testSendRegisterEMail()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));


        $blRet = $oEmail->sendRegisterEMail($this->_oUser);
        $this->assertTrue($blRet, 'Registration mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject'] = 'testUserRegistrationSubject';
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendRegisterEMail', $oEmail->getBody());
    }

    /**
     * Test sending forgot password to user
     */
    public function testSendForgotPwdEmail()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));

        $blRet = $oEmail->sendForgotPwdEmail('username@useremail.nl');
        $this->assertTrue($blRet, 'Forgot password email was not sent');

        // check mail fields
        $aFields['sRecipient'] = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject'] = 'testUserFogotPwdSubject';
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendForgotPwdEmail', $oEmail->getBody());
    }

    /**
     * Test sending forgot password to not existing user
     */
    public function testSendForgotPwdEmailToNotExistingUser()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->never())->method('_sendMail');
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->SendForgotPwdEmail('nosuchuser@useremail.nl');
        $this->assertFalse($blRet, 'Mail was sent to not existing user');
    }

    /**
     * Test sending contact info mail from user to shop owner
     */
    public function testSendContactMail()
    {
        $sSubject = 'testSubject';
        $sBody = 'testBodyMessage';
        $sUserMail = 'username@useremail.nl';
        $sShopOwnerMail = 'shopOwner@shopOwnerEmail.nl';

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendContactMail($sUserMail, $sSubject, $sBody);
        $this->assertTrue($blRet, 'Contact user mail was not sent to shop owner');

        // check mail fields
        $aFields['sRecipient'] = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = '';
        $aFields['sSubject'] = $sSubject;
        $aFields['sBody'] = $sBody;
        $aFields['sFrom'] = $sShopOwnerMail;
        $aFields['sFromName'] = '';
        $aFields['sReplyTo'] = $sUserMail;
        $aFields['sReplyToName'] = '';

        $this->checkMailFields($aFields, $oEmail);
    }

    /**
     * Test sending newsletter cofirmation mail to user
     */
    public function testSendNewsletterDBOptInMail()
    {
        $this->getSession()->setId('xsessx');

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages", "isSessionStarted"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('isSessionStarted')->will($this->returnValue(true));

        $blRet = $oEmail->sendNewsletterDbOptInMail($this->_oUser);
        $this->assertTrue($blRet, 'Newsletter confirmation mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject'] = 'Newsletter testShopName';
        $aFields['sFrom'] = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendNewsletterDBOptInMail', $oEmail->getBody());
    }

    /**
     * Test sending newsletter mail to user
     */
    public function testSendNewsletterMail()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oNewsletter = $this->getMock(\OxidEsales\Eshop\Application\Model\Newsletter::class, array("getHtmlText"));
        $oNewsletter->expects($this->once())->method("getHtmlText")->will($this->returnValue("testNewsletterHtmlText"));
        $oNewsletter->oxnewsletter__oxtitle = new oxField('testNewsletterTitle', oxField::T_RAW);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendNewsletterMail($oNewsletter, $this->_oUser);
        $this->assertTrue($blRet, 'Newsletter mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'testUserFName testUserLName';
        $aFields['sSubject'] = 'testNewsletterTitle';
        $aFields['sBody'] = 'testNewsletterHtmlText';
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
    }

    /**
     * Test sending suggest email
     */
    public function testSendSuggestMail()
    {
        $oParams = new stdClass();
        $oParams->rec_email = 'username@useremail.nl';
        $oParams->rec_name = 'testUserFName testUserLName';
        $oParams->send_subject = 'testSuggestSubject';
        $oParams->send_email = 'orderemail@orderemail.nl';
        $oParams->send_name = 'testShopName';


        $oProduct = oxNew('oxArticle');
        $oProduct->load('_testArticleId');

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));

        $blRet = $oEmail->sendSuggestMail($oParams, $oProduct);
        $this->assertTrue($blRet, 'Suggest mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = $oParams->rec_email;
        $aFields['sRecipientName'] = $oParams->rec_name;
        $aFields['sSubject'] = $oParams->send_subject;
        $aFields['sFrom'] = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sFromName'] = '';
        $aFields['sReplyTo'] = $oParams->send_email;
        $aFields['sReplyToName'] = $oParams->send_name;

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendSuggestMail', $oEmail->getBody());
    }

    /**
     * Test sending order
     */
    public function testSendSendedNowMail()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('blAdmin', true);
        $myConfig->setAdminMode(true);

        $oOrderArticle = oxNew("oxorderarticle");
        $oOrderArticle->load('_testOrderArtId');
        $aOrderArticles[] = $oOrderArticle;

        $oArticles = oxNew('oxList');
        $oArticles->assign($aOrderArticles);

        $oPayment = oxNew('oxPayment');
        $oPayment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser", "getOrderArticles", "getPayment"));
        $oOrder->expects($this->any())->method('getOrderUser')->will($this->returnValue($this->_oUser));
        $oOrder->expects($this->any())->method('getOrderArticles')->will($this->returnValue($oArticles));
        $oOrder->expects($this->any())->method('getPayment')->will($this->returnValue($oPayment));

        $oOrder->oxorder__oxbillcompany = new oxField('');
        $oOrder->oxorder__oxbillfname = new oxField('');
        $oOrder->oxorder__oxbilllname = new oxField('');
        $oOrder->oxorder__oxbilladdinfo = new oxField('');
        $oOrder->oxorder__oxbillstreet = new oxField('');
        $oOrder->oxorder__oxbillcity = new oxField('');
        $oOrder->oxorder__oxbillcountry = new oxField('');
        $oOrder->oxorder__oxdeltype = new oxField("oxidstandard");
        $oOrder->oxorder__oxordernr = new oxField('123456789', oxField::T_RAW);
        $oOrder->oxorder__oxbillemail = new oxField('testOrderEmail@testuser.eu', oxField::T_RAW);
        $oOrder->oxorder__oxbillfname = new oxField('testOrderBillFName', oxField::T_RAW);
        $oOrder->oxorder__oxbilllname = new oxField('testOrderBillLName', oxField::T_RAW);
        $oOrder->oxorder__oxuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages", 'getOrderFileList'));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('getOrderFileList')->will($this->returnValue(false));

        $blRet = $oEmail->sendSendedNowMail($oOrder);
        $this->assertTrue($blRet, 'Suggest mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = 'testOrderEmail@testuser.eu';
        $aFields['sRecipientName'] = 'testOrderBillFName testOrderBillLName';
        $aFields['sSubject'] = 'testSendedNowSubject';
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendNowMailSent', $oEmail->getBody());
    }

    /**
     * Test sending download links
     */
    public function testSendDownloadLinksMail()
    {
        $myConfig = $this->getConfig();
        $myConfig->setConfigParam('blAdmin', true);
        $myConfig->setAdminMode(true);

        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getId"));
        $oOrder->expects($this->any())->method('getId')->will($this->returnValue('_testOrder'));

        $oOrder->oxorder__oxordernr = new oxField('123456789', oxField::T_RAW);
        $oOrder->oxorder__oxpaid = new oxField(true);
        $oOrder->oxorder__oxbillemail = new oxField('testOrderEmail@testuser.eu', oxField::T_RAW);
        $oOrder->oxorder__oxbillfname = new oxField('testOrderBillFName', oxField::T_RAW);
        $oOrder->oxorder__oxbilllname = new oxField('testOrderBillLName', oxField::T_RAW);
        $oOrder->oxorder__oxuserid = new oxField($this->_oUser->getId(), oxField::T_RAW);

        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, array("getId", "getFileSize"));
        $oOrderFile->expects($this->any())->method('getId')->will($this->returnValue('_testOrder'));
        $oOrderFile->expects($this->any())->method('getFileSize')->will($this->returnValue('5000'));
        $oOrderFile->oxorderfiles__oxfilename = new oxField('testFileName', oxField::T_RAW);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages", 'getOrderFileList'));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('getOrderFileList')->will($this->returnValue([$oOrderFile]));

        $blRet = $oEmail->sendDownloadLinksMail($oOrder, 'testDownloadLinksSubject');
        $this->assertTrue($blRet, 'SendDownloadLinks mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = 'testOrderEmail@testuser.eu';
        $aFields['sRecipientName'] = 'testOrderBillFName testOrderBillLName';
        $aFields['sSubject'] = 'testDownloadLinksSubject';
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sFromName'] = 'testShopName';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';
        $aFields['sReplyToName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendDownloadLinksMail', $oEmail->getBody());
    }

    /**
     * Test sending backup mail to shop owner
     */
    public function testSendBackupMail()
    {
        $aAttFiles = array();
        $sAttPath = null;
        $sEmailAddress = 'username@useremail.nl';
        $sSubject = 'testBackupMailSubject';
        $sMessage = 'testBackupMailMessage';
        $aStatus = array();
        $aError = array();

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendBackupMail($aAttFiles, $sAttPath, $sEmailAddress, $sSubject, $sMessage, $aStatus, $aError);
        $this->assertTrue($blRet, 'Backup mail was not sent to shop owner');

        // check mail fields
        $aFields['sRecipient'] = 'shopInfoEmail@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = '';
        $aFields['sSubject'] = $sSubject;
        $aFields['sBody'] = $sMessage;
        $aFields['sFrom'] = $sEmailAddress;
        $aFields['sFromName'] = '';
        $aFields['sReplyTo'] = $sEmailAddress;
        $aFields['sReplyToName'] = '';

        $this->checkMailFields($aFields, $oEmail);
    }

    /**
     * Test sends reminder email to shop owner
     */
    public function testSendStockReminder()
    {
        //set params for stock reminder
        $this->_oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindamount = new oxField('9', oxField::T_RAW);
        $this->_oArticle->save();

        $oBasketItem = $this->getMock(\OxidEsales\Eshop\Application\Model\BasketItem::class, array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));

        $blRet = $oEmail->sendStockReminder($aBasketContents);
        $this->assertTrue($blRet, 'Stock remind mail was not sent');

        // check mail fields
        $aFields['sRecipient'] = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sRecipientName'] = 'testShopName';
        $aFields['sSubject'] = oxRegistry::getLang()->translateString('STOCK_LOW', 0);
        $aFields['sFrom'] = 'shopOwner@shopOwnerEmail.nl';
        $aFields['sFromName'] = 'testShopName';

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendStockReminder', $oEmail->getBody());
    }

    /**
     * Test sending whishlist mail to user
     */
    public function testSendWishlistMail()
    {
        $oParams = new stdClass();

        $oParams->rec_email = 'username@useremail.nl';
        $oParams->rec_name = 'testUserFName testUserLName';
        $oParams->send_subject = 'testSuggestSubject';
        $oParams->send_email = 'orderemail@orderemail.nl';
        $oParams->send_name = 'testShopName';
        $oParams->send_id = '123456789';

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));

        $blRet = $oEmail->sendWishlistMail($oParams);
        $this->assertTrue($blRet, 'Whishlist mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = $oParams->rec_email;
        $aFields['sRecipientName'] = $oParams->rec_name;
        $aFields['sSubject'] = $oParams->send_subject;
        $aFields['sFrom'] = $oParams->send_email;
        $aFields['sFromName'] = $oParams->send_name;
        $aFields['sReplyTo'] = $oParams->send_email;
        $aFields['sReplyToName'] = $oParams->send_name;

        $this->checkMailFields($aFields, $oEmail);
        $this->checkMailBody('testSendWishlistMail', $oEmail->getBody());
    }


    /**
     * Test sending a notification to the shop owner that pricealarm was subscribed
     */
    public function testSendPriceAlarmNotification()
    {
        $params['email'] = 'username@useremail.nl';
        $params['aid'] = '_testArticleId';

        $alarm = oxNew("oxpricealarm");
        $alarm->oxpricealarm__oxprice = new oxField('123', oxField::T_RAW);

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $email->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));

        $blRet = $email->sendPriceAlarmNotification($params, $alarm);
        $this->assertTrue($blRet, 'Price alarm mail was not sent to user');

        // check mail fields
        $fields['sRecipient'] = 'orderemail@orderemail.nl';
        $fields['sRecipientName'] = 'testShopName';
        $fields['sSubject'] = oxRegistry::getLang()->translateString('PRICE_ALERT_FOR_PRODUCT', 0) . " testArticle";
        $fields['sFrom'] = 'username@useremail.nl';
        $fields['sReplyTo'] = 'username@useremail.nl';

        $this->checkMailFields($fields, $email);
        $this->checkMailBody('testSendPriceAlarmNotification', $email->getBody());
    }

    /**
     * Test sending a notification to the customer that price alarm was subscribed
     */
    public function testSendPriceAlarmToCustomer()
    {
        $config = $this->getConfig();
        $config->setConfigParam('blAdmin', true);
        $config->setAdminMode(true);
        $oAlarm = oxNew("oxpricealarm");
        $oAlarm->oxpricealarm__oxprice = new oxField('123', oxField::T_RAW);
        $oAlarm->oxpricealarm__oxcurrency = new oxField('EUR');

        oxTestModules::addModuleObject("oxShop", $this->_oShop);

        $oSmarty = $this->getMock('Smarty', array("fetch"));
        $oSmarty->expects($this->once())->method('fetch')->will($this->returnValue("body"));

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop", "_getUseInlineImages", "_getSmarty"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));
        $oEmail->expects($this->any())->method('_getUseInlineImages')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getSmarty')->will($this->returnValue($oSmarty));

        $blRet = $oEmail->sendPriceAlarmToCustomer('username@useremail.nl', $oAlarm);
        $config->setConfigParam('blAdmin', false);
        $config->setAdminMode(false);
        $this->assertTrue($blRet, 'Price alarm mail was not sent to user');

        // check mail fields
        $aFields['sRecipient'] = 'username@useremail.nl';
        $aFields['sRecipientName'] = 'username@useremail.nl';
        $aFields['sSubject'] = $this->_oShop->oxshops__oxname->value;
        $aFields['sFrom'] = 'orderemail@orderemail.nl';
        $aFields['sReplyTo'] = 'orderemail@orderemail.nl';

        $this->checkMailFields($aFields, $oEmail);
    }

    /**
     * Test sending a notification to the shop owner that pricealarm was subscribed in other language
     */
    public function testSendPriceAlarmNotificationInEN()
    {
        $aParams['aid'] = $this->_oArticle->getId();
        $aParams['email'] = 'user@oxid-esales.com';

        /** @var oxShop|PHPUnit\Framework\MockObject\MockObject $oShop */
        $oShop = $this->getMock(\OxidEsales\Eshop\Application\Model\OxidEsalesshopApplicationControllerAdminShopController::class, array('getImageUrl'));
        $oShop->expects($this->any())->method('getImageUrl');
        //$oShop->loadInLang( 1, $this->getConfig()->getBaseShopId() );
        $oShop->oxshops__oxorderemail = new oxField('order@oxid-esales.com');
        $oShop->oxshops__oxname = new oxField('test shop');

        $oEmail = $this->getMock(
            'oxemail',
            array(
                '_clearMailer',
                '_getShop',
                '_setMailParams',
                'setRecipient',
                'setSubject',
                'setBody',
                'setFrom',
                'setReplyTo',
                'send'
            )
        );

        $oEmail->expects($this->once())->method('_clearMailer');
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($oShop));
        $oEmail->expects($this->once())->method('_setMailParams')->with($this->equalTo($oShop));
        $oEmail->expects($this->once())->method('setRecipient')->with($this->equalTo($oShop->oxshops__oxorderemail->value), $this->equalTo($oShop->oxshops__oxname->value));
        $oEmail->expects($this->once())->method('setSubject')->with($this->equalTo("Price alert for product testArticle_EN"));
        $oEmail->expects($this->once())->method('setBody');
        $oEmail->expects($this->once())->method('setFrom')->with($this->equalto($aParams['email']), $this->equalto(''));
        $oEmail->expects($this->once())->method('setReplyTo')->with($this->equalto($aParams['email']), $this->equalto(''));
        $oEmail->expects($this->once())->method('send')->will($this->returnValue('zzz'));

        $oAlarm = new stdClass();
        $oAlarm->oxpricealarm__oxprice = new oxField('100');
        $oAlarm->oxpricealarm__oxlang = new oxField('1');

        $this->assertEquals('zzz', $oEmail->sendPriceAlarmNotification($aParams, $oAlarm));
    }

    /**
     * @param array   $aFields
     * @param oxEmail $oEmail
     */
    protected function checkMailFields($aFields = array(), $oEmail = null)
    {
        if (!$oEmail) {
            $oEmail = $this->_oEmail;
        }

        if ($aFields['sRecipient']) {
            $aRecipient = $oEmail->getRecipient();
            $this->assertEquals(strtolower($aFields['sRecipient']), strtolower($aRecipient[0][0]), 'Incorect mail recipient');
        }

        if ($aFields['sRecipientName']) {
            $aRecipient = $oEmail->getRecipient();
            $this->assertEquals($aFields['sRecipientName'], $aRecipient[0][1], 'Incorect mail recipient name');
        }

        if ($aFields['sSubject']) {
            $this->assertEquals($aFields['sSubject'], $oEmail->getSubject(), 'Incorect mail subject');
        }

        if ($aFields['sFrom']) {
            $sFrom = $oEmail->getFrom();
            $this->assertEquals($aFields['sFrom'], $sFrom, 'Incorect mail from address');
        }

        if ($aFields['sFromName']) {
            $sFromName = $oEmail->getFromName();
            $this->assertEquals($aFields['sFromName'], $sFromName, 'Incorect mail from name');
        }

        if ($aFields['sReplyTo']) {
            $aReplyTo = $oEmail->getReplyTo();
            $this->assertEquals($aFields['sReplyTo'], $aReplyTo[0][0], 'Incorect mail reply to address');
        }

        if ($aFields['sReplyToName']) {
            $aReplyTo = $oEmail->getReplyTo();
            $this->assertEquals($aFields['sReplyToName'], $aReplyTo[0][1], 'Incorect mail reply to name');
        }

        if ($aFields['sBody']) {
            $this->assertEquals($aFields['sBody'], $oEmail->getBody(), 'Incorect mail body');
        }
    }

    /**
     * @param string $sFuncName
     * @param string $sBody
     * @param bool   $blWriteToTestFile
     */
    protected function checkMailBody($sFuncName, $sBody, $blWriteToTestFile = false)
    {
        // uncomment line to generate template for checking mail body
        // file_put_contents (__DIR__ ."/../TestData/email_templates/azure/$sFuncName.html", $sBody);

        $sPath = __DIR__ .'/../testData/email_templates/azure/' . $sFuncName . '.html';
        if (!($sExpectedBody = file_get_contents($sPath))) {
            $this->fail("Template '$sPath' was not found!");
        }

        // remove <img src="cid:1192193298470f6d12383b8" ... from body, because it is everytime different
        $sExpectedBody = preg_replace("/cid:[0-9a-zA-Z]+\"/", "cid:\"", $sExpectedBody);

        // replacing test shop id to good one
        $sExpectedBody = preg_replace("/shp\=testShopId/", "shp=" . $this->_oShop->getId(), $sExpectedBody);

        $sBody = preg_replace("/cid:[0-9a-zA-Z]+\"/", "cid:\"", $sBody);

        // A. very special case for user password reminder
        if ($sFuncName == 'testSendForgotPwdEmail') {
            $sExpectedBody = preg_replace("/uid=[0-9a-zA-Z]+\&amp;/", "", $sExpectedBody);
            $sBody = preg_replace("/uid=[0-9a-zA-Z]+\&amp;/", "", $sBody);
        }

        $sExpectedBody = preg_replace("/\s+/", " ", $sExpectedBody);
        $sBody = preg_replace("/\s+/", " ", $sBody);

        $sExpectedBody = str_replace("> <", "><", $sExpectedBody);
        $sBody = str_replace("> <", "><", $sBody);

        $sExpectedShopUrl = "http://eshop/";
        $sShopUrl = $this->getConfig()->getConfigParam('sShopURL');

        //remove shop url base path from links
        $sBody = str_replace($sShopUrl, $sExpectedShopUrl, $sBody);

        if ($blWriteToTestFile) {
            file_put_contents(__DIR__ .'/../testData/email_templates/azure/' . $sFuncName . '_test_expecting.html', $sExpectedBody);
            file_put_contents(__DIR__ .'/../testData/email_templates/azure/' . $sFuncName . '_test_result.html', $sBody);
        }

        $this->assertEquals(strtolower(trim($sExpectedBody)), strtolower(trim($sBody)), "Incorect mail body");
    }
}
