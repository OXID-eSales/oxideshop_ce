<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \Exception;
use \oxvendorlist;
use \oxField;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxVendorList class
 */
class VendorListTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        oxTestModules::addFunction('oxVendor', 'cleanRootVendor', '{oxVendor::$_aRootVendor = array();}');
        oxNew('oxvendor')->cleanRootVendor();

        parent::tearDown();
    }

    /**
     * Testing render() when passing existing vendor
     */
    public function testRenderExistingVendor()
    {
        $sActVendor = $this->getTestConfig()->getShopEdition() == 'EE'? "9437def212dc37c66f90cc249143510a" : '9437def212dc37c66f90cc249143510a';

        $oVendorTree = oxNew('oxVendorList');
        $oVendorTree->buildVendorTree('vendorlist', $sActVendor, $this->getConfig()->getShopHomeURL());

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sActVendor);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getVendorTree", "getActVendor"));
        $oView->expects($this->any())->method('getVendorTree')->will($this->returnValue($oVendorTree));
        $oView->expects($this->any())->method('getActVendor')->will($this->returnValue($oVendor));

        $this->assertEquals("page/list/list.tpl", $oView->render());
    }

    /**
     * Testign render() when passing existing vendor, but requested page number exceeds possible
     */
    public function testRenderExistingVendorRequestedPageNumerExceedsPossible()
    {
        $this->setRequestParameter("pgNr", 999);
        $this->setRequestParameter("cnid", 'cnid');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $sActVendor = $this->getTestConfig()->getShopEdition() == 'EE'? "9437def212dc37c66f90cc249143510a" : '9437def212dc37c66f90cc249143510a';

        $oVendorTree = oxNew('oxVendorList');
        $oVendorTree->buildVendorTree('vendorlist', $sActVendor, $this->getConfig()->getShopHomeURL());

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sActVendor);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getVendorTree", "getActVendor"));
        $oView->expects($this->any())->method('getVendorTree')->will($this->returnValue($oVendorTree));
        $oView->expects($this->any())->method('getActVendor')->will($this->returnValue($oVendor));

        try {
            $oView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals('OK', $oExcp->getMessage(), 'failed redirect on inactive category');

            return;
        }

        $this->fail('failed redirect on inactive category');
    }

    /**
     * Testign render() when passing existing vendor, but requested page number exceeds possible
     */
    public function testRenderVendorHasNoProductsAssigned()
    {
        $this->setRequestParameter("pgNr", 999);
        oxTestModules::addFunction("oxUtils", "handlePageNotFoundError", "{ throw new Exception('OK'); }");

        $sActVendor = $this->getTestConfig()->getShopEdition() == 'EE' ? "9437def212dc37c66f90cc249143510a" : "9437def212dc37c66f90cc249143510a";

        $oVendorTree = oxNew('oxVendorList');
        $oVendorTree->buildVendorTree('vendorlist', $sActVendor, $this->getConfig()->getShopHomeURL());

        $oVendor = oxNew('oxVendor');
        $oVendor->setId("123");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getVendorTree", "getActVendor"));
        $oView->expects($this->any())->method('getVendorTree')->will($this->returnValue($oVendorTree));
        $oView->expects($this->any())->method('getActVendor')->will($this->returnValue($oVendor));

        try {
            $oView->render();
        } catch (Exception $oExcp) {
            $this->fail('failed redirect on inactive category');
        }
    }

    public function testGetAddUrlParams()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId("testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));

        $oUBaseView = oxNew('oxUBase');
        $sTestParams = $oUBaseView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "listtype=vendor";
        $sTestParams .= "&amp;cnid=v_testVendorId";

        $this->assertEquals($sTestParams, $oView->getAddUrlParams());
    }

    public function testGetTreePath()
    {
        $this->setRequestParameter("cnid", 'cnid');

        $oVendorList = $this->getMock(\OxidEsales\Eshop\Application\Model\VendorList::class, array("getPath"));
        $oVendorList->expects($this->once())->method('getPath')->will($this->returnValue("testPath"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getVendorTree"));
        $oView->expects($this->once())->method('getVendorTree')->will($this->returnValue($oVendorList));

        $this->assertEquals("testPath", $oView->getTreePath());
    }

    public function testGetSubject()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue("testActVendor"));

        $this->assertEquals("testActVendor", $oView->UNITgetSubject(0));
    }

    public function testProcessListArticles()
    {
        $oListView = oxNew('VendorList');
        $this->assertEquals(1, $oListView->UNITgetProductLinkType());
    }

    public function testGetSubCatList()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $this->setRequestParameter('cnid', 'v_root');
        $oVendorTree = oxNew('oxVendorList');
        $oVendorTree->buildVendorTree('vendorlist', 'v_root', $this->getConfig()->getShopHomeURL());

        $oVendor = oxNew('VendorList');
        $oVendor->setVendorTree($oVendorTree);
        $oTree = $oVendor->getSubCatList();


        $this->assertEquals($oVendorTree, $oTree);
    }

    public function testHasVisibleSubCats()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $this->setRequestParameter('cnid', 'v_root');
        $oVendorTree = oxNew('oxVendorList');
        $oVendorTree->buildVendorTree('vendorlist', 'v_root', $this->getConfig()->getShopHomeURL());

        $oVendor = oxNew('VendorList');
        $oVendor->setVendorTree($oVendorTree);

        $this->assertEquals(3, $oVendor->hasVisibleSubCats());
    }

    public function testGetArticleListAndCount()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $sVendorId = $this->getTestConfig()->getShopEdition() == 'EE'? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';

        $this->setRequestParameter('cnid', $sVendorId);
        $this->getConfig()->setConfigParam('iNrofCatArticles', 20);
        // $oVendorTree = new oxvendorlist();
        // $oVendorTree->buildVendorTree( 'vendorlist', $sVendorId, $this->getConfig()->getShopHomeURL() );

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);

        $oVendorList = oxNew('VendorList');
        $oVendorList->setActVendor($oVendor);
        $oArtList = $oVendorList->getArticleList();

        $this->assertEquals(\OxidEsales\Eshop\Core\Registry::getUtilsCount()->getVendorArticleCount($sVendorId), $oArtList->count());
    }

    public function testGetPageNavigation()
    {
        $oVendor = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array('generatePageNavigation'));
        $oVendor->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oVendor->getPageNavigation());
    }

    public function testGeneratePageNavigationUrl()
    {
        $oVendor = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('generatePageNavigationUrl', 'getActVendor'));
        $oVendor->expects($this->any())->method('generatePageNavigationUrl')->will($this->returnValue("aaa"));
        $oVendor->expects($this->any())->method('getActVendor')->will($this->returnValue(false));
        $this->assertEquals('aaa', $oVendor->generatePageNavigationUrl());
    }

    public function testGeneratePageNavigationUrlIfSeo()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $sVendorId = $this->getTestConfig()->getShopEdition() == 'EE'? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';
        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);
        $this->assertEquals($oVendor->getLink(), $oVendorList->generatePageNavigationUrl());
    }

    public function testGetCatTitle()
    {
        $sVendorId = $this->getTestConfig()->getShopEdition() == 'EE'? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';
        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setVendorTree(new oxvendorlist());
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);

        $this->assertEquals($oVendor->oxvendor__oxtitle->value, $oVendorList->getTitle());
    }

    public function testGetActiveCategory()
    {
        $sVendorId = $this->getTestConfig()->getShopEdition() == 'EE'? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';

        $this->setRequestParameter("cnid", $sVendorId);

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setVendorTree(new oxvendorlist());
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);

        $this->assertEquals($oVendor, $oVendorList->getActiveCategory());
    }

    public function testGetCatTreePath()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');
        $this->setRequestParameter('cnid', 'v_root');
        $oVendorTree = oxNew('oxVendorList');
        $oVendorTree->buildVendorTree('vendorlist', 'v_root', $this->getConfig()->getShopHomeURL());

        $oVendor = $this->getProxyClass("vendorlist");
        $oVendor->setVendorTree($oVendorTree);
        $oVendor->init();

        $this->assertEquals($oVendorTree->getPath(), $oVendor->getCatTreePath());
    }

    public function testNoIndex()
    {
        $oVendor = oxNew('VendorList');
        $this->assertTrue(0 === $oVendor->noIndex());
    }

    public function testGetTitleSuffix()
    {
        $sVendorId = $this->getTestConfig()->getShopEdition() == 'EE'? 'd2e44d9b31fcce448.08890330' : '68342e2955d7401e6.18967838';
        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);
        $oVendor->oxvendor__oxshowsuffix = new oxField(1);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setVendorTree(new oxvendorlist());
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);

        $this->assertEquals('online kaufen', $oVendorList->getTitleSuffix());
    }

    public function testAddPageNrParamIfSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $sVendorId = 'd2e44d9b31fcce448.08890330';
            $sRez = $this->getConfig()->getShopURL() . "Nach-Lieferant/Hersteller-1/?pgNr=2";
        } else {
            $sVendorId = '68342e2955d7401e6.18967838';
            $sRez = $this->getConfig()->getShopURL() . "Nach-Lieferant/Haller-Stahlwaren/?pgNr=2";
        }

        $oVendor = oxNew('oxVendor');
        $oVendor->load($sVendorId);
        $oVendorList = $this->getProxyClass("VendorList");
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);
        $this->assertEquals($sRez, $oVendorList->UNITaddPageNrParam('aa', 2));
    }

    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oVendorList = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getActVendor"));
        $oVendorList->expects($this->atLeastOnce())->method('getActVendor')->will($this->returnValue(null));

        $this->assertEquals("aaaa?pgNr=2", $oVendorList->UNITaddPageNrParam('aaaa', 2));
    }

    public function testSetGetItemSorting()
    {
        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setItemSorting('v_aaa', 'oxprice', 'desc');
        $aSort = array("sortby" => "oxprice", "sortdir" => "desc");
        $this->assertEquals($aSort, $oVendorList->getSorting('v_aaa'));
    }

    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oCat1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Vendor::class, array('getLink'));
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Vendor::class, array('getLink'));
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\VendorList::class, array('getPath'));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue(array($oCat1, $oCat2)));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\VendorListController::class, array("getVendorTree"));
        $oView->expects($this->once())->method('getVendorTree')->will($this->returnValue($oCategoryList));

        $this->assertTrue(count($oView->getBreadCrumb()) >= 1);
    }
}
