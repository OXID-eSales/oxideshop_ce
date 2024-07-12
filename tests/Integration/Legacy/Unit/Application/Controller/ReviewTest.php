<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Core\Model\ListModel;
use \Exception;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class ReviewTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
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
     */
    protected function tearDown(): void
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
        $this->assertSame('testHash', $oView->getReviewUserHash());
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

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getReviewUserHash"]);
        $oReview->expects($this->once())->method('getReviewUserHash')->willReturn($sHash);
        $oUser = $oReview->getReviewUser();

        $this->assertNotNull($oUser);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\User::class, $oUser);
        $this->assertSame("oxdefaultadmin", $oUser->getId());
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

        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["getArtCount"]);
        $oRecommList->expects($this->atLeastOnce())->method('getArtCount')->willReturn(10);

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveRecommList", "getActiveRecommItems", "getReviewUser"]);
        $oReview->expects($this->once())->method('getActiveRecommList')->willReturn($oRecommList);
        $oReview->expects($this->once())->method('getActiveRecommItems')->willReturn($oProducts);
        $oReview->expects($this->once())->method('getReviewUser')->willReturn(true);

        $oReview->render();
    }

    public function testRender_NoUser()
    {
        $this->getConfig()->setConfigParam("bl_perfLoadReviews", true);

        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        oxNew('oxArticle');
        oxNew('oxArticle');

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveRecommList", "getActiveRecommItems", "getReviewUser"]);
        $oReview->expects($this->once())->method('getReviewUser');
        $oReview->expects($this->never())->method('getActiveRecommList');
        $oReview->expects($this->never())->method('getActiveRecommItems');
        $oReview->render();
    }

    public function testRender_reviewDisabled()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam("bl_perfLoadReviews", false);

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['redirect']);
        $oUtils->expects($this->once())->method('redirect')->with($oConfig->getShopHomeURL());
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oReview = oxNew('Review');

        $oReview->render();
    }

    public function testGetActiveRecommItemsNoRecommList()
    {
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveRecommList"]);
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

        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["getArticles", "getArtDescription"]);
        $oRecommList->expects($this->atLeastOnce())->method('getArticles')->willReturn($oProducts);
        $oRecommList->expects($this->atLeastOnce())->method('getArtDescription')->willReturn('testArtDescription');

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveRecommList"]);
        $oReview->expects($this->atLeastOnce())->method('getActiveRecommList')->willReturn($oRecommList);

        $this->assertSame(2, $oReview->getActiveRecommItems()->count());
    }

    public function testGetReviewSendStatus()
    {
        $oReview = oxNew('review');
        $this->assertNull($oReview->getReviewSendStatus());
    }

    public function testGetActiveType()
    {
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getProduct']);
        $oReview->expects($this->once())->method('getProduct')->willReturn(true);

        $this->assertSame('oxarticle', $oReview->getActiveType());

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getProduct', 'getActiveRecommList']);
        $oReview->expects($this->once())->method('getProduct')->willReturn(false);
        $oReview->expects($this->once())->method('getActiveRecommList')->willReturn(true);

        $this->assertSame('oxrecommlist', $oReview->getActiveType());
    }

    public function testGetViewId()
    {
        $oReview = oxNew('Review');
        $oUbase = oxNew('oxUBase');

        $this->assertEquals($oUbase->getViewId(), $oReview->getViewId());
    }

    public function testInit()
    {
        $this->setRequestParameter('recommid', 'testRecommId');
        $this->setRequestParameter('anid', '1126');

        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId('testRecommId');

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveRecommList"]);
        $oReview->method('getActiveRecommList')->willReturn($oRecommList);
        $oUbase = oxNew('oxUBase');

        $this->assertEquals($oUbase->init(), $oReview->init());
    }

    public function testInitNoRecommList()
    {
        $this->setRequestParameter('recommid', 'testRecommId');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception( 'testInitNoRecommListException' ); }");

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveRecommList"]);
        $oReview->expects($this->once())->method('getActiveRecommList')->willReturn(false);

        try {
            $oReview->init();
        } catch (Exception $exception) {
            $this->assertSame('testInitNoRecommListException', $exception->getMessage());

            return;
        }

        $this->fail("error in testInitNoRecommList");
    }

    public function testSaveReview()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->expects($this->once())->method('addToRatingAverage');

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var Review|PHPUnit\Framework\MockObject\MockObject $oReview */
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getReviewUser', 'getActiveObject', 'canAcceptFormData', "getActiveType"]);
        $oReview->expects($this->once())->method('getReviewUser')->willReturn($oUser);
        $oReview->expects($this->once())->method('canAcceptFormData')->willReturn(true);
        $oReview->expects($this->once())->method('getActiveObject')->willReturn($oProduct);
        $oReview->expects($this->once())->method('getActiveType')->willReturn("oxarticle");
        $oReview->saveReview();

        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfUserNotSet()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Review|PHPUnit\Framework\MockObject\MockObject $oReview */
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getReviewUser', 'getActiveObject', 'canAcceptFormData', "getActiveType"]);
        $oReview->expects($this->once())->method('getReviewUser')->willReturn(false);
        $oReview->expects($this->never())->method('canAcceptFormData');
        $oReview->expects($this->never())->method('getActiveObject');
        $oReview->expects($this->never())->method('getActiveType');
        $oReview->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfOnlyReviewIsSet()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', null);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->expects($this->never())->method('addToRatingAverage');

        /** @var Review|PHPUnit\Framework\MockObject\MockObject $oReview */
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getReviewUser', 'getActiveObject', 'canAcceptFormData', "getActiveType"]);
        $oReview->expects($this->once())->method('getReviewUser')->willReturn($oUser);
        $oReview->expects($this->once())->method('canAcceptFormData')->willReturn(true);
        $oReview->expects($this->once())->method('getActiveObject')->willReturn($oProduct);
        $oReview->expects($this->once())->method('getActiveType')->willReturn("oxarticle");
        $oReview->saveReview();

        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select 1 from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfWrongRating()
    {
        $this->setRequestParameter('rvw_txt', 'review test');
        $this->setRequestParameter('artrating', 6);
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->expects($this->never())->method('addToRatingAverage');

        /** @var Review|PHPUnit\Framework\MockObject\MockObject $oReview */
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getReviewUser', 'getActiveObject', 'canAcceptFormData', "getActiveType"]);
        $oReview->expects($this->once())->method('getReviewUser')->willReturn($oUser);
        $oReview->expects($this->once())->method('canAcceptFormData')->willReturn(true);
        $oReview->expects($this->once())->method('getActiveObject')->willReturn($oProduct);
        $oReview->expects($this->once())->method('getActiveType')->willReturn("oxarticle");
        $oReview->saveReview();

        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
    }

    public function testSaveReviewIfOnlyRatingIsSet()
    {
        $this->setRequestParameter('rvw_txt', null);
        $this->setRequestParameter('artrating', '4');
        $this->setRequestParameter('anid', 'test');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        /** @var oxArticle|PHPUnit\Framework\MockObject\MockObject $oProduct */
        $oProduct = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getId', 'addToRatingAverage']);
        $oProduct->method('getId')->willReturn('test');
        $oProduct->expects($this->once())->method('addToRatingAverage');

        /** @var Review|PHPUnit\Framework\MockObject\MockObject $oReview */
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getReviewUser', 'getActiveObject', 'canAcceptFormData', "getActiveType"]);
        $oReview->expects($this->once())->method('getReviewUser')->willReturn($oUser);
        $oReview->expects($this->once())->method('canAcceptFormData')->willReturn(true);
        $oReview->expects($this->once())->method('getActiveObject')->willReturn($oProduct);
        $oReview->expects($this->once())->method('getActiveType')->willReturn("oxarticle");
        $oReview->saveReview();

        $this->assertFalse(oxDb::getDB()->getOne('select oxobjectid from oxreviews where oxobjectid = "test"'));
        $this->assertSame("test", oxDb::getDB()->getOne('select oxobjectid from oxratings where oxobjectid = "test"'));
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
        $this->assertSame($sDynParams, $oReview->getDynUrlParams());
    }

    public function testCanRateForRecomm()
    {
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveObject", "getReviewUser", "getActiveType"]);
        $oReview->method('getActiveObject')->willReturn($oRecommtList);
        $oReview->method('getReviewUser')->willReturn($oUser);
        $oReview->method('getActiveType')->willReturn('oxarticle');

        $this->assertTrue($oReview->canRate());
    }

    public function testCanRateForArticle()
    {
        $this->getSession()->setVariable('reviewuserid', 'oxdefaultadmin');

        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');

        $oUser = oxNew('oxUser');
        $oUser->load("oxdefaultadmin");

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveObject", "getReviewUser", "getActiveType"]);
        $oReview->method('getActiveObject')->willReturn($oArticle);
        $oReview->method('getReviewUser')->willReturn($oUser);
        $oReview->method('getActiveType')->willReturn('oxarticle');


        $this->assertTrue($oReview->canRate());
    }

    public function testGetReviewsForRecomm()
    {
        $oRecommtList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["getReviews"]);
        $oRecommtList->method('getReviews')->willReturn("testReviews");

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getActiveObject"]);
        $oReview->method('getActiveObject')->willReturn($oRecommtList);

        $this->assertSame("testReviews", $oReview->getReviews());
    }

    public function testGetReviewsForArticle()
    {
        oxTestModules::addFunction('oxreview', 'loadList', '{$o=new oxlist();$o[0]="asd";$o->args=$aA;return $o;}');
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');

        $oReview->setNonPublicVar("_oProduct", $oArticle);
        $oResult = $oReview->getReviews();
        $this->assertSame("oxarticle", $oResult->args[0]);
        $this->assertSame("2000", current($oResult->args[1]));
    }

    public function testGetProduct()
    {
        $this->setRequestParameter('anid', '2000');
        $oReview = oxNew('review');

        $this->assertSame('2000', $oReview->getProduct()->getId());
    }

    public function testGetActiveObjectIfProduct()
    {
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew('oxArticle');
        $oArticle->load('2000');

        $oReview->setNonPublicVar("_oProduct", $oArticle);

        $this->assertSame('2000', $oReview->getActiveObject()->getId());
    }

    public function testGetActiveObjectIfRecommList()
    {
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->setId('testid');

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getProduct", "getActiveRecommList"]);
        $oReview->method('getActiveRecommList')->willReturn($oRecommtList);

        $this->assertSame('testid', $oReview->getActiveObject()->getId());
    }

    public function testGetCrossSelling()
    {
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew("oxArticle");
        $oArticle->load("1849");

        $oReview->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oReview->getCrossSelling();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Model\ListModel::class, $oList);
        $iCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 3 : 2;
        $this->assertEquals($iCount, $oList->count());
    }

    public function testGetSimilarProducts()
    {
        $oReview = $this->getProxyClass("review");
        $oArticle = oxNew("oxArticle");
        $oArticle->load("2000");

        $oReview->setNonPublicVar("_oProduct", $oArticle);
        $oList = $oReview->getSimilarProducts();
        $iCount = $this->getTestConfig()->getShopEdition() == 'EE' ? 4 : 5;
        $this->assertCount($iCount, $oList);
    }

    public function testGetRecommList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oRevew = $this->getProxyClass("review");
        $oArticle = oxNew("oxArticle");
        $oArticle->load('2000');

        $oRevew->setNonPublicVar("_oProduct", $oArticle);
        $aLists = $oRevew->getRecommList();
        $this->assertSame(1, $aLists->count());
        $this->assertSame('testlist', $aLists['testlist']->getId());
        $this->assertSame('2000', $aLists['testlist']->getFirstArticle()->getId());
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

        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['getActiveRecommList']);
        $oReview->method('getActiveRecommList')->willReturn($oRecommList);
        $this->assertSame($sParams . '&amp;recommid=testlist', $oReview->getAdditionalParams());
    }

    public function testGetPageNavigation()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $this->setRequestParameter('reviewuserid', 'oxdefaultadmin');
        $oReview = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ['generatePageNavigation']);
        $oReview->method('generatePageNavigation')->willReturn("aaa");
        $oReview->getActiveRecommList();
        $this->assertSame('aaa', $oReview->getPageNavigation());
    }

    /**
     * Test oxViewConfig::getShowListmania() affection
     */
    public function testGetActiveRecommListIfOff()
    {
        $oCfg = $this->getMock(Config::class, ["getShowListmania"]);
        $oCfg->expects($this->once())->method('getShowListmania')->willReturn(false);

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\ReviewController::class, ["getViewConfig"]);
        $oRecomm->expects($this->once())->method('getViewConfig')->willReturn($oCfg);

        $this->assertFalse($oRecomm->getActiveRecommList());
    }
}
