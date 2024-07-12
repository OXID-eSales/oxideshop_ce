<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use oxDb;
use oxField;
use OxidEsales\Eshop\Application\Controller\RecommListController;
use OxidEsales\Eshop\Application\Model\RecommendationList;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use oxRegistry;
use oxTestModules;
use PHPUnit\Framework\MockObject\MockObject;

final class RecommlistTest extends \PHPUnit\Framework\TestCase
{
    public $sArticleID;
    private $_sArticleID;

    protected function setUp(): void
    {
        parent::setUp();
        $myDB = oxDb::getDB();
        $sShopId = $this->getConfig()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist2", "oxdefaultadmin", "oxtest2", "oxtest2", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $this->_sArticleID = '1651';
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "", "testlist", "test" ) ';
        $myDB->Execute($sQ);
    }

    protected function tearDown(): void
    {
        $myDB = oxDb::getDB();
        $sDelete = 'delete from oxrecommlists where oxid like "testlist%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxobject2list where oxlistid like "testlist%" ';
        $myDB->execute($sDelete);

        // testing db for records
        $myDB->execute("delete from oxratings where oxobjectid = 'testRecommListId'");
        $myDB->execute("delete from oxreviews where oxobjectid = 'testRecommListId'");

        parent::tearDown();
    }

    /**
     * Testing product link type getter
     *
     * @return
     */
    public function testGetProductLinkType()
    {
        oxNew('oxArticle');
        $oView = oxNew('RecommList');
        $this->assertSame(5, $oView->getProductLinkType());
    }

    /**
     * Tests if navigation parameters getter collects all needed values
     */
    public function testGetNavigationParams()
    {
        $this->setRequestParameter("recommid", "paramValue");

        $oView = oxNew('RecommList');
        $aParams = $oView->getNavigationParams();
        $this->assertArrayHasKey("recommid", $aParams);
        $this->assertSame("paramValue", $aParams["recommid"]);
    }

    /**
     * Tests if recommlist rating is saved even there are no session user
     */
    public function testSaveReviewNoSessionUser()
    {
        $this->setRequestParameter("recommlistrating", 3);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["addToRatingAverage"]);
        $oRecommList->expects($this->never())->method('addToRatingAverage');

        /** @var RecommList|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, ["getActiveRecommList", "getUser"]);
        $oView->method('getUser')->willReturn(false);
        $oView->method('getActiveRecommList')->willReturn($oRecommList);

        $oView->saveReview();
    }

    public function testSaveReview(): void
    {
        $this->setRequestParameter('recommlistrating', 3);
        $this->setRequestParameter('rvw_txt', 'testRecommId');

        /** @var Session|MockObject $session */
        $session = $this->createConfiguredMock(Session::class, ['checkSessionChallenge' => true]);
        $session->expects($this->once())->method('checkSessionChallenge');
        Registry::set(Session::class, $session);

        /** @var RecommendationList|MockObject $recommendationList */
        $recommendationList = $this->createConfiguredMock(
            RecommendationList::class,
            [
                'addToRatingAverage' => null,
                'getId' => 'testRecommListId',
            ]
        );
        $recommendationList->expects($this->once())->method('addToRatingAverage');
        /** @var User|MockObject $user */
        $user = $this->createConfiguredMock(User::class, ['getId' => 'testUserId']);
        /** @var RecommListController|MockObject $controller */
        $controller = $this->getMock(
            RecommListController::class,
            ['getActiveRecommList', 'getUser', 'canAcceptFormData']
        );
        $controller->method('canAcceptFormData')->willReturn(true);
        $controller->method('getUser')->willReturn($user);
        $controller->method('getActiveRecommList')->willReturn($recommendationList);

        $controller->saveReview();

        $db = oxDb::getDb();

        // testing db for records
        $this->assertSame(
            1,
            $db->getOne("select 1 from oxratings where oxuserid='testUserId' and oxrating = '3' and oxobjectid = 'testRecommListId'")
        );
        $this->assertSame(
            1,
            $db->getOne("select 1 from oxreviews where oxuserid='testUserId' and oxobjectid = 'testRecommListId'")
        );
    }


    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxSeoEncoderRecomm', 'getRecommPageUrl', '{return "testPageUrl";}');

        $oRecommListView = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecommListView->method('getActiveRecommList')->willReturn(oxNew('oxrecommlist'));

        $this->assertSame("testPageUrl", $oRecommListView->addPageNrParam(null, 1));
    }

    public function testGeneratePageNavigationUrlSeoOn()
    {
        $oActRecommtList = $this->getMock(RecommendationList::class, ["getLink"]);
        $oActRecommtList->method('getLink')->willReturn("testLink");

        $oRecommListView = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecommListView->method('getActiveRecommList')->willReturn($oActRecommtList);

        $this->assertSame("testLink", $oRecommListView->generatePageNavigationUrl());
    }

    public function testGeneratePageNavigationUrlSeoOff()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');

        $oUBase = oxNew('oxUBase');
        $sTestParams = $oUBase->generatePageNavigationUrl();

        $oRecommListView = oxNew('RecommList');
        $this->assertEquals($sTestParams, $oRecommListView->generatePageNavigationUrl());
    }

    public function tesGetProductLinkType()
    {
        $oView = oxNew('RecommList');
        $this->assertEquals(OXARTICLE_LINKTYPE_RECOMM, $oView->getProductLinkType());
    }

    public function testGetAddUrlParams()
    {
        $oActRecommtList = oxNew('oxrecommlist');
        $oActRecommtList->setId("testRecommListId");

        $oRecommListView = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecommListView->method('getActiveRecommList')->willReturn($oActRecommtList);

        $oListView = oxNew('aList');
        $sTestParams = $oListView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "listtype=recommlist";
        $sTestParams .= "&amp;recommid=testRecommListId";

        $this->assertSame($sTestParams, $oRecommListView->getAddUrlParams());
    }

    public function testGetAddSeoUrlParams()
    {
        $this->setRequestParameter("searchrecomm", "testSearchRecommParam");

        $oListView = oxNew('aList');
        $sTestParams = $oListView->getAddSeoUrlParams();

        $oRecommListView = oxNew('RecommList');
        $this->assertSame($sTestParams . "&amp;searchrecomm=testSearchRecommParam", $oRecommListView->getAddSeoUrlParams());
    }

    public function testGetTreePath()
    {
        $sSearchparam = "testSearchParam";

        $oLang = oxRegistry::getLang();

        $aPath[0] = oxNew("oxCategory");
        $aPath[0]->setLink(false);
        $aPath[0]->oxcategories__oxtitle = new oxField($oLang->translateString('RECOMMLIST'));

        $sUrl = $this->getConfig()->getShopHomeURL() . "cl=recommlist&amp;searchrecomm=" . rawurlencode($sSearchparam);
        $sTitle = $oLang->translateString('RECOMMLIST_SEARCH') . ' "' . $sSearchparam . '"';

        $aPath[1] = oxNew("oxCategory");
        $aPath[1]->setLink($sUrl);
        $aPath[1]->oxcategories__oxtitle = new oxField($sTitle);

        $oView = $this->getMock(RecommListController::class, ["getRecommSearch"]);
        $oView->expects($this->once())->method('getRecommSearch')->willReturn($sSearchparam);

        $this->assertEquals($aPath, $oView->getTreePath());
    }

    /**
     * Getting view values
     */
    public function testGetActiveRecommList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oRecomm = oxNew('RecommList');
        $oRecommList = $oRecomm->getActiveRecommList();
        $this->assertSame('testlist', $oRecommList->getId());
    }

    public function testGetArticleList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecomm->setNonPublicVar("_oActiveRecommItems", $oRecommtList);
        $this->assertCount(1, $oRecomm->getArticleList());
    }

    public function testGetSimilarRecommLists()
    {
        $myDB = oxDb::getDB();
        $sArticleID = '2000';
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "' . $sArticleID . '", "testlist2", "test" ) ';
        $myDB->Execute($sQ);

        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->setId("testlist");

        $oRecommtListItem = $this->getMock(RecommendationList::class, ['arrayKeys']);
        $oRecommtListItem->method('arrayKeys')->willReturn([$this->_sArticleID, $sArticleID]);

        $oRecomm = $this->getMock(RecommListController::class, ["getArticleList", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getArticleList')->willReturn($oRecommtListItem);
        $oRecomm->expects($this->once())->method('getActiveRecommList')->willReturn($oRecommtList);

        $aLists = $oRecomm->getSimilarRecommLists();
        $this->assertNotNull($aLists);
        $this->assertSame(1, $aLists->count());
        $this->assertSame('testlist2', $aLists['testlist2']->getId());
        $this->assertSame($sArticleID, $aLists['testlist2']->getFirstArticle()->getId());
    }

    public function testGetReviews()
    {
        oxTestModules::addFunction('oxreview', 'loadList', '{$o=new oxlist();$o[0]="asd";$o->args=$aA;return $o;}');
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->setId('testid');

        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $oResult = $oRecomm->getReviews();
        $this->assertSame("oxrecommlist", $oResult->args[0]);
        $this->assertSame("testid", $oResult->args[1]);
    }

    public function testIsReviewActive()
    {
        $oRecomm = oxNew('RecommList');
        $this->assertTrue($oRecomm->isReviewActive());
    }

    public function testCanRate()
    {
        $this->getSession()->setVariable('usr', 'oxdefaultadmin');
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $this->assertTrue($oRecomm->canRate());
    }

    public function testGetRatingValue()
    {
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecommtList->oxrecommlists__oxrating = new oxField(3.5, oxField::T_RAW);
        $oRecommtList->oxrecommlists__oxratingcnt = new oxField(2, oxField::T_RAW);
        $oRecommtList->save();

        $this->getSession()->setVariable('usr', 'oxdefaultadmin');
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $this->assertEqualsWithDelta(3.5, $oRecomm->getRatingValue(), PHP_FLOAT_EPSILON);
    }

    public function testGetRatingValueNotNull()
    {
        $oRecomm = $this->getMock(RecommListController::class, ["isReviewActive"]);
        $oRecomm->method('isReviewActive')->willReturn(false);

        $this->assertSame((double) 0, $oRecomm->getRatingValue());
    }

    public function testGetRatingCount()
    {
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecommtList->oxrecommlists__oxrating = new oxField(3.5, oxField::T_RAW);
        $oRecommtList->oxrecommlists__oxratingcnt = new oxField(2, oxField::T_RAW);
        $oRecommtList->save();

        $oRecomm = $this->getProxyClass("recommlist");
        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $this->assertSame(2, $oRecomm->getRatingCount());
    }

    public function testGetRecommLists()
    {
        $myDB = oxDb::getDB();
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist2", "' . $this->sArticleID . '", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist3", "2000", "testlist2", "test" ) ';
        $myDB->Execute($sQ);
        $oRecomm = oxNew('RecommList');
        $this->setRequestParameter('searchrecomm', 'test');
        $aLists = $oRecomm->getRecommLists('test');
        $this->assertCount(2, $aLists);
    }

    public function testGetRecommSearch()
    {
        $this->setRequestParameter('searchrecomm', 'test');
        $oRecomm = oxNew('RecommList');
        $this->assertSame('test', $oRecomm->getRecommSearch());
    }

    public function testGetRecommSearchSpecialChar()
    {
        $this->setRequestParameter('searchrecomm', 'test"');
        $oRecomm = oxNew('RecommList');
        $this->assertSame('test&quot;', $oRecomm->getRecommSearch());
    }

    // #M43
    public function testGetRecommSearchForTwoWords()
    {
        $this->setRequestParameter('searchrecomm', 'test search');
        $oRecomm = oxNew('RecommList');
        $this->assertSame('test search', $oRecomm->getRecommSearch());
    }

    public function testGetAdditionalParams()
    {
        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId("testRecommId");

        $oRecomm = $this->getMock(RecommListController::class, ["getActiveRecommList", "getRecommSearch"]);
        $oRecomm->method('getActiveRecommList')->willReturn($oRecommList);
        $oRecomm->method('getRecommSearch')->willReturn("testRecommSearch");

        $oUBase = oxNew('oxUBase');
        $sTestParams = $oUBase->getAdditionalParams();

        $this->assertSame($sTestParams . "&amp;recommid=testRecommId&amp;searchrecomm=testRecommSearch", $oRecomm->getAdditionalParams());
    }

    public function testGetPageNavigation()
    {
        $oRecomm = $this->getMock(RecommListController::class, ['generatePageNavigation']);
        $oRecomm->method('generatePageNavigation')->willReturn("aaa");
        $this->assertSame('aaa', $oRecomm->getPageNavigation());
    }

    public function testGetSearchForHtml()
    {
        $this->setRequestParameter('searchrecomm', 'aaa');
        $oRecomm = oxNew('RecommList');
        $this->assertSame('aaa', $oRecomm->getSearchForHtml());
    }

    public function testGetSearchForHtmlWithActiveRecomm()
    {
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $this->assertSame('oxtest', $oRecomm->getSearchForHtml());
    }

    public function testGetLinkActiveRecommListAvailable()
    {
        $this->setRequestParameter('searchrecomm', 'aaa');

        $oRecList = $this->getMock(RecommendationList::class, ["getLink"]);
        $oRecList->expects($this->once())->method('getLink')->willReturn("testRecommListUrl");

        $oRecomm = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getActiveRecommList')->willReturn($oRecList);

        $this->assertSame("testRecommListUrl?searchrecomm=aaa", $oRecomm->getLink());
    }

    public function testGetLinkActiveRecommListUnAvailable()
    {
        $this->setRequestParameter('searchrecomm', 'aaa');

        $oRecomm = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecomm->expects($this->atLeastOnce())->method('getActiveRecommList')->willReturn(false);

        $oUBaseView = oxNew('oxUBase');
        $sTestLink = $oUBaseView->getLink(0);

        $this->assertSame($sTestLink . "&amp;searchrecomm=aaa", $oRecomm->getLink());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oRecommList = oxNew('RecommList');

        $this->assertCount(1, $oRecommList->getBreadCrumb());
    }

    /**
     * Test get title.
     */
    public function testGetTitleWithActiveRecommList()
    {
        $oRecommlist = oxNew('oxrecommlist');
        $oRecommlist->oxrecommlists__oxtitle = new oxField('title');
        $oRecommlist->oxrecommlists__oxauthor = new oxField('author');

        $oView = $this->getMock(RecommListController::class, ['getActiveRecommList']);
        $oView->method('getActiveRecommList')->willReturn($oRecommlist);

        $this->assertSame('title (' . oxRegistry::getLang()->translateString('LIST_BY', oxRegistry::getLang()->getBaseLanguage(), false) . ' author)', $oView->getTitle());
    }

    /**
     * Test get title.
     */
    public function testGetTitleWithoutActiveRecommList()
    {
        $oView = $this->getMock(RecommListController::class, ['getActiveRecommList', 'getArticleCount', 'getSearchForHtml']);
        $oView->method('getActiveRecommList')->willReturn(null);
        $oView->method('getArticleCount')->willReturn(7);
        $oView->method('getSearchForHtml')->willReturn('string');

        $this->assertSame('7 ' . oxRegistry::getLang()->translateString('HITS_FOR', oxRegistry::getLang()->getBaseLanguage(), false) . ' "string"', $oView->getTitle());
    }
}
