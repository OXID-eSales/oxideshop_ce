<?php
use OxidEsales\Eshop\Core\DiContainer;
use OxidEsales\Eshop\Core\MailClient;
use OxidEsales\Eshop\Core\MailClientInterface;

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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

class dummyMailClient implements MailClientInterface
{
    public function __call($method, $params)
    {

    }
}

class Unit_Core_oxemailTest extends OxidTestCase
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

        $this->_oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new MailClient])
            ->getMock();

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

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRegisterEmail'])
            ->getMock();

        $oEmail
            ->expects($this->once())
            ->method('sendRegisterEmail')
            ->with($this->equalTo($oUser), $this->equalTo(null));
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
        oxTestModules::addFunction("oxUtilsObject", "generateUId", "{ return 'xxx'; }");
        $config = $this->getConfig();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('1351');
        $sImgUrl = $oArticle->getThumbnailUrl();
        $iImgFile = basename($sImgUrl);
        $sTitle = $oArticle->oxarticles__oxtitle->value;
        $imageDirectory = $config->getImageDir();

        $imageGenerator = $this->getMock('oxDynImgGenerator', array('getImagePath'));
        $imageGenerator->expects($this->any())->method('getImagePath')->will($this->returnValue($config->getPictureDir(false) .'generated/product/thumb/185_150_75/nopic.jpg'));
        oxTestModules::addModuleObject('oxDynImgGenerator', $imageGenerator);

        $sBody = '<img src="' . $imageDirectory . 'stars.jpg" border="0" hspace="0" vspace="0" alt="stars" align="texttop">';
        $sBody .= '<img src="' . $config->getImageUrl() . 'logo.png" border="0" hspace="0" vspace="0" alt="logo" align="texttop">';
        $sBody .= '<img src="' . $sImgUrl . '" border="0" hspace="0" vspace="0" alt="' . $sTitle . '" align="texttop">';

        $sGenBody = '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="stars" align="texttop">';
        $sGenBody .= '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="logo" align="texttop">';
        $sGenBody .= '<img src="cid:xxx" border="0" hspace="0" vspace="0" alt="' . $sTitle . '" align="texttop">';

        /** @var oxEmail|PHPUnit_Framework_MockObject_MockObject $oEmail */
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBody', 'addEmbeddedImage', 'setBody'])
            ->getMock();

        $oEmail->expects($this->once())->method('getBody')->will($this->returnValue($sBody));
        $oEmail->expects($this->at(1))->method('addEmbeddedImage')->with($this->equalTo($imageDirectory . 'stars.jpg'), $this->equalTo('xxx'), $this->equalTo("image"), $this->equalTo("base64"), $this->equalTo('image/jpeg'))->will($this->returnValue(true));
        $oEmail->expects($this->at(2))->method('addEmbeddedImage')->with($this->equalTo($imageDirectory . 'logo.png'), $this->equalTo('xxx'), $this->equalTo("image"), $this->equalTo("base64"), $this->equalTo('image/png'))->will($this->returnValue(true));
        $oEmail->expects($this->at(3))->method('addEmbeddedImage')->with($this->equalTo($config->getPictureDir(false) . 'generated/product/thumb/185_150_75/' . $iImgFile), $this->equalTo('xxx'), $this->equalTo("image"), $this->equalTo("base64"), $this->equalTo('image/jpeg'))->will($this->returnValue(true));
        $oEmail->expects($this->once())->method('setBody')->with($this->equalTo($sGenBody));

        $oEmail->_includeImages(
            $imageDirectory, $config->getImageUrl(false), $config->getPictureUrl(null),
            $imageDirectory, $config->getPictureDir(false)
        );
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

        $oOrder = $this->getMock('oxOrder', array("getOrderUser", "getBasket", "getPayment"));
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

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->disableOriginalConstructor()
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["send", "_getShop"])
            ->getMock();
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
        $this->markTestSkipped("Too lazy");

        $fileToAttach = $this->createFile('alternativeFile.php', '');
        $filesToAttach = array(basename($fileToAttach));
        $filesToAttachDirectory = dirname($fileToAttach);
        $emailAddress = 'username@useremail.nl';
        $subject = 'testBackupMailSubject';
        $message = 'testBackupMailMessage';
        $status = array();
        $errors = array();

        /** @var oxEmail|PHPUnit_Framework_MockObject_MockObject $email */
        $email = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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
        $this->markTestSkipped("Too lazy");

        $fileToAttach = $this->createFile('alternativeFile.php', '');
        $filesToAttach = array(basename($fileToAttach));
        $filesToAttachDirectories = dirname($fileToAttach);
        $emailAddress = 'username@useremail.nl';
        $subject = 'testBackupMailSubject';
        $message = 'testBackupMailMessage';
        $status = array();
        $errors = array();

        /** @var oxEmail|PHPUnit_Framework_MockObject_MockObject $email */
        $email = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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

        /** @var oxEmail|PHPUnit_Framework_MockObject_MockObject $email */
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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
     * #1276: If product is "If out out stock, offline" and remaining stock is ordered, "Shp offline" error is shown in Order step 5
     */
    public function testSendStockReminderIfStockFlag2()
    {
        //set params for stock reminder
        $this->_oArticle->oxarticles__oxstock = new oxField('0', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxstockflag = new oxField('2', oxField::T_RAW);
        $this->_oArticle->oxarticles__oxremindamount = new oxField('0', oxField::T_RAW);
        $this->_oArticle->save();

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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

        $oBasketItem = $this->getMock('oxbasketitem', array('getArticle', 'getProductId'));
        $oBasketItem->expects($this->any())->method('getArticle')->will($this->returnValue($this->_oArticle));
        $oBasketItem->expects($this->any())->method('getProductId')->will($this->returnValue('_testArticleId'));

        $aBasketContents[] = $oBasketItem;

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getShop"])
            ->getMock();
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

        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);
        $oEmail->setBody("<img src='{$sImageDir}/logo.png'> --- <img src='{$sImageDir}/stars.jpg'>");

        $oEmail->UNITincludeImages(
            $myConfig->getImageDir(), $myConfig->getImageUrl(isAdmin()),
            $myConfig->getPictureUrl(null), $myConfig->getImageDir(),
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
        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);
        $oEmail->setSubject('testSubject');
        $this->assertEquals('testSubject', $oEmail->getSubject());
    }

    /*
     * Test setting/getting body
     */
    public function testSetGetBody()
    {
        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);
        $oEmail->setBody('testBody');
        $this->assertEquals('testBody', $oEmail->getBody());
    }

    /*
     * Test clearing sid from body
     */
    public function testClearSidFromBody()
    {
        $sShopId = $this->getConfig()->getBaseShopId();

        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);

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
        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);

        $oEmail->setAltBody('testAltBody');
        $this->assertEquals('testAltBody', $oEmail->getAltBody());
    }

    /*
     * Test clearing sid from alt body
     */
    public function testClearSidFromAltBody()
    {
        $this->markTestSkipped("Too lazy!");

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
        $this->markTestSkipped("Too lazy!");

        $this->_oEmail->setAltBody('testAltBody &amp; &quot; &#039; &lt; &gt;');
        $this->assertEquals('testAltBody & " \' < >', $this->_oEmail->getAltBody());
    }

    /*
     * Test setting/getting mail recipient
     */
    public function testSetGetRecipient()
    {
        $this->markTestSkipped("Too lazy!");

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
        $this->markTestSkipped("Too lazy!");

        $this->_oEmail->setRecipient("", "");
        $this->assertEquals(array(), $this->_oEmail->getRecipient());
    }

    /*
     * Test setting recipient with empty user name
     */
    public function testSetRecipient_emptyName()
    {
        $this->markTestSkipped("Too lazy!");

        $this->_oEmail->setRecipient("test@test.lt", "");
        $this->assertEquals(array(array("test@test.lt", "")), $this->_oEmail->getRecipient());
    }

    /*
     * Test setting/getting reply to
     */
    public function testSetGetReplyTo()
    {
        $this->markTestSkipped("Too lazy!");

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
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_getShop"])
            ->getMock();
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
        $this->markTestSkipped("Too lazy");

        $this->_oEmail->setFrom('testuser@testuser.com', 'testUserName');
        $this->assertEquals('testuser@testuser.com', $this->_oEmail->getFrom());
        $this->assertEquals('testUserName', $this->_oEmail->getFromName());
    }

    /*
     * Test getting use inline images property from config
     */
    public function testGetUseInlineImagesFromConfig()
    {
        $this->markTestSkipped("Too lazy");

        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);
        $this->assertTrue($oEmail->UNITgetUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", false);
        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);
        $this->assertFalse($oEmail->UNITgetUseInlineImages());

        $this->getConfig()->setConfigParam("blInlineImgEmail", true);
        $oEmail = DiContainer::getInstance()->get(DiContainer::CONTAINER_CORE_MAILER);
        $this->assertTrue($oEmail->UNITgetUseInlineImages());
    }

    /*
     * Test setting/getting use inline images
     */
    public function testSetGetUseInlineImages()
    {
        $this->markTestSkipped("Too lazy");

        $this->_oEmail->setUseInlineImages(true);
        $this->assertTrue($this->_oEmail->UNITgetUseInlineImages());
    }

    /*
     * Test addding attachment to mail
     */
    public function testAddAttachment()
    {
        $this->markTestSkipped("Too lazy");

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
        $this->markTestSkipped("Too lazy");

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
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"])
            ->getMock();
        $oEmail->expects($this->at(0))->method('getRecipient')->will($this->returnValue(1));
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
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"])
            ->getMock();
        $oEmail->expects($this->at(0))->method('getRecipient')->will($this->returnValue(1));
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
        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["getRecipient", "getMailer", "_sendMail", "_sendMailErrorMsg"])
            ->getMock();
        $oEmail->expects($this->at(0))->method('getRecipient')->will($this->returnValue(1));
        $oEmail->expects($this->at(1))->method('getMailer')->will($this->returnValue("mail"));
        $oEmail->expects($this->at(2))->method('_sendMail')->will($this->returnValue(false));
        $oEmail->expects($this->at(3))->method('_sendMailErrorMsg');
        $oEmail->expects($this->exactly(1))->method('_sendMail');
        $oEmail->expects($this->exactly(1))->method('_sendMailErrorMsg');

        $this->assertFalse($oEmail->send());
    }

    /*
     * Test clearing mail fields - recipient, reply to, error message
     */
    public function testClearMailer()
    {
        $this->markTestSkipped("Too lazy");

        $this->_oEmail->setRecipient('testuser@testuser.com', 'testUser');
        $this->_oEmail->setReplyTo('testuser@testuser.com', 'testUser');
        $this->_oEmail->ErrorInfo = 'testErrorMessage';

        $this->_oEmail->UNITclearMailer();

        $this->assertEquals(array(), $this->_oEmail->getRecipient());
        $this->assertEquals(array(), $this->_oEmail->getReplyTo());
        $this->assertEquals('', $this->_oEmail->getErrorInfo());
    }

    /**
     * Test passing mail body and alt body proccesing through oxoutput
     */
    public function testMakeOutputProcessingInUtf8Mode()
    {
        $this->markTestSkipped("Too lazy");

        $this->setConfigParam('iUtfMode', 1);
        $this->_oEmail->setBody('testbody 55 €'); //with euro sign
        $this->_oEmail->setAltBody('testaltbody 55 €'); //with euro sign
        $this->_oEmail->UNITmakeOutputProcessing();

        $this->assertEquals('testbody 55 €', $this->_oEmail->getBody());
        $this->assertEquals('testaltbody 55 €', $this->_oEmail->getAltBody());
    }

    public function testHeaderLine()
    {
        $this->markTestSkipped("Too lazy");

        $this->assertEquals("testName: testVar" . PHP_EOL, $this->_oEmail->headerLine('testName', 'testVar'));
    }

    public function testHeaderLineXMailer()
    {
        $this->assertNull($this->_oEmail->headerLine('X-Mailer', 'testVal'));
        $this->assertNull($this->_oEmail->headerLine('x-Mailer', 'testVal'));
        $this->assertNull($this->_oEmail->headerLine('X-Priority', 'testVal'));
    }

    public function testGetNewsSubsLink()
    {
        $this->markTestSkipped("Too lazy");

        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0';
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX'));
    }

    public function testGetNewsSubsLinkWithConfirm()
    {
        $this->markTestSkipped("Too lazy");

        $sUrl = $this->getConfig()->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=XXXX&amp;lang=0&amp;confirm=AAAA';
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX', 'AAAA'));
        $oActShop = $this->getConfig()->getActiveShop();
        $oActShop->setLanguage(1);
        $this->assertEquals($sUrl, $this->_oEmail->UNITgetNewsSubsLink('XXXX', 'AAAA'));
    }

    /**
     * Testing the correct recipient (#1964)
     */
    public function testSendOrderEmailToOwnerCorrectSenderReceiver()
    {
        $this->markTestSkipped("Too lazy");

        $oSmartyMock = $this->getMock("Smarty", array("fetch"));
        $oSmartyMock->expects($this->any())->method("fetch")->will($this->returnValue(true));

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["_sendMail", "_getSmarty"])
            ->getMock();
        $oEmail->expects($this->once())->method("_sendMail")->will($this->returnValue(true));
        $oEmail->expects($this->any())->method("_getSmarty")->will($this->returnValue($oSmartyMock));

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");
        //oxOrder mock
        $oOrder = $this->getMock("oxOrder", array("getOrderUser"));
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
        $this->markTestSkipped("Too lazy");

        $oSmartyMock = $this->getMock("Smarty", array("fetch"));
        $oSmartyMock->expects($this->any())->method("fetch")->will($this->returnValue(true));

        $oEmail = $this->getMockBuilder(oxEmail::class)
            ->setConstructorArgs([new dummyMailClient()])
            ->setMethods(["send", "_getSmarty"])
            ->getMock();
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

}

