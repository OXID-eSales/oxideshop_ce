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
        $this->assertEquals(5, $oView->getProductLinkType());
    }

    /**
     * Tests if navigation parameters getter collects all needed values
     */
    public function testGetNavigationParams()
    {
        $this->setRequestParameter("recommid", "paramValue");

        $oView = oxNew('RecommList');
        $aParams = $oView->getNavigationParams();
        $this->assertTrue(isset($aParams["recommid"]));
        $this->assertTrue("paramValue" === $aParams["recommid"]);
    }

    /**
     * Tests if recommlist rating is saved even there are no session user
     */
    public function testSaveReviewNoSessionUser()
    {
        $this->setRequestParameter("recommlistrating", 3);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["addToRatingAverage"]);
        $oRecommList->expects($this->never())->method('addToRatingAverage');

        /** @var RecommList|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, ["getActiveRecommList", "getUser"]);
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));

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
        $this->assertEquals(
            1,
            $db->getOne("select 1 from oxratings where oxuserid='testUserId' and oxrating = '3' and oxobjectid = 'testRecommListId'")
        );
        $this->assertEquals(
            1,
            $db->getOne("select 1 from oxreviews where oxuserid='testUserId' and oxobjectid = 'testRecommListId'")
        );
    }


    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxSeoEncoderRecomm', 'getRecommPageUrl', '{return "testPageUrl";}');

        $oRecommListView = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecommListView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue(oxNew('oxrecommlist')));

        $this->assertEquals("testPageUrl", $oRecommListView->addPageNrParam(null, 1));
    }

    public function testGeneratePageNavigationUrlSeoOn()
    {
        $oActRecommtList = $this->getMock(RecommendationList::class, ["getLink"]);
        $oActRecommtList->expects($this->any())->method('getLink')->will($this->returnValue("testLink"));

        $oRecommListView = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecommListView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oActRecommtList));

        $this->assertEquals("testLink", $oRecommListView->generatePageNavigationUrl());
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
        $oRecommListView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oActRecommtList));

        $oListView = oxNew('aList');
        $sTestParams = $oListView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "listtype=recommlist";
        $sTestParams .= "&amp;recommid=testRecommListId";

        $this->assertEquals($sTestParams, $oRecommListView->getAddUrlParams());
    }

    public function testGetAddSeoUrlParams()
    {
        $this->setRequestParameter("searchrecomm", "testSearchRecommParam");

        $oListView = oxNew('aList');
        $sTestParams = $oListView->getAddSeoUrlParams();

        $oRecommListView = oxNew('RecommList');
        $this->assertEquals($sTestParams . "&amp;searchrecomm=testSearchRecommParam", $oRecommListView->getAddSeoUrlParams());
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
        $oView->expects($this->once())->method('getRecommSearch')->will($this->returnValue($sSearchparam));

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
        $this->assertEquals('testlist', $oRecommList->getId());
    }

    public function testGetArticleList()
    {
        $this->setRequestParameter('recommid', 'testlist');
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecomm->setNonPublicVar("_oActiveRecommItems", $oRecommtList);
        $this->assertEquals(1, count($oRecomm->getArticleList()));
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
        $oRecommtListItem->expects($this->any())->method('arrayKeys')->will($this->returnValue([$this->_sArticleID, $sArticleID]));

        $oRecomm = $this->getMock(RecommListController::class, ["getArticleList", "getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getArticleList')->will($this->returnValue($oRecommtListItem));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommtList));

        $aLists = $oRecomm->getSimilarRecommLists();
        $this->assertNotNull($aLists);
        $this->assertEquals(1, $aLists->count());
        $this->assertEquals('testlist2', $aLists['testlist2']->getId());
        $this->assertTrue($aLists['testlist2']->getFirstArticle()->getId() == $sArticleID);
    }

    public function testGetReviews()
    {
        oxTestModules::addFunction('oxreview', 'loadList', '{$o=new oxlist();$o[0]="asd";$o->args=$aA;return $o;}');
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->setId('testid');

        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $oResult = $oRecomm->getReviews();
        $this->assertEquals("oxrecommlist", $oResult->args[0]);
        $this->assertEquals("testid", $oResult->args[1]);
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
        $this->assertEquals(3.5, $oRecomm->getRatingValue());
    }

    public function testGetRatingValueNotNull()
    {
        $oRecomm = $this->getMock(RecommListController::class, ["isReviewActive"]);
        $oRecomm->expects($this->any())->method('isReviewActive')->will($this->returnValue(false));

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
        $this->assertEquals(2, $oRecomm->getRatingCount());
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
        $this->assertEquals(2, count($aLists));
    }

    public function testGetRecommSearch()
    {
        $this->setRequestParameter('searchrecomm', 'test');
        $oRecomm = oxNew('RecommList');
        $this->assertEquals('test', $oRecomm->getRecommSearch());
    }

    public function testGetRecommSearchSpecialChar()
    {
        $this->setRequestParameter('searchrecomm', 'test"');
        $oRecomm = oxNew('RecommList');
        $this->assertEquals('test&quot;', $oRecomm->getRecommSearch());
    }

    // #M43
    public function testGetRecommSearchForTwoWords()
    {
        $this->setRequestParameter('searchrecomm', 'test search');
        $oRecomm = oxNew('RecommList');
        $this->assertEquals('test search', $oRecomm->getRecommSearch());
    }

    public function testGetAdditionalParams()
    {
        $oRecommList = oxNew('oxRecommList');
        $oRecommList->setId("testRecommId");

        $oRecomm = $this->getMock(RecommListController::class, ["getActiveRecommList", "getRecommSearch"]);
        $oRecomm->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->expects($this->any())->method('getRecommSearch')->will($this->returnValue("testRecommSearch"));

        $oUBase = oxNew('oxUBase');
        $sTestParams = $oUBase->getAdditionalParams();

        $this->assertEquals($sTestParams . "&amp;recommid=testRecommId&amp;searchrecomm=testRecommSearch", $oRecomm->getAdditionalParams());
    }

    public function testGetPageNavigation()
    {
        $oRecomm = $this->getMock(RecommListController::class, ['generatePageNavigation']);
        $oRecomm->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oRecomm->getPageNavigation());
    }

    public function testGetSearchForHtml()
    {
        $this->setRequestParameter('searchrecomm', 'aaa');
        $oRecomm = oxNew('RecommList');
        $this->assertEquals('aaa', $oRecomm->getSearchForHtml());
    }

    public function testGetSearchForHtmlWithActiveRecomm()
    {
        $oRecomm = $this->getProxyClass("recommlist");
        $oRecommtList = oxNew('oxRecommList');
        $oRecommtList->load('testlist');

        $oRecomm->setNonPublicVar("_oActiveRecommList", $oRecommtList);
        $this->assertEquals('oxtest', $oRecomm->getSearchForHtml());
    }

    public function testGetLinkActiveRecommListAvailable()
    {
        $this->setRequestParameter('searchrecomm', 'aaa');

        $oRecList = $this->getMock(RecommendationList::class, ["getLink"]);
        $oRecList->expects($this->once())->method('getLink')->will($this->returnValue("testRecommListUrl"));

        $oRecomm = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecList));

        $this->assertEquals("testRecommListUrl?searchrecomm=aaa", $oRecomm->getLink());
    }

    public function testGetLinkActiveRecommListUnAvailable()
    {
        $this->setRequestParameter('searchrecomm', 'aaa');

        $oRecomm = $this->getMock(RecommListController::class, ["getActiveRecommList"]);
        $oRecomm->expects($this->atLeastOnce())->method('getActiveRecommList')->will($this->returnValue(false));

        $oUBaseView = oxNew('oxUBase');
        $sTestLink = $oUBaseView->getLink(0);

        $this->assertEquals($sTestLink . "&amp;searchrecomm=aaa", $oRecomm->getLink());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oRecommList = oxNew('RecommList');

        $this->assertEquals(1, count($oRecommList->getBreadCrumb()));
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
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommlist));

        $this->assertEquals('title (' . oxRegistry::getLang()->translateString('LIST_BY', oxRegistry::getLang()->getBaseLanguage(), false) . ' author)', $oView->getTitle());
    }

    /**
     * Test get title.
     */
    public function testGetTitleWithoutActiveRecommList()
    {
        $oView = $this->getMock(RecommListController::class, ['getActiveRecommList', 'getArticleCount', 'getSearchForHtml']);
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue(null));
        $oView->expects($this->any())->method('getArticleCount')->will($this->returnValue(7));
        $oView->expects($this->any())->method('getSearchForHtml')->will($this->returnValue('string'));

        $this->assertEquals('7 ' . oxRegistry::getLang()->translateString('HITS_FOR', oxRegistry::getLang()->getBaseLanguage(), false) . ' "string"', $oView->getTitle());
    }
}
