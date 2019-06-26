<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \Exception;
use \oxDb;
use OxidEsales\Eshop\Application\Controller\ArticleListController;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for aList class
 */
class AlistTest extends \OxidTestCase
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
        $this->setRequestParameter("pgNr", 999);
        $this->setConfigParam('blSeoMode', false);

        $oView = oxNew('aList');

        $oUBaseView = oxNew('oxUBase');
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
        $oView = oxNew('AList');
        $this->assertEquals('', $oView->getAddSeoUrlParams());
    }

    /**
     * Test get page title sufix.
     *
     * @return null
     */
    public function testGetTitlePageSuffix()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getActPage"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(0));

        $this->assertNull($oView->getTitlePageSuffix());

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getActPage"));
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
        $sCatId = "6b6b64bdcf7c25e92191b1120974af4e";

        // Demo data is different in EE and CE
        $shopVersion = 6;
        $sPrefix = "Woman - Jackets. OXID eShop $shopVersion";

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCatId);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getActPage", "getActiveCategory"));
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
        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, array("getPath"));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue("testPath"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getCategoryTree"));
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

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array("getBaseSeoLink", "getBaseStdLink", "getLanguage"));
        $oCategory->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $oCategory->expects($this->never())->method('getBaseStdLink');
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getActPage", "getActiveCategory"));
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

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array("getBaseSeoLink", "getBaseStdLink", "getLanguage"));
        $oCategory->expects($this->never())->method('getBaseSeoLink');
        $oCategory->expects($this->once())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getActPage", "getActiveCategory"));
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
        $oListView = oxNew('AList');
        $this->assertEquals(0, $oListView->noIndex());
    }

    /**
     * Test list article url processing.
     *
     * @return null
     */
    public function testProcessListArticles()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('setLinkType', "appendStdLink", "appendLink"));
        $oArticle->expects($this->once())->method('setLinkType')->with($this->equalto('xxx'));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testSeoParams'));
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('setLinkType', "appendStdLink", "appendLink"));
        $oArticle->expects($this->once())->method('setLinkType')->with($this->equalto('xxx'));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testSeoParams'));
        $aArticleList[] = $oArticle;

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getArticleList', '_getProductLinkType', "getAddUrlParams", "getAddSeoUrlParams"));
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
        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('isPriceCategory'));
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(true));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(3, $oListView->UNITgetProductLinkType());


        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('isPriceCategory'));
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(false));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $this->setRequestParameter('cnid', 'oxmore');

        $oMoreCat = oxNew('oxCategory');
        $oMoreCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('setActiveCategory'));
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

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxpricefrom = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oCategory->oxcategories__oxpricefrom->expects($this->exactly(2))->method('__get')->will($this->returnValue(10));
        $oCategory->oxcategories__oxpriceto = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oCategory->oxcategories__oxpriceto->expects($this->once())->method('__get')->will($this->returnValue(100));

        try {
            $oListView = oxNew('aList');
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

        $oCat = oxNew('oxCategory');
        $oCat->oxcategories__oxactive = new oxField(0, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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

        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('canView'));
        $oCat->expects($this->any())->method('canView')->will($this->returnValue(true));
        $oCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount'));
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
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $utils->expects($this->once())->method('handlePageNotFoundError');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $category = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('canView'));
        $category->expects($this->any())->method('canView')->will($this->returnValue(true));
        $category->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $listView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount'));
        $listView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($category));
        $listView->expects($this->once())->method('getActPage')->will($this->returnValue(12));
        $listView->expects($this->once())->method('getPageCount')->will($this->returnValue(0));
        $listView->expects($this->atLeastOnce())->method('getArticleList');

        $listView->render();
    }

    /**
     * Test execute article filter.
     *
     * @return null
     */
    public function testExecutefilter()
    {
        $this->setRequestParameter('attrfilter', 'somefilter');
        $this->setRequestParameter('cnid', 'somecategory');
        $this->setSessionParam('session_attrfilter', null);

        $oListView = oxNew('aList');
        $oListView->executefilter();

        $this->assertEquals(array('somecategory' => array('0' => 'somefilter')), $this->getSessionParam('session_attrfilter'));
    }

    /**
     * Test reset filter.
     */
    public function testResetFilter()
    {
        $this->setRequestParameter('attrfilter', 'somefilter');
        $this->setRequestParameter('cnid', 'someCategory');

        $articleListController = oxNew(ArticleListController::class);
        $articleListController->executefilter();
        $articleListController->resetFilter();

        $this->assertSame(
            [],
            $this->getSessionParam('session_attrfilter')
        );
    }

    /**
     * Test get category subject.
     *
     * @return null
     */
    public function testGetSubject()
    {
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $oCat = oxNew('oxcategory');
        $oCat->oxcategories__oxshowsuffix = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oCat->oxcategories__oxshowsuffix->expects($this->once())->method('__get')->will($this->returnValue(true));

        $oShop = oxNew('oxshop');
        $oShop->oxshops__oxtitlesuffix = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oShop->oxshops__oxtitlesuffix->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getActiveShop'));
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory', 'getConfig'));
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
//        //$oList = oxNew('aList');
//
//        $oCat = oxNew('oxcategory');
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
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getDefaultSorting'));
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
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getDefaultSorting'));
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
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getDefaultSorting', 'getDefaultSortingMode'));
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
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getDefaultSorting', 'getDefaultSortingMode'));
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
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getDefaultSorting', 'getDefaultSortingMode'));
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

        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getLink'));
        $oCat->expects($this->once())->method('getLink')->will($this->returnValue($sTestLink));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue(null));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->generatePageNavigationUrl(), $oListView->generatePageNavigationUrl());
    }

    /**
     * Test PE view id getter.
     *
     * @return null
     */
    public function testGetViewIdPE()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $this->setRequestParameter('cnid', 'xxx');
        $this->setSessionParam('_artperpage', '100');
        $this->setSessionParam('ldtype', 'grid');

        $oView = oxNew('oxUBase');
        $sViewId = md5($oView->getViewId() . '|xxx|999|100|grid');

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActPage'));
        $oListView->expects($this->any())->method('getActPage')->will($this->returnValue('999'));
        $this->assertEquals($sViewId, $oListView->getViewId());
    }

    /**
     * Test view id getter when list type is not in session
     */
    public function testGetViewId_ListTypeNotInSession_ReturnsViewIdWithDefaultListTypeIncluded()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }
        $this->setRequestParameter('cnid', 'xxx');
        $this->setSessionParam('_artperpage', '100');
        $this->setSessionParam('session_attrfilter', array('xxx' => array('0' => array('100'))));

        $oView = oxNew('oxUBase');
        $sListType = $this->getConfig()->getConfigParam('sDefaultListDisplayType');

        $sViewId = md5($oView->getViewId() . '|xxx|999|100|' . $sListType);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActPage'));
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
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oCategory->oxcategories__oxtitle->expects($this->any())->method('__get')->will($this->returnValue('testTitle'));

        $aPath = array($oCategory, $oCategory);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getCatTreePath'));
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
        $oActCat = oxNew('oxcategory');
        $oActCat->oxcategories__oxlongdesc = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oActCat->oxcategories__oxlongdesc->expects($this->once())->method('__get')->will($this->returnValue(''));

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, array('__get'));
        $oArticle->oxarticles__oxtitle->expects($this->exactly(2))->method('__get')->will($this->returnValue('testtitle'));

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory', 'getArticleList', '_getCatPathString'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oActCat));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('_getCatPathString')->will($this->returnValue($sCatPathString));

        $sMeta = 'sCatPathString - testtitle, testtitle';

        $oView = oxNew('oxubase');
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
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getLongDescription'));
        $oArticle->expects($this->exactly(2))->method('getLongDescription')->will($this->returnValue($oLongDesc));

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getArticleList', '_getCatPathString', '_prepareMetaDescription'));
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
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getLongDescription'));
        $oArticle->expects($this->exactly(1))->method('getLongDescription')->will($this->returnValue($oLongDesc));

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getArticleList', '_getCatPathString', '_prepareMetaDescription'));
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
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtemplate = new oxfield('test.tpl');

        // default template name
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $this->assertEquals('page/list/list.tpl', $oListView->getTemplateName());

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        // category template name
        $this->assertEquals('test.tpl', $oListView->getTemplateName());

        $this->setRequestParameter('tpl', 'http://www.shop.com/somepath/test2.tpl');

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

        $oCategory = oxNew('oxcategory');
        $oCategory->load('6b6b64bdcf7c25e92191b1120974af4e');
        $sUrl = $oCategory->getLink();

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = oxNew('oxcategory');
        $oCategory->load('6b6b64bdcf7c25e92191b1120974af4e');

        $sUrl = $oCategory->getLink();

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals($sUrl . "?pgNr=1", $oListView->UNITaddPageNrParam($sUrl, 1, 0));
    }

    /**
     * Test add page nr to list url when seo is off.
     *
     * @return null
     */
    public function testAddPageNrParamSeoOff()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->load('6b6b64bdcf7c25e92191b1120974af4e');
        $sUrl = $oCategory->getStdLink();

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $aSubCats[0] = oxNew('oxcategory');
        $aSubCats[0]->oxcategories__oxtitle = new oxField('sub_category_1');

        $aSubCats[1] = oxNew('oxcategory');
        $aSubCats[1]->oxcategories__oxtitle = new oxField('Nada fedia nada');

        $oParentCategory = oxNew('oxcategory');
        $oParentCategory->oxcategories__oxtitle = new oxField('parent_category');

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtitle = new oxField('current_category');
        $oCategory->oxcategories__oxparentid = new oxField('parentCategoryId');

        $oCategory->setSubCats($aSubCats);
        $oCategory->setParentCategory($oParentCategory);

        $aCatTree[] = $oParentCategory;
        $aCatTree[] = $oCategory;

        $oCategoryTree = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, array('getPath'));
        $oCategoryTree->expects($this->any())->method('getPath')->will($this->returnValue($aCatTree));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory', 'getCategoryTree'));
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
        $oParentCategory = oxNew('oxcategory');
        $oParentCategory->oxcategories__oxtitle = new oxField('<span>parent</span> <style type="text/css">p {color:blue;}</style>category');

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtitle = new oxField('category');
        $oCategory->oxcategories__oxparentid = new oxField('parentcategory');

        $oCategory->setParentCategory($oParentCategory);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $shopVersion = 6;
        $sExpect = "parent category - category. OXID eShop $shopVersion";
        //expected string changed due to #2776
        $this->assertEquals(
            $sExpect,
            $oListView->UNITprepareMetaDescription(null, 1024, false)
        );
    }

    /**
     * Test get category attributes.
     *
     * @return null
     */
    public function testGetAttributes()
    {
        $oAttrList = oxNew('oxAttributeList');
        $oAttr = oxNew('oxAttribute');
        $oAttrList->offsetSet(1, $oAttr);

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getAttributes'));
        $oCategory->expects($this->any())->method('getAttributes')->will($this->returnValue($oAttrList));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $oArtList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, array("count", "arrayKeys"));
        $oArtList->expects($this->once())->method("arrayKeys")->will($this->returnValue($aArrayKeys));


        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getArticleList"));
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
        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('generatePageNavigation'));
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
        $oObj = $this->getProxyClass("alist");
        $this->setRequestParameter('cnid', '943173edecf6d6870a0f357b8ac84d32');
        $this->setConfigParam('iNrofCatArticles', 10);
        $oObj->render();

        $this->assertEquals(2, $oObj->getArticleList()->count());
    }

    /**
     * Test get categoty path.
     *
     * @return null
     */
    public function testGetCatTreePath()
    {
        $oCatTree = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, array('getPath'));
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
        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getHasVisibleSubCats'));
        $oCat->expects($this->any())->method('getHasVisibleSubCats')->will($this->returnValue(true));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getSubCats'));
        $oCat->expects($this->any())->method('getSubCats')->will($this->returnValue('aaa'));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
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
        $oCat = oxNew('oxCategory');
        $oCat->load('943173edecf6d6870a0f357b8ac84d32');

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getActiveCategory'));
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals('Men', $oListView->getTitle());
    }

    /**
     * Test get list title.
     */
    public function testGetTitleForMoreCategory()
    {
        $sCatId = 'oxmore';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getCategoryId'));
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
        $count = $this->getTestConfig()->getShopEdition() == 'EE' ? 6 : 4;
        $this->assertEquals($count, $aList->count());
    }

    /**
     * Test meta keywords getter.
     *
     * @return null
     */
    public function testMetaKeywordsGetter()
    {
        $sCatId = '943173edecf6d6870a0f357b8ac84d32';

        $this->setRequestParameter('cnid', $sCatId);

        $oSubj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('_prepareMetaKeyword'));
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
        $sCatId = '943173edecf6d6870a0f357b8ac84d32';

        $this->setRequestParameter('cnid', $sCatId);

        /** @var AList|PHPUnit\Framework\MockObject\MockObject $oSubj */
        $oSubj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('_prepareMetaKeyword'));
        $oSubj->expects($this->any())->method('_prepareMetaKeyword')->will($this->returnValue("aaa"));

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();
        $oSubj->getMetaKeywords();
        $this->assertEquals("aaa", $oSubj->getMetaKeywords());
    }

    /**
     * Test get active category getter.
     *
     * @return null
     */
    public function testGetActiveCategory()
    {
        $oArticleList = oxNew('aList');
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
        $oCat1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getLink'));
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('getLink'));
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, array('getPath'));
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue(array($oCat1, $oCat2)));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getCategoryTree"));
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
        $this->setRequestParameter('cnid', 'oxmore');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array("getCategoryTree", "getLink"));
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
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue(true));

        $oSubj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('getConfig'));
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
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, array('_getRequestPageNr'));
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
