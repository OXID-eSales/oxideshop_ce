<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \Exception;
use \oxManufacturerList;
use \oxField;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxManufacturerList class
 */
class ManufacturerlistTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();
        oxTestModules::addFunction('oxSeoEncoderManufacturer', '_saveToDb', '{return null;}');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxTestModules::addFunction('oxManufacturer', 'cleanRootManufacturer', '{oxManufacturer::$_aRootManufacturer = array();}');
        oxNew('oxManufacturer')->cleanRootManufacturer();
        parent::tearDown();
    }

    /**
     * manufacturerlist::render() test case
     */
    public function testRender()
    {
        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ["getId"]);
        $oManufacturer->expects($this->atLeastOnce())->method('getId')->willReturn("testId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getManufacturerTree", "getActManufacturer", "getArticleList", "processListArticles", "checkRequestedPage"]);
        $oView->method('getManufacturerTree')->willReturn(true);
        $oView->expects($this->atLeastOnce())->method('getActManufacturer')->willReturn($oManufacturer);
        $oView->expects($this->atLeastOnce())->method('getArticleList');
        $oView->expects($this->once())->method('processListArticles');
        $oView->expects($this->once())->method('checkRequestedPage');

        $this->assertSame("page/list/list", $oView->render());
    }

    /**
     * Testing render() when passing existing manufacturer
     */
    public function testRenderExistingManufacturer()
    {
        $sActManufacturer = '9434afb379a46d6c141de9c9e5b94fcf';

        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('manufacturerlist', $sActManufacturer, $this->getConfig()->getShopHomeURL());

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sActManufacturer);
        $oManufacturer->setIsVisible(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getManufacturerTree", "getActManufacturer"]);
        $oView->method('getManufacturerTree')->willReturn($oManufacturerTree);
        $oView->method('getActManufacturer')->willReturn($oManufacturer);

        $this->assertSame("page/list/list", $oView->render());
    }

    /**
     * Testing render() when passing existing manufacturer, but requested page number exceeds possible
     */
    public function testRenderExistingManufacturerRequestedPageNumerExceedsPossible()
    {
        $this->setRequestParameter("pgNr", 999);
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $sActManufacturer = '9434afb379a46d6c141de9c9e5b94fcf';

        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('manufacturerlist', $sActManufacturer, $this->getConfig()->getShopHomeURL());

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sActManufacturer);
        $oManufacturer->setIsVisible(true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getManufacturerTree", "getActManufacturer"]);
        $oView->method('getManufacturerTree')->willReturn($oManufacturerTree);
        $oView->method('getActManufacturer')->willReturn($oManufacturer);

        try {
            $oView->render();
        } catch (Exception $exception) {
            $this->assertSame('OK', $exception->getMessage(), 'failed redirect on inactive category');

            return;
        }

        $this->fail('failed redirect on inactive category');
    }

    /**
     * Testign render() when passing existing manufacturer, but requested page number exceeds possible
     */
    public function testRenderManufacturerHasNoProductsAssigned()
    {
        $this->setRequestParameter("pgNr", 999);
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['handlePageNotFoundError']);
        $utils->expects($this->once())->method('handlePageNotFoundError');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $actManufacturer = '9434afb379a46d6c141de9c9e5b94fcf';

        $manufacturerTree = oxNew('oxManufacturerList');
        $manufacturerTree->buildManufacturerTree('manufacturerlist', $actManufacturer, $this->getConfig()->getShopHomeURL());

        $manufacturer = oxNew('oxManufacturer');
        $manufacturer->setId("123");
        $manufacturer->setIsVisible(true);

        $view = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getManufacturerTree", "getActManufacturer"]);
        $view->method('getManufacturerTree')->willReturn($manufacturerTree);
        $view->method('getActManufacturer')->willReturn($manufacturer);

        $view->render();
    }

    /**
     * manufacturerlist::setItemSorting() & getSorting() test case
     */
    public function testSetItemSortingGetSorting()
    {
        $this->setRequestParameter('mnid', "testMnId");

        $oView = oxNew('manufacturerlist');
        $oView->setItemSorting("testCnid", "testBy", "testDir");
        $this->assertSame(["sortby" => "testBy", "sortdir" => "testDir"], $oView->getSorting("testCnid"));
    }

    /**
     * manufacturerlist::generatePageNavigationUrl() test case
     */
    public function testGeneratePageNavigationUrlSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ["getLink"]);
        $oManufacturer->expects($this->atLeastOnce())->method('getLink')->willReturn("testLink");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getActManufacturer"]);
        $oView->expects($this->once())->method('getActManufacturer')->willReturn($oManufacturer);
        $this->assertSame("testLink", $oView->generatePageNavigationUrl());
    }

    /**
     * manufacturerlist::generatePageNavigationUrl() test case
     */
    public function testGeneratePageNavigationUrl()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getActManufacturer"]);
        $oView->expects($this->never())->method('getActManufacturer');
        $oView->generatePageNavigationUrl();
    }

    /**
     * manufacturerlist::addPageNrParam() test case
     */
    public function testAddPageNrParamSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ["getBaseSeoLink"]);
        $oManufacturer->expects($this->once())->method('getBaseSeoLink')->willReturn("testLink");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getActManufacturer"]);
        $oView->expects($this->once())->method('getActManufacturer')->willReturn($oManufacturer);
        $this->assertSame("testLink", $oView->addPageNrParam("testUrl", 1));
    }

    /**
     * manufacturerlist::addPageNrParam() test case
     */
    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getActManufacturer"]);
        $oView->expects($this->never())->method('getActManufacturer');
        $oView->addPageNrParam("testUrl", 1);
    }

    /**
     * Test get additionall url parameters.
     */
    public function testGetAddUrlParams()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("testManufacturerId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getActManufacturer"]);
        $oView->expects($this->once())->method('getActManufacturer')->willReturn($oManufacturer);

        $oUBaseView = oxNew('oxUBase');
        $sTestParams = $oUBaseView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "listtype=manufacturer";
        $sTestParams .= "&amp;mnid=testManufacturerId";

        $this->assertSame($sTestParams, $oView->getAddUrlParams());
    }

    /**
     * Test get path.
     */
    public function testGetTreePath()
    {
        $oManufacturerList = $this->getMock(\OxidEsales\Eshop\Application\Model\ManufacturerList::class, ["getPath"]);
        $oManufacturerList->expects($this->once())->method('getPath')->willReturn("testPath");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getManufacturerTree"]);
        $oView->expects($this->once())->method('getManufacturerTree')->willReturn($oManufacturerList);

        $this->assertSame("testPath", $oView->getTreePath());
    }

    /**
     * Test get subject.
     */
    public function testGetSubject()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getActManufacturer"]);
        $oView->expects($this->once())->method('getActManufacturer')->willReturn("testActManufacturer");

        $this->assertSame("testActManufacturer", $oView->getSubject(0));
    }

    /**
     * Test process article urls.
     */
    public function testProcessListArticles()
    {
        oxNew('oxArticle');

        $oListView = oxNew('manufacturerlist');
        $this->assertSame(2, $oListView->getProductLinkType());
    }

    /**
     * Test get sub category list.
     */
    public function testGetSubCatList()
    {
        $this->setRequestParameter('mnid', 'root');
        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('manufacturerlist', 'root', $this->getConfig()->getShopHomeURL());

        $oManufacturer = oxNew('Manufacturerlist');
        $oManufacturer->setManufacturerTree($oManufacturerTree);

        $oTree = $oManufacturer->getSubCatList();

        $this->assertEquals($oManufacturerTree, $oTree);
    }

    /**
     * Test if there sub categories exist.
     */
    public function testHasVisibleSubCats()
    {
        $this->setRequestParameter('mnid', 'root');
        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('manufacturerlist', 'root', $this->getConfig()->getShopHomeURL());

        $oManufacturer = oxNew('Manufacturerlist');
        $oManufacturer->setManufacturerTree($oManufacturerTree);

        $this->assertSame(4, $oManufacturer->hasVisibleSubCats());
    }

    /**
     * Test get article list and count.
     */
    public function testGetArticleListAndCount()
    {
        //testing over mock
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE' ? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';

        $this->setRequestParameter('cnid', $sManufacturerId);
        $this->getConfig()->setConfigParam('iNrofCatArticles', 20);
        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('Manufacturerlist', $sManufacturerId, $this->getConfig()->getShopHomeURL());

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);
        $oManufacturer->setIsVisible(true);


        $oManufacturerList = $this->getProxyClass("Manufacturerlist");
        $oManufacturerList->setManufacturerTree($oManufacturerTree);
        $oManufacturerList->setNonPublicVar("_oActManufacturer", $oManufacturer);

        $oArtList = $oManufacturerList->getArticleList();

        $this->assertCount(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->getManufacturerArticleCount($sManufacturerId), $oArtList);
    }

    /**
     * Test get page navigation.
     */
    public function testGetPageNavigation()
    {
        $oManufacturer = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ['generatePageNavigation']);
        $oManufacturer->method('generatePageNavigation')->willReturn("aaa");
        $this->assertSame('aaa', $oManufacturer->getPageNavigation());
    }

    /**
     * Test get list title.
     */
    public function testGetCatTitle()
    {
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE' ? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);

        $oManufacturerList = $this->getProxyClass("Manufacturerlist");
        $oManufacturerList->setManufacturerTree(oxNew('oxManufacturerList'));
        $oManufacturerList->setNonPublicVar("_oActManufacturer", $oManufacturer);

        $this->assertEquals($oManufacturer->oxmanufacturers__oxtitle->value, $oManufacturerList->getTitle());
    }

    /**
     * Test get active category.
     */
    public function testGetActiveCategory()
    {
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE' ? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);

        $oManufacturerList = $this->getProxyClass("Manufacturerlist");
        $oManufacturerList->setManufacturerTree(new oxManufacturerList());
        $oManufacturerList->setNonPublicVar("_oActManufacturer", $oManufacturer);

        $this->assertEquals($oManufacturer, $oManufacturerList->getActiveCategory());
    }

    /**
     * Test get category path.
     */
    public function testgetCatTreePath()
    {
        $this->setRequestParameter('cnid', 'v_root');
        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('Manufacturerlist', 'v_root', $this->getConfig()->getShopHomeURL());

        $oManufacturer = $this->getProxyClass("Manufacturerlist");
        $oManufacturer->setManufacturerTree($oManufacturerTree);
        $oManufacturer->init();

        $this->assertEquals($oManufacturerTree->getPath(), $oManufacturer->getCatTreePath());
    }

    /**
     * Test noIndex property getter.
     */
    public function testNoIndex()
    {
        $oManufacturer = oxNew('manufacturerlist');
        $this->assertSame(0, $oManufacturer->noIndex());
    }

    /**
     * Test get tile sufix.
     */
    public function testGetTitleSuffix()
    {
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE' ? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);

        $oManufacturer->oxManufacturer__oxshowsuffix = new oxField(1);

        $oManufacturerList = $this->getProxyClass("Manufacturerlist");
        $oManufacturerList->setManufacturerTree(new oxManufacturerList());
        $oManufacturerList->setNonPublicVar("_oActManufacturer", $oManufacturer);

        $this->assertSame('online kaufen', $oManufacturerList->getTitleSuffix());
    }

    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oCat1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ['getLink']);
        $oCat1->expects($this->once())->method('getLink')->willReturn('linkas1');
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ['getLink']);
        $oCat2->expects($this->once())->method('getLink')->willReturn('linkas2');
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock('oxmanufacturelist', ['getPath']);
        $oCategoryList->expects($this->once())->method('getPath')->willReturn([$oCat1, $oCat2]);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ManufacturerListController::class, ["getManufacturerTree"]);
        $oView->expects($this->once())->method('getManufacturerTree')->willReturn($oCategoryList);

        $this->assertGreaterThanOrEqual(1, count($oView->getBreadCrumb()));
    }
}
