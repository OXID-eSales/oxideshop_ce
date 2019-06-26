<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use Exception;
use oxException;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use \oxNewsLetter;
use \oxEmail;
use \oxDb;

class modOxNewsLetter extends oxNewsLetter
{
    public function getSmarty()
    {
        return $this->_oSmarty;
    }

    public function getUser()
    {
        return $this->_oUser;
    }

    public function getShop()
    {
        return $this->_oShop;
    }
}

class modEmailOxNewsLetter extends oxEmail
{
    public $Timeout = 2;

    public function sendNewsletterMail($oNews, $oUser, $sSubject = null)
    {
        return false;
    }
}

class modEmailOxNewsLetter2 extends oxEmail
{
    public $Timeout = 2;

    public function sendNewsletterMail($oNews, $oUser, $sSubject = null)
    {
        return true;
    }
}

/*
 * Dummy class for newsletter subject test.
 *
 */
class modEmailOxNewsLetterSubject extends oxEmail
{
    public $Timeout = 2;

    public function sendNewsletterMail($oNews, $oUser, $sSubject = null)
    {
        throw new oxException($sSubject);
    }
}

class oxnewsletterForUnit_oxnewsletterTest extends oxnewsletter
{
    public function setNonPublicVar($sVarName, $sVarValue)
    {
        $this->$sVarName = $sVarValue;
    }

    public function getNonPublicVar($sVarName)
    {
        return $this->$sVarName;
    }
}

class NewsletterTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $oDB = oxDb::getDb();

        $shopId = ShopIdCalculator::BASE_SHOP_ID;
        if ($this->getConfig()->getEdition() === 'EE') {
            $shopId = 1;
        }

        $sInsert = "INSERT INTO `oxnewsletter` VALUES ( 'newstest', '{$shopId}', 'Test', 'TestHTML', 'TestPlain', 'TestSubject', NOW() )";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxobject2group` VALUES ( 'test', '{$shopId}', 'newstest', 'oxidnewcustomer', NOW() )";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxorder` SET
      `OXID` = '9a94569819f6c7368.72892345',
      `OXSHOPID` = '{$shopId}',
      `OXUSERID` = 'oxdefaultadmin',
      `OXORDERDATE` = '2006-11-26 13:59:34',
      `OXORDERNR` = '8',
      `OXBILLCOMPANY` = 'Ihr Firmenname',
      `OXBILLEMAIL` = '" . oxADMIN_LOGIN . "',
      `OXBILLFNAME` = 'Hans',
      `OXBILLLNAME` = 'Mustermann',
      `OXBILLSTREET` = 'Musterstr.',
      `OXBILLSTREETNR` = '10',
      `OXBILLCITY` = 'Musterstadt',
      `OXBILLCOUNTRYID` = 'a7c40f631fc920687.20179984',
      `OXBILLZIP` = '79098',
      `OXBILLFON` = '0800 1234567',
      `OXBILLFAX` = '0800 1234567',
      `OXBILLSAL` = 'Herr',
      `OXPAYMENTTYPE` = 'oxidcreditcard',
      `OXTOTALNETSUM` = 3.9,
      `OXTOTALBRUTSUM` = 0,
      `OXTOTALORDERSUM` = 20.9,
      `OXREMARK` = 'Hier können Sie uns noch etwas mitteilen.',
      `OXVOUCHERDISCOUNT` = 0,
      `OXCURRENCY` = 'EUR',
      `OXCURRATE` = 1,
      `OXFOLDER` = 'Neu',
      `OXTRANSSTATUS` = 'OK',
      `OXLANG` = 0,
      `OXINVOICENR` = 0,
      `OXDELTYPE` = 'oxidstandard'
    ";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxorderarticles` SET
        `OXID` = '9a9456981a6530fe2.51471234',
        `OXORDERID` = '9a94569819f6c7368.72892345',
        `OXAMOUNT` = 1,
        `OXARTID` = '2080',
        `OXARTNUM` = '2080',
        `OXTITLE` = 'Eiswürfel HERZ',
        `OXSHORTDESC` = 'Das Original aus Filmen wie Eis am Stil & Co.',
        `OXSELVARIANT` = '',
        `OXNETPRICE` = 68.88,
        `OXBRUTPRICE` = 68.88,
        `OXVATPRICE` = 0,
        `OXVAT` = 0,
        `OXPERSPARAM` = '',
        `OXPRICE` = 79.9,
        `OXBPRICE` = 0,
        `OXNPRICE` = 89.9,
        `OXWRAPID` = '',
        `OXEXTURL` = '',
        `OXURLDESC` = '',
        `OXURLIMG` = '',
        `OXTHUMB` = '0/1964_th.jpg',
        `OXPIC1` = '1/1964_p1.jpg',
        `OXPIC2` = '2/nopic.jpg',
        `OXPIC3` = '3/nopic.jpg',
        `OXPIC4` = '4/nopic.jpg',
        `OXPIC5` = '5/nopic.jpg',
        `OXWEIGHT` = 0,
        `OXSTOCK` = 0,
        `OXDELIVERY` = 0x303030302d30302d3030,
        `OXINSERT` = 0x303030302d30302d3030,
        `OXTIMESTAMP` = 0x323030352d30372d32382030303a30303a3030,
        `OXLENGTH` = 0,
        `OXWIDTH` = 0,
        `OXHEIGHT` = 0,
        `OXFILE` = '',
        `OXSEARCHKEYS` = '',
        `OXTEMPLATE` = '',
        `OXQUESTIONEMAIL` = '',
        `OXISSEARCH` = 1,
        `OXFOLDER` = '',
        `OXSUBCLASS` = '',
        `OXSTORNO` = '',
        `OXORDERSHOPID` = '{$shopId}',
        `OXISBUNDLE` = 0";

        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxactions2article` VALUES ('d8842e3ca1c35e146.46512345', '{$shopId}', 'oxnewsletter', '1351', 0, NOW())";
        $oDB->Execute($sInsert);

        $sInsert = "INSERT INTO `oxactions2article` VALUES ('d8842e3ca27489886.81509876', '{$shopId}', 'oxnewsletter', '2000', 1, NOW())";
        $oDB->Execute($sInsert);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDB = oxDb::getDb();
        $sDelete = "delete from oxnewsletter where oxid='newstest'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxobject2group where oxobjectid='newstest'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxorder where oxid = '9a94569819f6c7368.72892345'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxorderarticles where oxorderid = '9a94569819f6c7368.72892345'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxactions2article where oxactionid = 'oxnewsletter'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxuser where oxusername = 'test'";
        $oDB->Execute($sDelete);

        $sDelete = "delete from oxnewssubscribed where oxuserid = 'test'";
        $oDB->Execute($sDelete);

        $sSql = "update oxnewssubscribed set oxemailfailed = '0' where oxuserid = 'oxdefaultadmin' ";
        $oDB->Execute($sSql);

        oxRemClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modEmailOxNewsLetter::class);
        oxRemClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modEmailOxNewsLetter2::class);

        parent::tearDown();
    }

    /**
     * Testing if deletion removes all db records
     */
    public function testDelete()
    {
        $oTestNews = oxNew("oxNewsLetter");
        $this->assertEquals(true, $oTestNews->delete('newstest'));

        $oDB = oxDb::getDb();

        $sSelect = 'select * from oxobject2group where oxobjectid="newstest"';
        $this->assertEquals(false, $oDB->getOne($sSelect));

        $sSelect = 'select * from oxnewsletter where oxid="newstest"';
        $this->assertEquals(false, $oDB->getOne($sSelect));
    }

    public function testDeleteLoadedNews()
    {
        $oTestNews = oxNew("oxNewsLetter");
        $oTestNews->load('newstest');
        $this->assertEquals($oTestNews->delete('newstest'), true);

        $oDB = oxDb::getDb();

        $sSelect = 'select count(*) from oxobject2group ';
        $sSelect .= 'where oxobjectid="newstest"';
        $this->assertEquals($oDB->getOne($sSelect), 0);

        $sSelect = 'select count(*) from oxnewsletter ';
        $sSelect .= 'where oxid="newstest"';
        $this->assertEquals($oDB->getOne($sSelect), 0);
    }

    public function testDeleteNotExistingNewsletter()
    {
        $oTestNews = oxNew("oxNewsLetter");
        $oTestNews->load('111111');
        try {
            $this->assertEquals($oTestNews->delete(), true);
        } catch (Exception $e) {
            return; // OK !
        }

        $this->fail();
    }

    public function test_setParams()
    {
        $myConfig = $this->getConfig();

        // preparing input
        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oTestNews = $this->getMock(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\oxnewsletterForUnit_oxnewsletterTest::class, array('_assignProducts'));
        $oTestNews->expects($this->once())->method('_assignProducts')->with($this->isInstanceOf('\OxidEsales\EshopCommunity\Application\Controller\FrontendController'), $this->equalTo(true));
        $oTestNews->load('newstest');
        $oTestNews->setNonPublicVar('_oUser', $oUser);

        // executing
        $oTestNews->UNITsetParams(true);

        //testing
        $this->assertEquals($oTestNews->getPlainText(), 'TestPlain');
        $this->assertEquals($oTestNews->getHtmlText(), 'TestHTML');
    }

    /**
     * Testing newsletter groups getter
     */
    public function testGetGroups()
    {
        $oTestNews = $this->getProxyClass('oxNewsLetter');
        $oTestNews->load('newstest');
        $oGroups = $oTestNews->getGroups();
        foreach ($oGroups as $sInGroup) {
            if (strpos($sInGroup->getId(), 'oxidnewcustomer') == 0) {
                $blGroup = true;
            }
        }
        $this->assertEquals($blGroup, true);

        // testing cache
        $this->assertEquals($oGroups, $oTestNews->getNonPublicVar('_oGroups'));
    }

    /**
     * Testing newsletter groups getter
     */
    public function testGetGroupsIfGroupIsSet()
    {
        $oTestNews = $this->getProxyClass('oxNewsLetter');
        $oTestNews->load('newstest');
        $oGroup = oxNew('oxgroups');
        $oGroup->load('oxidcustomer');
        $oTestNews->setNonPublicVar('_oGroups', array($oGroup));
        $oGroups = $oTestNews->getGroups();
        foreach ($oGroups as $sInGroup) {
            if (strpos($sInGroup->getId(), 'oxidcustomer') == 0) {
                $blGroup = true;
            }
        }
        $this->assertEquals($blGroup, true);
    }

    /**
     * Testing email preparer
     */
    public function testPrepare()
    {
        $oTestNews = $this->getMock(
            'oxNewsLetter',
            array('isAdmin',
                  'setAdminMode',
                  '_setUser',
                  '_setParams')
        );
        $oTestNews->expects($this->once())->method('isAdmin')->will($this->returnValue('false'));
        $oTestNews->expects($this->exactly(2))->method('setAdminMode');
        $oTestNews->expects($this->once())->method('_setUser')->with($this->equalTo('xxx'));
        $oTestNews->expects($this->once())->method('_setParams')->with($this->equalTo(false));

        // testing
        $oTestNews->prepare('xxx', false);
    }

    /**
     * Testing user setter
     */
    // setting by id
    public function testSetUserId()
    {
        $oTestNews = new modOxNewsLetter();
        $oTestNews->UNITsetUser('oxdefaultadmin');
        $oUser = $oTestNews->getUser();
        $this->assertEquals($oUser->oxuser__oxid->value, 'oxdefaultadmin');
    }

    // setting by object
    public function testSetUserObject()
    {
        $oTestNews = new modOxNewsLetter();
        $oUser = oxNew("oxUser");
        $oUser->load('oxdefaultadmin');
        $oTestNews->UNITsetUser($oUser);
        $oNewsUser = $oTestNews->getUser();
        $this->assertEquals($oNewsUser->oxuser__oxid->value, 'oxdefaultadmin');
    }

    // setting wrong id
    public function testSetUserWrongId()
    {
        $oTestNews = new modOxNewsLetter();
        $oTestNews->UNITsetUser('123');
        $oUser = $oTestNews->getUser();
        $this->assertEquals($oUser->oxuser__oxid->value, null);
    }

    /**
     * Testing smarty variables
     */
    public function testAssignProducts()
    {
        $myConfig = $this->getConfig();

        $oView = oxNew('oxubase');

        $oUser = oxNew('oxuser');
        $oUser->load('oxdefaultadmin');

        $oTestNews = $this->getProxyClass('oxnewsLetter');
        $oTestNews->setNonPublicVar('_oUser', $oUser);
        $oTestNews->UNITassignProducts($oView, true);

        $oArtList = $oView->getViewDataElement('articlelist');
        $oSimilarArticlesList = $oView->getViewDataElement('simlist');

        // test the view data
        $this->assertNotNull($oArtList);
        $this->assertEquals(2, $oArtList->count());

        $this->assertNotNull($oSimilarArticlesList);
        $this->assertEquals(2, $oSimilarArticlesList->count());

        $this->assertNotNull($oView->getViewDataElement('simarticle0'));
        $this->assertNotNull($oView->getViewDataElement('simarticle1'));
    }

    public function testSendMail()
    {
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modEmailOxNewsLetter2::class, 'oxEmail');

        $oTestNews = oxNew("oxNewsLetter");
        if (!$oTestNews->load('oxidnewsletter')) {
            $this->fail('can not load news');
        }

        $oTestNews->UNITsetUser('oxdefaultadmin');
        $blMailWasSent = $oTestNews->send();
        $this->assertTrue($blMailWasSent);
    }

    /**
     * oxNewsletter::send - Testing for correct subject value.
     *
     * @return null
     */
    public function testSendMail_Subject()
    {
        oxAddClassModule(\OxidEsales\EshopCommunity\Tests\Unit\Application\Model\modEmailOxNewsLetterSubject::class, 'oxEmail');

        $oTestNews = oxNew("oxNewsLetter");
        if (!$oTestNews->load('oxidnewsletter')) {
            $this->fail('can not load news');
        }

        $oTestNews->oxnewsletter__oxsubject->value = "TestSubject";

        $this->expectException('oxException');
        $this->expectExceptionMessage("TestSubject");

        $oTestNews->UNITsetUser('oxdefaultadmin');
        $blMailWasSent = $oTestNews->send();
    }

    public function testSendMailAndFail()
    {
        $oTestNews = oxNew("oxNewsLetter");
        if (!$oTestNews->load('oxidnewsletter')) {
            $this->fail('can not load news');
        }

        $oTestNews->UNITsetUser('oxdefaultadmin');
        $blMailWasSent = $oTestNews->send();
        $this->assertFalse($blMailWasSent);
    }

    /**
     * Testing how newsletter is saved and loaded
     */
    public function testSetFieldData()
    {
        // keep all this formatted like it is now
        $sHtmlData = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OXID eSales Newsletter</title>
<style media="screen" type="text/css">';
        $sHtmlData = str_replace("\r", "", $sHtmlData);
        $sHtmlData = str_replace("\n", "\r\n", $sHtmlData);
        $sPlainData = 'OXID eSales Newsletter

Hallo, [{ $myuser->oxuser__oxsal->value|oxmultilangsal }] [{ $myuser->oxuser__oxfname->getRawValue() }] [{ $myuser->oxuser__oxlname->getRawValue() }],';
        $sPlainData = str_replace("\r", "", $sPlainData);
        $sPlainData = str_replace("\n", "\r\n", $sPlainData);

        $oNewsletter = oxNew("oxNewsLetter");
        $oNewsletter->load('oxidnewsletter');

        $this->assertEquals($sHtmlData, substr($oNewsletter->oxnewsletter__oxtemplate->value, 0, strlen($sHtmlData)));
        $this->assertEquals($sPlainData, substr($oNewsletter->oxnewsletter__oxplaintemplate->value, 0, strlen($sPlainData)));
    }
}
