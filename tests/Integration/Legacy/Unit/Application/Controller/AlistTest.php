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
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for aList class
 */
class AlistTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        // deleting test data
        oxDb::getDb()->execute("delete from oxseo where oxtype != 'static' ");

        parent::tearDown();
    }

    /**
     * Test get added url parameters.
     */
    public function testGetAddUrlParams()
    {
        $this->setRequestParameter("pgNr", 999);
        $this->setConfigParam('blSeoMode', false);

        $oView = oxNew('aList');

        $oUBaseView = oxNew('oxUBase');
        $sTestParams = $oUBaseView->getAddUrlParams();
        $sTestParams .= ($sTestParams ? '&amp;' : '') . "pgNr=999";

        $this->assertSame($sTestParams, $oView->getAddUrlParams());
    }

    /**
     * Test get added seo url parameters.
     */
    public function testGetAddSeoUrlParams()
    {
        $oView = oxNew('AList');
        $this->assertSame('', $oView->getAddSeoUrlParams());
    }

    /**
     * Test get page title sufix.
     */
    public function testGetTitlePageSuffix()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage"]);
        $oView->expects($this->once())->method('getActPage')->willReturn(0);

        $this->assertNull($oView->getTitlePageSuffix());

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage"]);
        $oView->expects($this->once())->method('getActPage')->willReturn(1);

        $this->assertSame(oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getTitlePageSuffix());
    }

    /**
     * Test get page meta description sufix.
     */
    public function testGetMetaDescription()
    {
        $sCatId = "6b6b64bdcf7c25e92191b1120974af4e";

        // Demo data is different in EE and CE
        $sPrefix = "Woman - Jackets. OXID eShop";

        $oCategory = oxNew('oxCategory');
        $oCategory->load($sCatId);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage", "getActiveCategory"]);
        $oView->expects($this->once())->method('getActPage')->willReturn(1);
        $oView->method('getActiveCategory')->willReturn($oCategory);

        $this->assertSame($sPrefix . ", " . oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getMetaDescription());
    }

    /**
     * Test get category path.
     */
    public function testGetTreePath()
    {
        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ["getPath"]);
        $oCategoryList->expects($this->once())->method('getPath')->willReturn("testPath");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getCategoryTree"]);
        $oView->expects($this->once())->method('getCategoryTree')->willReturn($oCategoryList);

        $this->assertSame("testPath", $oView->getTreePath());
    }

    /**
     * Test get canonical url with seo on.
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ["getBaseSeoLink", "getBaseStdLink", "getLanguage"]);
        $oCategory->expects($this->once())->method('getBaseSeoLink')->willReturn("testSeoUrl");
        $oCategory->expects($this->never())->method('getBaseStdLink');
        $oCategory->expects($this->once())->method('getLanguage')->willReturn(1);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage", "getActiveCategory"]);
        $oListView->expects($this->once())->method('getActPage')->willReturn(1);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn($oCategory);

        $this->assertSame("testSeoUrl", $oListView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ["getBaseSeoLink", "getBaseStdLink", "getLanguage"]);
        $oCategory->expects($this->never())->method('getBaseSeoLink');
        $oCategory->expects($this->once())->method('getBaseStdLink')->willReturn("testStdUrl");
        $oCategory->expects($this->once())->method('getLanguage')->willReturn(1);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage", "getActiveCategory"]);
        $oListView->expects($this->once())->method('getActPage')->willReturn(1);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn($oCategory);

        $this->assertSame("testStdUrl", $oListView->getCanonicalUrl());
    }

    /**
     * Test get noIndex property.
     */
    public function testNoIndex()
    {
        // regular category
        $oListView = oxNew('AList');
        $this->assertSame(0, $oListView->noIndex());
    }

    /**
     * Test list article url processing.
     */
    public function testProcessListArticles()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['setLinkType', "appendStdLink", "appendLink"]);
        $oArticle->expects($this->once())->method('setLinkType')->with('xxx');
        $oArticle->expects($this->once())->method('appendStdLink')->with('testStdParams');
        $oArticle->expects($this->once())->method('appendLink')->with('testSeoParams');
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['setLinkType', "appendStdLink", "appendLink"]);
        $oArticle->expects($this->once())->method('setLinkType')->with('xxx');
        $oArticle->expects($this->once())->method('appendStdLink')->with('testStdParams');
        $oArticle->expects($this->once())->method('appendLink')->with('testSeoParams');
        $aArticleList[] = $oArticle;

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getArticleList', 'getProductLinkType', "getAddUrlParams", "getAddSeoUrlParams"]);
        $oListView->expects($this->once())->method('getArticleList')->willReturn($aArticleList);
        $oListView->expects($this->once())->method('getProductLinkType')->willReturn('xxx');
        $oListView->expects($this->once())->method('getAddUrlParams')->willReturn('testStdParams');
        $oListView->expects($this->once())->method('getAddSeoUrlParams')->willReturn('testSeoParams');

        $oListView->processListArticles();
    }

    /**
     * Test get product link type.
     */
    public function testGetProductLinkType()
    {
        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['isPriceCategory']);
        $oCategory->expects($this->once())->method('isPriceCategory')->willReturn(true);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn($oCategory);
        $this->assertSame(3, $oListView->getProductLinkType());


        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['isPriceCategory']);
        $oCategory->expects($this->once())->method('isPriceCategory')->willReturn(false);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn($oCategory);
        $this->assertSame(0, $oListView->getProductLinkType());
    }

    /**
     * Test render more categoty list page.
     */
    public function testRenderForMoreCategory()
    {
        $this->setRequestParameter('cnid', 'oxmore');

        $oMoreCat = oxNew('oxCategory');
        $oMoreCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['setActiveCategory']);
        $oListView->expects($this->once())->method('setActiveCategory')->with($oMoreCat);
        $this->assertSame('page/list/morecategories', $oListView->render());
    }

    /**
     * Test load price category articles.
     */
    public function testLoadArticlesForPriceCategory()
    {
        oxTestModules::addFunction("oxarticlelist", "loadPriceArticles", "{ throw new Exception( \$aA[0] . \$aA[1] ); }");

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxpricefrom = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCategory->oxcategories__oxpricefrom->expects($this->exactly(2))->method('__get')->willReturn(10);
        $oCategory->oxcategories__oxpriceto = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCategory->oxcategories__oxpriceto->expects($this->once())->method('__get')->willReturn(100);

        try {
            $oListView = oxNew('aList');
            $oListView->loadArticles($oCategory);
        } catch (Exception $exception) {
            $this->assertSame('10100', $exception->getMessage());

            return;
        }

        $this->fail('failed testLoadArticlesForPriceCategory');
    }

    /**
     * Test render inactive category page.
     */
    public function testRenderInactiveCategory()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $oCat = oxNew('oxCategory');
        $oCat->oxcategories__oxactive = new oxField(0, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->willReturn($oCat);

        try {
            $oListView->render();
        } catch (Exception $exception) {
            $this->assertSame('OK', $exception->getMessage(), 'failed redirect on inactive category');

            return;
        }

        $this->fail('failed redirect on inactive category');
    }

    /**
     * Test render actual page count exceeds real page count
     */
    public function testRender_pageCountIsIncorrect()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new Exception('OK'); }");

        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['canView']);
        $oCat->method('canView')->willReturn(true);
        $oCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount']);
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->willReturn($oCat);
        $oListView->expects($this->once())->method('getActPage')->willReturn(12);
        $oListView->expects($this->once())->method('getPageCount')->willReturn(10);
        $oListView->expects($this->atLeastOnce())->method('getArticleList');

        try {
            $oListView->render();
        } catch (Exception $exception) {
            $this->assertSame('OK', $exception->getMessage());

            return;
        }

        $this->fail('failed redirect when page count is incorrect');
    }

    /**
     * Test render actual page count is 0
     */
    public function testRender_pageCountIsZero()
    {
        $utils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ['handlePageNotFoundError']);
        $utils->expects($this->once())->method('handlePageNotFoundError');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $category = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['canView']);
        $category->method('canView')->willReturn(true);
        $category->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $listView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount']);
        $listView->expects($this->atLeastOnce())->method('getActiveCategory')->willReturn($category);
        $listView->expects($this->once())->method('getActPage')->willReturn(12);
        $listView->expects($this->once())->method('getPageCount')->willReturn(0);
        $listView->expects($this->atLeastOnce())->method('getArticleList');

        $listView->render();
    }

    /**
     * Test execute article filter.
     */
    public function testExecutefilter()
    {
        $this->setRequestParameter('attrfilter', 'somefilter');
        $this->setRequestParameter('cnid', 'somecategory');
        $this->setSessionParam('session_attrfilter', null);

        $oListView = oxNew('aList');
        $oListView->executefilter();

        $this->assertSame(['somecategory' => ['0' => 'somefilter']], $this->getSessionParam('session_attrfilter'));
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
     */
    public function testGetSubject()
    {
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn('getActiveCategory');

        $this->assertSame('getActiveCategory', $oListView->getSubject(oxRegistry::getLang()->getBaseLanguage()));
    }

    /**
     * Test get list title sufix.
     */
    public function testGetTitleSuffix()
    {
        $oCat = oxNew('oxcategory');
        $oCat->oxcategories__oxshowsuffix = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCat->oxcategories__oxshowsuffix->expects($this->once())->method('__get')->willReturn(true);

        $oShop = oxNew('oxshop');
        $oShop->oxshops__oxtitlesuffix = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oShop->oxshops__oxtitlesuffix->expects($this->once())->method('__get')->willReturn('testsuffix');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveShop']);
        $oConfig->expects($this->once())->method('getActiveShop')->willReturn($oShop);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class,
            ['getActiveCategory', 'getConfig']);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn($oCat);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame('testsuffix', $oListView->getTitleSuffix());
    }

    /**
     * Test getDefaultSorting when default sorting is not set
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting']);
        $oCategory->method('getDefaultSorting')->willReturn('');
        $oController->setActiveCategory($oCategory);

        $this->assertEquals(null, $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when default sorting is set
     */
    public function testGetDefaultSortingDefinedSorting()
    {
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oController->setActiveCategory($oCategory);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $this->assertEquals(['sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "asc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is undefined
     */
    public function testDefaultSortingWhenSortingModeIsUndefined()
    {
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(null);
        $oController->setActiveCategory($oCategory);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $this->assertEquals(['sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "asc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'asc'
     * This might be a little too much, but it's a case
     */
    public function testDefaultSortingWhenSortingModeIsAsc()
    {
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(false);

        $oController->setActiveCategory($oCategory);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $this->assertEquals(['sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "asc"], $oController->getDefaultSorting());
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     */
    public function testDefaultSortingWhenSortingModeIsDesc()
    {
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting', 'getDefaultSortingMode']);
        $oCategory->method('getDefaultSorting')->willReturn('testsort');
        $oCategory->method('getDefaultSortingMode')->willReturn(true);

        $oController->setActiveCategory($oCategory);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName('oxarticles');
        $this->assertEquals(['sortby' => $sArticleTable . '.' . 'testsort', 'sortdir' => "desc"], $oController->getDefaultSorting());
    }

    /**
     * Test list page navigation and seo url generation.
     */
    public function testGeneratePageNavigationUrlForCategoryPlusSeo()
    {
        $sTestLink = 'testLink';

        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat->expects($this->once())->method('getLink')->willReturn($sTestLink);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn($oCat);

        $this->assertSame($sTestLink, $oListView->generatePageNavigationUrl());
    }

    /**
     * Test list page navigation url generation.
     */
    public function testGeneratePageNavigationUrl()
    {
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->willReturn(null);

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->generatePageNavigationUrl(), $oListView->generatePageNavigationUrl());
    }

    /**
     * Test PE view id getter.
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

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActPage']);
        $oListView->method('getActPage')->willReturn('999');
        $this->assertSame($sViewId, $oListView->getViewId());
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
        $this->setSessionParam('session_attrfilter', ['xxx' => ['0' => ['100']]]);

        $oView = oxNew('oxUBase');
        $sListType = $this->getConfig()->getConfigParam('sDefaultListDisplayType');

        $sViewId = md5($oView->getViewId() . '|xxx|999|100|' . $sListType);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActPage']);
        $oListView->method('getActPage')->willReturn('999');
        $this->assertSame($sViewId, $oListView->getViewId());
    }

    /**
     * Test get category path as string.
     */
    public function testGetCatPathString()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCategory->oxcategories__oxtitle->method('__get')->willReturn('testTitle');

        $aPath = [$oCategory, $oCategory];

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getCatTreePath']);
        $oListView->method('getCatTreePath')->willReturn($aPath);

        $this->assertSame(strtolower('testTitle, testTitle'), $oListView->getCatPathString());
    }

    /**
     * Test prepare list meta description info.
     */
    public function testCollectMetaDescription()
    {
        $oActCat = oxNew('oxcategory');
        $oActCat->oxcategories__oxlongdesc = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oActCat->oxcategories__oxlongdesc->expects($this->once())->method('__get')->willReturn('');

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oArticle->oxarticles__oxtitle->expects($this->exactly(2))->method('__get')->willReturn('testtitle');

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getArticleList', 'getCatPathString']);
        $oListView->method('getActiveCategory')->willReturn($oActCat);
        $oListView->method('getArticleList')->willReturn($oArtList);
        $oListView->method('getCatPathString')->willReturn($sCatPathString);

        $sMeta = 'sCatPathString - testtitle, testtitle';

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->prepareMetaDescription($sMeta), $oListView->collectMetaDescription(false));
    }

    /**
     * Test prapare list meta keyword info.
     */
    public function testCollectMetaKeyword()
    {
        $oLongDesc = new oxField('testtitle');
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getLongDescription']);
        $oArticle->expects($this->exactly(2))->method('getLongDescription')->willReturn($oLongDesc);

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getArticleList', 'getCatPathString', 'prepareMetaDescription']);
        $oListView->method('prepareMetaDescription')->with('sCatPathString, testtitle, testtitle')->willReturn('test');
        $oListView->method('getArticleList')->willReturn($oArtList);
        $oListView->method('getCatPathString')->willReturn($sCatPathString);

        $this->assertSame('test', $oListView->collectMetaKeyword(null));
    }

    /**
     * Test prepare list meta keyword info longer then 60 symbols.
     */
    public function testCollectMetaKeywordLongerThen60()
    {
        $oLongDesc = new oxField('testtitle Originelle, witzige Geschenkideen - Lifestyle, Trends, Accessoires');
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getLongDescription']);
        $oArticle->expects($this->exactly(1))->method('getLongDescription')->willReturn($oLongDesc);

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getArticleList', 'getCatPathString', 'prepareMetaDescription']);
        $oListView->method('prepareMetaDescription')->with('sCatPathString, testtitle originelle, witzige geschenkideen - lifestyle, ')->willReturn('test');
        $oListView->method('getArticleList')->willReturn($oArtList);
        $oListView->method('getCatPathString')->willReturn($sCatPathString);

        $this->assertSame('test', $oListView->collectMetaKeyword(null));
    }

    /**
     * Test list view template name getter
     */
    public function testGetTemplateName()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtemplate = new oxfield('test');

        // default template name
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $this->assertSame('page/list/list', $oListView->getTemplateName());

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCategory);

        // category template name
        $this->assertSame('test', $oListView->getTemplateName());

        $this->setRequestParameter('tpl', 'http://www.shop.com/somepath/test2');

        // template name passed by request param
        $this->assertSame('custom/test2', $oListView->getTemplateName());
    }

    /**
     * Test do not add page nr to list seo url for first page.
     */
    public function testAddPageNrParamSeoOnFirstPage()
    {
        $this->setConfigParam('blSeoMode', true);

        $oCategory = oxNew('oxcategory');
        $oCategory->load('6b6b64bdcf7c25e92191b1120974af4e');

        $sUrl = $oCategory->getLink();

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCategory);
        $this->assertEquals($sUrl, $oListView->addPageNrParam($sUrl, 0, 0));
    }

    /**
     * Test add page nr to list seo url for second page.
     */
    public function testAddPageNrParamSeoOnSecondPage()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $oCategory = oxNew('oxcategory');
        $oCategory->load('6b6b64bdcf7c25e92191b1120974af4e');

        $sUrl = $oCategory->getLink();

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCategory);
        $this->assertSame($sUrl . "?pgNr=1", $oListView->addPageNrParam($sUrl, 1, 0));
    }

    /**
     * Test add page nr to list url when seo is off.
     */
    public function testAddPageNrParamSeoOff()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->load('6b6b64bdcf7c25e92191b1120974af4e');

        $sUrl = $oCategory->getStdLink();

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn(null);

        $this->assertSame($sUrl . "&amp;pgNr=10", $oListView->addPageNrParam($sUrl, 10, 0));
    }

    /**
     * Test prepare meta keywords.
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

        $oCategoryTree = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ['getPath']);
        $oCategoryTree->method('getPath')->willReturn($aCatTree);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getCategoryTree']);
        $oListView->method('getActiveCategory')->willReturn($oCategory);
        $oListView->method('getCategoryTree')->willReturn($oCategoryTree);

        $this->assertSame('parent_category, current_category, sub_category_1, nada, fedia', $oListView->prepareMetaKeyword(null));
    }

    /**
     * Test prepare meta description.
     */
    public function testPrepareMetaDescription()
    {
        $oParentCategory = oxNew('oxcategory');
        $oParentCategory->oxcategories__oxtitle = new oxField('<span>parent</span> <style type="text/css">p {color:blue;}</style>category');

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtitle = new oxField('category');
        $oCategory->oxcategories__oxparentid = new oxField('parentcategory');

        $oCategory->setParentCategory($oParentCategory);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCategory);

        $sExpect = "parent category - category. OXID eShop";
        //expected string changed due to #2776
        $this->assertSame(
            $sExpect,
            $oListView->prepareMetaDescription(null, 1024, false)
        );
    }

    /**
     * Test get category attributes.
     */
    public function testGetAttributes()
    {
        $oAttrList = oxNew('oxAttributeList');
        $oAttr = oxNew('oxAttribute');
        $oAttrList->offsetSet(1, $oAttr);

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getAttributes']);
        $oCategory->method('getAttributes')->willReturn($oAttrList);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCategory);

        $this->assertEquals($oAttrList->getArray(), $oListView->getAttributes()->getArray());
    }

    /**
     * Test get ids for simmilar recommendation list.
     */
    public function testGetSimilarRecommListIds()
    {
        $aArrayKeys = ["articleId"];
        $oArtList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["count", "arrayKeys"]);
        $oArtList->expects($this->once())->method("arrayKeys")->willReturn($aArrayKeys);


        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getArticleList"]);
        $oSearch->expects($this->once())->method("getArticleList")->willReturn($oArtList);
        $this->assertSame($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getArticleList()");
    }

    /**
     * Test get list page navigation.
     */
    public function testGetPageNavigation()
    {
        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['generatePageNavigation']);
        $oObj->method('generatePageNavigation')->willReturn("aaa");
        $this->assertSame('aaa', $oObj->getPageNavigation());
    }

    /**
     * Test get article list.
     */
    public function testGetArticleList()
    {
        $oObj = $this->getProxyClass("alist");
        $this->setRequestParameter('cnid', '943173edecf6d6870a0f357b8ac84d32');
        $this->setConfigParam('iNrofCatArticles', 10);
        $oObj->render();

        $this->assertSame(2, $oObj->getArticleList()->count());
    }

    /**
     * Test get categoty path.
     */
    public function testGetCatTreePath()
    {
        $oCatTree = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ['getPath']);
        $oCatTree->method('getPath')->willReturn("aaa");
        $oObj = $this->getProxyClass("alist");
        $oObj->setCategoryTree($oCatTree);
        $this->assertSame('aaa', $oObj->getCatTreePath());
    }

    /**
     * Test if active category has visible subcategories.
     */
    public function testHasVisibleSubCats()
    {
        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getHasVisibleSubCats']);
        $oCat->method('getHasVisibleSubCats')->willReturn(true);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCat);

        $this->assertTrue($oListView->hasVisibleSubCats());
    }

    /**
     * Test if subcategory list of active category.
     */
    public function testGetSubCatList()
    {
        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getSubCats']);
        $oCat->method('getSubCats')->willReturn('aaa');

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCat);

        $this->assertSame('aaa', $oListView->getSubCatList());
    }

    /**
     * Test get list title.
     */
    public function testGetTitle()
    {
        $oCat = oxNew('oxCategory');
        $oCat->load('943173edecf6d6870a0f357b8ac84d32');

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->method('getActiveCategory')->willReturn($oCat);

        $this->assertSame('Men', $oListView->getTitle());
    }

    /**
     * Test get list title.
     */
    public function testGetTitleForMoreCategory()
    {
        $sCatId = 'oxmore';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getCategoryId']);
        $oListView->method('getCategoryId')->willReturn($sCatId);

        $this->assertEquals(oxRegistry::getLang()->translateString('CATEGORY_OVERVIEW', oxRegistry::getLang()->getBaseLanguage(), false), $oListView->getTitle());
    }

    /**
     * Test get bargain article list.
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
     */
    public function testMetaKeywordsGetter()
    {
        $sCatId = '943173edecf6d6870a0f357b8ac84d32';

        $this->setRequestParameter('cnid', $sCatId);

        $oSubj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['prepareMetaKeyword']);
        $oSubj->method('prepareMetaKeyword')->willReturn("aaa");

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();

        $sMetaKeywords = $oSubj->getMetaKeywords();
        $this->assertSame("aaa", $sMetaKeywords);
    }

    /**
     * Test meta keywords set to view data.
     */
    public function testViewMetaKeywords()
    {
        $sCatId = '943173edecf6d6870a0f357b8ac84d32';

        $this->setRequestParameter('cnid', $sCatId);

        /** @var AList|PHPUnit\Framework\MockObject\MockObject $oSubj */
        $oSubj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['prepareMetaKeyword']);
        $oSubj->method('prepareMetaKeyword')->willReturn("aaa");

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();
        $oSubj->getMetaKeywords();
        $this->assertSame("aaa", $oSubj->getMetaKeywords());
    }

    /**
     * Test get active category getter.
     */
    public function testGetActiveCategory()
    {
        $oArticleList = oxNew('aList');
        $oArticleList->setActiveCategory('aaa');
        $this->assertSame('aaa', $oArticleList->getActiveCategory());
    }


    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oCat1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat1->expects($this->once())->method('getLink')->willReturn('linkas1');
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat2->expects($this->once())->method('getLink')->willReturn('linkas2');
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ['getPath']);
        $oCategoryList->expects($this->once())->method('getPath')->willReturn([$oCat1, $oCat2]);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getCategoryTree"]);
        $oView->expects($this->once())->method('getCategoryTree')->willReturn($oCategoryList);

        $this->assertCount(2, $oView->getBreadCrumb());
    }

    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumbForMorePage()
    {
        $this->setRequestParameter('cnid', 'oxmore');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getCategoryTree", "getLink"]);
        $oView->expects($this->never())->method('getCategoryTree');
        $oView->expects($this->once())->method('getLink')->willReturn("moreLink");

        $aPath = $oView->getBreadCrumb();
        $this->assertCount(1, $aPath);
        $this->assertNotNull($aPath[0]['title']);
        $this->assertSame("moreLink", $aPath[0]['link']);
    }

    /**
     * Test can display type selector getter
     */
    public function testCanSelectDisplayType()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->willReturn(true);

        $oSubj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals(true, $oSubj->canSelectDisplayType());
    }

    /**
     * Test get active page nr
     */
    public function testGetActPage()
    {
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getRequestPageNr']);
        $oList->expects($this->once())->method('getRequestPageNr')->willReturn("10");

        $this->assertSame(10, $oList->getActPage());
    }

    /**
     * Test get list pages count
     */
    public function testGetPageCount()
    {
        $oList = $this->getProxyClass("aList");
        $oList->setNonPublicVar("_iCntPages", 10);

        $this->assertSame(10, $oList->getPageCount());
    }

    public function testGetArticleCount()
    {
        $oList = $this->getProxyClass('aList');
        $oList->setNonPublicVar('_iAllArtCnt', 3);

        $this->assertSame(3, $oList->getArticleCount());
    }
}
