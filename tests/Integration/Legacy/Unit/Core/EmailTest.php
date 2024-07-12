<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use oxField;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\Price;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Core\UtilsObject;
use oxPrice;
use oxRegistry;
use oxTestModules;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophet;
use Psr\Container\ContainerInterface;

final class EmailTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    private $email;

    private $user;

    private $shop;

    private $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('sTheme', ACTIVE_THEME);

        $this->email = oxNew("oxEmail");

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxorderarticles');

        //set default user
        $this->user = oxNew("oxuser");
        $this->user->setId('_testUserId');

        $this->user->oxuser__oxactive = new oxField('1', oxField::T_RAW);
        $this->user->oxuser__oxusername = new oxField('username@useremail.nl', oxField::T_RAW);
        $this->user->oxuser__oxcustnr = new oxField('998', oxField::T_RAW);
        $this->user->oxuser__oxfname = new oxField('testUserFName', oxField::T_RAW);
        $this->user->oxuser__oxlname = new oxField('testUserLName', oxField::T_RAW);
        $this->user->oxuser__oxpassword = new oxField('ox_BBpaRCslUU8u', oxField::T_RAW); //pass = admin
        $this->user->oxuser__oxregister = new oxField(date("Y-m-d H:i:s"), oxField::T_RAW);
        $this->user->save();

        // set shop params for testing
        $this->shop = oxNew("oxshop");
        $this->shop->load($this->getConfig()->getShopId());

        $this->shop->oxshops__oxorderemail = new oxField('orderemail@orderemail.nl', oxField::T_RAW);
        $this->shop->oxshops__oxordersubject = new oxField('testOrderSubject', oxField::T_RAW);
        $this->shop->oxshops__oxsendednowsubject = new oxField('testSendedNowSubject', oxField::T_RAW);
        $this->shop->oxshops__oxname = new oxField('testShopName', oxField::T_RAW);
        $this->shop->oxshops__oxowneremail = new oxField('shopOwner@shopOwnerEmail.nl', oxField::T_RAW);
        $this->shop->oxshops__oxinfoemail = new oxField('shopInfoEmail@shopOwnerEmail.nl', oxField::T_RAW);
        //$this->shop->oxshops__oxsmtp = new oxField('localhost', oxField::T_RAW);
        $this->shop->oxshops__oxsmtp = new oxField('127.0.0.1', oxField::T_RAW);
        $this->shop->oxshops__oxsmtpuser = new oxField('testSmtpUser', oxField::T_RAW);
        $this->shop->oxshops__oxsmtppwd = new oxField('testSmtpPassword', oxField::T_RAW);
        $this->shop->oxshops__oxregistersubject = new oxField('testUserRegistrationSubject', oxField::T_RAW);
        $this->shop->oxshops__oxforgotpwdsubject = new oxField('testUserFogotPwdSubject', oxField::T_RAW);

        // insert test article
        $this->article = oxNew("oxArticle");
        $this->article->setId('_testArticleId');

        $this->article->oxarticles__oxtitle = new oxField('testArticle', oxField::T_RAW);
        $this->article->oxarticles__oxtitle_1 = new oxField('testArticle_EN', oxField::T_RAW);
        $this->article->oxarticles__oxartnum = new oxField('123456789', oxField::T_RAW);
        $this->article->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->article->oxarticles__oxshortdesc = new oxField('testArticleDescription', oxField::T_RAW);
        $this->article->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
        $this->article->oxarticles__oxremindactive = new oxField('1', oxField::T_RAW);
        $this->article->oxarticles__oxstock = new oxField('9', oxField::T_RAW);

        $this->article->save();

        oxDb::getDb()->Execute(
            "Insert into oxorderarticles (`oxid`, `oxartid`, `oxamount`, `oxtitle`, `oxartnum`)
                             values ('_testOrderArtId', '_testArticleId' , '7' , 'testArticleTitle', '5')"
        );
    }

    protected function tearDown(): void
    {
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(0);
        oxRegistry::getLang()->setBaseLanguage(0);
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxorderarticles');
        $this->cleanUpTable('oxarticles');

        $this->cleanUpTable('oxremark', 'oxparentid');

        parent::tearDown();
    }

    /*-------------------------------------------------------------*/
    /**
     * oxEmail::sendRegisterConfirmEmail() test case
     */
    public function testSendRegisterConfirmEmail()
    {
        $user = oxNew('oxuser');

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendRegisterEmail"]);
        $email->expects($this->once())->method('sendRegisterEmail')->with($user, null);

        $email->sendRegisterConfirmEmail($user);

        $viewData = $email->getViewData();
        $this->assertEquals($viewData["contentident"], "oxregisteraltemail");
        $this->assertEquals($viewData["contentplainident"], "oxregisterplainaltemail");
    }

    public function testIncludeImagesErrorTestCase(): void
    {
        $config = $this->getConfig();

        $article = oxNew('oxArticle');
        $article->load('1351');

        $imageUrl = $article->getThumbnailUrl();
        $title = $article->oxarticles__oxtitle->value;
        $imageDirectory = $config->getImageDir();

        $imageGenerator = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, ['getImagePath']);
        $imageGenerator->method('getImagePath')->willReturn($config->getPictureDir(false) . 'generated/product/thumb/185_150_75/nopic.jpg');
        oxTestModules::addModuleObject('oxDynImgGenerator', $imageGenerator);

        $body = '<img src="' . $imageDirectory . 'stars.jpg" border="0" hspace="0" vspace="0" alt="stars" align="texttop">';
        $body .= '<img src="' . $config->getImageUrl() . 'logo.png" border="0" hspace="0" vspace="0" alt="logo" align="texttop">';
        $body .= '<img src="' . $imageUrl . '" border="0" hspace="0" vspace="0" alt="' . $title . '" align="texttop">';

        $generatedEmailBody = '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="stars" align="texttop">';
        $generatedEmailBody .= '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="logo" align="texttop">';
        $generatedEmailBody .= '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="' . $title . '" align="texttop">';

        $utilsObjectMock = $this->getMockBuilder(UtilsObject::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateUId'])
            ->getMock();
        $utilsObjectMock
            ->method('generateUId')
            ->willReturn('xxx');

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $email */
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['getBody', 'addEmbeddedImage', 'setBody', 'getUtilsObjectInstance']);
        $email->method('getUtilsObjectInstance')->willReturn($utilsObjectMock);
        $email->method('addEmbeddedImage')->willReturn(true);
        $email->expects($this->once())->method('getBody')->willReturn($body);
        $email->expects($this->once())->method('setBody')->with($generatedEmailBody);

        $email->includeImages(
            $imageDirectory,
            $config->getImageUrl(false),
            $config->getPictureUrl(null),
            $imageDirectory,
            $config->getPictureDir(false)
        );
    }

    public function testSendMailBySmtp()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail"]);
        $email->expects($this->once())->method('sendMail')->willReturn(true);

        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $email->setHost("localhost");
        $email->setMailer("smtp");

        $this->assertTrue($email->send());
        $this->assertSame('smtp', $email->getMailer());
    }

    public function testSendMailByPhpMailFunction()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail"]);
        $email->expects($this->once())->method('sendMail')->willReturn(true);

        $email->setMailer('mail');
        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $this->assertTrue($email->send());
        $this->assertSame('mail', $email->getMailer());
    }

    public function testSendMailByPhpMailWhenSmtpFails()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['sendMail', 'sendMailErrorMsg']);
        $email->expects($this->atLeast(2))->method('sendMail')->willReturn(false);
        $email->expects($this->atLeastOnce())->method('sendMailErrorMsg');

        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $email->setHost("localhost");
        $email->setMailer("smtp");

        $this->assertFalse($email->send());
        $this->assertSame('mail', $email->getMailer());
    }

    public function testSendMailErrorMsgWhenMailingFails()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "sendMailErrorMsg", "getMailer"]);
        $email->expects($this->exactly(2))->method('sendMail')->willReturn(false);
        $email->expects($this->exactly(2))->method('sendMailErrorMsg');
        $email->expects($this->once())->method('getMailer')->willReturn("smtp");

        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $email->send();
    }

    public function testSetSmtp()
    {
        // just forcing to connect to webserver..
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['isValidSmtpHost']);
        $email->expects($this->once())->method('isValidSmtpHost')
            ->with('127.0.0.1')
            ->willReturn(true);

        $email->setSmtp($this->shop);
        $this->assertSame('smtp', $email->getMailer());
        $this->assertSame('127.0.0.1', $email->Host);
        $this->assertSame('testSmtpUser', $email->Username);
        $this->assertSame('testSmtpPassword', $email->Password);
    }

    public function testSetSmtpWithNoSmtpValues()
    {
        $email = oxNew('oxEmail');

        $this->shop->oxshops__oxsmtp = new oxField(null, oxField::T_RAW);
        $email->setSmtp($this->shop);
        $this->assertSame('mail', $email->getMailer());
    }

    public function testSendOrderEMailToOwnerAddsHistoryRecord()
    {
        $myDb = oxDb::getDb();

        $payment = oxNew('oxPayment');
        $payment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $basket = oxNew('oxBasket');
        $basket->setCost('oxpayment', new oxPrice(0));
        $basket->setCost('oxdelivery', new oxPrice(6626));

        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, ["getOrderUser", "getBasket", "getPayment"]);
        $order->method('getOrderUser')->willReturn($this->user);
        $order->method('getBasket')->willReturn($basket);
        $order->method('getPayment')->willReturn($payment);

        $order->oxorder__oxbillcompany = new oxField('');
        $order->oxorder__oxbillfname = new oxField('');
        $order->oxorder__oxbilllname = new oxField('');
        $order->oxorder__oxbilladdinfo = new oxField('');
        $order->oxorder__oxbillstreet = new oxField('');
        $order->oxorder__oxbillcity = new oxField('');
        $order->oxorder__oxbillcountry = new oxField('');
        $order->oxorder__oxdeltype = new oxField("oxidstandard");

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop"]);
        $email->expects($this->once())->method('sendMail')->willReturn(true);
        $email->method('getShop')->willReturn($this->shop);

        $blRet = $email->sendOrderEmailToOwner($order);
        $this->assertTrue($blRet, 'Order email was not sent to shop owner');

        $this->assertEquals($email->getAltBody(), $myDb->getOne('SELECT oxtext FROM oxremark where oxparentid=' . $myDb->quote($this->user->getId())));
    }

    public function testSendForgotPwdEmailToNotExistingUser()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop"]);
        $email->expects($this->never())->method('sendMail');
        $email->method('getShop')->willReturn($this->shop);

        $blRet = $email->SendForgotPwdEmail('nosuchuser@useremail.nl');
        $this->assertFalse($blRet, 'Mail was sent to not existing user');
    }

    public function testSendForgotPwdEmailSendingFailed()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["send", "getShop"]);
        $email->method('send')->willReturn(false);
        $email->method('getShop')->willReturn($this->shop);

        $blRet = $email->SendForgotPwdEmail('username@useremail.nl');
        $this->assertSame(-1, $blRet);
    }

    public function testSendEmailToMultipleUsers()
    {
        $aTo = ['username@useremail.nl', 'username2@useremail.nl'];
        $sSubject = 'testSubject';
        $sBody = 'testBody';

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop"]);
        $email->expects($this->once())->method('sendMail')->willReturn(true);
        $email->expects($this->once())->method('getShop')->willReturn($this->shop);

        $blRet = $email->sendEmail($aTo, $sSubject, $sBody);
        $this->assertTrue($blRet, 'Mail was not sent');

        $aRecipients = $email->getRecipient();
        $this->assertCount(2, $aRecipients);
        $this->assertSame('username@useremail.nl', $aRecipients[0][0]);
        $this->assertSame('username2@useremail.nl', $aRecipients[1][0]);
    }

    /*
     * #1276: If product is "If out out stock, offline" and remaining stock is ordered, "Shp offline" error is shown in Order step 5
     */
    public function testSendStockReminderIfStockFlag2()
    {
        //set params for stock reminder
        $this->article->oxarticles__oxstock = new oxField('0', oxField::T_RAW);
        $this->article->oxarticles__oxstockflag = new oxField('2', oxField::T_RAW);
        $this->article->oxarticles__oxremindamount = new oxField('0', oxField::T_RAW);
        $this->article->save();

        $basketItem = $this->getMock(BasketItem::class, ['getArticle', 'getProductId']);
        $basketItem->method('getArticle')->willReturn($this->article);
        $basketItem->method('getProductId')->willReturn('_testArticleId');

        $aBasketContents[] = $basketItem;

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop"]);
        $email->expects($this->once())->method('sendMail')->willReturn(true);
        $email->method('getShop')->willReturn($this->shop);

        $blRet = $email->sendStockReminder($aBasketContents);
        $this->assertTrue($blRet, 'Stock remind mail was not sent');
    }

    public function testSendStockReminderWhenStockAmountIsGreaterThanRemindAmount()
    {
        //set params for stock reminder
        $this->article->oxarticles__oxstock = new oxField('10', oxField::T_RAW);
        $this->article->oxarticles__oxremindamount = new oxField('9', oxField::T_RAW);
        $this->article->save();

        $basketItem = $this->getMock(BasketItem::class, ['getArticle', 'getProductId']);
        $basketItem->method('getArticle')->willReturn($this->article);
        $basketItem->method('getProductId')->willReturn('testArticleId');

        $aBasketContents[] = $basketItem;

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop"]);
        $email->expects($this->never())->method('sendMail')->willReturn(true);
        $email->method('getShop')->willReturn($this->shop);

        $blRet = $email->sendStockReminder($aBasketContents);
        $this->assertFalse($blRet, 'No need to send stock remind mail');
    }

    public function testSendStockReminderWhenRemindIsOff()
    {
        //set params for stock reminder
        $this->article->oxarticles__oxremindactive = new oxField('0', oxField::T_RAW);
        $this->article->oxarticles__oxstock = new oxField('9', oxField::T_RAW);
        $this->article->oxarticles__oxremindamount = new oxField('10', oxField::T_RAW);
        $this->article->save();

        $basketItem = $this->getMock(BasketItem::class, ['getArticle', 'getProductId']);
        $basketItem->method('getArticle')->willReturn($this->article);
        $basketItem->method('getProductId')->willReturn('testArticleId');

        $aBasketContents[] = $basketItem;

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getShop"]);
        $email->expects($this->never())->method('sendMail')->willReturn(true);
        $email->method('getShop')->willReturn($this->shop);

        $blRet = $email->sendStockReminder($aBasketContents);
        $this->assertFalse($blRet, 'No need to send stock remind mail');
    }

    public function testIncludeImages()
    {
        $myConfig = $this->getConfig();
        $sImageDir = __DIR__ . "/../testData/misc" . DIRECTORY_SEPARATOR;

        $email = oxNew('oxEmail');
        $email->setBody(sprintf("<img src='%s/test.png'> --- <img src='%s/test.jpg'>", $sImageDir, $sImageDir));

        $email->includeImages(
            $sImageDir,
            $sImageDir,
            $myConfig->getPictureUrl(null),
            $sImageDir,
            $myConfig->getPictureDir(false)
        );

        $aAttachments = $email->getAttachments();
        $this->assertSame('test.png', $aAttachments[0][1]);
        $this->assertSame('test.jpg', $aAttachments[1][1]);
    }

    public function testSetGetSubject()
    {
        $email = oxNew('oxEmail');
        $email->setSubject('testSubject');
        $this->assertSame('testSubject', $email->getSubject());
    }

    public function testSetGetBody()
    {
        $email = oxNew('oxEmail');
        $email->setBody('testBody');
        $this->assertSame('testBody', $email->getBody());
    }

    public function testClearSidFromBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $email = oxNew('oxEmail');

        $email->setBody('testBody index.php?bonusid=111&sid=123456789 blabla', true);
        $this->assertSame('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());

        $email->setBody('testBody index.php?bonusid=111&force_sid=123456789 blabla', true);
        $this->assertSame('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());

        $email->setBody('testBody index.php?bonusid=111&admin_sid=123456789 blabla', true);
        $this->assertSame('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());

        $email->setBody('testBody index.php?bonusid=111&force_admin_sid=123456789 blabla', true);
        $this->assertSame('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());
    }

    public function testSetGetAltBody()
    {
        $email = oxNew('oxEmail');

        $email->setAltBody('testAltBody');
        $this->assertSame('testAltBody', $email->getAltBody());
    }

    public function testClearSidFromAltBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $this->email->setAltBody('testAltBody index.php?bonusid=111&sid=123456789 blabla', true);
        $this->assertSame('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());

        $this->email->setAltBody('testAltBody index.php?bonusid=111&force_sid=123456789 blabla', true);
        $this->assertSame('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());

        $this->email->setAltBody('testAltBody index.php?bonusid=111&admin_sid=123456789 blabla', true);
        $this->assertSame('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());

        $this->email->setAltBody('testAltBody index.php?bonusid=111&force_admin_sid=123456789 blabla', true);
        $this->assertSame('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());
    }

    public function testClearHtmlEntitiesFromAltBody()
    {
        $this->email->setAltBody('testAltBody &amp; &quot; &#039; &lt; &gt;');
        $this->assertSame('testAltBody & " \' < >', $this->email->getAltBody());
    }

    public function testSetGetRecipient()
    {
        $aUser[0][0] = 'testuser@testuser.com';
        $aUser[0][1] = 'testUserName';

        $this->email->setRecipient($aUser[0][0], $aUser[0][1]);
        $this->assertSame($aUser, $this->email->getRecipient());
    }

    public function testSetRecipient_emptyEmail()
    {
        $this->email->setRecipient("", "");
        $this->assertSame([], $this->email->getRecipient());
    }

    public function testSetRecipient_emptyName()
    {
        $this->email->setRecipient("test@test.lt", "");
        $this->assertSame([["test@test.lt", ""]], $this->email->getRecipient());
    }

    public function testSetGetReplyTo()
    {
        $aUser[0][0] = 'testuser@testuser.com';
        $aUser[0][1] = 'testUserName';

        $this->email->setReplyTo($aUser[0][0], $aUser[0][1]);
        $this->assertSame($aUser, $this->email->getReplyTo());
    }

    public function testSetReplyToWithNoParams()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["getShop"]);
        $email->method('getShop')->willReturn($this->shop);

        $email->setReplyTo();
        $aReplyTo = $email->getReplyTo();

        $this->assertEquals($this->shop->oxshops__oxorderemail->value, $aReplyTo[0][0]);
    }

    public function testSetGetFrom()
    {
        $this->email->setFrom('testuser@testuser.com', 'testUserName');
        $this->assertSame('testuser@testuser.com', $this->email->getFrom());
        $this->assertSame('testUserName', $this->email->getFromName());
    }

    public function testSetCharSet()
    {
        $this->email->setCharSet('testCharset');
        $this->assertSame('testCharset', $this->email->getCharSet());
    }

    public function testSetDefaultCharSet()
    {
        $this->email->setCharSet();
        $this->assertEquals(oxRegistry::getLang()->translateString("charset"), $this->email->getCharSet());
    }

    public function testSetGetMailer()
    {
        $this->email->setMailer('smtp');
        $this->assertSame('smtp', $this->email->getMailer());
    }

    public function testSetGetHost()
    {
        $this->email->setHost('localhost');
        $this->assertSame('localhost', $this->email->Host);
    }

    public function testGetErrorInfo()
    {
        $this->email->ErrorInfo = 'testErrorMessage';
        $this->assertSame('testErrorMessage', $this->email->getErrorInfo());
    }

    public function testSetMailWordWrap()
    {
        $this->email->setMailWordWrap('500');
        $this->assertSame('500', $this->email->WordWrap);
    }

    public function testGetUseInlineImagesFromConfig()
    {
        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $email = oxNew("oxemail");
        $this->assertTrue($email->getUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", false);
        $email = oxNew("oxemail");
        $this->assertFalse($email->getUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $email = oxNew("oxemail");
        $this->assertTrue($email->getUseInlineImages());
    }

    public function testSetGetUseInlineImages()
    {
        $this->email->setUseInlineImages(true);
        $this->assertTrue($this->email->getUseInlineImages());
    }

    public function testSendMailErrorMsg()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["getRecipient", "getMailer", "sendMail", "sendMailErrorMsg"]);
        $email->method('getRecipient')->willReturn([1]);
        $email->method('getMailer')->willReturn("smtp");
        $email->method('sendMail')->willReturn(false);

        $this->assertFalse($email->send());
    }

    public function testSendMailErrorMsg_failsOnlySmtp()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["getRecipient", "getMailer", "sendMail", "sendMailErrorMsg"]);
        $email->method('getRecipient')->willReturn([1]);
        $email->method('getMailer')->willReturn("smtp");

        $email
            ->method('sendMail')
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );

        $this->assertTrue($email->send());
    }

    public function testSendMailErrorMsg_failsMail()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["getRecipient", "getMailer", "sendMail", "sendMailErrorMsg"]);
        $email->method('getRecipient')->willReturn([1]);
        $email->method('getMailer')->willReturn("mail");
        $email->method('sendMail')->willReturn(false);
        $email->method('sendMailErrorMsg');

        $this->assertFalse($email->send());
    }

    /*
     * Test hook up method
     */
    public function testAddUserInfoOrderEmail()
    {
        $order = oxNew("oxorder");
        $this->assertEquals($order, $this->email->addUserInfoOrderEmail($order));
    }

    public function testAddUserRegisterEmail()
    {
        $this->assertEquals($this->user, $this->email->addUserRegisterEmail($this->user));
    }

    public function testAddForgotPwdEmail()
    {
        $this->assertEquals($this->shop, $this->email->addForgotPwdEmail($this->shop));
    }

    public function testAddNewsletterDBOptInMail()
    {
        $this->assertEquals($this->user, $this->email->addNewsletterDbOptInMail($this->user));
    }

    public function testClearMailer()
    {
        $this->email->setRecipient('testuser@testuser.com', 'testUser');
        $this->email->setReplyTo('testuser@testuser.com', 'testUser');

        $this->email->ErrorInfo = 'testErrorMessage';

        $this->email->clearMailer();

        $this->assertSame([], $this->email->getRecipient());
        $this->assertSame([], $this->email->getReplyTo());
        $this->assertSame('', $this->email->getErrorInfo());
    }

    public function testSetMailParamsWithDefaultShop()
    {
        // no smtp connect
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['isValidSmtpHost', 'getShop']);
        $email->method('isValidSmtpHost')->willReturn(false);
        $email->method('getShop')->willReturn($this->shop);

        //with no params must get default shop values
        $email->setMailParams();

        $this->assertSame('orderemail@orderemail.nl', $email->getFrom());
        $this->assertSame('testShopName', $email->getFromName());
        $this->assertSame('mail', $email->getMailer());
    }

    public function testSetMailParamsWithSelectedShop()
    {
        // with smtp connect
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['isValidSmtpHost', 'getShop']);
        $email->method('isValidSmtpHost')->willReturn(true);
        $email->method('getShop')->willReturn($this->shop);

        $oShop = oxNew("oxshop");
        $oShop->oxshops__oxorderemail = new oxField('orderemail2@orderemail2.nl', oxField::T_RAW);
        $oShop->oxshops__oxname = new oxField('testShopName2', oxField::T_RAW);
        $oShop->oxshops__oxsmtp = new oxField('127.0.0.1', oxField::T_RAW);
        $oShop->oxshops__oxsmtpuser = new oxField('testSmtpUser2', oxField::T_RAW);
        $oShop->oxshops__oxsmtppwd = new oxField('testSmtpPassword2', oxField::T_RAW);

        $email->setMailParams($oShop);

        $this->assertSame('orderemail2@orderemail2.nl', $email->getFrom());
        $this->assertSame('testShopName2', $email->getFromName());
        $this->assertSame('smtp', $email->getMailer());
        $this->assertSame('127.0.0.1', $email->Host);
        $this->assertSame('testSmtpUser2', $email->Username);
        $this->assertSame('testSmtpPassword2', $email->Password);
    }

    public function testGetShopWhenShopIsNotSet()
    {
        $this->assertEquals($this->getConfig()->getActiveShop(), $this->email->getShop());
    }

    public function testGetShopWithShopId()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $oShop = $this->email->getShop(null, $sShopId);
        $this->assertEquals($sShopId, $oShop->getShopId());
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oShop->getLanguage());
    }

    public function testGetShopWithLanguageId()
    {
        $oShop = $this->email->getShop(1);
        $this->assertSame(1, $oShop->getLanguage());
        $this->assertEquals($this->getConfig()->getShopId(), $oShop->getShopId());
    }

    public function testGetShopWithLanguageIdAndShopId()
    {
        $this->email->setShop($this->shop);

        $sShopId = $this->getConfig()->getBaseShopId();

        $oShop = $this->email->getShop(1, $sShopId);
        $this->assertSame(1, $oShop->getLanguage());
        $this->assertEquals($sShopId, $oShop->getShopId());
        $this->assertEquals($this->shop, $this->email->getShop());
    }

    public function testSetSmtpAuthInfo()
    {
        $this->email->setSmtpAuthInfo('testUserName', 'testPassword');

        $this->assertSame('testUserName', $this->email->Username);
        $this->assertSame('testPassword', $this->email->Password);
    }

    public function testSetSmtpDebug()
    {
        $this->email->setSmtpDebug(true);
        $this->assertTrue($this->email->SMTPDebug);
    }

    public function testMakeOutputProcessingInUtf8Mode()
    {
        $this->email->setBody('testbody 55 €'); //with euro sign
        $this->email->setAltBody('testaltbody 55 €'); //with euro sign
        $this->email->makeOutputProcessing();

        $this->assertSame('testbody 55 €', $this->email->getBody());
        $this->assertSame('testaltbody 55 €', $this->email->getAltBody());
    }

    public function testHeaderLine()
    {
        $headerLine = $this->email->headerLine('testName', 'testValue');

        $this->assertStringContainsString('testName', $headerLine);
        $this->assertStringContainsString('testValue', $headerLine);
    }

    public function testHeaderLineXMailer()
    {
        $this->assertNull($this->email->headerLine('X-Mailer', 'testVal'));
        $this->assertNull($this->email->headerLine('x-Mailer', 'testVal'));
        $this->assertNull($this->email->headerLine('X-Priority', 'testVal'));
    }

    public function testSend_noRecipient()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['sendMail']);
        $email->expects($this->never())->method('sendMail');
        $email->setRecipient("");
        $this->assertFalse($this->email->send());
    }

    public function testGetNewsSubsLink()
    {
        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0';
        $this->assertSame($sUrl, $this->email->getNewsSubsLink('XXXX'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertSame($sUrl, $this->email->getNewsSubsLink('XXXX'));
    }

    public function testGetNewsSubsLinkWithConfirm()
    {
        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0&amp;confirm=AAAA';
        $this->assertSame($sUrl, $this->email->getNewsSubsLink('XXXX', 'AAAA'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertSame($sUrl, $this->email->getNewsSubsLink('XXXX', 'AAAA'));
    }

    public function testSetSmtpProtocol()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['set']);
        $email
            ->method('set')
            ->withConsecutive(['SMTPSecure', 'ssl'], ['SMTPSecure', 'tls']);

        $this->assertSame("hostname:23", $email->setSmtpProtocol('ssl://hostname:23'));
        $this->assertSame("hostname:23", $email->setSmtpProtocol('tls://hostname:23'));
        $this->assertSame("ssx://hostname:23", $email->setSmtpProtocol('ssx://hostname:23'));
    }

    public function testSendOrderEmailToOwnerCorrectSenderReceiver()
    {
        $renderer = $this->prophesize(TemplateRendererInterface::class);
        $renderer->renderTemplate(Argument::type('string'), Argument::type('array'))->willReturn('');
        $renderer->exists(Argument::type('string'))->willReturn(false);

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendMail", "getRenderer"]);
        $email->expects($this->once())->method("sendMail")->willReturn(true);
        $email->method("getRenderer")->willReturn($renderer->reveal());

        $user = oxNew('oxUser');
        $user->load("oxdefaultadmin");
        //oxOrder mock
        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, ["getOrderUser"]);
        $order->expects($this->once())->method("getOrderUser")->willReturn($user);

        $email->sendOrderEmailToOwner($order);

        //testing actual From field Value
        $this->assertSame("order@myoxideshop.com", $email->getFrom());
        //testing actual To field Value
        $aTo = [];
        $aTo[0][0] = 'order@myoxideshop.com';
        $aTo[0][1] = 'order';
        $this->assertSame($aTo, $email->getRecipient());
    }

    public function testProductReviewLinksAreIncludedByDefaultInSendedNowMail()
    {
        $orderStub = $this->getOrderStub();
        $emailStub = $this->getEmailStub();

        $emailStub->sendSendedNowMail($orderStub);

        $body = $emailStub->getBody();

        $this->assertTrue($this->isReviewLinkIncluded($body), 'Links to product reviews are included in the email body by default');
        ContainerFactory::resetContainer();
    }

    /**
     *
     * @dataProvider dataProviderTestProductReviewLinksAreIncludedInSendedNowMailAccordingConfiguration
     */
    public function testProductReviewLinksAreIncludedInSendedNowMailAccordingConfiguration(
        bool $configParameterLoadReviewsValue,
        bool $isReviewLinkExpectedToBeIncluded,
        string $message
    ) {
        $this->setConfigParam('bl_perfLoadReviews', $configParameterLoadReviewsValue);
        $orderStub = $this->getOrderStub();
        $emailStub = $this->getEmailStub();

        $emailStub->sendSendedNowMail($orderStub);

        $body = $emailStub->getBody();

        $this->assertSame($isReviewLinkExpectedToBeIncluded, $this->isReviewLinkIncluded($body), $message);
        ContainerFactory::resetContainer();
    }

    public function dataProviderTestProductReviewLinksAreIncludedInSendedNowMailAccordingConfiguration(): \Iterator
    {
        yield [
            'configParameterLoadReviewsValue'  => true,
            'isReviewLinkExpectedToBeIncluded' => true,
            'message'                          => 'Links to product reviews are included in the email body'
        ];
        yield [
            'configParameterLoadReviewsValue'  => false,
            'isReviewLinkExpectedToBeIncluded' => false,
            'message'                          => 'No links to product reviews are included in the email body'
        ];
    }

    public function testProductReviewLinksAreNotIncludedByDefaultInOrderEmail()
    {
        $orderStub = $this->getOrderStub();
        $emailStub = $this->getEmailStub();

        $emailStub->sendOrderEmailToUser($orderStub);

        $body = $emailStub->getBody();

        $this->assertFalse($this->isReviewLinkIncluded($body));
    }

    /**
     *
     * @dataProvider dataProviderTestProductReviewLinksAreIncludedInOrderEmailAccordingConfiguration
     */
    public function testProductReviewLinksAreIncludedInOrderEmailAccordingConfiguration(
        bool $configParameterLoadReviews,
        bool $configParameterIncludeProductReviewLinksInEmail,
        bool $isReviewLinkExpectedToBeIncluded,
        string $message
    ) {
        $this->setConfigParam('bl_perfLoadReviews', $configParameterLoadReviews);
        $this->setConfigParam('includeProductReviewLinksInEmail', $configParameterIncludeProductReviewLinksInEmail);
        $orderStub = $this->getOrderStub();
        $emailStub = $this->getEmailStub();

        $emailStub->sendOrderEmailToUser($orderStub);

        $body = $emailStub->getBody();

        $this->assertSame($isReviewLinkExpectedToBeIncluded, $this->isReviewLinkIncluded($body), $message);
    }

    public function dataProviderTestProductReviewLinksAreIncludedInOrderEmailAccordingConfiguration(): \Iterator
    {
        yield [
            'configParameterLoadReviewsValue'                 => true,
            'configParameterIncludeProductReviewLinksInEmail' => true,
            'isReviewLinkExpectedToBeIncluded'                => true,
            'message'                                         => 'Links to product reviews are included in the email body'
        ];
        yield [
            'configParameterLoadReviewsValue'                 => true,
            'configParameterIncludeProductReviewLinksInEmail' => false,
            'isReviewLinkExpectedToBeIncluded'                => false,
            'message'                                         => 'No links to product reviews are included in the email body'
        ];
        yield [
            'configParameterLoadReviewsValue'                 => false,
            'configParameterIncludeProductReviewLinksInEmail' => true,
            'isReviewLinkExpectedToBeIncluded'                => false,
            'message'                                         => 'No links to product reviews are included in the email body'
        ];
        yield [
            'configParameterLoadReviewsValue'                 => false,
            'configParameterIncludeProductReviewLinksInEmail' => false,
            'isReviewLinkExpectedToBeIncluded'                => false,
            'message'                                         => 'No links to product reviews are included in the email body'
        ];
    }

    /**
     * @param $basketContents
     * @param $basketArticles
     *
     * @return \OxidEsales\Eshop\Application\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getOrderStub()
    {
        $priceStub = $this->getMockBuilder(Price::class)
            ->getMock();
        $priceStub->method('getPrice')->willReturn(256);
        $priceStub->method('getBruttoPrice')->willReturn(8);

        $basketItemStub = $this->getMockBuilder(BasketItem::class)
            ->setMethods(['getPrice', 'getUnitPrice', 'getRegularUnitPrice', 'getTitle'])
            ->getMock();
        $basketItemStub->method('getPrice')->willReturn($priceStub);
        $basketItemStub->method('getUnitPrice')->willReturn($priceStub);
        $basketItemStub->method('getRegularUnitPrice')->willReturn($priceStub);
        $basketItemStub->method('getTitle')->willReturn("testarticle");

        // insert test article
        $article = oxNew("oxArticle");
        $article->setId('_testArticleId');
        $article->setId('_testArticleId');

        $article->oxarticles__oxtitle = new oxField();

        $priceStub->setPrice(0);

        $basketStub = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class)
            ->setMethods(['getBasketArticles', 'getContents', 'getCosts', 'getBruttoSum',])
            ->getMock();
        $basketStub->method('getBasketArticles')->willReturn([$article]);
        $basketStub->method('getContents')->willReturn([$basketItemStub]);
        $basketStub->method('getCosts')->willReturn($priceStub);
        $basketStub->method('getBruttoSum')->willReturn(7);

        $payment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
        $payment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $user = oxNew("oxuser");
        $user->setId('_testUserId');

        $user->oxuser__oxusername = new oxField('username@useremail.nl', oxField::T_RAW);
        $user->oxuser__oxfname = new oxField('testUserFName', oxField::T_RAW);
        $user->oxuser__oxlname = new oxField('testUserLName', oxField::T_RAW);

        $orderStub = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Order::class)
            ->setMethods(['getOrderUser', 'getBasket', 'getPayment'])
            ->getMock();
        $orderStub->method('getOrderUser')->willReturn($user);
        $orderStub->method('getBasket')->willReturn($basketStub);
        $orderStub->method('getPayment')->willReturn($payment);

        $orderStub->oxorder__oxordernr = new oxField('987654321', oxField::T_RAW);
        $orderStub->oxorder__oxbillfname = new oxField('');
        $orderStub->oxorder__oxbilllname = new oxField('');
        $orderStub->oxorder__oxbilladdinfo = new oxField('');
        $orderStub->oxorder__oxbillstreet = new oxField('');
        $orderStub->oxorder__oxbillcity = new oxField('');
        $orderStub->oxorder__oxbillcountry = new oxField('');
        $orderStub->oxorder__oxbillcompany = new oxField('');
        $orderStub->oxorder__oxdeltype = new oxField("oxidstandard");

        return $orderStub;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Email|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEmailStub()
    {
        $shop = oxNew(Shop::class);
        $shop->load($this->getConfig()->getShopId());

        $emailStub = $this->getMockBuilder(\OxidEsales\Eshop\Core\Email::class)
            ->setMethods(['sendMail', 'getShop', 'getOrderFileList'])
            ->getMock();
        ;
        $emailStub->method('sendMail')->willReturn(true);
        $emailStub->method('getShop')->willReturn($shop);
        $emailStub->method('getOrderFileList')->willReturn(false);

        return $emailStub;
    }

    /**
     * @param $body
     */
    private function isReviewLinkIncluded($body): bool
    {
        return str_contains((string) $body, 'cl=review');
    }
}
