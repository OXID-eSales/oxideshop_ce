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

class EmailTest extends \OxidTestCase
{
    protected $_oEmail = null;
    protected $_oUser = null;
    protected $_oShop = null;
    protected $_oArticle = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getConfig()->setConfigParam('sTheme', 'azure');

        $this->_oEmail = oxNew("oxEmail");

        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxorderarticles');

        //set default user
        $this->_oUser = oxNew("oxuser");
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
        $this->_oShop = oxNew("oxshop");
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

        // insert test article
        $this->_oArticle = oxNew("oxArticle");
        $this->_oArticle->setId('_testArticleId');
        $this->_oArticle->oxarticles__oxtitle = new oxField('testArticle', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxtitle_1 = new oxField('testArticle_EN', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxartnum = new oxField('123456789', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
        $this->_oArticle->oxarticles__oxshortdesc = new oxField('testArticleDescription', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxprice = new oxField('256', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindactive = new oxField('1', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);

        $this->_oArticle->save();

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
    protected function tearDown()
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
        $oUser = oxNew('oxuser');

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendRegisterEmail"));
        $oEmail->expects($this->once())->method('sendRegisterEmail')->with($this->equalTo($oUser), $this->equalTo(null));

        $oEmail->sendRegisterConfirmEmail($oUser);

        $aViewData = $oEmail->getViewData();
        $this->assertEquals($aViewData["contentident"], "oxregisteraltemail");
        $this->assertEquals($aViewData["contentplainident"], "oxregisterplainaltemail");
    }

    /**
     * When image is taken using getter, it is not included into email by native oxid code
     */
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
        $imageGenerator->expects($this->any())->method('getImagePath')->will($this->returnValue($config->getPictureDir(false) .'generated/product/thumb/185_150_75/nopic.jpg'));
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

    /*
     * Test sending message by smtp
     */
    public function testSendMailBySmtp()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));

        $oEmail->setRecipient($this->_oShop->oxshops__oxorderemail->value, $this->_oShop->oxshops__oxname->value);
        $oEmail->setHost("localhost");
        $oEmail->setMailer("smtp");

        $this->assertTrue($oEmail->send());
        $this->assertEquals('smtp', $oEmail->getMailer());
    }

    /*
     * Test sending mail by mail()
     */
    public function testSendMailByPhpMailFunction()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));

        $oEmail->setMailer('mail');
        $oEmail->setRecipient($this->_oShop->oxshops__oxorderemail->value, $this->_oShop->oxshops__oxname->value);
        $this->assertTrue($oEmail->send());
        $this->assertEquals('mail', $oEmail->getMailer());
    }

    /*
     * Test sending mail by mail() function when sending by smtp fails
     */
    public function testSendMailByPhpMailWhenSmtpFails()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_sendMail', '_sendMailErrorMsg'));
        $oEmail->expects($this->atLeast(2))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->atLeastOnce())->method('_sendMailErrorMsg');

        $oEmail->setRecipient($this->_oShop->oxshops__oxorderemail->value, $this->_oShop->oxshops__oxname->value);
        $oEmail->setHost("localhost");
        $oEmail->setMailer("smtp");

        $this->assertFalse($oEmail->send());
        $this->assertEquals('mail', $oEmail->getMailer());
    }

    /*
     * Test sending error message to shop owner when mailing fails
     */
    public function testSendMailErrorMsgWhenMailingFails()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_sendMailErrorMsg", "getMailer"));
        $oEmail->expects($this->exactly(2))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->exactly(2))->method('_sendMailErrorMsg');
        $oEmail->expects($this->once())->method('getMailer')->will($this->returnValue("smtp"));

        $oEmail->setRecipient($this->_oShop->oxshops__oxorderemail->value, $this->_oShop->oxshops__oxname->value);
        $oEmail->send();
    }

    /*
     * Test set SMTP params
     */
    public function testSetSmtp()
    {
        // just forcing to connect to webserver..
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_isValidSmtpHost'));
        $oEmail->expects($this->once())->method('_isValidSmtpHost')
            ->with($this->equalTo('127.0.0.1'))
            ->will($this->returnValue(true));

        $oEmail->setSmtp($this->_oShop);
        $this->assertEquals('smtp', $oEmail->getMailer());
        $this->assertEquals('127.0.0.1', $oEmail->Host);
        $this->assertEquals('testSmtpUser', $oEmail->Username);
        $this->assertEquals('testSmtpPassword', $oEmail->Password);
    }

    /*
     * Test set SMTP params when no smtp values is set
     */
    public function testSetSmtpWithNoSmtpValues()
    {
        $oEmail = oxNew('oxEmail');

        $this->_oShop->oxshops__oxsmtp = new oxField(null, oxField::T_RAW);
        $oEmail->setSmtp($this->_oShop);
        $this->assertEquals('mail', $oEmail->getMailer());
    }

    /**
     * Test if sending ordering mail to shop owner adds history record into DB
     */
    public function testSendOrderEMailToOwnerAddsHistoryRecord()
    {
        $myDb = oxDb::getDb();

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

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendOrderEmailToOwner($oOrder);
        $this->assertTrue($blRet, 'Order email was not sent to shop owner');

        $this->assertEquals($oEmail->getAltBody(), $myDb->getOne('SELECT oxtext FROM oxremark where oxparentid=' . $myDb->quote($this->_oUser->getId())));
    }

    /*
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

    /*
     * Test sending forgot password when oxemail::send fails
     */
    public function testSendForgotPwdEmailSendingFailed()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("send", "_getShop"));
        $oEmail->expects($this->any())->method('send')->will($this->returnValue(false));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->SendForgotPwdEmail('username@useremail.nl');
        $this->assertEquals(-1, $blRet);
    }

    /*
     * Test sending backup mail to shop owner with attachment
     */
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
        $email->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $email->sendBackupMail($filesToAttach, $filesToAttachDirectory, $emailAddress, $subject, $message, $status, $errors);
        $this->assertTrue($blRet, 'Backup mail was not sent to shop owner');
    }

    /*
     * Test sending backup mail to shop owner with attachment status code
     */
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
        $email->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $email->sendBackupMail($filesToAttach, $filesToAttachDirectories, $emailAddress, $subject, $message, $status, $errors);

        //check status code
        $this->assertEquals(3, $status[0], "Attachment was not icluded im mail");
    }

    /*
     * Test sending backup mail to shop owner with wrong attachment
     * generates error codes
     */
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
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->never())->method('_sendMail');
        $oEmail->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendBackupMail($filesToAttach, $filesToAttachDirectory, $emailAddress, $subject, $message, $status, $errors);
        $this->assertFalse($blRet, 'Bad backup mail was not sent to shop owner');

        // checking error codes
        // 4 - backup mail was not sent
        // 5 - file not found
        $this->assertTrue((in_array(5, $errors[0])), "Wrong attachment was icluded in mail");
        $this->assertTrue((in_array(4, $errors[1])), "Wrong attachment was was sent");
    }

    /*
     * Test sending mail with to multiple users
     */
    public function testSendEmailToMultipleUsers()
    {
        $aTo = array('username@useremail.nl', 'username2@useremail.nl');
        $sSubject = 'testSubject';
        $sBody = 'testBody';

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->once())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendEmail($aTo, $sSubject, $sBody);
        $this->assertTrue($blRet, 'Mail was not sent');

        $aRecipients = $oEmail->getRecipient();
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
        $this->_oArticle->oxarticles__oxstock = new oxField('0', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxstockflag = new oxField('2', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindamount = new oxField('0', oxField::T_RAW);
        $this->_oArticle->save();

        $oBasketItem = $this->getMock(BasketItem::class, array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->once())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendStockReminder($aBasketContents);
        $this->assertTrue($blRet, 'Stock remind mail was not sent');
    }

    /*
     * Test sends reminder email to shop owner when articles amount is more than
     * remind amount
     */
    public function testSendStockReminderWhenStockAmountIsGreaterThanRemindAmount()
    {
        //set params for stock reminder
        $this->_oArticle->oxarticles__oxstock = new oxField('10', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindamount = new oxField('9', oxField::T_RAW);
        $this->_oArticle->save();

        $oBasketItem = $this->getMock(BasketItem::class, array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->never())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendStockReminder($aBasketContents);
        $this->assertFalse($blRet, 'No need to send stock remind mail');
    }

    /*
     * Test sends reminder email to shop owner when remind is off
     */
    public function testSendStockReminderWhenRemindIsOff()
    {
        //set params for stock reminder
        $this->_oArticle->oxarticles__oxremindactive = new oxField('0', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxstock = new oxField('9', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindamount = new oxField('10', oxField::T_RAW);
        $this->_oArticle->save();

        $oBasketItem = $this->getMock(BasketItem::class, array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getShop"));
        $oEmail->expects($this->never())->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $blRet = $oEmail->sendStockReminder($aBasketContents);
        $this->assertFalse($blRet, 'No need to send stock remind mail');
    }

    /*
     * Test including images to mail
     */
    public function testIncludeImages()
    {
        $myConfig = $this->getConfig();
        $sImageDir = $myConfig->getImageDir();

        $oEmail = oxNew('oxEmail');
        $oEmail->setBody("<img src='{$sImageDir}/logo.png'> --- <img src='{$sImageDir}/stars.jpg'>");

        $oEmail->UNITincludeImages(
            $myConfig->getImageDir(),
            $myConfig->getImageUrl(isAdmin()),
            $myConfig->getPictureUrl(null),
            $myConfig->getImageDir(),
            $myConfig->getPictureDir(false)
        );

        $aAttachments = $oEmail->getAttachments();
        $this->assertEquals('logo.png', $aAttachments[0][1]);
        $this->assertEquals('stars.jpg', $aAttachments[1][1]);
    }

    /*
     * Test setting/getting subject
     */
    public function testSetGetSubject()
    {
        $oEmail = oxNew('oxEmail');
        $oEmail->setSubject('testSubject');
        $this->assertEquals('testSubject', $oEmail->getSubject());
    }

    /*
     * Test setting/getting body
     */
    public function testSetGetBody()
    {
        $oEmail = oxNew('oxEmail');
        $oEmail->setBody('testBody');
        $this->assertEquals('testBody', $oEmail->getBody());
    }

    /*
     * Test clearing sid from body
     */
    public function testClearSidFromBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $oEmail = oxNew('oxEmail');

        $oEmail->setBody('testBody index.php?bonusid=111&sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $oEmail->getBody());

        $oEmail->setBody('testBody index.php?bonusid=111&force_sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $oEmail->getBody());

        $oEmail->setBody('testBody index.php?bonusid=111&admin_sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $oEmail->getBody());

        $oEmail->setBody('testBody index.php?bonusid=111&force_admin_sid=123456789 blabla', true);
        $this->assertEquals('testBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $oEmail->getBody());
    }

    /*
     * Test setting/getting alt body
     */
    public function testSetGetAltBody()
    {
        $oEmail = oxNew('oxEmail');

        $oEmail->setAltBody('testAltBody');
        $this->assertEquals('testAltBody', $oEmail->getAltBody());
    }

    /*
     * Test clearing sid from alt body
     */
    public function testClearSidFromAltBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $this->_oEmail->setAltBody('testAltBody index.php?bonusid=111&sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->_oEmail->getAltBody());

        $this->_oEmail->setAltBody('testAltBody index.php?bonusid=111&force_sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->_oEmail->getAltBody());

        $this->_oEmail->setAltBody('testAltBody index.php?bonusid=111&admin_sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->_oEmail->getAltBody());

        $this->_oEmail->setAltBody('testAltBody index.php?bonusid=111&force_admin_sid=123456789 blabla', true);
        $this->assertEquals('testAltBody index.php?bonusid=111&shp=' . $sShopId . ' blabla', $this->_oEmail->getAltBody());
    }

    /*
     * Test eliminate HTML entities from body
     */
    public function testClearHtmlEntitiesFromAltBody()
    {
        $this->_oEmail->setAltBody('testAltBody &amp; &quot; &#039; &lt; &gt;');
        $this->assertEquals('testAltBody & " \' < >', $this->_oEmail->getAltBody());
    }

    /*
     * Test setting/getting mail recipient
     */
    public function testSetGetRecipient()
    {
        $aUser[0][0] = 'testuser@testuser.com';
        $aUser[0][1] = 'testUserName';

        $this->_oEmail->setRecipient($aUser[0][0], $aUser[0][1]);
        $this->assertEquals($aUser, $this->_oEmail->getRecipient());
    }

    /*
     * Test setting recipient with empty email
     */
    public function testSetRecipient_emptyEmail()
    {
        $this->_oEmail->setRecipient("", "");
        $this->assertEquals(array(), $this->_oEmail->getRecipient());
    }

    /*
     * Test setting recipient with empty user name
     */
    public function testSetRecipient_emptyName()
    {
        $this->_oEmail->setRecipient("test@test.lt", "");
        $this->assertEquals(array(array("test@test.lt", "")), $this->_oEmail->getRecipient());
    }

    /*
     * Test setting/getting reply to
     */
    public function testSetGetReplyTo()
    {
        $aUser[0][0] = 'testuser@testuser.com';
        $aUser[0][1] = 'testUserName';

        $this->_oEmail->setReplyTo($aUser[0][0], $aUser[0][1]);
        $this->assertEquals($aUser, $this->_oEmail->getReplyTo());
    }

    /*
     * Test setting reply to with empty value. Should assign deffault reply to address
     */
    public function testSetReplyToWithNoParams()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_getShop"));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $oEmail->setReplyTo();
        $aReplyTo = $oEmail->getReplyTo();

        $this->assertEquals($this->_oShop->oxshops__oxorderemail->value, $aReplyTo[0][0]);
    }

    /*
     * Test setting/getting from field
     */
    public function testSetGetFrom()
    {
        $this->_oEmail->setFrom('testuser@testuser.com', 'testUserName');
        $this->assertEquals('testuser@testuser.com', $this->_oEmail->getFrom());
        $this->assertEquals('testUserName', $this->_oEmail->getFromName());
    }

    /*
     * Test setting/getting charset
     */
    public function testSetCharSet()
    {
        $this->_oEmail->setCharSet('testCharset');
        $this->assertEquals('testCharset', $this->_oEmail->getCharSet());
    }

    /*
     * Test getting charset default charset
     */
    public function testSetDefaultCharSet()
    {
        $this->_oEmail->setCharSet();
        $this->assertEquals(oxRegistry::getLang()->translateString("charset"), $this->_oEmail->getCharSet());
    }

    /*
     * Test setting/getting mailer
     */
    public function testSetGetMailer()
    {
        $this->_oEmail->setMailer('smtp');
        $this->assertEquals('smtp', $this->_oEmail->getMailer());
    }

    /*
     * Test setting/getting host
     */
    public function testSetGetHost()
    {
        $this->_oEmail->setHost('localhost');
        $this->assertEquals('localhost', $this->_oEmail->Host);
    }

    /*
     * Test getting error message
     */
    public function testGetErrorInfo()
    {
        $this->_oEmail->ErrorInfo = 'testErrorMessage';
        $this->assertEquals('testErrorMessage', $this->_oEmail->getErrorInfo());
    }

    /*
     * Test setting mail word wrapping
     */
    public function testSetMailWordWrap()
    {
        $this->_oEmail->setMailWordWrap('500');
        $this->assertEquals('500', $this->_oEmail->WordWrap);
    }


    /*
     * Test getting use inline images property from config
     */
    public function testGetUseInlineImagesFromConfig()
    {
        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $oEmail = oxNew("oxemail");
        $this->assertTrue($oEmail->UNITgetUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", false);
        $oEmail = oxNew("oxemail");
        $this->assertFalse($oEmail->UNITgetUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $oEmail = oxNew("oxemail");
        $this->assertTrue($oEmail->UNITgetUseInlineImages());
    }

    /*
     * Test setting/getting use inline images
     */
    public function testSetGetUseInlineImages()
    {
        $this->_oEmail->setUseInlineImages(true);
        $this->assertTrue($this->_oEmail->UNITgetUseInlineImages());
    }

    /*
     * Test addding attachment to mail
     */
    public function testAddAttachment()
    {
        $myConfig = $this->getConfig();
        $sImageDir = $myConfig->getImageDir() . '/';

        $this->_oEmail->AddAttachment($sImageDir, 'barrcode.gif');
        $aAttachment = $this->_oEmail->getAttachments();

        $this->assertEquals('barrcode.gif', $aAttachment[0][1]);
    }

    /*
     * Test clearing attachments from mail
     */
    public function testClearAttachments()
    {
        $myConfig = $this->getConfig();
        $sImageDir = $myConfig->getImageDir() . '/';

        $this->_oEmail->AddAttachment($sImageDir, 'barrcode.gif');
        $aAttachment = $this->_oEmail->getAttachments();
        $this->assertEquals('barrcode.gif', $aAttachment[0][1]);

        $this->_oEmail->clearAttachments();
        $aAttachment = $this->_oEmail->getAttachments();
        $this->assertEquals(0, count($aAttachment));
    }

    /*
     * Test sending error message to shop owner when mailing by smtp and via mail() fails
     */
    public function testSendMailErrorMsg()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"));
        $oEmail->expects($this->at(0))->method('getRecipient')->will($this->returnValue([1]));
        $oEmail->expects($this->at(1))->method('getMailer')->will($this->returnValue("smtp"));
        $oEmail->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->at(3))->method('_sendMailErrorMsg');
        $oEmail->expects($this->at(4))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->exactly(2))->method('_sendMail');
        $oEmail->expects($this->exactly(2))->method('_sendMailErrorMsg');

        $this->assertFalse($oEmail->send());
    }

    /*
     * Test sending error message to shop owner when only mailing by smtp fails
     */
    public function testSendMailErrorMsg_failsOnlySmtp()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"));
        $oEmail->expects($this->at(0))->method('getRecipient')->will($this->returnValue([1]));
        $oEmail->expects($this->at(1))->method('getMailer')->will($this->returnValue("smtp"));
        $oEmail->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->at(3))->method('_sendMailErrorMsg');
        $oEmail->expects($this->at(4))->method('_sendMail')->will($this->returnValue(true));
        $oEmail->expects($this->exactly(2))->method('_sendMail');
        $oEmail->expects($this->exactly(1))->method('_sendMailErrorMsg');

        $this->assertTrue($oEmail->send());
    }

    /*
     * Test sending error message to shop owner when only mailing by "mail" fails
     */
    public function testSendMailErrorMsg_failsMail()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"));
        $oEmail->expects($this->at(0))->method('getRecipient')->will($this->returnValue([1]));
        $oEmail->expects($this->at(1))->method('getMailer')->will($this->returnValue("mail"));
        $oEmail->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->at(3))->method('_sendMailErrorMsg');
        $oEmail->expects($this->exactly(1))->method('_sendMail');
        $oEmail->expects($this->exactly(1))->method('_sendMailErrorMsg');

        $this->assertFalse($oEmail->send());
    }

    /*
     * Test hook up method
     */
    public function testAddUserInfoOrderEmail()
    {
        $oOrder = oxNew("oxorder");
        //$this->assertEquals( $oOrder, $this->_oEmail->UNITaddUserInfoOrderEmail($oOrder) );
    }

    /*
     * Test hook up method
     */
    public function testAddUserRegisterEmail()
    {
        $this->assertEquals($this->_oUser, $this->_oEmail->UNITaddUserRegisterEmail($this->_oUser));
    }

    /*
     * Test hook up method
     */
    public function testAddForgotPwdEmail()
    {
        $this->assertEquals($this->_oShop, $this->_oEmail->UNITaddForgotPwdEmail($this->_oShop));
    }

    /*
     * Test hook up method
     */
    public function testAddNewsletterDBOptInMail()
    {
        $this->assertEquals($this->_oUser, $this->_oEmail->UNITaddNewsletterDbOptInMail($this->_oUser));
    }

    /*
     * Test clearing mail fields - recipient, reply to, error message
     */
    public function testClearMailer()
    {
        $this->_oEmail->setRecipient('testuser@testuser.com', 'testUser');
        $this->_oEmail->setReplyTo('testuser@testuser.com', 'testUser');
        $this->_oEmail->ErrorInfo = 'testErrorMessage';

        $this->_oEmail->UNITclearMailer();

        $this->assertEquals(array(), $this->_oEmail->getRecipient());
        $this->assertEquals(array(), $this->_oEmail->getReplyTo());
        $this->assertEquals('', $this->_oEmail->getErrorInfo());
    }

    /*
     * Test setting mail From, FromName, SMTP values with default shop
     */
    public function testSetMailParamsWithDefaultShop()
    {
        // no smtp connect
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_isValidSmtpHost', '_getShop'));
        $oEmail->expects($this->any())->method('_isValidSmtpHost')->will($this->returnValue(false));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        //with no params must get default shop values
        $oEmail->UNITsetMailParams();

        $this->assertEquals('orderemail@orderemail.nl', $oEmail->getFrom());
        $this->assertEquals('testShopName', $oEmail->getFromName());
        $this->assertEquals('mail', $oEmail->getMailer());
    }

    /*
     * Test setting mail From, FromName, SMTP values with shop param
     */
    public function testSetMailParamsWithSelectedShop()
    {
        // with smtp connect
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_isValidSmtpHost', '_getShop'));
        $oEmail->expects($this->any())->method('_isValidSmtpHost')->will($this->returnValue(true));
        $oEmail->expects($this->any())->method('_getShop')->will($this->returnValue($this->_oShop));

        $oShop = oxNew("oxshop");
        $oShop->oxshops__oxorderemail = new oxField('orderemail2@orderemail2.nl', oxField::T_RAW);
        $oShop->oxshops__oxname = new oxField('testShopName2', oxField::T_RAW);
        $oShop->oxshops__oxsmtp = new oxField('127.0.0.1', oxField::T_RAW);
        $oShop->oxshops__oxsmtpuser = new oxField('testSmtpUser2', oxField::T_RAW);
        $oShop->oxshops__oxsmtppwd = new oxField('testSmtpPassword2', oxField::T_RAW);

        $oEmail->UNITsetMailParams($oShop);

        $this->assertEquals('orderemail2@orderemail2.nl', $oEmail->getFrom());
        $this->assertEquals('testShopName2', $oEmail->getFromName());
        $this->assertEquals('smtp', $oEmail->getMailer());
        $this->assertEquals('127.0.0.1', $oEmail->Host);
        $this->assertEquals('testSmtpUser2', $oEmail->Username);
        $this->assertEquals('testSmtpPassword2', $oEmail->Password);
    }

    /*
     * Test getting active shop when shop is not set
     */
    public function testGetShopWhenShopIsNotSet()
    {
        $this->assertEquals($this->getConfig()->getActiveShop(), $this->_oEmail->UNITgetShop());
    }

    /*
     * Test getting shop when only shop id is given
     */
    public function testGetShopWithShopId()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $oShop = $this->_oEmail->UNITgetShop(null, $sShopId);
        $this->assertEquals($sShopId, $oShop->getShopId());
        $this->assertEquals(oxRegistry::getLang()->getBaseLanguage(), $oShop->getLanguage());
    }

    /*
     * Test getting shop when only language id is given
     */
    public function testGetShopWithLanguageId()
    {
        $oShop = $this->_oEmail->UNITgetShop(1);
        $this->assertEquals(1, $oShop->getLanguage());
        $this->assertEquals($this->getConfig()->getShopId(), $oShop->getShopId());
    }

    /*
    * Test getting active shop when both language id and shop id is given
    */
    public function testGetShopWithLanguageIdAndShopId()
    {
        $this->_oEmail->setShop($this->_oShop);

        $sShopId = $this->getConfig()->getBaseShopId();

        $oShop = $this->_oEmail->UNITgetShop(1, $sShopId);
        $this->assertEquals(1, $oShop->getLanguage());
        $this->assertEquals($sShopId, $oShop->getShopId());
        $this->assertEquals($this->_oShop, $this->_oEmail->UNITgetShop());
    }

    /*
     * Test setting smtp authentification information
     */
    public function testSetSmtpAuthInfo()
    {
        $this->_oEmail->UNITsetSmtpAuthInfo('testUserName', 'testPassword');

        $this->assertEquals('testUserName', $this->_oEmail->Username);
        $this->assertEquals('testPassword', $this->_oEmail->Password);
    }

    /*
     * Test setting smtp debug
     */
    public function testSetSmtpDebug()
    {
        $this->_oEmail->UNITsetSmtpDebug(true);
        $this->assertTrue($this->_oEmail->SMTPDebug);
    }

    /**
     * Test passing mail body and alt body proccesing through oxoutput
     */
    public function testMakeOutputProcessingInUtf8Mode()
    {
        $this->_oEmail->setBody('testbody 55 €'); //with euro sign
        $this->_oEmail->setAltBody('testaltbody 55 €'); //with euro sign
        $this->_oEmail->UNITmakeOutputProcessing();

        $this->assertEquals('testbody 55 €', $this->_oEmail->getBody());
        $this->assertEquals('testaltbody 55 €', $this->_oEmail->getAltBody());
    }

    public function testHeaderLine()
    {
        $headerLine = $this->_oEmail->headerLine('testName', 'testValue');

        $this->assertContains('testName', $headerLine);
        $this->assertContains('testValue', $headerLine);
    }

    public function testHeaderLineXMailer()
    {
        $this->assertNull($this->_oEmail->headerLine('X-Mailer', 'testVal'));
        $this->assertNull($this->_oEmail->headerLine('x-Mailer', 'testVal'));
        $this->assertNull($this->_oEmail->headerLine('X-Priority', 'testVal'));
    }

    /*
     * Test sending mail when no recipient defined
     */
    public function testSend_noRecipient()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('_sendMail'));
        $oEmail->expects($this->never())->method('_sendMail');
        $oEmail->setRecipient("");
        $this->assertFalse($this->_oEmail->send());
    }

    public function testGetNewsSubsLink()
    {
        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0';
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX'));
    }

    public function testGetNewsSubsLinkWithConfirm()
    {
        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0&amp;confirm=AAAA';
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX', 'AAAA'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX', 'AAAA'));
    }

    public function testSetSmtpProtocol()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('set'));
        $oEmail->expects($this->at(0))->method('set')
            ->with(
                $this->equalTo('SMTPSecure'),
                $this->equalTo('ssl')
            );
        $oEmail->expects($this->at(1))->method('set')
            ->with(
                $this->equalTo('SMTPSecure'),
                $this->equalTo('tls')
            );
        $this->assertEquals("hostname:23", $oEmail->UNITsetSmtpProtocol('ssl://hostname:23'));
        $this->assertEquals("hostname:23", $oEmail->UNITsetSmtpProtocol('tls://hostname:23'));
        $this->assertEquals("ssx://hostname:23", $oEmail->UNITsetSmtpProtocol('ssx://hostname:23'));
    }

    /**
     * Testing the correct recipient (#1964)
     */
    public function testSendOrderEmailToOwnerCorrectSenderReceiver()
    {
        $oSmartyMock = $this->getMock("Smarty", array("fetch"));
        $oSmartyMock->expects($this->any())->method("fetch")->will($this->returnValue(''));

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("_sendMail", "_getSmarty"));
        $oEmail->expects($this->once())->method("_sendMail")->will($this->returnValue(true));
        $oEmail->expects($this->any())->method("_getSmarty")->will($this->returnValue($oSmartyMock));

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");
        //oxOrder mock
        $oOrder = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array("getOrderUser"));
        $oOrder->expects($this->once())->method("getOrderUser")->will($this->returnValue($oUser));

        $oEmail->sendOrderEmailToOwner($oOrder);

        //testing actual From field Value
        $this->assertEquals("order@myoxideshop.com", $oEmail->getFrom());
        //testing actual To field Value
        $aTo = array();
        $aTo[0][0] = 'order@myoxideshop.com';
        $aTo[0][1] = 'order';
        $this->assertEquals($aTo, $oEmail->getRecipient());
    }

    /**
     * Testing the correct recipient (#3586)
     */
    public function testSendSuggestMailCorrectSender()
    {
        $oSmartyMock = $this->getMock("Smarty", array("fetch"));
        $oSmartyMock->expects($this->any())->method("fetch")->will($this->returnValue(''));

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("send", "_getSmarty"));
        $oEmail->expects($this->once())->method("send")->will($this->returnValue(true));
        $oEmail->expects($this->any())->method("_getSmarty")->will($this->returnValue($oSmartyMock));

        // oxParams mock
        $oParams = $this->getMock("oxParams");

        // oxProduct mock
        $oProduct = $this->getMock("oxProduct", array("getId", "getLanguage", "setLanguage", "load", "getLink"));
        $oProduct->expects($this->once())->method("getId")->will($this->returnValue(true));
        $oProduct->expects($this->once())->method("getLanguage")->will($this->returnValue(true));
        $oProduct->expects($this->once())->method("setLanguage")->will($this->returnValue(true));
        $oProduct->expects($this->once())->method("load")->will($this->returnValue(true));
        $oProduct->expects($this->once())->method("getLink")->will($this->returnValue(true));

        $oEmail->sendSuggestMail($oParams, $oProduct);

        //testing actual From field Value
        $this->assertEquals("info@myoxideshop.com", $oEmail->getFrom());
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
