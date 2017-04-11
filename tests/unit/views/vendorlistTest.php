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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Testing oxVendorList class
 */
class Unit_Views_VendorListTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxTestModules::addFunction('oxVendor', 'cleanRootVendor', '{oxVendor::$_aRootVendor = array();}');
        oxNew('oxvendor')->cleanRootVendor();

        parent::tearDown();
    }

    /**
     * Testing render() when passing existing vendor
     *
     * @return null
     */
    public function testRenderExistingVendor()
    {
        $sActVendor = "9437def212dc37c66f90cc249143510a";

        $oVendorTree = oxNew('oxvendorlist');
        $oVendorTree->buildVendorTree('vendorlist', $sActVendor, oxRegistry::getConfig()->getShopHomeURL());

        $oVendor = oxNew('oxvendor');
        $oVendor->load($sActVendor);

        $oView = $this->getMock("vendorlist", array("getVendorTree", "getActVendor"));
        $oView->expects($this->any())->method('getVendorTree')->will($this->returnValue($oVendorTree));
        $oView->expects($this->any())->method('getActVendor')->will($this->returnValue($oVendor));

        $this->assertEquals("page/list/list.tpl", $oView->render());
    }

    /**
     * Testign render() when passing existing vendor, but requested page number exceeds possible
     *
     * @return null
     */
    public function testRenderExistingVendorRequestedPageNumerExceedsPossible()
    {
        $this->getConfig()->setRequestParameter("pgNr", 999);
        $this->getConfig()->setRequestParameter("cnid", 'cnid');
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $sActVendor = "9437def212dc37c66f90cc249143510a";

        $oVendorTree = oxNew('oxvendorlist');
        $oVendorTree->buildVendorTree('vendorlist', $sActVendor, oxRegistry::getConfig()->getShopHomeURL());

        $oVendor = oxNew('oxvendor');
        $oVendor->load($sActVendor);

        $oView = $this->getMock("vendorlist", array("getVendorTree", "getActVendor"));
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
     *
     * @return null
     */
    public function testRenderVendorHasNoProductsAssigned()
    {
        modConfig::setRequestParameter("pgNr", 999);
        oxTestModules::addFunction("oxUtils", "handlePageNotFoundError", "{ throw new Exception('OK'); }");

        $sActVendor = "9437def212dc37c66f90cc249143510a";

        $oVendorTree = oxNew('oxvendorlist');
        $oVendorTree->buildVendorTree('vendorlist', $sActVendor, oxRegistry::getConfig()->getShopHomeURL());

        $oVendor = oxNew('oxvendor');
        $oVendor->setId("123");

        $oView = $this->getMock("vendorlist", array("getVendorTree", "getActVendor"));
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
        $oVendor = new oxVendor();
        $oVendor->setId("testVendorId");

        $oView = $this->getMock("vendorlist", array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue($oVendor));

        $oUBaseView = new oxUBase();
        $sTestParams = $oUBaseView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "listtype=vendor";
        $sTestParams .= "&amp;cnid=v_testVendorId";

        $this->assertEquals($sTestParams, $oView->getAddUrlParams());
    }

    public function testGetTreePath()
    {
        $this->getConfig()->setRequestParameter("cnid", 'cnid');

        $oVendorList = $this->getMock("oxvendorlist", array("getPath"));
        $oVendorList->expects($this->once())->method('getPath')->will($this->returnValue("testPath"));

        $oView = $this->getMock("vendorlist", array("getVendorTree"));
        $oView->expects($this->once())->method('getVendorTree')->will($this->returnValue($oVendorList));

        $this->assertEquals("testPath", $oView->getTreePath());
    }

    public function testGetSubject()
    {
        $oView = $this->getMock("vendorlist", array("getActVendor"));
        $oView->expects($this->once())->method('getActVendor')->will($this->returnValue("testActVendor"));

        $this->assertEquals("testActVendor", $oView->UNITgetSubject(0));
    }

    public function testProcessListArticles()
    {
        $oArticle = new oxArticle();

        $oListView = new vendorlist();
        $this->assertEquals(1, $oListView->UNITgetProductLinkType());
    }

    public function testGetSubCatList()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        modConfig::setRequestParameter('cnid', 'v_root');
        $oVendorTree = new oxvendorlist();
        $oVendorTree->buildVendorTree('vendorlist', 'v_root', oxRegistry::getConfig()->getShopHomeURL());

        $oVendor = new vendorlist();
        $oVendor->setVendorTree($oVendorTree);
        $oTree = $oVendor->getSubCatList();


        $this->assertEquals($oVendorTree, $oTree);
    }

    public function testHasVisibleSubCats()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        modConfig::setRequestParameter('cnid', 'v_root');
        $oVendorTree = new oxvendorlist();
        $oVendorTree->buildVendorTree('vendorlist', 'v_root', oxRegistry::getConfig()->getShopHomeURL());

        $oVendor = new vendorlist();
        $oVendor->setVendorTree($oVendorTree);

        $this->assertEquals(3, $oVendor->hasVisibleSubCats());
    }

    public function testGetArticleListAndCount()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        //testing over mock
        $sVendorId = '68342e2955d7401e6.18967838';


        modConfig::setRequestParameter('cnid', $sVendorId);
        modConfig::getInstance()->setConfigParam('iNrofCatArticles', 20);
        // $oVendorTree = new oxvendorlist();
        // $oVendorTree->buildVendorTree( 'vendorlist', $sVendorId, oxRegistry::getConfig()->getShopHomeURL() );

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oVendorList = new vendorList();
        // $oVendorList->setVendorTree( $oVendorTree );
        $oVendorList->setActVendor($oVendor);
        $oArtList = $oVendorList->getArticleList();

        $this->assertEquals(oxRegistry::get("oxUtilsCount")->getVendorArticleCount($sVendorId), $oArtList->count());
    }

    public function testGetPageNavigation()
    {
        $oVendor = $this->getMock('vendorlist', array('generatePageNavigation'));
        $oVendor->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oVendor->getPageNavigation());
    }

    public function testGeneratePageNavigationUrl()
    {
        $oVendor = $this->getMock('alist', array('generatePageNavigationUrl', 'getActVendor'));
        $oVendor->expects($this->any())->method('generatePageNavigationUrl')->will($this->returnValue("aaa"));
        $oVendor->expects($this->any())->method('getActVendor')->will($this->returnValue(false));
        $this->assertEquals('aaa', $oVendor->generatePageNavigationUrl());
    }

    public function testGeneratePageNavigationUrlIfSeo()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $sVendorId = '68342e2955d7401e6.18967838';

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);
        $this->assertEquals($oVendor->getLink(), $oVendorList->generatePageNavigationUrl());
    }

    public function testGetCatTitle()
    {
        $sVendorId = '68342e2955d7401e6.18967838';

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setVendorTree(new oxvendorlist());
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);

        $this->assertEquals($oVendor->oxvendor__oxtitle->value, $oVendorList->getTitle());
    }

    public function testGetActiveCategory()
    {
        $sVendorId = '68342e2955d7401e6.18967838';


        $this->getConfig()->setRequestParameter("cnid", $sVendorId);

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setVendorTree(new oxvendorlist());
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);

        $this->assertEquals($oVendor, $oVendorList->getActiveCategory());
    }

    public function testGetCatTreePath()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');
        modConfig::setRequestParameter('cnid', 'v_root');
        $oVendorTree = new oxvendorlist();
        $oVendorTree->buildVendorTree('vendorlist', 'v_root', oxRegistry::getConfig()->getShopHomeURL());

        $oVendor = $this->getProxyClass("vendorlist");
        $oVendor->setVendorTree($oVendorTree);
        $oVendor->init();

        $this->assertEquals($oVendorTree->getPath(), $oVendor->getCatTreePath());
    }

    public function testNoIndex()
    {
        $oVendor = new vendorlist();
        $this->assertTrue(0 === $oVendor->noIndex());
    }

    public function testGetTitleSuffix()
    {
        $sVendorId = '68342e2955d7401e6.18967838';

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);
        $oVendor->oxvendor__oxshowsuffix = new oxField(1);

        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setVendorTree(new oxvendorlist());
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);

        $this->assertEquals('online kaufen', $oVendorList->getTitleSuffix());
    }

    public function testAddPageNrParamIfSeo()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $sVendorId = '68342e2955d7401e6.18967838';
        $sRez = oxRegistry::getConfig()->getShopURL() . "Nach-Lieferant/Haller-Stahlwaren/3/";

        $oVendor = new oxVendor();
        $oVendor->load($sVendorId);
        $oVendorList = $this->getProxyClass("vendorlist");
        $oVendorList->setNonPublicVar("_oActVendor", $oVendor);
        $this->assertEquals($sRez, $oVendorList->UNITaddPageNrParam('aa', 2));
    }

    public function testAddPageNrParam()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oVendorList = $this->getMock("vendorlist", array("getActVendor"));
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
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oCat1 = $this->getMock('oxvendor', array('getLink'));
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock('oxvendor', array('getLink'));
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock('oxvendorlist', array('getPath'));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue(array($oCat1, $oCat2)));

        $oView = $this->getMock("vendorlist", array("getVendorTree"));
        $oView->expects($this->once())->method('getVendorTree')->will($this->returnValue($oCategoryList));

        $this->assertTrue(count($oView->getBreadCrumb()) >= 1);
    }

}
