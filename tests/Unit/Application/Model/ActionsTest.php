<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxDb;
use oxField;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use oxRegistry;
use oxTestModules;

/**
 * Testing oxactions class.
 */
class ActionsTest extends \OxidTestCase
{

    /**
     * Contains a object of oxactions()
     *
     * @var object
     */
    protected $_oAction = null;

    /**
     * Contains a object of oxactions()
     *
     * @var object
     */
    public $_oPromo = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oAction = oxNew('oxActions');
        $this->_oAction->oxactions__oxtitle = new oxField("test", oxField::T_RAW);
        $this->_oAction->save();


        $this->_oPromo = oxNew('oxActions');
        $this->_oPromo->assign(
            array(
                 'oxtitle'    => 'title',
                 'oxlongdesc' => 'longdesc',
                 'oxtype'     => 2,
                 'oxsort'     => 1,
                 'oxactive'   => 1,
            )
        );
        $this->_oPromo->save();

        //   oxTestModules::addFunction('oxStr', 'setH($h)', '{oxStr::$_oHandler = $h;}');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->_oAction->delete();
        $this->_oPromo->delete();

        parent::tearDown();
    }

    /**
     * oxActions::addArticle() test case
     * Testing action article adding.
     *
     * @return null
     */
    public function testAddArticle()
    {
        $sArtOxid = 'xxx';
        $this->_oAction->addArticle($sArtOxid);

        $sCheckOxid = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne("select oxid from oxactions2article where oxactionid = '" . $this->_oAction->getId() . "' and oxartid = '$sArtOxid' ");
        if (!$sCheckOxid) {
            $this->fail("fail adding article");
        }
    }

    /**
     * oxActions::removeArticle() test case
     * Testing action article removal.
     *
     * @return null
     */
    public function testRemoveArticle()
    {
        $sArtOxid = 'xxx';
        $this->_oAction->addArticle($sArtOxid);
        $this->assertTrue($this->_oAction->removeArticle($sArtOxid));

        $sCheckOxid = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->getOne("select oxid from oxactions2article where oxactionid = '" . $this->_oAction->getId() . "' and oxartid = '$sArtOxid' ");
        if ($sCheckOxid) {
            $this->fail("fail removing article");
        }
    }

    /**
     * oxActions::removeArticle() test case
     * Testing non existing action article removal.
     *
     * @return null
     */
    public function testRemoveArticleNotExisting()
    {
        $sArtOxid = 'xxx';
        $this->assertFalse($this->_oAction->removeArticle($sArtOxid));
    }

    /**
     * oxActions::removeArticle() test case
     * Trying to delete not existing action, deletion must return false
     *
     * @return null
     */
    public function testDeleteNotExistingAction()
    {
        $sArtOxid = 'xxx';
        $oAction = oxNew('oxActions');
        $this->assertFalse($oAction->delete());
    }

    /**
     * oxActions::delete() test case
     * Deleting existing action, everything must go fine
     *
     * @return null
     */
    public function testDelete()
    {
        $sArtOxid = 'xxx';
        $this->_oAction->addArticle($sArtOxid);
        $this->_oAction->delete();

        $sCheckOxid = oxDb::getDb(oxDB::FETCH_MODE_ASSOC)->GetOne("select oxid from oxactions2article where oxactionid = '" . $this->_oAction->getId() . "'");
        $oAction = oxNew("oxActions");
        if ($sCheckOxid || $oAction->Load($this->sOxId)) {
            $this->fail("fail deleting");
        }
    }

    /**
     * oxActions::getTimeLeft() test case
     * Test if the setted timeleft in database equals what we expect
     *
     * @return null
     */
    public function testGetTimeLeft()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 10));
        $this->assertEquals(10, $this->_oPromo->getTimeLeft());
    }

    /**
     * oxActions::getTimeUntilStart() test case
     * Test if promo starts at the setted time we expect
     *
     * @return null
     */
    public function testGetTimeUntilStart()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $this->_oPromo->oxactions__oxactivefrom = new oxField(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 10));
        $this->assertEquals(10, $this->_oPromo->getTimeUntilStart());
    }

    /**
     * oxActions::start() test case
     * Create a new promo action and check if they are active until our setted date.
     * Save the date into the db and read it with the id of the promo out.
     *
     * @return null
     */
    public function testStart()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $this->_oPromo->oxactions__oxactiveto = new oxField('');
        $this->_oPromo->oxactions__oxactivefrom = new oxField('');
        $this->_oPromo->save();

        $id = $this->_oPromo->getId();
        $this->_oPromo = oxNew('oxActions');
        $this->_oPromo->load($id);

        $this->assertEquals('0000-00-00 00:00:00', $this->_oPromo->oxactions__oxactiveto->value);
        $this->assertEquals('0000-00-00 00:00:00', $this->_oPromo->oxactions__oxactivefrom->value);

        $this->_oPromo->start();
        $iNow = strtotime(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));

        $this->assertEquals(date('Y-m-d H:i:s', $iNow), $this->_oPromo->oxactions__oxactivefrom->value);
        $this->assertEquals('0000-00-00 00:00:00', $this->_oPromo->oxactions__oxactiveto->value);

        $id = $this->_oPromo->getId();
        $this->_oPromo = oxNew('oxActions');
        $this->_oPromo->load($id);
        $this->assertEquals(date('Y-m-d H:i:s', $iNow), $this->_oPromo->oxactions__oxactivefrom->value);
        $this->assertEquals('0000-00-00 00:00:00', $this->_oPromo->oxactions__oxactiveto->value);


        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() + 10));
        $sTo = $this->_oPromo->oxactions__oxactiveto->value;
        $this->_oPromo->save();
        $id = $this->_oPromo->getId();
        $this->_oPromo = oxNew('oxActions');
        $this->_oPromo->load($id);
        $this->assertEquals($sTo, $this->_oPromo->oxactions__oxactiveto->value);

        $this->_oPromo->start();
        $id = $this->_oPromo->getId();
        $this->_oPromo = oxNew('oxActions');
        $this->_oPromo->load($id);
        $this->assertEquals($sTo, $this->_oPromo->oxactions__oxactiveto->value);
    }

    /**
     * oxActions::stop() test case
     * stops the current promo action and test if oxactiveto equals the current date.
     *
     * @return null
     */
    public function testStop()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $this->_oPromo->stop();
        $iNow = strtotime(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));

        $this->assertEquals(date('Y-m-d H:i:s', $iNow), $this->_oPromo->oxactions__oxactiveto->value);

        $id = $this->_oPromo->getId();
        $this->_oPromo = oxNew('oxActions');
        $this->_oPromo->load($id);

        $this->assertEquals(date('Y-m-d H:i:s', $iNow), $this->_oPromo->oxactions__oxactiveto->value);
    }

    /**
     * oxActions::isRunning() test case
     * check if actions are active or not
     *
     * @return null
     */
    public function testIsTestRunning()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . time() . ';}');
        $iNow = strtotime(date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
        $this->_oPromo->oxactions__oxactivefrom = new oxField(date('Y-m-d H:i:s', $iNow - 10));
        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', $iNow + 10));
        $this->assertTrue($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', $iNow - 1));
        $this->assertFalse($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxactivefrom = new oxField(date('Y-m-d H:i:s', $iNow + 1));
        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', $iNow + 10));
        $this->assertFalse($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxactivefrom = new oxField(date('Y-m-d H:i:s', $iNow - 10));
        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', $iNow + 10));
        $this->assertTrue($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxactivefrom = new oxField('0000-00-00 00:00:00');
        $this->assertFalse($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxactivefrom = new oxField(date('Y-m-d H:i:s', $iNow - 10));
        $this->_oPromo->oxactions__oxactiveto = new oxField(date('Y-m-d H:i:s', $iNow + 10));
        $this->assertTrue($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxtype = new oxField(0);
        $this->assertFalse($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxtype = new oxField(1);
        $this->assertFalse($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxtype = new oxField(2);
        $this->assertTrue($this->_oPromo->isRunning());

        $this->_oPromo->oxactions__oxactive = new oxField(0);
        $this->assertFalse($this->_oPromo->isRunning());
    }

    /**
     * oxActions::getLongDesc() test case
     * test getted long description with smarty tags
     *
     * @return null
     */
    public function testGetLongDescTags()
    {
        $this->_oPromo->oxactions__oxlongdesc = new oxField("[{* *}]parsed");
        $this->assertEquals('parsed', $this->_oPromo->getLongDesc());
    }

    /**
     * getLongDesc() test case
     * test returned long description with smarty tags when template regeneration is disabled
     * and template is saved twice.
     *
     * @return null
     */
    public function testGetLongDescTagsWhenTemplateAlreadyGeneratedAndRegenerationDisabled()
    {
        $this->getConfig()->setConfigParam('blCheckTemplates', false);

        $this->_oPromo->oxactions__oxlongdesc = new oxField("[{* *}]generated");
        $this->_oPromo->getLongDesc();

        $this->_oPromo->oxactions__oxlongdesc = new oxField("[{* *}]regenerated");
        $this->assertEquals('regenerated', $this->_oPromo->getLongDesc());
    }

    public function testGetBannerArticle_notAssigned()
    {
        $databaseResult = false;

        $oArticle = $this->getMock('stdclass', array('load'));
        $oArticle->expects($this->never())->method('load');

        oxTestModules::addModuleObject('oxarticle', $oArticle);

        $promotionMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Actions::class, array('fetchBannerArticleId'));
        $promotionMock->expects($this->any())->method('fetchBannerArticleId')->willReturn($databaseResult);
        $promotionMock->setId('promoid');
        $this->assertNull($promotionMock->getBannerArticle());
    }

    public function testGetBannerArticle_notExisting()
    {
        $databaseResult = 'asdabsdbdsf';

        $oArticle = $this->getMock('stdclass', array('load'));
        $oArticle->expects($this->once())->method('load')
            ->with($this->equalTo($databaseResult))
            ->will($this->returnValue(false));

        oxTestModules::addModuleObject('oxarticle', $oArticle);

        $promotionMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Actions::class, array('fetchBannerArticleId'));
        $promotionMock->expects($this->any())->method('fetchBannerArticleId')->willReturn($databaseResult);
        $promotionMock->setId('promoid');
        $this->assertNull($promotionMock->getBannerArticle());
    }

    public function testGetBannerArticle_Existing()
    {
        $databaseResult = '2000';

        $oArticle = $this->getMock('stdclass', array('load'));
        $oArticle->expects($this->once())->method('load')
            ->with($this->equalTo('2000'))
            ->will($this->returnValue(true));

        oxTestModules::addModuleObject('oxarticle', $oArticle);

        $promotionMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Actions::class, array('fetchBannerArticleId'));
        $promotionMock->expects($this->any())->method('fetchBannerArticleId')->willReturn($databaseResult);
        $promotionMock->setId('promoid');
        $oArt = $promotionMock->getBannerArticle();
        $this->assertNotNull($oArt);
        $this->assertSame($oArticle, $oArt);
    }

    /**
     * Helper function for testGetBannerArticle_notAssigned, testGetBannerArticle_notExisting, testGetBannerArticle_Existing
     *
     * @param $valueToReturn
     *
     * @return PHPUnit\Framework\MockObject\MockObject
     */
    private function getBannerArticleMockWithSpecificReturn($valueToReturn)
    {
        $dbMock = $this->getDbObjectMock();
        $dbMock->expects($this->any())
            ->method('getOne')
            ->with($this->equalTo('select oxobjectid from oxobject2action where oxactionid=\'promoid\' and oxclass="oxarticle"'))
            ->will($this->returnValue($valueToReturn));

        return $dbMock;
    }


    /**
     * test
     */
    public function testGetBannerPictureUrl()
    {
        $oPromo = oxNew('oxActions');
        $oPromo->oxactions__oxpic = new oxField("current_de.jpg");
        $oConfig = $this->getConfig();

        $this->assertEquals($oConfig->getPictureUrl("promo/") . "current_de.jpg", $oPromo->getBannerPictureUrl());
    }

    /**
     * test
     */
    public function testGetBannerPictureUrl_noPicture()
    {
        $oPromo = oxNew('oxActions');
        $oConfig = $this->getConfig();

        $this->assertNull($oPromo->getBannerPictureUrl());
    }

    /**
     * test
     */
    public function testGetBannerPictureUrl_pictureNotUploaded()
    {
        $oPromo = oxNew('oxActions');
        $oPromo->oxactions__oxpic = new oxField("noSuchPic.jpg");
        $this->assertEquals($this->getConfig()->getPictureUrl("master/") . "nopic.jpg", $oPromo->getBannerPictureUrl());
    }

    /**
     * test
     */
    public function testGetBannerLink()
    {
        $sUrl = "action-link";

        $oUtilsUrl = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, array('processUrl', 'addShopHost'));
        $oUtilsUrl->expects($this->any())->method('addShopHost')->with($sUrl)->will($this->returnValue('http://with-url/' . $sUrl));
        $oUtilsUrl->expects($this->any())->method('processUrl')->with('http://with-url/' . $sUrl)->will($this->returnValue($sUrl . '/with-params'));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsUrl::class, $oUtilsUrl);

        $oPromo = oxNew('oxActions');
        $oPromo->oxactions__oxlink = new oxField($sUrl);

        $this->assertEquals($sUrl . '/with-params', $oPromo->getBannerLink());
    }

    /**
     * test
     */
    public function testGetBannerLink_noLink()
    {
        $oPromo = oxNew('oxActions');
        $oPromo->oxactions__oxlink = new oxField(null);

        $this->assertNull($oPromo->getBannerLink());
    }

    /**
     * test
     */
    public function testGetBannerLink_noLinkWithAssignedArticle()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getLink'));
        $oArticle->expects($this->once())->method('getLink')
            ->will($this->returnValue("testLinkToArticle"));

        $oPromo = $this->getMock(\OxidEsales\Eshop\Application\Model\Actions::class, array('getBannerArticle'));
        $oPromo->expects($this->once())->method('getBannerArticle')
            ->will($this->returnValue($oArticle));

        $oPromo->oxactions__oxlink = new oxField(null);

        $this->assertEquals("testLinkToArticle", $oPromo->getBannerLink());
    }
}
