<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxDb;
use OxidEsales\Eshop\Application\Controller\CompareController;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for compare class
 */
class CompareControllerTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        $myDB = oxDb::getDB();
        $sShopId = $this->getConfig()->getShopId();
        // adding article to recommend list
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "' . $sShopId . '" ) ';
        $myDB->Execute($sQ);
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "2000", "testlist", "test" ) ';
        $myDB->Execute($sQ);
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();
        $sDelete = 'delete from oxrecommlists where oxid like "testlist%" ';
        $myDB->execute($sDelete);

        $sDelete = 'delete from oxobject2list where oxlistid like "testlist%" ';
        $myDB->execute($sDelete);

        $this->cleanUpTable('oxreviews');
        parent::tearDown();
    }

    /**
     * compare::moveLeft() test case
     */
    public function testMoveLeft()
    {
        $this->setRequestParameter('aid', "testId2");
        $aItems = array("testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3");
        $aResult = array("testId1" => true, "testId2" => true, "testId3" => true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItems", "setCompareItems"));
        $oView->expects($this->once())->method('getCompareItems')->will($this->returnValue($aItems));
        $oView->expects($this->once())->method('setCompareItems')->with($this->equalTo($aResult));
        $oView->moveLeft();
    }

    /**
     * bug #0001566
     */
    public function testMoveLeftSkipsIfNoAnid()
    {
        $this->setRequestParameter('aid', "");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItems", "setCompareItems"));
        $oView->expects($this->never())->method('getCompareItems');
        $oView->expects($this->never())->method('setCompareItems');
        $oView->moveLeft();
    }

    /**
     * compare::moveRight() test case
     */
    public function testMoveRight()
    {
        $this->setRequestParameter('aid', "testId2");
        $aItems = array("testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3");
        $aResult = array("testId1" => true, "testId2" => true, "testId3" => true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItems", "setCompareItems"));
        $oView->expects($this->once())->method('getCompareItems')->will($this->returnValue($aItems));
        $oView->expects($this->once())->method('setCompareItems')->with($this->equalTo($aResult));
        $oView->moveRight();
    }

    /**
     * bug #0001566
     */
    public function testMoveRightSkipsIfNoAnId()
    {
        $this->setRequestParameter('aid', "");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompareItems", "setCompareItems"));
        $oView->expects($this->never())->method('getCompareItems');
        $oView->expects($this->never())->method('setCompareItems');
        $oView->moveRight();
    }

    /**
     * compare::render() test case
     */
    public function testRender()
    {
        $oView = oxNew('compare');

        $this->assertEquals("page/compare/compare.tpl", $oView->render());

    }

    /**
     * compare::render() & compare::inPopup() test case
     */
    public function testRenderInPopup()
    {
        $oView = oxNew('compare');

        $oView->inPopup();
        $this->assertEquals("compare_popup.tpl", $oView->render());

    }

    /**
     * compare::getOrderCnt() test case
     */
    public function testGetOrderCnt()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("getOrderCount"));
        $oUser->expects($this->once())->method('getOrderCount')->will($this->returnValue(999));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals(999, $oView->getOrderCnt());
    }

    /**
     * Test for getCompareItems
     */
    public function testSetCompareItemsGetCompareItems()
    {
        $this->getSession()->setVariable('aFiltcompproducts', array("testItems1"));
        $oView = oxNew('compare');
        $this->assertEquals(array("testItems1"), $oView->getCompareItems());

        $oView = oxNew('compare');
        $oView->setCompareItems(array("testItems2"));
        $this->assertEquals(array("testItems2"), $oView->getCompareItems());
        $this->assertEquals(array("testItems2"), oxRegistry::getSession()->getVariable('aFiltcompproducts'));
    }

    /**
     * Test get compare article list.
     */
    public function testGetCompArtList()
    {
        $oCompare = $this->getProxyClass("compare");
        $oArticle = oxNew("oxArticle");
        $oArticle->load('1672');
        $oCompare->setNonPublicVar("_aCompItems", array('1672' => $oArticle));
        $aArtList = $oCompare->getCompArtList();
        $this->assertEquals(array('1672'), array_keys($aArtList));
    }

    /**
     * Test get compare article count.
     */
    public function testGetCompareItemsCnt()
    {
        $oCompare = $this->getProxyClass("compare");
        $oArticle = oxNew("oxArticle");
        $oCompare->setNonPublicVar("_aCompItems", array('1672' => $oArticle, '2000' => $oArticle));
        $this->assertEquals(2, $oCompare->getCompareItemsCnt());
    }

    /**
     * Test for getCompareItemsCnt
     */
    public function testGetSetCompareItemsCnt()
    {
        $oView = $this->getProxyClass('compare');
        $oView->setCompareItemsCnt(10);
        $this->assertEquals(10, $oView->getCompareItemsCnt());
    }

    /**
     * Test get attribute list.
     *
     * @return null
     */
    public function testGetAttributeList()
    {
        $oCompare = $this->getProxyClass("compare");
        $oArticle = oxNew("oxArticle");
        $oCompare->setNonPublicVar("_oArtList", array('1672' => $oArticle, '6b661dda79318ca64ca06e97e4fbcb0a' =>$oArticle));
        $aAttributes = $oCompare->getAttributeList();

        $sSelect = "select oxattrid, oxvalue from oxobject2attribute where oxobjectid = '1672'";
        $rs = oxDb::getDB()->select($sSelect);
        $sSelect = "select oxtitle from oxattribute where oxid = '" . $rs->fields[0] . "'";
        $sTitle = oxDb::getDB()->getOne($sSelect);

        $this->assertEquals(9, count($aAttributes));

        $this->assertEquals($rs->fields[1], $aAttributes[$rs->fields[0]]->aProd['1672']->value);
        $this->assertEquals($sTitle, $aAttributes[$rs->fields[0]]->title);
    }

    /**
     * Test get ids for similar recommendation list.
     */
    public function testGetSimilarRecommListIds()
    {
        $sArrayKey = "articleId";
        $aArrayKeys = array($sArrayKey);
        $oArtList = array($sArrayKey => "zyyy");

        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array("getCompArtList"));
        $oSearch->expects($this->once())->method("getCompArtList")->will($this->returnValue($oArtList));
        $this->assertEquals($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getCompArtList()");
    }

    /**
     * Test get page navigation.
     */
    public function testGetPageNavigation()
    {
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array('generatePageNavigation'));
        $oCompare->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oCompare->getPageNavigation());
    }

    /**
     * Test paging off
     */
    public function testSetNoPaging()
    {
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, array('_setArticlesPerPage'));

        $oCompare->expects($this->once())->method('_setArticlesPerPage')->with($this->equalTo(0));
        $oCompare->setNoPaging();
    }

    /**
     * Test number of item in compare list
     */
    public function testSetArticlesPerPage()
    {
        $cl = oxTestModules::addFunction('compare', '_getArticlesPerPage', '{return $this->_iArticlesPerPage;}');
        $oCompare = new $cl;

        $oCompare->UNITsetArticlesPerPage(5);
        $this->assertEquals(5, $oCompare->_getArticlesPerPage());
        $oCompare->UNITsetArticlesPerPage(50);
        $this->assertEquals(50, $oCompare->_getArticlesPerPage());
        $oCompare->UNITsetArticlesPerPage(-50);
        $this->assertEquals(-50, $oCompare->_getArticlesPerPage());
    }

    /**
     * Bred crumb test
     */
    public function testGetBreadCrumb()
    {
        $oCompare = oxNew('Compare');
        $aCatPath = array();
        $aResult = array();

        $aCatPath['title'] = oxRegistry::getLang()->translateString('MY_ACCOUNT', 0, false);
        $aCatPath['link'] = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($oCompare->getViewConfig()->getSelfLink() . 'cl=account');

        $aResult[] = $aCatPath;

        $aCatPath['title'] = oxRegistry::getLang()->translateString('PRODUCT_COMPARISON', 0, false);
        $aCatPath['link'] = $oCompare->getLink();

        $aResult[] = $aCatPath;

        $this->assertEquals($aResult, $oCompare->getBreadCrumb());
    }

    /**
     * Testing #4391 fix
     */
    public function testChangeArtListOrderWithNotExistingProduct()
    {
        $oSubj = $this->getProxyClass("Compare");
        $aItems = array("1126" => true, "nonExistingVal" => true, "1127" => true);
        $oArtList = oxNew('oxArticleList');
        $oArtList->loadIds(array_keys($aItems));

        $oResList = $oSubj->UNITchangeArtListOrder($aItems, $oArtList);

        $this->assertArrayHasKey("1126", $oResList);
        $this->assertArrayNotHasKey("nonExistingVal", $oResList);
        $this->assertArrayHasKey("1127", $oResList);
    }

    public function testGetReviewAndRatingItemsCountWhenUserIsNotLoggedIn()
    {
        $compareController = oxNew(CompareController::class);
        $this->getConfig()->setUser(null);
        $this->assertSame(0, $compareController->getReviewAndRatingItemsCount());
    }
}
