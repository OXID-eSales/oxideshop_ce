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

use \Exception;
use \oxManufacturerList;
use \oxField;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxManufacturerList class
 */
class ManufacturerlistTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();
        oxTestModules::addFunction('oxSeoEncoderManufacturer', '_saveToDb', '{return null;}');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
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
        $oManufacturer = $this->getMock("oxManufacturer", array("getId"));
        $oManufacturer->expects($this->atLeastOnce())->method('getId')->will($this->returnValue("testId"));

        $oView = $this->getMock("manufacturerlist", array("getManufacturerTree", "getActManufacturer", "getArticleList", "_processListArticles", "_checkRequestedPage"));
        $oView->expects($this->any())->method('getManufacturerTree')->will($this->returnValue(true));
        $oView->expects($this->atLeastOnce())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $oView->expects($this->atLeastOnce())->method('getArticleList');
        $oView->expects($this->once())->method('_processListArticles');
        $oView->expects($this->once())->method('_checkRequestedPage');

        $this->assertEquals("page/list/list.tpl", $oView->render());
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

        $oView = $this->getMock("manufacturerlist", array("getManufacturerTree", "getActManufacturer"));
        $oView->expects($this->any())->method('getManufacturerTree')->will($this->returnValue($oManufacturerTree));
        $oView->expects($this->any())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        $this->assertEquals("page/list/list.tpl", $oView->render());
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

        $oView = $this->getMock("manufacturerlist", array("getManufacturerTree", "getActManufacturer"));
        $oView->expects($this->any())->method('getManufacturerTree')->will($this->returnValue($oManufacturerTree));
        $oView->expects($this->any())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        try {
            $oView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals('OK', $oExcp->getMessage(), 'failed redirect on inactive category');

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
        oxTestModules::addFunction("oxUtils", "handlePageNotFoundError", "{ throw new Exception('OK'); }");

        $sActManufacturer = '9434afb379a46d6c141de9c9e5b94fcf';

        $oManufacturerTree = oxNew('oxManufacturerList');
        $oManufacturerTree->buildManufacturerTree('manufacturerlist', $sActManufacturer, $this->getConfig()->getShopHomeURL());

        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("123");
        $oManufacturer->setIsVisible(true);

        $oView = $this->getMock("manufacturerlist", array("getManufacturerTree", "getActManufacturer"));
        $oView->expects($this->any())->method('getManufacturerTree')->will($this->returnValue($oManufacturerTree));
        $oView->expects($this->any())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        try {
            $oView->render();
        } catch (Exception $oExcp) {
            $this->fail('failed redirect on inactive category');
        }
    }

    /**
     * manufacturerlist::setItemSorting() & getSorting() test case
     */
    public function testSetItemSortingGetSorting()
    {
        $this->setRequestParameter('mnid', "testMnId");

        $oView = oxNew('manufacturerlist');
        $oView->setItemSorting("testCnid", "testBy", "testDir");
        $this->assertEquals(array("sortby" => "testBy", "sortdir" => "testDir"), $oView->getSorting("testCnid"));
    }

    /**
     * manufacturerlist::generatePageNavigationUrl() test case
     */
    public function testGeneratePageNavigationUrlSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oManufacturer = $this->getMock("oxManufacturer", array("getLink"));
        $oManufacturer->expects($this->atLeastOnce())->method('getLink')->will($this->returnValue("testLink"));

        $oView = $this->getMock("manufacturerlist", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals("testLink", $oView->generatePageNavigationUrl());
    }

    /**
     * manufacturerlist::generatePageNavigationUrl() test case
     */
    public function testGeneratePageNavigationUrl()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');

        $oView = $this->getMock("manufacturerlist", array("getActManufacturer"));
        $oView->expects($this->never())->method('getActManufacturer');
        $oView->generatePageNavigationUrl();
    }

    /**
     * manufacturerlist::_addPageNrParam() test case
     */
    public function testAddPageNrParamSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oManufacturer = $this->getMock("oxManufacturer", array("getBaseSeoLink"));
        $oManufacturer->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue("testLink"));

        $oView = $this->getMock("manufacturerlist", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));
        $this->assertEquals("testLink", $oView->UNITaddPageNrParam("testUrl", 1));
    }

    /**
     * manufacturerlist::_addPageNrParam() test case
     */
    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');

        $oView = $this->getMock("manufacturerlist", array("getActManufacturer"));
        $oView->expects($this->never())->method('getActManufacturer');
        $oView->UNITaddPageNrParam("testUrl", 1);
    }

    /**
     * Test get additionall url parameters.
     */
    public function testGetAddUrlParams()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("testManufacturerId");

        $oView = $this->getMock("manufacturerlist", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue($oManufacturer));

        $oUBaseView = oxNew('oxUBase');
        $sTestParams = $oUBaseView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "listtype=manufacturer";
        $sTestParams .= "&amp;mnid=testManufacturerId";

        $this->assertEquals($sTestParams, $oView->getAddUrlParams());
    }

    /**
     * Test get path.
     */
    public function testGetTreePath()
    {
        $oManufacturerList = $this->getMock("oxManufacturerList", array("getPath"));
        $oManufacturerList->expects($this->once())->method('getPath')->will($this->returnValue("testPath"));

        $oView = $this->getMock("manufacturerlist", array("getManufacturerTree"));
        $oView->expects($this->once())->method('getManufacturerTree')->will($this->returnValue($oManufacturerList));

        $this->assertEquals("testPath", $oView->getTreePath());
    }

    /**
     * Test get subject.
     */
    public function testGetSubject()
    {
        $oView = $this->getMock("manufacturerlist", array("getActManufacturer"));
        $oView->expects($this->once())->method('getActManufacturer')->will($this->returnValue("testActManufacturer"));

        $this->assertEquals("testActManufacturer", $oView->UNITgetSubject(0));
    }

    /**
     * Test process article urls.
     */
    public function testProcessListArticles()
    {
        $oArticle = oxNew('oxArticle');

        $oListView = oxNew('manufacturerlist');
        $this->assertEquals(2, $oListView->UNITgetProductLinkType());
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

        $this->assertEquals(4, $oManufacturer->hasVisibleSubCats());
    }

    /**
     * Test get article list and count.
     */
    public function testGetArticleListAndCount()
    {
        //testing over mock
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE'? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';

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

        $this->assertEquals(oxRegistry::get("oxUtilsCount")->getManufacturerArticleCount($sManufacturerId), count($oArtList));
    }

    /**
     * Test get page navigation.
     */
    public function testGetPageNavigation()
    {
        $oManufacturer = $this->getMock('Manufacturerlist', array('generatePageNavigation'));
        $oManufacturer->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oManufacturer->getPageNavigation());
    }

    /**
     * Test get list title.
     */
    public function testGetCatTitle()
    {
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE'? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
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
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE'? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
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
        $this->assertTrue(0 === $oManufacturer->noIndex());
    }

    /**
     * Test get tile sufix.
     */
    public function testGetTitleSuffix()
    {
        $sManufacturerId = $this->getTestConfig()->getShopEdition() == 'EE'? '88a996f859f94176da943f38ee067984' : 'fe07958b49de225bd1dbc7594fb9a6b0';
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->load($sManufacturerId);
        $oManufacturer->oxManufacturer__oxshowsuffix = new oxField(1);

        $oManufacturerList = $this->getProxyClass("Manufacturerlist");
        $oManufacturerList->setManufacturerTree(new oxManufacturerList());
        $oManufacturerList->setNonPublicVar("_oActManufacturer", $oManufacturer);

        $this->assertEquals('online kaufen', $oManufacturerList->getTitleSuffix());
    }

    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {

        $oCat1 = $this->getMock('oxManufacturer', array('getLink'));
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock('oxManufacturer', array('getLink'));
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock('oxmanufacturelist', array('getPath'));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue(array($oCat1, $oCat2)));

        $oView = $this->getMock("manufacturerlist", array("getManufacturerTree"));
        $oView->expects($this->once())->method('getManufacturerTree')->will($this->returnValue($oCategoryList));

        $this->assertTrue(count($oView->getBreadCrumb()) >= 1);

    }

}
