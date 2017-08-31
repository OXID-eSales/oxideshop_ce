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
 * Tests for aList class
 */
class Unit_Views_alistTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // deleting test data
        oxDb::getDb()->execute("delete from oxseo where oxtype != 'static' ");

        parent::tearDown();
    }

    /**
     * Test get added url parameters.
     *
     * @return null
     */
    public function testGetAddUrlParams()
    {
        $this->setRequestParam("pgNr", 999);
        $this->setConfigParam('blSeoMode', false);

        $oView = new aList();

        $oUBaseView = new oxUBase();
        $sTestParams = $oUBaseView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "pgNr=999";

        $this->assertEquals($sTestParams, $oView->getAddUrlParams());
    }

    /**
     * Test get added seo url parameters.
     *
     * @return null
     */
    public function testGetAddSeoUrlParams()
    {
        $oView = new alist();
        $this->assertNull($oView->getAddSeoUrlParams());
    }

    /**
     * Test get page title sufix.
     *
     * @return null
     */
    public function testGetTitlePageSuffix()
    {
        $oView = $this->getMock("alist", array("getActPage"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(0));

        $this->assertNull($oView->getTitlePageSuffix());

        $oView = $this->getMock("alist", array("getActPage"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(1));

        $this->assertEquals(oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getTitlePageSuffix());
    }

    /**
     * Test get page meta description sufix.
     *
     * @return null
     */
    public function testGetMetaDescription()
    {
        $sCatId = '8a142c3e60a535f16.78077188';
        $sPrefix = "Wohnen - Uhren. OXID eShop 4";

        $oCategory = new oxCategory();
        $oCategory->load($sCatId);

        $oView = $this->getMock("alist", array("getActPage", "getActiveCategory"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals($sPrefix . ", " . oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getMetaDescription());
    }

    /**
     * Test get category path.
     *
     * @return null
     */
    public function testGetTreePath()
    {
        $oCategoryList = $this->getMock("oxcategorylist", array("getPath"));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue("testPath"));

        $oView = $this->getMock("alist", array("getCategoryTree"));
        $oView->expects($this->once())->method('getCategoryTree')->will($this->returnValue($oCategoryList));

        $this->assertEquals("testPath", $oView->getTreePath());
    }

    /**
     * Test get canonical url with seo on.
     *
     * @return null
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $oCategory = $this->getMock("oxcategory", array("getBaseSeoLink", "getBaseStdLink", "getLanguage"));
        $oCategory->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $oCategory->expects($this->never())->method('getBaseStdLink');
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oListView = $this->getMock("alist", array("getActPage", "getActiveCategory"));
        $oListView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals("testSeoUrl", $oListView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     *
     * @return null
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $oCategory = $this->getMock("oxcategory", array("getBaseSeoLink", "getBaseStdLink", "getLanguage"));
        $oCategory->expects($this->never())->method('getBaseSeoLink');
        $oCategory->expects($this->once())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oListView = $this->getMock("alist", array("getActPage", "getActiveCategory"));
        $oListView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals("testStdUrl", $oListView->getCanonicalUrl());
    }

    /**
     * Test get noIndex property.
     *
     * @return null
     */
    public function testNoIndex()
    {
        // regular category
        $oListView = new alist();
        $this->assertEquals(0, $oListView->noIndex());
    }

    /**
     * Test list article url processing.
     *
     * @return null
     */
    public function testProcessListArticles()
    {
        $oArticle = $this->getMock('oxarticle', array('setLinkType', "appendStdLink", "appendLink"));
        $oArticle->expects($this->once())->method('setLinkType')->with($this->equalto('xxx'));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testSeoParams'));
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock('oxarticle', array('setLinkType', "appendStdLink", "appendLink"));
        $oArticle->expects($this->once())->method('setLinkType')->with($this->equalto('xxx'));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testSeoParams'));
        $aArticleList[] = $oArticle;

        $oListView = $this->getMock('alist', array('getArticleList', '_getProductLinkType', "getAddUrlParams", "getAddSeoUrlParams"));
        $oListView->expects($this->once())->method('getArticleList')->will($this->returnValue($aArticleList));
        $oListView->expects($this->once())->method('_getProductLinkType')->will($this->returnValue('xxx'));
        $oListView->expects($this->once())->method('getAddUrlParams')->will($this->returnValue('testStdParams'));
        $oListView->expects($this->once())->method('getAddSeoUrlParams')->will($this->returnValue('testSeoParams'));

        $oListView->UNITprocessListArticles();
    }

    /**
     * Test get product link type.
     *
     * @return null
     */
    public function testGetProductLinkType()
    {
        $oCategory = $this->getMock('oxcategory', array('isPriceCategory'));
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(true));

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(3, $oListView->UNITgetProductLinkType());


        $oCategory = $this->getMock('oxcategory', array('isPriceCategory'));
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(false));

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(0, $oListView->UNITgetProductLinkType());
    }

    /**
     * Test render more categoty list page.
     *
     * @return null
     */
    public function testRenderForMoreCategory()
    {
        $this->setRequestParam('cnid', 'oxmore');

        $oMoreCat = oxNew('oxcategory');
        $oMoreCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock("aList", array('setActiveCategory'));
        $oListView->expects($this->once())->method('setActiveCategory')->with($this->equalto($oMoreCat));
        $this->assertEquals('page/list/morecategories.tpl', $oListView->render());
    }

    /**
     * Test load price category articles.
     *
     * @return null
     */
    public function testLoadArticlesForPriceCategory()
    {
        oxTestModules::addFunction("oxarticlelist", "loadPriceArticles", "{ throw new Exception( \$aA[0] . \$aA[1] ); }");

        $oCategory = new oxcategory();
        $oCategory->oxcategories__oxpricefrom = $this->getMock('oxField', array('__get'));
        $oCategory->oxcategories__oxpricefrom->expects($this->exactly(2))->method('__get')->will($this->returnValue(10));
        $oCategory->oxcategories__oxpriceto = $this->getMock('oxField', array('__get'));
        $oCategory->oxcategories__oxpriceto->expects($this->once())->method('__get')->will($this->returnValue(100));

        try {
            $oListView = new aList();
            $oListView->UNITloadArticles($oCategory);
        } catch (Exception $oExcp) {
            $this->assertEquals('10100', $oExcp->getMessage());

            return;
        }
        $this->fail('failed testLoadArticlesForPriceCategory');
    }

    /**
     * Test render inactive category page.
     *
     * @return null
     */
    public function testRenderInactiveCategory()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $oCat = oxNew('oxcategory');
        $oCat->oxcategories__oxactive = new oxField(0, oxField::T_RAW);

        $oListView = $this->getMock("aList", array('getActiveCategory'));
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($oCat));

        try {
            $oListView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals('OK', $oExcp->getMessage(), 'failed redirect on inactive category');

            return;
        }

        $this->fail('failed redirect on inactive category');
    }

    /**
     * Test render actual page count exceeds real page count
     *
     * @return null
     */
    public function testRender_pageCountIsIncorrect()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $oCat = $this->getMock("oxcategory", array('canView'));
        $oCat->expects($this->any())->method('canView')->will($this->returnValue(true));
        $oCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock("aList", array('getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount'));
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($oCat));
        $oListView->expects($this->once())->method('getActPage')->will($this->returnValue(12));
        $oListView->expects($this->once())->method('getPageCount')->will($this->returnValue(10));
        $oListView->expects($this->atLeastOnce())->method('getArticleList');

        try {
            $oListView->render();
        } catch (Exception $oExcp) {
            $this->assertEquals('OK', $oExcp->getMessage());

            return;
        }

        $this->fail('failed redirect when page count is incorrect');
    }

    /**
     * Test render actual page count is 0
     *
     * @return null
     */
    public function testRender_pageCountIsZero()
    {
        oxTestModules::addFunction("oxUtils", "handlePageNotFoundError", "{ throw new Exception('page not found redirect is OK'); }");
        //oxTestModules::addFunction( "oxUtils", "redirect", "{ throw new Exception('OK'); }" );

        $oCat = $this->getMock("oxcategory", array('canView'));
        $oCat->expects($this->any())->method('canView')->will($this->returnValue(true));
        $oCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock("aList", array('getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount'));
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($oCat));
        $oListView->expects($this->once())->method('getActPage')->will( $this->returnValue( 12 ));
        $oListView->expects($this->once())->method('getPageCount')->will($this->returnValue(0));
        $oListView->expects($this->atLeastOnce())->method('getArticleList');

        $this->setExpectedException('Exception', 'page not found redirect is OK');
        $oListView->render();
    }

    /**
     * Test execute article filter.
     *
     * @return null
     */
    public function testExecutefilter()
    {
        $this->setRequestParam('attrfilter', 'somefilter');
        $this->setRequestParam('cnid', 'somecategory');
        $this->setSessionParam('session_attrfilter', null);

        $oListView = new aList();
        $oListView->executefilter();

        $this->assertEquals(array('somecategory' => array('0' => 'somefilter')), $this->getSessionParam('session_attrfilter'));
    }

    /**
     * Test get category subject.
     *
     * @return null
     */
    public function testGetSubject()
    {
        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue('getActiveCategory'));

        $this->assertEquals('getActiveCategory', $oListView->UNITgetSubject(oxRegistry::getLang()->getBaseLanguage()));
    }

    /**
     * Test get list title sufix.
     *
     * @return null
     */
    public function testGetTitleSuffix()
    {
        $oCat = new oxcategory();
        $oCat->oxcategories__oxshowsuffix = $this->getMock('oxfield', array('__get'));
        $oCat->oxcategories__oxshowsuffix->expects($this->once())->method('__get')->will($this->returnValue(true));

        $oShop = new oxshop();
        $oShop->oxshops__oxtitlesuffix = $this->getMock('oxfield', array('__get'));
        $oShop->oxshops__oxtitlesuffix->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock('oxconfig', array('getActiveShop'));
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oListView = $this->getMock('alist', array('getActiveCategory', 'getConfig'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCat));
        $oListView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('testsuffix', $oListView->getTitleSuffix());
    }

    /**
     * Test default Sorting
     *
     * @return null
     */
//    public function testGetDefaultSorting()
//    {
//        //$oList = new aList();
//
//        $oCat = new oxcategory();
//        $sArticleTable = getViewName( 'oxarticles' );
//        $aSorting = array( 'sortby' => $sArticleTable.'.'.'oxid', 'sortdir' => 'asc' );
//
//        $oCat->oxcategories__oxdefsort = new oxField( 'oxid' );
//        //$oList->setActCategory($oCat);
//        $oListView = $this->getMock( 'alist', array( 'getActiveCategory' ) );
//        $oListView->expects( $this->once() )->method( 'getActiveCategory')->will( $this->returnValue( $oCat ) );
//
//        $this->assertEquals($aSorting ,$oListView->getDefaultSorting());
//    }

    /**
     * Test getDefaultSorting when default sorting is not set
     *
     * @return null
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = new aList();

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue(''));
        $oController->setActiveCategory($oCategory);

        $this->assertEquals(null, $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when default sorting is set
     *
     * @return null
     */
    public function testGetDefaultSortingDefinedSorting()
    {
        $oController = new aList();

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oController->setActiveCategory($oCategory);

        $sArticleTable = getViewName('oxarticles');
        $this->assertEquals(array('sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "asc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is undefined
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsUndefined()
    {
        $oController = new aList();

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(null));
        $oController->setActiveCategory($oCategory);

        $sArticleTable = getViewName('oxarticles');
        $this->assertEquals(array('sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "asc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'asc'
     * This might be a little too much, but it's a case
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsAsc()
    {
        $oController = new aList();

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(false));

        $oController->setActiveCategory($oCategory);

        $sArticleTable = getViewName('oxarticles');
        $this->assertEquals(array('sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "asc"), $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsDesc()
    {
        $oController = new aList();

        $oCategory = $this->getMock('oxCategory', array('getDefaultSorting', 'getDefaultSortingMode'));
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(true));

        $oController->setActiveCategory($oCategory);

        $sArticleTable = getViewName('oxarticles');
        $this->assertEquals(array('sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "desc"), $oController->getDefaultSorting());
    }

    /**
     * Test list page navigation and seo url generation.
     *
     * @return null
     */
    public function testGeneratePageNavigationUrlForCategoryPlusSeo()
    {
        $sTestLink = 'testLink';

        $oCat = $this->getMock('oxcategory', array('getLink'));
        $oCat->expects($this->once())->method('getLink')->will($this->returnValue($sTestLink));

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals($sTestLink, $oListView->generatePageNavigationUrl());
    }

    /**
     * Test list page navigation url generation.
     *
     * @return null
     */
    public function testGeneratePageNavigationUrl()
    {
        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue(null));

        $oView = new oxubase();
        $this->assertEquals($oView->generatePageNavigationUrl(), $oListView->generatePageNavigationUrl());
    }

    /**
     * Test PE view id getter.
     *
     * @return null
     */
    public function testGetViewIdPE()
    {

        $this->setRequestParam('cnid', 'xxx');
        $this->setSessionParam('_artperpage', '100');
        $this->setSessionParam('ldtype', 'grid');

        $oView = new oxUBase();
        $sViewId = md5($oView->getViewId() . '|xxx|999|100|grid');

        $oListView = $this->getMock('alist', array('getActPage'));
        $oListView->expects($this->any())->method('getActPage')->will($this->returnValue('999'));
        $this->assertEquals($sViewId, $oListView->getViewId());
    }



    /**
     * Test view id getter when list type is not in session
     */
    public function testGetViewId_ListTypeNotInSession_ReturnsViewIdWithDefaultListTypeIncluded()
    {
        $this->setRequestParam('cnid', 'xxx');
        $this->setSessionParam('_artperpage', '100');
        $this->setSessionParam('session_attrfilter', array('xxx' => array('0' => array('100'))));


        $oView = new oxUBase();
        $sListType = $this->getConfig()->getConfigParam('sDefaultListDisplayType');


        $sViewId = md5($oView->getViewId() . '|xxx|999|100|' . $sListType);

        $oListView = $this->getMock('alist', array('getActPage'));
        $oListView->expects($this->any())->method('getActPage')->will($this->returnValue('999'));
        $this->assertEquals($sViewId, $oListView->getViewId());
    }

    /**
     * Test get category path as string.
     *
     * @return null
     */
    public function testGetCatPathString()
    {
        $oCategory = new oxcategory();
        $oCategory->oxcategories__oxtitle = $this->getMock('oxField', array('__get'));
        $oCategory->oxcategories__oxtitle->expects($this->any())->method('__get')->will($this->returnValue('testTitle'));

        $aPath = array($oCategory, $oCategory);

        $oListView = $this->getMock('alist', array('getCatTreePath'));
        $oListView->expects($this->any())->method('getCatTreePath')->will($this->returnValue($aPath));

        $this->assertEquals(strtolower('testTitle, testTitle'), $oListView->UNITgetCatPathString());
    }

    /**
     * Test prepare list meta description info.
     *
     * @return null
     */
    public function testCollectMetaDescription()
    {
        $oActCat = new oxcategory();
        $oActCat->oxcategories__oxlongdesc = $this->getMock('oxField', array('__get'));
        $oActCat->oxcategories__oxlongdesc->expects($this->once())->method('__get')->will($this->returnValue(''));

        $oArticle = new oxArticle();
        $oArticle->oxarticles__oxtitle = $this->getMock('oxField', array('__get'));
        $oArticle->oxarticles__oxtitle->expects($this->exactly(2))->method('__get')->will($this->returnValue('testtitle'));

        $oArtList = new oxlist();
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock('alist', array('getActiveCategory', 'getArticleList', '_getCatPathString'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oActCat));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('_getCatPathString')->will($this->returnValue($sCatPathString));

        $sMeta = 'sCatPathString - testtitle, testtitle';

        $oView = new oxubase();
        $this->assertEquals($oView->UNITprepareMetaDescription($sMeta), $oListView->UNITcollectMetaDescription(false));
    }

    /**
     * Test prapare list meta keyword info.
     *
     * @return null
     */
    public function testCollectMetaKeyword()
    {
        $oLongDesc = new oxField('testtitle');
        $oArticle = $this->getMock('oxarticle', array('getLongDescription'));
        $oArticle->expects($this->exactly(2))->method('getLongDescription')->will($this->returnValue($oLongDesc));

        $oArtList = new oxlist();
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock('alist', array('getArticleList', '_getCatPathString', '_prepareMetaDescription'));
        $oListView->expects($this->any())->method('_prepareMetaDescription')->with($this->equalTo('sCatPathString, testtitle, testtitle'))->will($this->returnValue('test'));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('_getCatPathString')->will($this->returnValue($sCatPathString));

        $this->assertEquals('test', $oListView->UNITcollectMetaKeyword(null));
    }

    /**
     * Test prepare list meta keyword info longer then 60 symbols.
     *
     * @return null
     */
    public function testCollectMetaKeywordLongerThen60()
    {
        $oLongDesc = new oxField('testtitle Originelle, witzige Geschenkideen - Lifestyle, Trends, Accessoires');
        $oArticle = $this->getMock('oxarticle', array('getLongDescription'));
        $oArticle->expects($this->exactly(1))->method('getLongDescription')->will($this->returnValue($oLongDesc));

        $oArtList = new oxlist();
        $oArtList->offsetSet(0, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock('alist', array('getArticleList', '_getCatPathString', '_prepareMetaDescription'));
        $oListView->expects($this->any())->method('_prepareMetaDescription')->with($this->equalTo('sCatPathString, testtitle originelle, witzige geschenkideen - lifestyle, '))->will($this->returnValue('test'));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('_getCatPathString')->will($this->returnValue($sCatPathString));

        $this->assertEquals('test', $oListView->UNITcollectMetaKeyword(null));
    }

    /**
     * Test list view template name getter
     *
     * @return null
     */
    public function testGetTemplateName()
    {
        $oCategory = new oxcategory();
        $oCategory->oxcategories__oxtemplate = new oxfield('test.tpl');

        // default template name
        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $this->assertEquals('page/list/list.tpl', $oListView->getTemplateName());

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        // category template name
        $this->assertEquals('test.tpl', $oListView->getTemplateName());

        $this->setRequestParam('tpl', 'http://www.shop.com/somepath/test2.tpl');

        // template name passed by request param
        $this->assertSame('custom/test2.tpl', $oListView->getTemplateName());
    }

    /**
     * Test do not add page nr to list seo url for first page.
     *
     * @return null
     */
    public function testAddPageNrParamSeoOnFirstPage()
    {
        $this->setConfigParam('blSeoMode', true);

        $oCategory = new oxcategory();
        $oCategory->load('30e44ab83159266c7.83602558');
        $sUrl = $oCategory->getLink();

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals($sUrl, $oListView->UNITaddPageNrParam($sUrl, 0, 0));
    }

    /**
     * Test add page nr to list seo url for second page.
     *
     * @return null
     */
    public function testAddPageNrParamSeoOnSecondPage()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = new oxcategory();
        $oCategory->load('30e44ab83159266c7.83602558');
        $sUrl = $oCategory->getLink();

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals($sUrl . "2/", $oListView->UNITaddPageNrParam($sUrl, 1, 0));
    }

    /**
     * Test add page nr to list url when seo is off.
     *
     * @return null
     */
    public function testAddPageNrParamSeoOff()
    {
        $oCategory = new oxcategory();
        $oCategory->load('30e44ab83159266c7.83602558');
        $sUrl = $oCategory->getStdLink();

        $oListView = $this->getMock('alist', array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue(null));

        $this->assertEquals($sUrl . "&amp;pgNr=10", $oListView->UNITaddPageNrParam($sUrl, 10, 0));
    }

    /**
     * Test prepare meta keywords.
     *
     * @return null
     */
    public function testPrepareMetaKeyword()
    {
        $aSubCats[0] = new oxcategory();
        $aSubCats[0]->oxcategories__oxtitle = new oxField('sub_category_1');

        $aSubCats[1] = new oxcategory();
        $aSubCats[1]->oxcategories__oxtitle = new oxField('Nada fedia nada');

        $oParentCategory = new oxcategory();
        $oParentCategory->oxcategories__oxtitle = new oxField('parent_category');

        $oCategory = new oxcategory();
        $oCategory->oxcategories__oxtitle = new oxField('current_category');
        $oCategory->oxcategories__oxparentid = new oxField('parentCategoryId');

        $oCategory->setSubCats($aSubCats);
        $oCategory->setParentCategory($oParentCategory);

        $aCatTree[] = $oParentCategory;
        $aCatTree[] = $oCategory;

        $oCategoryTree = $this->getMock('oxcategorylist', array('getPath'));
        $oCategoryTree->expects($this->any())->method('getPath')->will($this->returnValue($aCatTree));

        $oListView = $this->getMock('alist', array('getActiveCategory', 'getCategoryTree'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oListView->expects($this->any())->method('getCategoryTree')->will($this->returnValue($oCategoryTree));

        $this->assertEquals('parent_category, current_category, sub_category_1, nada, fedia', $oListView->UNITprepareMetaKeyword(null));
    }

    /**
     * Test prepare meta description.
     *
     * @return null
     */
    public function testPrepareMetaDescription()
    {
        $oParentCategory = new oxcategory();
        $oParentCategory->oxcategories__oxtitle = new oxField('<span>parent</span> <style type="text/css">p {color:blue;}</style>category');

        $oCategory = new oxcategory();
        $oCategory->oxcategories__oxtitle = new oxField('category');
        $oCategory->oxcategories__oxparentid = new oxField('parentcategory');

        $oCategory->setParentCategory($oParentCategory);

        $oListView = $this->getMock("alist", array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $sExpect = 'parent category - category. OXID eShop 4';
        //expected string changed due to #2776
        $this->assertEquals(
            $sExpect,
            $oListView->UNITprepareMetaDescription($aCatPath, 1024, false)
        );
    }

    /**
     * Test get category attributes.
     *
     * @return null
     */
    public function testGetAttributes()
    {
        $oAttrList = new oxAttributeList();
        $oAttr = new oxAttribute();
        $oAttrList->offsetSet(1, $oAttr);

        $oCategory = $this->getMock('oxCategory', array('getAttributes'));
        $oCategory->expects($this->any())->method('getAttributes')->will($this->returnValue($oAttrList));

        $oListView = $this->getMock("alist", array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals($oAttrList->getArray(), $oListView->getAttributes()->getArray());
    }

    /**
     * Test get ids for simmilar recommendation list.
     *
     * @return null
     */
    public function testGetSimilarRecommListIds()
    {
        $aArrayKeys = array("articleId");
        $oArtList = $this->getMock("oxarticlelist", array("count", "arrayKeys"));
        $oArtList->expects($this->once())->method("arrayKeys")->will($this->returnValue($aArrayKeys));


        $oSearch = $this->getMock("alist", array("getArticleList"));
        $oSearch->expects($this->once())->method("getArticleList")->will($this->returnValue($oArtList));
        $this->assertEquals($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getArticleList()");
    }

    /**
     * Test get list page navigation.
     *
     * @return null
     */
    public function testGetPageNavigation()
    {
        $oObj = $this->getMock('alist', array('generatePageNavigation'));
        $oObj->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oObj->getPageNavigation());
    }

    /**
     * Test get article list.
     *
     * @return null
     */
    public function testGetArticleList()
    {
        $sCatId = '30e44ab83b6e585c9.63147165';
        $iExptCount = 4;
        $sCatId = '8a142c3e49b5a80c1.23676990';
        $iExptCount = 10;

        $oObj = $this->getProxyClass("alist");
        $this->setRequestParam('cnid', $sCatId);
        $this->setConfigParam('iNrofCatArticles', 10);
        $oObj->render();

        $this->assertEquals($iExptCount, $oObj->getArticleList()->count());
    }

    /**
     * Test get categoty path.
     *
     * @return null
     */
    public function testGetCatTreePath()
    {
        $oCatTree = $this->getMock('oxcategorylist', array('getPath'));
        $oCatTree->expects($this->any())->method('getPath')->will($this->returnValue("aaa"));
        $oObj = $this->getProxyClass("alist");
        $oObj->setCategoryTree($oCatTree);
        $this->assertEquals('aaa', $oObj->getCatTreePath());
    }

    /**
     * Test if active category has visible subcategories.
     *
     * @return null
     */
    public function testHasVisibleSubCats()
    {
        $oCat = $this->getMock('oxcategory', array('getHasVisibleSubCats'));
        $oCat->expects($this->any())->method('getHasVisibleSubCats')->will($this->returnValue(true));

        $oListView = $this->getMock("alist", array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertTrue($oListView->hasVisibleSubCats());
    }

    /**
     * Test if subcategory list of active category.
     *
     * @return null
     */
    public function testGetSubCatList()
    {
        $oCat = $this->getMock('oxcategory', array('getSubCats'));
        $oCat->expects($this->any())->method('getSubCats')->will($this->returnValue('aaa'));

        $oListView = $this->getMock("alist", array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals('aaa', $oListView->getSubCatList());
    }

    /**
     * Test get list title.
     *
     * @return null
     */
    public function testGetTitle()
    {
        $sCatId = '30e44ab83b6e585c9.63147165';
        $iExptName = 'Wohnen';
        $sCatId = '8a142c3e49b5a80c1.23676990';
        $iExptName = 'Bar-Equipment';

        $oCat = new oxcategory();
        $oCat->load($sCatId);

        $oListView = $this->getMock("alist", array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals($iExptName, $oListView->getTitle());

    }

    /**
     * Test get list title.
     */
    public function testGetTitleForMoreCategory()
    {
        $sCatId = 'oxmore';

        $oListView = $this->getMock("alist", array('getCategoryId'));
        $oListView->expects($this->any())->method('getCategoryId')->will($this->returnValue($sCatId));

        $this->assertEquals(oxRegistry::getLang()->translateString('CATEGORY_OVERVIEW', oxRegistry::getLang()->getBaseLanguage(), false), $oListView->getTitle());
    }

    /**
     * Test get bargain article list.
     *
     * @return null
     */
    public function testGetBargainArticleList()
    {
        $oObj = $this->getProxyClass("alist");
        $oObj->setNonPublicVar("_blIsCat", true);

        $aList = $oObj->getBargainArticleList();
        $this->assertEquals(4, $aList->count());
    }

    /**
     * Test meta keywords getter.
     *
     * @return null
     */
    public function testMetaKeywordsGetter()
    {


        $sCatId = '8a142c3e44ea4e714.31136811';

        $this->setRequestParam('cnid', $sCatId);

        $oSubj = $this->getMock('alist', array('_prepareMetaKeyword'));
        $oSubj->expects($this->any())->method('_prepareMetaKeyword')->will($this->returnValue("aaa"));

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();
        $sMetaKeywords = $oSubj->getMetaKeywords();
        $this->assertEquals("aaa", $sMetaKeywords);
    }

    /**
     * Test meta keywords set to view data.
     *
     * @return null
     */
    public function testViewMetaKeywords()
    {

        $sCatId = '8a142c3e44ea4e714.31136811';

        $this->setRequestParam('cnid', $sCatId);

        $oSubj = $this->getMock('alist', array('_prepareMetaKeyword'));
        $oSubj->expects($this->any())->method('_prepareMetaKeyword')->will($this->returnValue("aaa"));

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();
        $sMetaKeywords = $oSubj->getMetaKeywords();
        $this->assertEquals("aaa", $oSubj->getMetaKeywords());
    }

    /**
     * Test get active category getter.
     *
     * @return null
     */
    public function testGetActiveCategory()
    {
        $oArticleList = new aList();
        $oArticleList->setActiveCategory('aaa');
        $this->assertEquals('aaa', $oArticleList->getActiveCategory());
    }


    /**
     * Testing allist::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oCat1 = $this->getMock('oxcategory', array('getLink'));
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock('oxcategory', array('getLink'));
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock('oxcategorylist', array('getPath'));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue(array($oCat1, $oCat2)));

        $oView = $this->getMock("alist", array("getCategoryTree"));
        $oView->expects($this->once())->method('getCategoryTree')->will($this->returnValue($oCategoryList));

        $this->assertTrue(count($oView->getBreadCrumb()) == 2);

    }

    /**
     * Testing allist::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumbForMorePage()
    {
        $this->setRequestParam('cnid', 'oxmore');

        $oView = $this->getMock("alist", array("getCategoryTree", "getLink"));
        $oView->expects($this->never())->method('getCategoryTree');
        $oView->expects($this->once())->method('getLink')->will($this->returnValue("moreLink"));

        $aPath = $oView->getBreadCrumb();
        $this->assertEquals(1, count($aPath));
        $this->assertNotNull($aPath[0]['title']);
        $this->assertEquals("moreLink", $aPath[0]['link']);

    }

    /**
     * Test can display type selector getter
     *
     * @return null
     */
    public function testCanSelectDisplayType()
    {
        $oConfig = $this->getMock('oxConfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue(true));

        $oSubj = $this->getMock('alist', array('getConfig'));
        $oSubj->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals(true, $oSubj->canSelectDisplayType());
    }

    /**
     * Test get active page nr
     *
     * @return null
     */
    public function testGetActPage()
    {
        $oList = $this->getMock('alist', array('_getRequestPageNr'));
        $oList->expects($this->once())->method('_getRequestPageNr')->will($this->returnValue("10"));

        $this->assertEquals(10, $oList->getActPage());
    }

    /**
     * Test get list pages count
     *
     * @return null
     */
    public function testGetPageCount()
    {
        $oList = $this->getProxyClass("aList");
        $oList->setNonPublicVar("_iCntPages", 10);

        $this->assertEquals(10, $oList->getPageCount());
    }

    public function testGetArticleCount()
    {
        $oList = $this->getProxyClass('aList');
        $oList->setNonPublicVar('_iAllArtCnt', 3);

        $this->assertEquals(3, $oList->getArticleCount());
    }
}
