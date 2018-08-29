<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing newsletter class.
 */
class NewsletterTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setConfigParam('blEnterNetPrice', false);

        $oUser = oxNew('oxuser');
        $oUser->setId('test');
        $oUser->save();
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $oDB = oxDb::getDb();
        $sDelete = "delete from oxobject2group where oxobjectid='test'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxuser where oxid = 'test' or oxusername = 'test@test.de'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxnewssubscribed where oxfname = 'test' or oxuserid = 'test'";
        $oDB->Execute($sDelete);

        $sDelete = "update oxnewssubscribed set oxunsubscribed='0000-00-00 00:00:00', oxdboptin = '1' where oxuserid = 'oxdefaultadmin'";
        $oDB->Execute($sDelete);
        parent::tearDown();
    }

    /**
     * Test get top start article.
     */
    public function testGetTopStartArticlePE()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $oTestNews = oxNew("NewsLetter");
        $oArticleList = $oTestNews->getTopStartArticle();

        $this->assertEquals('1849', $oArticleList->getId());
    }

    /**
     * Test get top start action articles.
     */
    public function testGetTopStartActionArticlesPE()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $oTestNews = oxNew("NewsLetter");
        $oArticleList = $oTestNews->getTopStartActionArticles();

        $this->assertEquals(1, count($oArticleList));
        $this->assertEquals(89.9, $oArticleList[1849]->getPrice()->getBruttoPrice());
        $this->assertEquals("Bar Butler 6 BOTTLES", $oArticleList[1849]->oxarticles__oxtitle->value);
    }

    /**
     * Test get home country id.
     */
    public function testGetHomeCountryId()
    {
        $oTestNews = oxNew("NewsLetter");
        $this->setConfigParam('aHomeCountry', array('testcountry', 'testcountry1'));
        $sCountryId = $oTestNews->getHomeCountryId();

        $this->assertEquals('testcountry', $sCountryId);
    }

    /**
     * Test get newsletter status after remove.
     */
    public function testGetNewsletterStatusAfterRemoveme()
    {
        $oTestNews = oxNew("NewsLetter");
        $this->setRequestParameter('uid', 'test');
        $oTestNews->removeme();
        $iStatus = $oTestNews->getNewsletterStatus();

        $this->assertEquals(3, $iStatus);
    }

    public function testRemovemeGroupsRemoved()
    {
        $oUser = oxNew('oxuser');
        $oUser->setId('testAddMe');
        $oUser->oxuser__oxusername = new oxField('test@addme.com', oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField('salt', oxField::T_RAW);
        $oUser->save();

        $oTestNews = oxNew("NewsLetter");
        $this->setRequestParameter('uid', 'testAddMe');
        $this->setRequestParameter('confirm', md5('test@addme.comsalt'));

        $oTestNews->addme();
        $oUserGroups = $oUser->getUserGroups();
        $this->assertTrue(isset($oUserGroups['oxidnewsletter']), 'user should be subscribed for newsletter group.');

        $oTestNews->removeme();
        $oUser2 = oxNew('oxuser');
        $oUser2->load('testAddMe');
        $oUserGroups = $oUser2->getUserGroups();
        $this->assertFalse(isset($oUserGroups['oxidnewsletter']), 'user should be unsubscribed from newsletter group.');
    }

    public function testGetNewsletterStatusAfterAddme()
    {
        $oUser = oxNew('oxuser');
        $oUser->setId('testAddMe');
        $oUser->oxuser__oxusername = new oxField('test@addme.com', oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField('salt', oxField::T_RAW);
        $oUser->save();

        $oTestNews = oxNew("NewsLetter");
        $this->setRequestParameter('uid', 'testAddMe');
        $this->setRequestParameter('confirm', md5('test@addme.comsalt'));
        $oTestNews->addme();
        $iStatus = $oTestNews->getNewsletterStatus();

        $this->assertEquals(2, $iStatus);

        $oUser->delete();
    }

    /**
     * Test get newsletter status after send.
     */
    public function testGetNewsletterStatusAfterSend()
    {
        oxTestModules::addFunction("oxemail", "send", "{return true;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return true;}");

        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $aParams['oxuser__oxlname'] = 'test';
        $aParams['oxuser__oxcountryid'] = 'test';
        $this->setRequestParameter('editval', $aParams);
        $this->setRequestParameter('subscribeStatus', 1);
        $oTestNews->send();
        $iStatus = $oTestNews->getNewsletterStatus();

        $this->assertEquals(1, $iStatus);
    }

    /**
     * Test get newsletter status after send.
     */
    public function testGetNewsletterStatusAfterSendNoDbOptIn()
    {
        oxTestModules::addFunction("oxemail", "send", "{return true;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return true;}");
        $this->setConfigParam('blOrderOptInEmail', 0);

        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $aParams['oxuser__oxlname'] = 'test';
        $aParams['oxuser__oxcountryid'] = 'test';
        $this->setRequestParameter('editval', $aParams);
        $this->setRequestParameter('subscribeStatus', 1);
        $oTestNews->send();
        $iStatus = $oTestNews->getNewsletterStatus();

        $this->assertEquals(2, $iStatus);
    }

    /**
     * Test get newsletter status after send if user exists.
     *
     * (FS#2406)
     */
    public function testGetNewsletterStatusAfterSendIfUserExist()
    {
        oxTestModules::addFunction("oxemail", "send", "{return true;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return true;}");

        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@oxid-esales.com';
        $aParams['oxuser__oxfname'] = 'test';
        $this->setRequestParameter('editval', $aParams);
        $this->setRequestParameter('subscribeStatus', 1);
        $oTestNews->send();
        $iStatus = $oTestNews->getNewsletterStatus();

        $this->assertEquals(1, $iStatus);
    }

    /**
     * Test if new user was created after subscribe.
     */
    public function testNewUserWasCreatedAfterSubscribe()
    {
        oxTestModules::addFunction("oxemail", "send", "{return true;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return true;}");

        $oDB = oxDb::getDb();

        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $this->setRequestParameter('editval', $aParams);
        $this->setRequestParameter('subscribeStatus', 1);
        $oTestNews->send();

        $sSql = "select oxusername from oxuser where oxusername='test@test.de'";
        $sUserName = $oDB->getOne($sSql);
        $this->assertEquals('test@test.de', $sUserName);
    }

    /**
     * Test if user was added to newsletter list.
     */
    public function testUserWasAddedToNewsletterList()
    {
        oxTestModules::addFunction("oxemail", "send", "{return true;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return true;}");

        $oDB = oxDb::getDb();

        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $aParams['oxuser__oxlname'] = 'test';
        $this->setRequestParameter('editval', $aParams);
        $this->setRequestParameter('subscribeStatus', 1);
        $oTestNews->send();

        $sSql = "select oxdboptin from oxnewssubscribed where oxfname = 'test' AND oxlname = 'test'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals('2', $sStatus);
    }

    /**
     * Test if user unsubscribed list.
     */
    public function testUserUnsubscribe()
    {
        oxTestModules::addFunction("oxemail", "send", "{return true;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return true;}");

        $oDB = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
        $sSql = "select oxusername from oxuser where oxusername='test@test.de'";
        $sUserName = $oDB->getOne($sSql);
        $this->assertFalse($sUserName);

        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $aParams['oxuser__oxlname'] = 'test';
        $this->setRequestParameter('subscribeStatus', 1);
        $this->setRequestParameter('editval', $aParams);
        $oTestNews->send();

        $sSql = "select oxdboptin from oxnewssubscribed where oxfname = 'test' AND oxlname = 'test'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals('2', $sStatus);

        //unsubscribing
        $this->setRequestParameter('subscribeStatus', null);
        $oTestNews->send();

        $sSql = "select oxdboptin from oxnewssubscribed where oxfname = 'test' AND oxlname = 'test'";
        $sStatus = $oDB->getOne($sSql);
        $this->assertEquals('0', $sStatus);
    }

    /**
     * Test get filled registration paremeters.
     */
    public function testGetRegParamsFill()
    {
        $oTestNews = oxNew("NewsLetter");
        $aParams = array();
        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $aParams['oxuser__oxlname'] = 'test';
        $aParams['oxuser__oxcountryid'] = 'test';
        $this->setRequestParameter('editval', $aParams);
        $oTestNews->fill();
        $aRegParams = $oTestNews->getRegParams();

        $this->assertEquals($aParams, $aRegParams);
    }

    /**
     * Test remove rom admin.
     *
     * FS#2525, FS#2522
     */
    public function testRemovemeForAdmin()
    {
        $oTestNews = oxNew("NewsLetter");
        $this->setRequestParameter('uid', 'oxdefaultadmin');
        $oTestNews->removeme();
        $iStatus = $oTestNews->getNewsletterStatus();

        $this->assertEquals(3, $iStatus);
        $this->assertEquals('malladmin', oxDb::getDb()->getOne('select oxrights from oxuser where oxid="oxdefaultadmin"'));
    }

    /**
     * Testing view render method
     */
    public function testRender()
    {
        $oTestNews = $this->getMock(\OxidEsales\Eshop\Application\Controller\NewsletterController::class, array('getTopStartArticle', 'getTopStartActionArticles', 'getHomeCountryId', 'getNewsletterStatus', 'getRegParams'));
        $oTestNews->expects($this->once())->method('getTopStartArticle')->will($this->returnValue(1));
        $oTestNews->expects($this->once())->method('getTopStartActionArticles')->will($this->returnValue(2));
        $oTestNews->expects($this->once())->method('getHomeCountryId')->will($this->returnValue(3));
        $oTestNews->expects($this->once())->method('getNewsletterStatus')->will($this->returnValue(4));
        $oTestNews->expects($this->once())->method('getRegParams')->will($this->returnValue(5));

        $this->assertEquals('page/info/newsletter.tpl', $oTestNews->render());

        $this->assertEquals('1', $oTestNews->getTopStartArticle());
        $this->assertEquals('2', $oTestNews->getTopStartActionArticles());
        $this->assertEquals('3', $oTestNews->getHomeCountryId());
        $this->assertEquals('4', $oTestNews->getNewsletterStatus());
        $this->assertEquals('5', $oTestNews->getRegParams());
    }

    /**
     * Testing error messages on worng input
     */
    public function testSubscribingWithWrongInputs()
    {
        oxRegistry::getLang()->setBaseLanguage(1);
        $oTestNews = oxNew("NewsLetter");
        $aParams = array();

        // no email
        $aParams['oxuser__oxusername'] = '';
        $this->setRequestParameter('editval', $aParams);

        $oTestNews->send();
        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErrors['default'][0]);
        $this->assertEquals(oxRegistry::getLang()->translateString('ERROR_MESSAGE_COMPLETE_FIELDS_CORRECTLY'), $oErr->getOxMessage());

        //reseting errors
        $this->getSession()->setVariable('Errors', null);

        // wrong email
        $aParams['oxuser__oxusername'] = 'aaaaaa@';
        $this->setRequestParameter('editval', $aParams);
        $oTestNews->send();

        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErrors['default'][0]);
        $this->assertEquals(oxRegistry::getLang()->translateString('MESSAGE_INVALID_EMAIL'), $oErr->getOxMessage());
    }

    /**
     * Testing error message when sending email about subscribtion fails
     */
    public function testNewsletterErrorOnFailedEmailSending()
    {
        oxTestModules::addFunction("oxemail", "send", "{return false;}");
        oxTestModules::addFunction("oxemail", "sendNewsletterDbOptInMail", "{return false;}");

        oxRegistry::getLang()->setBaseLanguage(1);
        $oTestNews = oxNew("NewsLetter");
        $aParams = array();

        $aParams['oxuser__oxusername'] = 'test@test.de';
        $aParams['oxuser__oxfname'] = 'test';
        $this->setRequestParameter('subscribeStatus', 1);
        $this->setRequestParameter('editval', $aParams);
        $oTestNews->send();

        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErrors['default'][0]);
        $this->assertEquals(oxRegistry::getLang()->translateString('MESSAGE_NOT_ABLE_TO_SEND_EMAIL'), $oErr->getOxMessage());
    }

    /**
     * Testing newsLetter::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oNewsLetter = oxNew('Newsletter');
        $aResults = array();
        $aResult = array();

        $aResult["title"] = "Lassen Sie sich informieren!";
        $aResult["link"] = $oNewsLetter->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oNewsLetter->getBreadCrumb());
    }

    /**
     * Test get title.
     */
    public function testGetTitle_KeepSubscribed()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\NewsletterController::class, array('getNewsletterStatus'));
        $oView->expects($this->any())->method('getNewsletterStatus')->will($this->returnValue(null));

        $this->assertEquals(oxRegistry::getLang()->translateString('STAY_INFORMED', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }

    /**
     * Test get title.
     */
    public function testGetTitle_NeedsConfirmation()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\NewsletterController::class, array('getNewsletterStatus'));
        $oView->expects($this->any())->method('getNewsletterStatus')->will($this->returnValue(1));

        $this->assertEquals(oxRegistry::getLang()->translateString('MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }

    /**
     * Test get title, when password update screen is shown
     */
    public function testGetTitle_SuccessfulSubscription()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\NewsletterController::class, array('getNewsletterStatus'));
        $oView->expects($this->any())->method('getNewsletterStatus')->will($this->returnValue(2));

        $this->assertEquals(oxRegistry::getLang()->translateString('MESSAGE_NEWSLETTER_CONGRATULATIONS', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }

    /**
     * Test get title, after successful password update
     */
    public function testGetTitle_RemovedSubscription()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\NewsletterController::class, array('getNewsletterStatus'));
        $oView->expects($this->any())->method('getNewsletterStatus')->will($this->returnValue(3));

        $this->assertEquals(oxRegistry::getLang()->translateString('SUCCESS', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }
}
