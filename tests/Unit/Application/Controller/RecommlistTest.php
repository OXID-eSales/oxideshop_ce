<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxlist;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class RecommlistTest extends \OxidTestCase
{
    private $_sArticleID;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
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
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "' . $this->sArticleID . '", "testlist", "test" ) ';
        $myDB->Execute($sQ);

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
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
        $this->assertEquals(5, $oView->UNITgetProductLinkType());
    }

    /**
     * Tests if navigation parameters getter collects all needed values
     *
     * @return null
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
     *
     * @return null
     */
    public function testSaveReviewNoSessionUser()
    {
        $this->setRequestParameter("recommlistrating", 3);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array("addToRatingAverage"));
        $oRecommList->expects($this->never())->method('addToRatingAverage');

        /** @var RecommList|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList", "getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));

        $oView->saveReview();
    }

    /**
     * Tests if recommlist rating is saved
     *
     * @return null
     */
    public function testSaveReview()
    {
        $this->setRequestParameter("recommlistrating", 3);
        $this->setRequestParameter("rvw_txt", "testRecommId");

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxRecommList|PHPUnit\Framework\MockObject\MockObject $oRecommList */
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array("addToRatingAverage", "getId"));
        $oRecommList->expects($this->once())->method('addToRatingAverage');
        $oRecommList->expects($this->any())->method('getId')->will($this->returnValue("testRecommListId"));

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("getId"));
        $oUser->expects($this->any())->method('getId')->will($this->returnValue("testUserId"));

        /** @var RecommList|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList", "getUser", "canAcceptFormData"));
        $oView->expects($this->any())->method('canAcceptFormData')->will($this->returnValue(true));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));

        $oView->saveReview();

        $oDb = oxDb::getDb();

        // testing db for records
        $this->assertTrue("1" === $oDb->getOne("select 1 from oxratings where oxuserid='testUserId' and oxrating = '3' and oxobjectid = 'testRecommListId'"));
        $this->assertTrue("1" === $oDb->getOne("select 1 from oxreviews where oxuserid='testUserId' and oxobjectid = 'testRecommListId'"));
    }


    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxSeoEncoderRecomm', 'getRecommPageUrl', '{return "testPageUrl";}');

        $oRecommListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList"));
        $oRecommListView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue(oxNew('oxrecommlist')));

        $this->assertEquals("testPageUrl", $oRecommListView->UNITaddPageNrParam(null, 1));
    }

    public function testGeneratePageNavigationUrlSeoOn()
    {
        $oActRecommtList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array("getLink"));
        $oActRecommtList->expects($this->any())->method('getLink')->will($this->returnValue("testLink"));

        $oRecommListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList"));
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
        $this->assertEquals(OXARTICLE_LINKTYPE_RECOMM, $oView->UNITgetProductLinkType());
    }

    public function testGetAddUrlParams()
    {
        $oActRecommtList = oxNew('oxrecommlist');
        $oActRecommtList->setId("testRecommListId");

        $oRecommListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList"));
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getRecommSearch"));
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

        $oRecommtListItem = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array('arrayKeys'));
        $oRecommtListItem->expects($this->any())->method('arrayKeys')->will($this->returnValue(array($this->_sArticleID, $sArticleID)));

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getArticleList", "getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getArticleList')->will($this->returnValue($oRecommtListItem));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecommtList));

        $aLists = $oRecomm->getSimilarRecommLists();
        $this->assertNotNull($aLists);
        $this->assertEquals(1, $aLists->count());
        $this->assertEquals('testlist2', $aLists['testlist2']->getId());
        $this->assertTrue(in_array($aLists['testlist2']->getFirstArticle()->getId(), array($sArticleID)));
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
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("isReviewActive"));
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

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList", "getRecommSearch"));
        $oRecomm->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommList));
        $oRecomm->expects($this->any())->method('getRecommSearch')->will($this->returnValue("testRecommSearch"));

        $oUBase = oxNew('oxUBase');
        $sTestParams = $oUBase->getAdditionalParams();

        $this->assertEquals($sTestParams . "&amp;recommid=testRecommId&amp;searchrecomm=testRecommSearch", $oRecomm->getAdditionalParams());
    }

    public function testGetPageNavigation()
    {
        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array('generatePageNavigation'));
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

        $oRecList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array("getLink"));
        $oRecList->expects($this->once())->method('getLink')->will($this->returnValue("testRecommListUrl"));

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList"));
        $oRecomm->expects($this->once())->method('getActiveRecommList')->will($this->returnValue($oRecList));

        $this->assertEquals("testRecommListUrl?searchrecomm=aaa", $oRecomm->getLink());
    }

    public function testGetLinkActiveRecommListUnAvailable()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');
        $this->setRequestParameter('searchrecomm', 'aaa');

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array("getActiveRecommList"));
        $oRecomm->expects($this->atLeastOnce())->method('getActiveRecommList')->will($this->returnValue(false));

        $oUBaseView = oxNew('oxUBase');
        $sTestLink = $oUBaseView->getLink(0);

        $this->assertEquals($sTestLink . "&amp;searchrecomm=aaa", $oRecomm->getLink());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     *
     * @return null
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array('getActiveRecommList'));
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue($oRecommlist));

        $this->assertEquals('title (' . oxRegistry::getLang()->translateString('LIST_BY', oxRegistry::getLang()->getBaseLanguage(), false) . ' author)', $oView->getTitle());
    }

    /**
     * Test get title.
     */
    public function testGetTitleWithoutActiveRecommList()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommListController::class, array('getActiveRecommList', 'getArticleCount', 'getSearchForHtml'));
        $oView->expects($this->any())->method('getActiveRecommList')->will($this->returnValue(null));
        $oView->expects($this->any())->method('getArticleCount')->will($this->returnValue(7));
        $oView->expects($this->any())->method('getSearchForHtml')->will($this->returnValue('string'));

        $this->assertEquals('7 ' . oxRegistry::getLang()->translateString('HITS_FOR', oxRegistry::getLang()->getBaseLanguage(), false) . ' "string"', $oView->getTitle());
    }
}
