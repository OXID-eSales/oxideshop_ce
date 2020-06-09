<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use oxDb;
use oxField;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\Shop;
use OxidEsales\Eshop\Core\Price;
use oxPrice;
use oxRegistry;
use oxTestModules;

final class EmailTest extends \OxidTestCase
{
    protected $email = null;
    protected $user = null;
    protected $shop = null;
    protected $article = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('sTheme', 'azure');

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

    /**
     * Tear down the fixture.
     *
     * @return null
     */
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
     *
     * @return null
     */
    public function testSendRegisterConfirmEmail()
    {
        $user = oxNew('oxuser');

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendRegisterEmail"));
        $email->expects($this->once())->method('sendRegisterEmail')->with($this->equalTo($user), $this->equalTo(null));

        $email->sendRegisterConfirmEmail($user);

        $viewData = $email->getViewData();
        $this->assertEquals($viewData["contentident"], "oxregisteraltemail");
        $this->assertEquals($viewData["contentplainident"], "oxregisterplainaltemail");
    }

    public function testIncludeImagesErrorTestCase()
    {
        $config = $this->getConfig();

        $article = oxNew('oxArticle');
        $article->load('1351');
        $imageUrl = $article->getThumbnailUrl();
        $imageFile = basename($imageUrl);
        $title = $article->oxarticles__oxtitle->value;
        $imageDirectory = $config->getImageDir();

        $imageGenerator = $this->getMock(\OxidEsales\Eshop\Core\DynamicImageGenerator::class, array('getImagePath'));
        $imageGenerator->expects($this->any())->method('getImagePath')->will($this->returnValue($config->getPictureDir(false) . 'generated/product/thumb/185_150_75/nopic.jpg'));
        oxTestModules::addModuleObject('oxDynImgGenerator', $imageGenerator);

        $body = '<img src="' . $imageDirectory . 'stars.jpg" border="0" hspace="0" vspace="0" alt="stars" align="texttop">';
        $body .= '<img src="' . $config->getImageUrl() . 'logo.png" border="0" hspace="0" vspace="0" alt="logo" align="texttop">';
        $body .= '<img src="' . $imageUrl . '" border="0" hspace="0" vspace="0" alt="' . $title . '" align="texttop">';

        $generatedEmailBody = '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="stars" align="texttop">';
        $generatedEmailBody .= '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="logo" align="texttop">';
        $generatedEmailBody .= '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="' . $title . '" align="texttop">';

        $utilsObjectMock = $this->getMock(\OxidEsales\Eshop\Core\UtilsObject::class, ['generateUId']);
        $utilsObjectMock->expects($this->any())->method('generateUId')->will($this->returnValue('xxx'));

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $email */
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('getBody', 'addEmbeddedImage', 'setBody', 'getUtilsObjectInstance'));
        $email->expects($this->at(1))->method('getUtilsObjectInstance')->will($this->returnValue($utilsObjectMock));
        $email->expects($this->at(2))->method('addEmbeddedImage')->with($this->equalTo($imageDirectory . 'stars.jpg'), $this->equalTo('xxx'), $this->equalTo("image"), $this->equalTo("base64"), $this->equalTo('image/jpeg'))->will($this->returnValue(true));
        $email->expects($this->at(3))->method('addEmbeddedImage')->with($this->equalTo($imageDirectory . 'logo.png'), $this->equalTo('xxx'), $this->equalTo("image"), $this->equalTo("base64"), $this->equalTo('image/png'))->will($this->returnValue(true));
        $email->expects($this->at(4))->method('addEmbeddedImage')->with($this->equalTo($config->getPictureDir(false) . 'generated/product/thumb/185_150_75/' . $imageFile), $this->equalTo('xxx'), $this->equalTo("image"), $this->equalTo("base64"), $this->equalTo('image/jpeg'))->will($this->returnValue(true));
        $email->expects($this->once())->method('getBody')->will($this->returnValue($body));
        $email->expects($this->once())->method('setBody')->with($this->equalTo($generatedEmailBody));

        $email->_includeImages(
            $imageDirectory,
            $config->getImageUrl(false),
            $config->getPictureUrl(null),
            $imageDirectory,
            $config->getPictureDir(false)
        );
    }

    public function testSendMailBySmtp()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));

        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $email->setHost("localhost");
        $email->setMailer("smtp");

        $this->assertTrue($email->send());
        $this->assertEquals('smtp', $email->getMailer());
    }

    public function testSendMailByPhpMailFunction()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));

        $email->setMailer('mail');
        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $this->assertTrue($email->send());
        $this->assertEquals('mail', $email->getMailer());
    }

    public function testSendMailByPhpMailWhenSmtpFails()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_sendMail', '_sendMailErrorMsg'));
        $email->expects($this->atLeast(2))->method('_sendMail')->will($this->returnValue(false));
        $email->expects($this->atLeastOnce())->method('_sendMailErrorMsg');

        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $email->setHost("localhost");
        $email->setMailer("smtp");

        $this->assertFalse($email->send());
        $this->assertEquals('mail', $email->getMailer());
    }

    public function testSendMailErrorMsgWhenMailingFails()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_sendMailErrorMsg", "getMailer"));
        $email->expects($this->exactly(2))->method('_sendMail')->will($this->returnValue(false));
        $email->expects($this->exactly(2))->method('_sendMailErrorMsg');
        $email->expects($this->once())->method('getMailer')->will($this->returnValue("smtp"));

        $email->setRecipient($this->shop->oxshops__oxorderemail->value, $this->shop->oxshops__oxname->value);
        $email->send();
    }

    public function testSetSmtp()
    {
        // just forcing to connect to webserver..
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_isValidSmtpHost'));
        $email->expects($this->once())->method('_isValidSmtpHost')
            ->with($this->equalTo('127.0.0.1'))
            ->will($this->returnValue(true));

        $email->setSmtp($this->shop);
        $this->assertEquals('smtp', $email->getMailer());
        $this->assertEquals('127.0.0.1', $email->Host);
        $this->assertEquals('testSmtpUser', $email->Username);
        $this->assertEquals('testSmtpPassword', $email->Password);
    }

    public function testSetSmtpWithNoSmtpValues()
    {
        $email = oxNew('oxEmail');

        $this->shop->oxshops__oxsmtp = new oxField(null, oxField::T_RAW);
        $email->setSmtp($this->shop);
        $this->assertEquals('mail', $email->getMailer());
    }

    public function testSendOrderEMailToOwnerAddsHistoryRecord()
    {
        $myDb = oxDb::getDb();

        $payment = oxNew('oxPayment');
        $payment->oxpayments__oxdesc = new oxField("testPaymentDesc");

        $basket = oxNew('oxBasket');
        $basket->setCost('oxpayment', new oxPrice(0));
        $basket->setCost('oxdelivery', new oxPrice(6626));

        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser", "getBasket", "getPayment"));
        $order->expects($this->any())->method('getOrderUser')->will($this->returnValue($this->user));
        $order->expects($this->any())->method('getBasket')->will($this->returnValue($basket));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($payment));

        $order->oxorder__oxbillcompany = new oxField('');
        $order->oxorder__oxbillfname = new oxField('');
        $order->oxorder__oxbilllname = new oxField('');
        $order->oxorder__oxbilladdinfo = new oxField('');
        $order->oxorder__oxbillstreet = new oxField('');
        $order->oxorder__oxbillcity = new oxField('');
        $order->oxorder__oxbillcountry = new oxField('');
        $order->oxorder__oxdeltype = new oxField("oxidstandard");

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->sendOrderEmailToOwner($order);
        $this->assertTrue($blRet, 'Order email was not sent to shop owner');

        $this->assertEquals($email->getAltBody(), $myDb->getOne('SELECT oxtext FROM oxremark where oxparentid=' . $myDb->quote($this->user->getId())));
    }

    public function testSendForgotPwdEmailToNotExistingUser()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->never())->method('_sendMail');
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->SendForgotPwdEmail('nosuchuser@useremail.nl');
        $this->assertFalse($blRet, 'Mail was sent to not existing user');
    }

    public function testSendForgotPwdEmailSendingFailed()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("send", "_getShop"));
        $email->expects($this->any())->method('send')->will($this->returnValue(false));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->SendForgotPwdEmail('username@useremail.nl');
        $this->assertEquals(-1, $blRet);
    }

    public function testSendBackupMailWithAttachment()
    {
        $fileToAttach = $this->getFileToAttach();
        $filesToAttach = array(basename($fileToAttach));
        $filesToAttachDirectory = dirname($fileToAttach);
        $emailAddress = 'username@useremail.nl';
        $subject = 'testBackupMailSubject';
        $message = 'testBackupMailMessage';
        $status = array();
        $errors = array();

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $email */
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->once())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->sendBackupMail($filesToAttach, $filesToAttachDirectory, $emailAddress, $subject, $message, $status, $errors);
        $this->assertTrue($blRet, 'Backup mail was not sent to shop owner');
    }

    public function testSendBackupMailWithAttachmentStatusCode()
    {
        $fileToAttach = $this->getFileToAttach();
        $filesToAttach = array(basename($fileToAttach));
        $filesToAttachDirectories = dirname($fileToAttach);
        $emailAddress = 'username@useremail.nl';
        $subject = 'testBackupMailSubject';
        $message = 'testBackupMailMessage';
        $status = array();
        $errors = array();

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $email */
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->once())->method('_getShop')->will($this->returnValue($this->shop));

        $email->sendBackupMail($filesToAttach, $filesToAttachDirectories, $emailAddress, $subject, $message, $status, $errors);

        //check status code
        $this->assertEquals(3, $status[0], "Attachment was not icluded im mail");
    }

    public function testSendBackupMailWithWrongAttachmentGeneratesErrorCodes()
    {
        $fileToAttach = $this->createFile('alternativeFile.php', '');
        $filesToAttach = array(basename($fileToAttach));
        $filesToAttachDirectory = 'nosuchdir';

        $emailAddress = 'username@useremail.nl';
        $subject = 'testBackupMailSubject';
        $message = 'testBackupMailMessage';
        $status = array();
        $errors = array();

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $email */
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->never())->method('_sendMail');
        $email->expects($this->once())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->sendBackupMail($filesToAttach, $filesToAttachDirectory, $emailAddress, $subject, $message, $status, $errors);
        $this->assertFalse($blRet, 'Bad backup mail was not sent to shop owner');

        // checking error codes
        // 4 - backup mail was not sent
        // 5 - file not found
        $this->assertTrue((in_array(5, $errors[0])), "Wrong attachment was icluded in mail");
        $this->assertTrue((in_array(4, $errors[1])), "Wrong attachment was was sent");
    }

    public function testSendEmailToMultipleUsers()
    {
        $aTo = array('username@useremail.nl', 'username2@useremail.nl');
        $sSubject = 'testSubject';
        $sBody = 'testBody';

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->once())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->sendEmail($aTo, $sSubject, $sBody);
        $this->assertTrue($blRet, 'Mail was not sent');

        $aRecipients = $email->getRecipient();
        $this->assertEquals(2, count($aRecipients));
        $this->assertEquals('username@useremail.nl', $aRecipients[0][0]);
        $this->assertEquals('username2@useremail.nl', $aRecipients[1][0]);
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

        $basketItem = $this->getMock(BasketItem::class, array('getArticle', 'getProductId'));
        $basketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->article));
        $basketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $basketItem;

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->sendStockReminder($aBasketContents);
        $this->assertTrue($blRet, 'Stock remind mail was not sent');
    }

    public function testSendStockReminderWhenStockAmountIsGreaterThanRemindAmount()
    {
        //set params for stock reminder
        $this->article->oxarticles__oxstock = new oxField('10', oxField::T_RAW);
        $this->article->oxarticles__oxremindamount = new oxField('9', oxField::T_RAW);
        $this->article->save();

        $basketItem = $this->getMock(BasketItem::class, array('getArticle', 'getProductId'));
        $basketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->article));
        $basketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $basketItem;

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->never())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

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

        $basketItem = $this->getMock(BasketItem::class, array('getArticle', 'getProductId'));
        $basketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->article));
        $basketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $basketItem;

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $email->expects($this->never())->method('_sendMail')->will($this->returnValue(true));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $blRet = $email->sendStockReminder($aBasketContents);
        $this->assertFalse($blRet, 'No need to send stock remind mail');
    }

    public function testIncludeImages()
    {
        $myConfig = $this->getConfig();
        $sImageDir = $myConfig->getImageDir();

        $email = oxNew('oxEmail');
        $email->setBody("<img src='{$sImageDir}/logo.png'> --- <img src='{$sImageDir}/stars.jpg'>");

        $email->UNITincludeImages(
            $myConfig->getImageDir(),
            $myConfig->getImageUrl(isAdmin()),
            $myConfig->getPictureUrl(null),
            $myConfig->getImageDir(),
            $myConfig->getPictureDir(false)
        );

        $aAttachments = $email->getAttachments();
        $this->assertEquals('logo.png', $aAttachments[0][1]);
        $this->assertEquals('stars.jpg', $aAttachments[1][1]);
    }

    public function testSetGetSubject()
    {
        $email = oxNew('oxEmail');
        $email->setSubject('testSubject');
        $this->assertEquals('testSubject', $email->getSubject());
    }

    public function testSetGetBody()
    {
        $email = oxNew('oxEmail');
        $email->setBody('testBody');
        $this->assertEquals('testBody', $email->getBody());
    }

    public function testClearSidFromBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $email = oxNew('oxEmail');

        $email->setBody('testBody index.php?bonusid=111&sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());

        $email->setBody('testBody index.php?bonusid=111&force_sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());

        $email->setBody('testBody index.php?bonusid=111&admin_sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());

        $email->setBody('testBody index.php?bonusid=111&force_admin_sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $email->getBody());
    }

    public function testSetGetAltBody()
    {
        $email = oxNew('oxEmail');

        $email->setAltBody('testAltBody');
        $this->assertEquals('testAltBody', $email->getAltBody());
    }

    public function testClearSidFromAltBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $this->email->setAltBody('testAltBody index.php?bonusid=111&sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());

        $this->email->setAltBody('testAltBody index.php?bonusid=111&force_sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());

        $this->email->setAltBody('testAltBody index.php?bonusid=111&admin_sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());

        $this->email->setAltBody('testAltBody index.php?bonusid=111&force_admin_sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->email->getAltBody());
    }

    public function testClearHtmlEntitiesFromAltBody()
    {
        $this->email->setAltBody('testAltBody &amp; &quot; &#039; &lt; &gt;');
        $this->assertEquals('testAltBody & " \' < >', $this->email->getAltBody());
    }

    public function testSetGetRecipient()
    {
        $aUser[0][0] = 'testuser@testuser.com';
        $aUser[0][1] = 'testUserName';

        $this->email->setRecipient($aUser[0][0], $aUser[0][1]);
        $this->assertEquals($aUser, $this->email->getRecipient());
    }

    public function testSetRecipient_emptyEmail()
    {
        $this->email->setRecipient("", "");
        $this->assertEquals(array(), $this->email->getRecipient());
    }

    public function testSetRecipient_emptyName()
    {
        $this->email->setRecipient("test@test.lt", "");
        $this->assertEquals(array(array("test@test.lt", "")), $this->email->getRecipient());
    }

    public function testSetGetReplyTo()
    {
        $aUser[0][0] = 'testuser@testuser.com';
        $aUser[0][1] = 'testUserName';

        $this->email->setReplyTo($aUser[0][0], $aUser[0][1]);
        $this->assertEquals($aUser, $this->email->getReplyTo());
    }

    public function testSetReplyToWithNoParams()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_getShop"));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $email->setReplyTo();
        $aReplyTo = $email->getReplyTo();

        $this->assertEquals($this->shop->oxshops__oxorderemail->value, $aReplyTo[0][0]);
    }

    public function testSetGetFrom()
    {
        $this->email->setFrom('testuser@testuser.com', 'testUserName');
        $this->assertEquals('testuser@testuser.com', $this->email->getFrom());
        $this->assertEquals('testUserName', $this->email->getFromName());
    }

    public function testSetCharSet()
    {
        $this->email->setCharSet('testCharset');
        $this->assertEquals('testCharset', $this->email->getCharSet());
    }

    public function testSetDefaultCharSet()
    {
        $this->email->setCharSet();
        $this->assertEquals(oxRegistry::getLang()->translateString("charset"), $this->email->getCharSet());
    }

    public function testSetGetMailer()
    {
        $this->email->setMailer('smtp');
        $this->assertEquals('smtp', $this->email->getMailer());
    }

    public function testSetGetHost()
    {
        $this->email->setHost('localhost');
        $this->assertEquals('localhost', $this->email->Host);
    }

    public function testGetErrorInfo()
    {
        $this->email->ErrorInfo = 'testErrorMessage';
        $this->assertEquals('testErrorMessage', $this->email->getErrorInfo());
    }

    public function testSetMailWordWrap()
    {
        $this->email->setMailWordWrap('500');
        $this->assertEquals('500', $this->email->WordWrap);
    }

    public function testGetUseInlineImagesFromConfig()
    {
        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $email = oxNew("oxemail");
        $this->assertTrue($email->UNITgetUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", false);
        $email = oxNew("oxemail");
        $this->assertFalse($email->UNITgetUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $email = oxNew("oxemail");
        $this->assertTrue($email->UNITgetUseInlineImages());
    }

    public function testSetGetUseInlineImages()
    {
        $this->email->setUseInlineImages(true);
        $this->assertTrue($this->email->UNITgetUseInlineImages());
    }

    public function testAddAttachment()
    {
        $myConfig = $this->getConfig();
        $sImageDir = $myConfig->getImageDir() . '/';

        $this->email->AddAttachment($sImageDir, 'barrcode.gif');
        $aAttachment = $this->email->getAttachments();

        $this->assertEquals('barrcode.gif', $aAttachment[0][1]);
    }

    public function testClearAttachments()
    {
        $myConfig = $this->getConfig();
        $sImageDir = $myConfig->getImageDir() . '/';

        $this->email->AddAttachment($sImageDir, 'barrcode.gif');
        $aAttachment = $this->email->getAttachments();
        $this->assertEquals('barrcode.gif', $aAttachment[0][1]);

        $this->email->clearAttachments();
        $aAttachment = $this->email->getAttachments();
        $this->assertEquals(0, count($aAttachment));
    }

    public function testSendMailErrorMsg()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"));
        $email->expects($this->at(0))->method('getRecipient')->will($this->returnValue([1]));
        $email->expects($this->at(1))->method('getMailer')->will($this->returnValue("smtp"));
        $email->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $email->expects($this->at(3))->method('_sendMailErrorMsg');
        $email->expects($this->at(4))->method('_sendMail')->will($this->returnValue(false));
        $email->expects($this->at(5))->method('_sendMailErrorMsg');

        $this->assertFalse($email->send());
    }

    public function testSendMailErrorMsg_failsOnlySmtp()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"));
        $email->expects($this->at(0))->method('getRecipient')->will($this->returnValue([1]));
        $email->expects($this->at(1))->method('getMailer')->will($this->returnValue("smtp"));
        $email->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $email->expects($this->at(3))->method('_sendMailErrorMsg');
        $email->expects($this->at(4))->method('_sendMail')->will($this->returnValue(true));

        $this->assertTrue($email->send());
    }

    public function testSendMailErrorMsg_failsMail()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"));
        $email->expects($this->at(0))->method('getRecipient')->will($this->returnValue([1]));
        $email->expects($this->at(1))->method('getMailer')->will($this->returnValue("mail"));
        $email->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $email->expects($this->at(3))->method('_sendMailErrorMsg');

        $this->assertFalse($email->send());
    }

    /*
     * Test hook up method
     */
    public function testAddUserInfoOrderEmail()
    {
        $order = oxNew("oxorder");
        $this->assertEquals($order, $this->email->UNITaddUserInfoOrderEmail($order));
    }

    public function testAddUserRegisterEmail()
    {
        $this->assertEquals($this->user, $this->email->UNITaddUserRegisterEmail($this->user));
    }

    public function testAddForgotPwdEmail()
    {
        $this->assertEquals($this->shop, $this->email->UNITaddForgotPwdEmail($this->shop));
    }
    public function testAddNewsletterDBOptInMail()
    {
        $this->assertEquals($this->user, $this->email->UNITaddNewsletterDbOptInMail($this->user));
    }

    public function testClearMailer()
    {
        $this->email->setRecipient('testuser@testuser.com', 'testUser');
        $this->email->setReplyTo('testuser@testuser.com', 'testUser');
        $this->email->ErrorInfo = 'testErrorMessage';

        $this->email->UNITclearMailer();

        $this->assertEquals(array(), $this->email->getRecipient());
        $this->assertEquals(array(), $this->email->getReplyTo());
        $this->assertEquals('', $this->email->getErrorInfo());
    }

    public function testSetMailParamsWithDefaultShop()
    {
        // no smtp connect
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_isValidSmtpHost', '_getShop'));
        $email->expects($this->any())->method('_isValidSmtpHost')->will($this->returnValue(false));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        //with no params must get default shop values
        $email->UNITsetMailParams();

        $this->assertEquals('orderemail@orderemail.nl', $email->getFrom());
        $this->assertEquals('testShopName', $email->getFromName());
        $this->assertEquals('mail', $email->getMailer());
    }

    public function testSetMailParamsWithSelectedShop()
    {
        // with smtp connect
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_isValidSmtpHost', '_getShop'));
        $email->expects($this->any())->method('_isValidSmtpHost')->will($this->returnValue(true));
        $email->expects($this->any())->method('_getShop')->will($this->returnValue($this->shop));

        $oShop = oxNew("oxshop");
        $oShop->oxshops__oxorderemail = new oxField('orderemail2@orderemail2.nl', oxField::T_RAW);
        $oShop->oxshops__oxname = new oxField('testShopName2', oxField::T_RAW);
        $oShop->oxshops__oxsmtp = new oxField('127.0.0.1', oxField::T_RAW);
        $oShop->oxshops__oxsmtpuser = new oxField('testSmtpUser2', oxField::T_RAW);
        $oShop->oxshops__oxsmtppwd = new oxField('testSmtpPassword2', oxField::T_RAW);

        $email->UNITsetMailParams($oShop);

        $this->assertEquals('orderemail2@orderemail2.nl', $email->getFrom());
        $this->assertEquals('testShopName2', $email->getFromName());
        $this->assertEquals('smtp', $email->getMailer());
        $this->assertEquals('127.0.0.1', $email->Host);
        $this->assertEquals('testSmtpUser2', $email->Username);
        $this->assertEquals('testSmtpPassword2', $email->Password);
    }

    public function testGetShopWhenShopIsNotSet()
    {
        $this->assertEquals($this->getConfig()->getActiveShop(), $this->email->UNITgetShop());
    }

    public function testGetShopWithShopId()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $oShop = $this->email->UNITgetShop(null, $sShopId);
        $this->assertEquals($sShopId, $oShop->getShopId());
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oShop->getLanguage());
    }

    public function testGetShopWithLanguageId()
    {
        $oShop = $this->email->UNITgetShop(1);
        $this->assertEquals(1, $oShop->getLanguage());
        $this->assertEquals($this->getConfig()->getShopId(), $oShop->getShopId());
    }

    public function testGetShopWithLanguageIdAndShopId()
    {
        $this->email->setShop($this->shop);

        $sShopId = $this->getConfig()->getBaseShopId();

        $oShop = $this->email->UNITgetShop(1, $sShopId);
        $this->assertEquals(1, $oShop->getLanguage());
        $this->assertEquals($sShopId, $oShop->getShopId());
        $this->assertEquals($this->shop, $this->email->UNITgetShop());
    }

    public function testSetSmtpAuthInfo()
    {
        $this->email->UNITsetSmtpAuthInfo('testUserName', 'testPassword');

        $this->assertEquals('testUserName', $this->email->Username);
        $this->assertEquals('testPassword', $this->email->Password);
    }

    public function testSetSmtpDebug()
    {
        $this->email->UNITsetSmtpDebug(true);
        $this->assertTrue($this->email->SMTPDebug);
    }

    public function testMakeOutputProcessingInUtf8Mode()
    {
        $this->email->setBody('testbody 55 €'); //with euro sign
        $this->email->setAltBody('testaltbody 55 €'); //with euro sign
        $this->email->UNITmakeOutputProcessing();

        $this->assertEquals('testbody 55 €', $this->email->getBody());
        $this->assertEquals('testaltbody 55 €', $this->email->getAltBody());
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
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_sendMail'));
        $email->expects($this->never())->method('_sendMail');
        $email->setRecipient("");
        $this->assertFalse($this->email->send());
    }

    public function testGetNewsSubsLink()
    {
        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0';
        $this->assertEquals($sUrl, $this->email->UNITgetNewsSubsLink('XXXX'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertEquals($sUrl, $this->email->UNITgetNewsSubsLink('XXXX'));
    }

    public function testGetNewsSubsLinkWithConfirm()
    {
        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0&amp;confirm=AAAA';
        $this->assertEquals($sUrl, $this->email->UNITgetNewsSubsLink('XXXX', 'AAAA'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertEquals($sUrl, $this->email->UNITgetNewsSubsLink('XXXX', 'AAAA'));
    }

    public function testSetSmtpProtocol()
    {
        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('set'));
        $email->expects($this->at(0))->method('set')
            ->with(
                $this->equalTo('SMTPSecure'),
                $this->equalTo('ssl')
            );
        $email->expects($this->at(1))->method('set')
            ->with(
                $this->equalTo('SMTPSecure'),
                $this->equalTo('tls')
            );
        $this->assertEquals("hostname:23", $email->UNITsetSmtpProtocol('ssl://hostname:23'));
        $this->assertEquals("hostname:23", $email->UNITsetSmtpProtocol('tls://hostname:23'));
        $this->assertEquals("ssx://hostname:23", $email->UNITsetSmtpProtocol('ssx://hostname:23'));
    }

    public function testSendOrderEmailToOwnerCorrectSenderReceiver()
    {
        $oSmartyMock = $this->getMock("Smarty", array("fetch"));
        $oSmartyMock->expects($this->any())->method("fetch")->will($this->returnValue(''));

        $email = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getSmarty"));
        $email->expects($this->once())->method("_sendMail")->will($this->returnValue(true));
        $email->expects($this->any())->method("_getSmarty")->will($this->returnValue($oSmartyMock));

        $user = oxNew('oxUser');
        $user->load("oxdefaultadmin");
        //oxOrder mock
        $order = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser"));
        $order->expects($this->once())->method("getOrderUser")->will($this->returnValue($user));

        $email->sendOrderEmailToOwner($order);

        //testing actual From field Value
        $this->assertEquals("order@myoxideshop.com", $email->getFrom());
        //testing actual To field Value
        $aTo = array();
        $aTo[0][0] = 'order@myoxideshop.com';
        $aTo[0][1] = 'order';
        $this->assertEquals($aTo, $email->getRecipient());
    }

    public function testProductReviewLinksAreIncludedByDefaultInSendedNowMail()
    {
        $orderStub = $this->getOrderStub();
        $emailStub = $this->getEmailStub();

        $emailStub->sendSendedNowMail($orderStub);

        $body = $emailStub->getBody();

        $this->assertTrue($this->isReviewLinkIncluded($body), 'Links to product reviews are included in the email body by default');
    }

    /**
     * @param bool   $configParameterLoadReviewsValue
     * @param bool   $isReviewLinkExpectedToBeIncluded
     * @param string $message
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
    }

    public function dataProviderTestProductReviewLinksAreIncludedInSendedNowMailAccordingConfiguration()
    {
        return [
            [
                'configParameterLoadReviewsValue'  => true,
                'isReviewLinkExpectedToBeIncluded' => true,
                'message'                          => 'Links to product reviews are included in the email body'
            ],
            [
                'configParameterLoadReviewsValue'  => false,
                'isReviewLinkExpectedToBeIncluded' => false,
                'message'                          => 'No links to product reviews are included in the email body'
            ],

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
     * @param bool   $configParameterLoadReviews
     * @param bool   $configParameterIncludeProductReviewLinksInEmail
     * @param bool   $isReviewLinkExpectedToBeIncluded
     * @param string $message
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

    public function dataProviderTestProductReviewLinksAreIncludedInOrderEmailAccordingConfiguration()
    {
        return [
            [
                'configParameterLoadReviewsValue'                 => true,
                'configParameterIncludeProductReviewLinksInEmail' => true,
                'isReviewLinkExpectedToBeIncluded'                => true,
                'message'                                         => 'Links to product reviews are included in the email body'
            ],
            [
                'configParameterLoadReviewsValue'                 => true,
                'configParameterIncludeProductReviewLinksInEmail' => false,
                'isReviewLinkExpectedToBeIncluded'                => false,
                'message'                                         => 'No links to product reviews are included in the email body'
            ],
            [
                'configParameterLoadReviewsValue'                 => false,
                'configParameterIncludeProductReviewLinksInEmail' => true,
                'isReviewLinkExpectedToBeIncluded'                => false,
                'message'                                         => 'No links to product reviews are included in the email body'
            ],
            [
                'configParameterLoadReviewsValue'                 => false,
                'configParameterIncludeProductReviewLinksInEmail' => false,
                'isReviewLinkExpectedToBeIncluded'                => false,
                'message'                                         => 'No links to product reviews are included in the email body'
            ],
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
        $priceStub->method('getPrice')->will($this->returnValue(256));
        $priceStub->method('getBruttoPrice')->will($this->returnValue(8));

        $basketItemStub = $this->getMockBuilder(BasketItem::class)
            ->setMethods(['getPrice', 'getUnitPrice', 'getRegularUnitPrice', 'getTitle'])
            ->getMock();
        $basketItemStub->method('getPrice')->will($this->returnValue($priceStub));
        $basketItemStub->method('getUnitPrice')->will($this->returnValue($priceStub));
        $basketItemStub->method('getRegularUnitPrice')->will($this->returnValue($priceStub));
        $basketItemStub->method('getTitle')->will($this->returnValue("testarticle"));

        // insert test article
        $article = oxNew("oxArticle");
        $article->setId('_testArticleId');
        $article->setId('_testArticleId');
        $article->oxarticles__oxtitle = new oxField();

        $priceStub->setPrice(0);

        $basketStub = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\Basket::class)
            ->setMethods(['getBasketArticles', 'getContents', 'getCosts', 'getBruttoSum',])
            ->getMock();
        $basketStub->method('getBasketArticles')->will($this->returnValue([$article]));
        $basketStub->method('getContents')->will($this->returnValue([$basketItemStub]));
        $basketStub->method('getCosts')->will($this->returnValue($priceStub));
        $basketStub->method('getBruttoSum')->will($this->returnValue(7));

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
        $orderStub->method('getOrderUser')->will($this->returnValue($user));
        $orderStub->method('getBasket')->will($this->returnValue($basketStub));
        $orderStub->method('getPayment')->will($this->returnValue($payment));

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
            ->setMethods(['_sendMail', '_getShop', 'getOrderFileList'])
            ->getMock();
        ;
        $emailStub->method('_sendMail')->will($this->returnValue(true));
        $emailStub->method('_getShop')->will($this->returnValue($shop));
        $emailStub->method('getOrderFileList')->will($this->returnValue(false));

        return $emailStub;
    }

    /**
     * @return string
     */
    private function getTemporaryFilePath(): string
    {
        $temporaryFileHandle = tmpfile();

        return  stream_get_meta_data($temporaryFileHandle)['uri'];
    }

    /**
     * @return mixed
     */
    private function getFileToAttach()
    {
        $fileToAttach = $this->getTemporaryFilePath();
        file_put_contents($fileToAttach, 'test');

        return $fileToAttach;
    }

    /**
     * @param $body
     *
     * @return bool
     */
    private function isReviewLinkIncluded($body): bool
    {
        return false !== strpos($body, 'cl=review');
    }
}
