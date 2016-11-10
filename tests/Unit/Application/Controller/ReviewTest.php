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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Model\ListModel;
use \Exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class ReviewTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $myDB = oxDb::getDB();
        $sShopId = $this->getConfig()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "2000", "testlist", "test" ) ';
        $myDB->Execute($sQ);
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();
        $sDelete = 'delete from oxrecommlists where oxid like "test%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxobject2list where oxlistid like "test%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxreviews where oxobjectid like "test%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxratings where oxobjectid like "test%" ';
        $myDB->execute($sDelete);

        $this->cleanUpTable('oxreviews');

        parent::tearDown();
    }

    /**
     * oxUbase::getReviewUserHash() test case
     *
     * @return string
     */
    public function testGetReviewUserHash()
    {
        $this->setRequestParameter('reviewuserhash', 'testHash');

        $oView = oxNew('review');
        $this->assertEquals('testHash', $oView->getReviewUserHash());
    }

    /**
     * review::getReviewUser() test case
     *
     * @return
     */
    public function testGetReviewUser()
    {
        $oUser = oxNew("oxuser");
        $sHash = $oUser->getReviewUserHash("oxdefaultadmin");

        $oReview = $this->getMock("review", array("getReviewUserHash"));
        $oReview->expects($this->once())->method('getReviewUserHash')->will($this->returnValue($sHash));
        $oUser = $oReview->getReviewUser();

        $this->assertNotNull($oUser);
        $this->assertTrue($oUser instanceof user);
        $this->assertEquals("oxdefaultadmin", $oUser->getId());
    }

    public function testRender()
    {
        $this->getConfig()->setConfigParam("bl_perfLoadReviews", true);

        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        $oProd1 = oxNew('oxArticle');
        $oProd2 = oxNew('oxArticle');

        $oProducts = oxNew('oxArticleList');
        $oProducts->offsetSet(0, $oProd1);
        $oProducts->offsetSet(1, $oProd2);

        $oRecommList = $this->getMock("oxRecommList", array("getArtCount"));
        $oRecommList->expects($this->atLeastOnce())->method('getArtCount')->will($this->returnValue(10));

        $oReview = $this->getMock("review", array("getActiveRecommList", "getActiveRecommItems", "getReviewUser"));
        $oReview->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oReview->expects($this->once())->method('getActiveRecommItems')->will($this->returnValue($oProducts));
        $oReview->expects($this->once())->method('getReviewUser')->will($this->returnValue(true));

        $oReview->render();
    }

    public function testRender_NoUser()
    {
        $this->getConfig()->setConfigParam("bl_perfLoadReviews", true);

        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        $oProd1 = oxNew('oxArticle');
        $oProd2 = oxNew('oxArticle');

        $oReview = $this->getMock("review", array("getActiveRecommList", "getActiveRecommItems", "getReviewUser"));
        $oReview->expects($this->once())->method('getReviewUser');
        $oReview->expects($this->never())->method('getActiveRecommList');
        $oReview->expects($this->never())->method('getActiveRecommItems');
        $oReview->render();
    }

    public function testRender_reviewDisabled()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam("bl_perfLoadReviews", false);

        $oUtils = $this->getMock('oxUtils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo($oConfig->getShopHomeURL()));
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oReview = oxNew('Review');

        $oReview->render();
    }

    public function testGetActiveRecommItemsNoRecommList()
    {
        $oReview = $this->getMock("review", array("getActiveRecommList",));
        $this->assertFalse($oReview->getActiveRecommItems());
    }

    public function testGetActiveRecommItems()
    {
        $oProd1 = oxNew('oxArticle');
        $oProd2 = oxNew('oxArticle');

        $oProd3 = oxNew('oxArticle');
        $oProd3->text = 'testArtDescription';
        $oProd4 = oxNew('oxArticle');
        $oProd4->text = 'testArtDescription';

        $oProducts = oxNew('oxArticleList');
        $oProducts->offsetSet(0, $oProd1);
        $oProducts->offsetSet(1, $oProd2);

        $oTestProducts = oxNew('oxArticleList');
        $oTestProducts->offsetSet(0, $oProd3);
        $oTestProducts->offsetSet(1, $oProd4);

        $oRecommList = $this->getMock("oxRecommList", array("getArticles", "getArtDescription"));
        $oRecommList->expects($this->atLeastOnce())->method('getArticles')->will($this->returnValue($oProducts));
        $oRecommList->expects($this->atLeastOnce())->method('getArtDescription')->will($this->returnValue('testArtDescription'));

        $oReview = $this->getMock("review", array("getActiveRecommList",));
        $oReview->expects($this->atLeastOnce())->method('getActiveRecommList')->will($this->returnValue($oRecommList));

        $this->assertEquals(2, $oReview->getActiveRecommItems()->count());
    }

    public function testGetReviewSendStatus()
    {
        $oReview = oxNew('review');
        $this->assertNull($oReview->getReviewSendStatus());
    }

    public function testGetActiveType()
    {
        $oReview = $this->getMock('review', array('getProduct'));
        $oReview->expects($this->once())->method('getProduct')->will($this->returnValue(true));

        $this->assertEquals('oxarticle', $oReview->UNITgetActiveType());

        $oReview = $this->getMock('review', array('getProduct', 'getActiveRecommList'));
        $oReview->expects($this->once())->method('getProduct')->will($this->returnValue(false));
        $oReview->expects($this->once())->method('getActiveRecommList')->will($this->returnValue(true));

        $this->assertEquals('oxrecommlist', $oReview->UNITgetActiveType());
    }

    public function testGetViewId()
    {
        $oReview = oxNew('Review');
        $oUbase = oxNew('oxUBase');

        $this->assertEquals($oUbase->getViewId(), $oReview->getViewId());
    }

    public function testInit()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $this->setRequestParameter('recommid', 'testRecommId');
        $this->setRequestParameter('anid', '1126');

        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId('testRecommId');

        $oReview = $this->getMock("review", array("getActiveRecommList"));
        $oReview->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oUbase = oxNew('oxUBase');

        $this->assertEquals($oUbase->init(), $oReview->init());
    }

    public function testInitNoRecommList()
    {
        $this->setRequestParameter('recommid', 'testRecommId');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( 'testInitNoRecommListException' ); }");

        $oReview = $this->getMock("review", array("getActiveRecommList"));
        $oReview->expects($this->once())->method('getActiveRecommList')->will($this->returnValue(false));

        try {
            $oReview->init();
        } catch (Exception $oExcp) {
            $this->assertEquals('testInitNoRecommListException', $oExcp->getMessage());

            return;
        }
        $this->fail("error in testInitNoRecommList");
    }

    public function testSaveReview()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->once())->method('addToRatingAverage');

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var Review|PHPUnit_Framework_MockObject_MockObject $oReview */
        $oReview = $this->getMock('review', array('getReviewUser', '_getActiveObject', 'canAcceptFormData', "_getActiveType"));
        $oReview->expects($this->once())->method('getReviewUser')->will($this->returnValue($oUser));
        $oReview->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(true));
        $oReview->expects($this->once())->method('_getActiveObject')->will($this->returnValue($oProduct));
        $oReview->expects($this->once())->method('_getActiveType')->will($this->returnValue("oxarticle"));
        $oReview->saveReview();

        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfUserNotSet()
    {
        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        /** @var Review|PHPUnit_Framework_MockObject_MockObject $oReview */
        $oReview = $this->getMock('review', array('getReviewUser', '_getActiveObject', 'canAcceptFormData', "_getActiveType"));
        $oReview->expects($this->once())->method('getReviewUser')->will($this->returnValue(false));
        $oReview->expects($this->never())->method('canAcceptFormData');
        $oReview->expects($this->never())->method('_getActiveObject');
        $oReview->expects($this->never())->method('_getActiveType');
        $oReview->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfOnlyReviewIsSet()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', null);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->never())->method('addToRatingAverage');

        /** @var Review|PHPUnit_Framework_MockObject_MockObject $oReview */
        $oReview = $this->getMock('review', array('getReviewUser', '_getActiveObject', 'canAcceptFormData', "_getActiveType"));
        $oReview->expects($this->once())->method('getReviewUser')->will($this->returnValue($oUser));
        $oReview->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(true));
        $oReview->expects($this->once())->method('_getActiveObject')->will($this->returnValue($oProduct));
        $oReview->expects($this->once())->method('_getActiveType')->will($this->returnValue("oxarticle"));
        $oReview->saveReview();

        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select 1 from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfWrongRating()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', 6);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->never())->method('addToRatingAverage');

        /** @var Review|PHPUnit_Framework_MockObject_MockObject $oReview */
        $oReview = $this->getMock('review', array('getReviewUser', '_getActiveObject', 'canAcceptFormData', "_getActiveType"));
        $oReview->expects($this->once())->method('getReviewUser')->will($this->returnValue($oUser));
        $oReview->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(true));
        $oReview->expects($this->once())->method('_getActiveObject')->will($this->returnValue($oProduct));
        $oReview->expects($this->once())->method('_getActiveType')->will($this->returnValue("oxarticle"));
        $oReview->saveReview();

        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfOnlyRatingIsSet()
    {
        $this->setRequestParameter('rvw_txt', null);
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $oSession */
        $oSession = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        oxRegistry::set('oxSession', $oSession);

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var oxArticle|PHPUnit_Framework_MockObject_MockObject $oProduct */
        $oProduct = $this->getMock('oxarticle', array('getId', 'addToRatingAverage'));
        $oProduct->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oProduct->expects($this->once())->method('addToRatingAverage');

        /** @var Review|PHPUnit_Framework_MockObject_MockObject $oReview */
        $oReview = $this->getMock('review', array('getReviewUser', '_getActiveObject', 'canAcceptFormData', "_getActiveType"));
        $oReview->expects($this->once())->method('getReviewUser')->will($this->returnValue($oUser));
        $oReview->expects($this->once())->method('canAcceptFormData')->will($this->returnValue(true));
        $oReview->expects($this->once())->method('_getActiveObject')->will($this->returnValue($oProduct));
        $oReview->expects($this->once())->method('_getActiveType')->will($this->returnValue("oxarticle"));
        $oReview->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertEquals("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testGetDynUrlParams()
    {
        $this->setRequestParameter('cnid', 'testcnid');
        $this->setRequestParameter('anid', 'testanid');
        $this->setRequestParameter('listtype', 'testlisttype');
        $this->setRequestParameter('recommid', 'testrecommid');

        $oUbase = oxNew('oxUBase');
        $sDynParams = $oUbase->getDynUrlParams();
        $sDynParams .= "&amp;cnid=testcnid&amp;anid=testanid&amp;listtype=testlisttype&amp;recommid=testrecommid";

        $oReview = oxNew('review');
        $this->assertEquals($sDynParams, $oReview->getDynUrlParams());
    }

    public function testCanRateForRecomm()
    {
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oReview = $this->getMock("review", array("_getActiveObject", "getReviewUser", "_getActiveType"));
        $oReview->expects($this->any())->method('_getActiveObject')->will($this->returnValue($oRecommtList));
        $oReview->expects($this->any())->method('getReviewUser')->will($this->returnValue($oUser));
        $oReview->expects($this->any())->method('_getActiveType')->will($this->returnValue('oxarticle'));

        $this->assertTrue($oReview->canRate());
    }

    public function testCanRateForArticle()
    {
        $this->getSession()->setVariable('reviewuserid', 'oxdefaultadmin');

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oReview = $this->getMock("review", array("_getActiveObject", "getReviewUser", "_getActiveType"));
        $oReview->expects($this->any())->method('_getActiveObject')->will($this->returnValue($oArticle));
        $oReview->expects($this->any())->method('getReviewUser')->will($this->returnValue($oUser));
        $oReview->expects($this->any())->method('_getActiveType')->will($this->returnValue('oxarticle'));


        $this->assertTrue($oReview->canRate());
    }

    public function testGetReviewsForRecomm()
    {
        $oRecommtList = $this->getMock("oxRecommList", array("getReviews"));
        $oRecommtList->expects($this->any())->method('getReviews')->will($this->returnValue("testReviews"));

        $oReview = $this->getMock("review", array("_getActiveObject"));
        $oReview->expects($this->any())->method('_getActiveObject')->will($this->returnValue($oRecommtList));

        $this->assertEquals("testReviews", $oReview->getReviews());
    }

    public function testGetReviewsForArticle()
    {
        oxTestModules::addFunction('oxreview', 'loadList', '{$o=new oxlist();$o[0]="asd";$o->args=$aA;return $o;}');
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');
        $oReview->setNonPublicVar("_oProduct", $oArticle);
        $oResult = $oReview->getReviews();
        $this->assertEquals("oxarticle", $oResult->args[0]);
        $this->assertEquals("2000", current($oResult->args[1]));
    }

    public function testGetProduct()
    {
        $this->setRequestParameter('anid', '2000');
        $oReview = oxNew('review');

        $this->assertEquals('2000', $oReview->getProduct()->getId());
    }

    public function testGetActiveObjectIfProduct()
    {
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');
        $oReview->setNonPublicVar("_oProduct", $oArticle);

        $this->assertEquals('2000', $oReview->UNITgetActiveObject()->getId());
    }

    public function testGetActiveObjectIfRecommList()
    {
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->setId('testid');

        $oReview = $this->getMock("review", array("getProduct", "getActiveRecommList"));
        $oReview->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommtList));

        $this->assertEquals('testid', $oReview->UNITgetActiveObject()->getId());
    }

    public function testGetCrossSelling()
    {
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");
        $oReview->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oReview->getCrossSelling();
        $this->assertTrue($oList instanceof ListModel);
        $iCount = $this->getTestConfig()->getShopEdition() == 'EE'? 3 : 2;
        $this->assertEquals($iCount, $oList->count());
    }

    public function testGetSimilarProducts()
    {
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");
        $oReview->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oReview->getSimilarProducts();
        $iCount = $this->getTestConfig()->getShopEdition() == 'EE'? 4 : 5;
        $this->assertEquals($iCount, count($oList));
    }

    public function testGetRecommList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oRevew = $this->getProxyClass("review");
        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');
        $oRevew->setNonPublicVar("_oProduct", $oArticle);
        $aLists = $oRevew->getRecommList();
        $this->assertEquals(1, $aLists->count());
        $this->assertEquals('testlist', $aLists['testlist']->getId());
        $this->assertTrue(in_array($aLists['testlist']->getFirstArticle()->getId(), array('2000')));
    }

    public function testGetAdditionalParams()
    {
        $this->setRequestParameter('searchparam', 'testsearchparam');
        $this->setRequestParameter('recommid', 'testlist');
        $this->setRequestParameter('reviewuserid', 'oxdefaultadmin');

        $oUbase = oxNew('oxUBase');
        $sParams = $oUbase->getAdditionalParams();

        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId("testlist");

        $oReview = $this->getMock('review', array('getActiveRecommList'));
        $oReview->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $this->assertEquals($sParams . '&amp;recommid=testlist', $oReview->getAdditionalParams());
    }

    public function testGetPageNavigation()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $this->setRequestParameter('reviewuserid', 'oxdefaultadmin');
        $oReview = $this->getMock('review', array('generatePageNavigation'));
        $oReview->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $oReview->getActiveRecommList();
        $this->assertEquals('aaa', $oReview->getPageNavigation());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     *
     * @return null
     */
    public function testGetActiveRecommListIfOff()
    {
        $oCfg = $this->getMock("stdClass", array("getShowListmania"));
        $oCfg->expects($this->once())->method('getShowListmania')->will($this->returnValue(false));

        $oRecomm = $this->getMock("review", array("getViewConfig"));
        $oRecomm->expects($this->once())->method('getViewConfig')->will($this->returnValue($oCfg));

        $this->assertSame(false, $oRecomm->getActiveRecommList());
    }
}
