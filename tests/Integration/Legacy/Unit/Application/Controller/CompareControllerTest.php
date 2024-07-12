<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\DatabaseProvider;
use \oxTestModules;
use OxidEsales\Eshop\Core\Registry;

/**
 * Tests for compare class
 */
class CompareControllerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $myDB = DatabaseProvider::getDb();
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
    protected function tearDown(): void
    {
        $myDB = DatabaseProvider::getDb();
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
        $aItems = ["testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3"];
        $aResult = ["testId1" => true, "testId2" => true, "testId3" => true];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItems", "setCompareItems"]);
        $oView->expects($this->once())->method('getCompareItems')->willReturn($aItems);
        $oView->expects($this->once())->method('setCompareItems')->with($aResult);
        $oView->moveLeft();
    }

    /**
     * bug #0001566
     */
    public function testMoveLeftSkipsIfNoAnid()
    {
        $this->setRequestParameter('aid', "");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItems", "setCompareItems"]);
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
        $aItems = ["testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3"];
        $aResult = ["testId1" => true, "testId2" => true, "testId3" => true];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItems", "setCompareItems"]);
        $oView->expects($this->once())->method('getCompareItems')->willReturn($aItems);
        $oView->expects($this->once())->method('setCompareItems')->with($aResult);
        $oView->moveRight();
    }

    /**
     * bug #0001566
     */
    public function testMoveRightSkipsIfNoAnId()
    {
        $this->setRequestParameter('aid', "");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompareItems", "setCompareItems"]);
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

        $this->assertSame("page/compare/compare", $oView->render());
    }

    /**
     * compare::render() & compare::inPopup() test case
     */
    public function testRenderInPopup()
    {
        $oView = oxNew('compare');

        $oView->inPopup();
        $this->assertSame("compare_popup", $oView->render());
    }

    /**
     * compare::getOrderCnt() test case
     */
    public function testGetOrderCnt()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["getOrderCount"]);
        $oUser->expects($this->once())->method('getOrderCount')->willReturn(999);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->willReturn($oUser);

        $this->assertSame(999, $oView->getOrderCnt());
    }

    /**
     * Test for getCompareItems
     */
    public function testSetCompareItemsGetCompareItems()
    {
        $this->getSession()->setVariable('aFiltcompproducts', ["testItems1"]);
        $oView = oxNew('compare');
        $this->assertSame(["testItems1"], $oView->getCompareItems());

        $oView = oxNew('compare');
        $oView->setCompareItems(["testItems2"]);
        $this->assertSame(["testItems2"], $oView->getCompareItems());
        $this->assertSame(["testItems2"], Registry::getSession()->getVariable('aFiltcompproducts'));
    }

    /**
     * Test get compare article list.
     */
    public function testGetCompArtList()
    {
        $oCompare = $this->getProxyClass("compare");
        $oArticle = oxNew("oxArticle");
        $oArticle->load('1672');

        $oCompare->setNonPublicVar("_aCompItems", ['1672' => $oArticle]);
        $aArtList = $oCompare->getCompArtList();
        $this->assertSame(['1672'], array_keys($aArtList));
    }

    /**
     * Test get compare article count.
     */
    public function testGetCompareItemsCnt()
    {
        $oCompare = $this->getProxyClass("compare");
        $oArticle = oxNew("oxArticle");
        $oCompare->setNonPublicVar("_aCompItems", ['1672' => $oArticle, '2000' => $oArticle]);
        $this->assertSame(2, $oCompare->getCompareItemsCnt());
    }

    /**
     * Test for getCompareItemsCnt
     */
    public function testGetSetCompareItemsCnt()
    {
        $oView = $this->getProxyClass('compare');
        $oView->setCompareItemsCnt(10);
        $this->assertSame(10, $oView->getCompareItemsCnt());
    }

    /**
     * Test get attribute list.
     */
    public function testGetAttributeList()
    {
        $oCompare = $this->getProxyClass("compare");
        $oArticle = oxNew("oxArticle");
        $oCompare->setNonPublicVar("_oArtList", ['1672' => $oArticle, '6b661dda79318ca64ca06e97e4fbcb0a' => $oArticle]);
        $aAttributes = $oCompare->getAttributeList();

        $sSelect = "select oxattrid, oxvalue from oxobject2attribute where oxobjectid = '1672'";
        $rs = DatabaseProvider::getDb()->select($sSelect);
        $sSelect = "select oxtitle from oxattribute where oxid = '" . $rs->fields[0] . "'";
        $sTitle = DatabaseProvider::getDb()->getOne($sSelect);

        $this->assertCount(9, $aAttributes);

        $this->assertEquals($rs->fields[1], $aAttributes[$rs->fields[0]]->aProd['1672']->value);
        $this->assertEquals($sTitle, $aAttributes[$rs->fields[0]]->title);
    }

    /**
     * Test get ids for similar recommendation list.
     */
    public function testGetSimilarRecommListIds()
    {
        $sArrayKey = "articleId";
        $aArrayKeys = [$sArrayKey];
        $oArtList = [$sArrayKey => "zyyy"];

        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ["getCompArtList"]);
        $oSearch->expects($this->once())->method("getCompArtList")->willReturn($oArtList);
        $this->assertSame($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getCompArtList()");
    }

    /**
     * Test get page navigation.
     */
    public function testGetPageNavigation()
    {
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ['generatePageNavigation']);
        $oCompare->method('generatePageNavigation')->willReturn("aaa");
        $this->assertSame('aaa', $oCompare->getPageNavigation());
    }

    /**
     * Test paging off
     */
    public function testSetNoPaging()
    {
        $oCompare = $this->getMock(\OxidEsales\Eshop\Application\Controller\CompareController::class, ['setArticlesPerPage']);

        $oCompare->expects($this->once())->method('setArticlesPerPage')->with(0);
        $oCompare->setNoPaging();
    }

    /**
     * Test number of item in compare list
     */
    public function testSetArticlesPerPage()
    {
        $cl = oxTestModules::addFunction('compare', 'getArticlesPerPage', '{return $this->_iArticlesPerPage;}');
        $oCompare = new $cl();

        $oCompare->setArticlesPerPage(5);
        $this->assertSame(5, $oCompare->getArticlesPerPage());
        $oCompare->setArticlesPerPage(50);
        $this->assertSame(50, $oCompare->getArticlesPerPage());
        $oCompare->setArticlesPerPage(-50);
        $this->assertSame(-50, $oCompare->getArticlesPerPage());
    }

    /**
     * Bred crumb test
     */
    public function testGetBreadCrumb()
    {
        $oCompare = oxNew('Compare');
        $aCatPath = [];
        $aResult = [];

        $aCatPath['title'] = Registry::getLang()->translateString('MY_ACCOUNT', 0, false);
        $aCatPath['link'] = \OxidEsales\Eshop\Core\Registry::getSeoEncoder()->getStaticUrl($oCompare->getViewConfig()->getSelfLink() . 'cl=account');

        $aResult[] = $aCatPath;

        $aCatPath['title'] = Registry::getLang()->translateString('PRODUCT_COMPARISON', 0, false);
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
        $aItems = ["1126" => true, "nonExistingVal" => true, "1127" => true];
        $oArtList = oxNew('oxArticleList');
        $oArtList->loadIds(array_keys($aItems));

        $oResList = $oSubj->changeArtListOrder($aItems, $oArtList);

        $this->assertArrayHasKey("1126", $oResList);
        $this->assertArrayNotHasKey("nonExistingVal", $oResList);
        $this->assertArrayHasKey("1127", $oResList);
    }
}
