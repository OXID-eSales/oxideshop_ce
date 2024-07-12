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
class AlistTest extends \OxidTestCase
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

        $this->assertEquals($sTestParams, $oView->getAddUrlParams());
    }

    /**
     * Test get added seo url parameters.
     */
    public function testGetAddSeoUrlParams()
    {
        $oView = oxNew('AList');
        $this->assertEquals('', $oView->getAddSeoUrlParams());
    }

    /**
     * Test get page title sufix.
     */
    public function testGetTitlePageSuffix()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage"]);
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(0));

        $this->assertNull($oView->getTitlePageSuffix());

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage"]);
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(1));

        $this->assertEquals(oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getTitlePageSuffix());
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
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals($sPrefix . ", " . oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getMetaDescription());
    }

    /**
     * Test get category path.
     */
    public function testGetTreePath()
    {
        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ["getPath"]);
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue("testPath"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getCategoryTree"]);
        $oView->expects($this->once())->method('getCategoryTree')->will($this->returnValue($oCategoryList));

        $this->assertEquals("testPath", $oView->getTreePath());
    }

    /**
     * Test get canonical url with seo on.
     */
    public function testGetCanonicalUrlSeoOn()
    {
        $this->setConfigParam('blSeoMode', true);

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ["getBaseSeoLink", "getBaseStdLink", "getLanguage"]);
        $oCategory->expects($this->once())->method('getBaseSeoLink')->will($this->returnValue("testSeoUrl"));
        $oCategory->expects($this->never())->method('getBaseStdLink');
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage", "getActiveCategory"]);
        $oListView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals("testSeoUrl", $oListView->getCanonicalUrl());
    }

    /**
     * Test get canonical url with seo off.
     */
    public function testGetCanonicalUrlSeoOff()
    {
        $this->setConfigParam('blSeoMode', false);

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ["getBaseSeoLink", "getBaseStdLink", "getLanguage"]);
        $oCategory->expects($this->never())->method('getBaseSeoLink');
        $oCategory->expects($this->once())->method('getBaseStdLink')->will($this->returnValue("testStdUrl"));
        $oCategory->expects($this->once())->method('getLanguage')->will($this->returnValue(1));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getActPage", "getActiveCategory"]);
        $oListView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals("testStdUrl", $oListView->getCanonicalUrl());
    }

    /**
     * Test get noIndex property.
     */
    public function testNoIndex()
    {
        // regular category
        $oListView = oxNew('AList');
        $this->assertEquals(0, $oListView->noIndex());
    }

    /**
     * Test list article url processing.
     */
    public function testProcessListArticles()
    {
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['setLinkType', "appendStdLink", "appendLink"]);
        $oArticle->expects($this->once())->method('setLinkType')->with($this->equalto('xxx'));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testSeoParams'));
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['setLinkType', "appendStdLink", "appendLink"]);
        $oArticle->expects($this->once())->method('setLinkType')->with($this->equalto('xxx'));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testSeoParams'));
        $aArticleList[] = $oArticle;

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getArticleList', 'getProductLinkType', "getAddUrlParams", "getAddSeoUrlParams"]);
        $oListView->expects($this->once())->method('getArticleList')->will($this->returnValue($aArticleList));
        $oListView->expects($this->once())->method('getProductLinkType')->will($this->returnValue('xxx'));
        $oListView->expects($this->once())->method('getAddUrlParams')->will($this->returnValue('testStdParams'));
        $oListView->expects($this->once())->method('getAddSeoUrlParams')->will($this->returnValue('testSeoParams'));

        $oListView->processListArticles();
    }

    /**
     * Test get product link type.
     */
    public function testGetProductLinkType()
    {
        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['isPriceCategory']);
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(true));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(3, $oListView->getProductLinkType());


        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['isPriceCategory']);
        $oCategory->expects($this->once())->method('isPriceCategory')->will($this->returnValue(false));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals(0, $oListView->getProductLinkType());
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
        $oListView->expects($this->once())->method('setActiveCategory')->with($this->equalto($oMoreCat));
        $this->assertEquals('page/list/morecategories', $oListView->render());
    }

    /**
     * Test load price category articles.
     */
    public function testLoadArticlesForPriceCategory()
    {
        oxTestModules::addFunction("oxarticlelist", "loadPriceArticles", "{ throw new Exception( \$aA[0] . \$aA[1] ); }");

        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxpricefrom = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCategory->oxcategories__oxpricefrom->expects($this->exactly(2))->method('__get')->will($this->returnValue(10));
        $oCategory->oxcategories__oxpriceto = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCategory->oxcategories__oxpriceto->expects($this->once())->method('__get')->will($this->returnValue(100));

        try {
            $oListView = oxNew('aList');
            $oListView->loadArticles($oCategory);
        } catch (Exception $exception) {
            $this->assertEquals('10100', $exception->getMessage());

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
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($oCat));

        try {
            $oListView->render();
        } catch (Exception $exception) {
            $this->assertEquals('OK', $exception->getMessage(), 'failed redirect on inactive category');

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
        $oCat->expects($this->any())->method('canView')->will($this->returnValue(true));
        $oCat->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount']);
        $oListView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($oCat));
        $oListView->expects($this->once())->method('getActPage')->will($this->returnValue(12));
        $oListView->expects($this->once())->method('getPageCount')->will($this->returnValue(10));
        $oListView->expects($this->atLeastOnce())->method('getArticleList');

        try {
            $oListView->render();
        } catch (Exception $exception) {
            $this->assertEquals('OK', $exception->getMessage());

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
        $category->expects($this->any())->method('canView')->will($this->returnValue(true));
        $category->oxcategories__oxactive = new oxField(1, oxField::T_RAW);

        $listView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getArticleList', 'getActPage', 'getPageCount']);
        $listView->expects($this->atLeastOnce())->method('getActiveCategory')->will($this->returnValue($category));
        $listView->expects($this->once())->method('getActPage')->will($this->returnValue(12));
        $listView->expects($this->once())->method('getPageCount')->will($this->returnValue(0));
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

        $this->assertEquals(['somecategory' => ['0' => 'somefilter']], $this->getSessionParam('session_attrfilter'));
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
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue('getActiveCategory'));

        $this->assertEquals('getActiveCategory', $oListView->getSubject(oxRegistry::getLang()->getBaseLanguage()));
    }

    /**
     * Test get list title sufix.
     */
    public function testGetTitleSuffix()
    {
        $oCat = oxNew('oxcategory');
        $oCat->oxcategories__oxshowsuffix = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCat->oxcategories__oxshowsuffix->expects($this->once())->method('__get')->will($this->returnValue(true));

        $oShop = oxNew('oxshop');
        $oShop->oxshops__oxtitlesuffix = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oShop->oxshops__oxtitlesuffix->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveShop']);
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class,
            ['getActiveCategory', 'getConfig']);
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCat));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals('testsuffix', $oListView->getTitleSuffix());
    }

    /**
     * Test getDefaultSorting when default sorting is not set
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = oxNew('aList');

        $oCategory = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getDefaultSorting']);
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue(''));
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
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
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
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(null));
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
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(false));

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
        $oCategory->expects($this->any())->method('getDefaultSorting')->will($this->returnValue('testsort'));
        $oCategory->expects($this->any())->method('getDefaultSortingMode')->will($this->returnValue(true));

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
        $oCat->expects($this->once())->method('getLink')->will($this->returnValue($sTestLink));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals($sTestLink, $oListView->generatePageNavigationUrl());
    }

    /**
     * Test list page navigation url generation.
     */
    public function testGeneratePageNavigationUrl()
    {
        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->once())->method('getActiveCategory')->will($this->returnValue(null));

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
        $this->setSessionParam('session_attrfilter', ['xxx' => ['0' => ['100']]]);

        $oView = oxNew('oxUBase');
        $sListType = $this->getConfig()->getConfigParam('sDefaultListDisplayType');

        $sViewId = md5($oView->getViewId() . '|xxx|999|100|' . $sListType);

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActPage']);
        $oListView->expects($this->any())->method('getActPage')->will($this->returnValue('999'));
        $this->assertEquals($sViewId, $oListView->getViewId());
    }

    /**
     * Test get category path as string.
     */
    public function testGetCatPathString()
    {
        $oCategory = oxNew('oxcategory');
        $oCategory->oxcategories__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oCategory->oxcategories__oxtitle->expects($this->any())->method('__get')->will($this->returnValue('testTitle'));

        $aPath = [$oCategory, $oCategory];

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getCatTreePath']);
        $oListView->expects($this->any())->method('getCatTreePath')->will($this->returnValue($aPath));

        $this->assertEquals(strtolower('testTitle, testTitle'), $oListView->getCatPathString());
    }

    /**
     * Test prepare list meta description info.
     */
    public function testCollectMetaDescription()
    {
        $oActCat = oxNew('oxcategory');
        $oActCat->oxcategories__oxlongdesc = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oActCat->oxcategories__oxlongdesc->expects($this->once())->method('__get')->will($this->returnValue(''));

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oArticle->oxarticles__oxtitle->expects($this->exactly(2))->method('__get')->will($this->returnValue('testtitle'));

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getArticleList', 'getCatPathString']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oActCat));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('getCatPathString')->will($this->returnValue($sCatPathString));

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
        $oArticle->expects($this->exactly(2))->method('getLongDescription')->will($this->returnValue($oLongDesc));

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);
        $oArtList->offsetSet(1, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getArticleList', 'getCatPathString', 'prepareMetaDescription']);
        $oListView->expects($this->any())->method('prepareMetaDescription')->with($this->equalTo('sCatPathString, testtitle, testtitle'))->will($this->returnValue('test'));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('getCatPathString')->will($this->returnValue($sCatPathString));

        $this->assertEquals('test', $oListView->collectMetaKeyword(null));
    }

    /**
     * Test prepare list meta keyword info longer then 60 symbols.
     */
    public function testCollectMetaKeywordLongerThen60()
    {
        $oLongDesc = new oxField('testtitle Originelle, witzige Geschenkideen - Lifestyle, Trends, Accessoires');
        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getLongDescription']);
        $oArticle->expects($this->exactly(1))->method('getLongDescription')->will($this->returnValue($oLongDesc));

        $oArtList = oxNew('oxlist');
        $oArtList->offsetSet(0, $oArticle);

        $sCatPathString = 'sCatPathString';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getArticleList', 'getCatPathString', 'prepareMetaDescription']);
        $oListView->expects($this->any())->method('prepareMetaDescription')->with($this->equalTo('sCatPathString, testtitle originelle, witzige geschenkideen - lifestyle, '))->will($this->returnValue('test'));
        $oListView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $oListView->expects($this->any())->method('getCatPathString')->will($this->returnValue($sCatPathString));

        $this->assertEquals('test', $oListView->collectMetaKeyword(null));
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
        $this->assertEquals('page/list/list', $oListView->getTemplateName());

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        // category template name
        $this->assertEquals('test', $oListView->getTemplateName());

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
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
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
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $this->assertEquals($sUrl . "?pgNr=1", $oListView->addPageNrParam($sUrl, 1, 0));
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
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue(null));

        $this->assertEquals($sUrl . "&amp;pgNr=10", $oListView->addPageNrParam($sUrl, 10, 0));
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
        $oCategoryTree->expects($this->any())->method('getPath')->will($this->returnValue($aCatTree));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory', 'getCategoryTree']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));
        $oListView->expects($this->any())->method('getCategoryTree')->will($this->returnValue($oCategoryTree));

        $this->assertEquals('parent_category, current_category, sub_category_1, nada, fedia', $oListView->prepareMetaKeyword(null));
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
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $sExpect = "parent category - category. OXID eShop";
        //expected string changed due to #2776
        $this->assertEquals(
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
        $oCategory->expects($this->any())->method('getAttributes')->will($this->returnValue($oAttrList));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCategory));

        $this->assertEquals($oAttrList->getArray(), $oListView->getAttributes()->getArray());
    }

    /**
     * Test get ids for simmilar recommendation list.
     */
    public function testGetSimilarRecommListIds()
    {
        $aArrayKeys = ["articleId"];
        $oArtList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["count", "arrayKeys"]);
        $oArtList->expects($this->once())->method("arrayKeys")->will($this->returnValue($aArrayKeys));


        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getArticleList"]);
        $oSearch->expects($this->once())->method("getArticleList")->will($this->returnValue($oArtList));
        $this->assertEquals($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getArticleList()");
    }

    /**
     * Test get list page navigation.
     */
    public function testGetPageNavigation()
    {
        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['generatePageNavigation']);
        $oObj->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oObj->getPageNavigation());
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

        $this->assertEquals(2, $oObj->getArticleList()->count());
    }

    /**
     * Test get categoty path.
     */
    public function testGetCatTreePath()
    {
        $oCatTree = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ['getPath']);
        $oCatTree->expects($this->any())->method('getPath')->will($this->returnValue("aaa"));
        $oObj = $this->getProxyClass("alist");
        $oObj->setCategoryTree($oCatTree);
        $this->assertEquals('aaa', $oObj->getCatTreePath());
    }

    /**
     * Test if active category has visible subcategories.
     */
    public function testHasVisibleSubCats()
    {
        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getHasVisibleSubCats']);
        $oCat->expects($this->any())->method('getHasVisibleSubCats')->will($this->returnValue(true));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertTrue($oListView->hasVisibleSubCats());
    }

    /**
     * Test if subcategory list of active category.
     */
    public function testGetSubCatList()
    {
        $oCat = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getSubCats']);
        $oCat->expects($this->any())->method('getSubCats')->will($this->returnValue('aaa'));

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals('aaa', $oListView->getSubCatList());
    }

    /**
     * Test get list title.
     */
    public function testGetTitle()
    {
        $oCat = oxNew('oxCategory');
        $oCat->load('943173edecf6d6870a0f357b8ac84d32');

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getActiveCategory']);
        $oListView->expects($this->any())->method('getActiveCategory')->will($this->returnValue($oCat));

        $this->assertEquals('Men', $oListView->getTitle());
    }

    /**
     * Test get list title.
     */
    public function testGetTitleForMoreCategory()
    {
        $sCatId = 'oxmore';

        $oListView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ['getCategoryId']);
        $oListView->expects($this->any())->method('getCategoryId')->will($this->returnValue($sCatId));

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
        $oSubj->expects($this->any())->method('prepareMetaKeyword')->will($this->returnValue("aaa"));

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();

        $sMetaKeywords = $oSubj->getMetaKeywords();
        $this->assertEquals("aaa", $sMetaKeywords);
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
        $oSubj->expects($this->any())->method('prepareMetaKeyword')->will($this->returnValue("aaa"));

        $oSubj->setCategoryId($sCatId);
        $oSubj->render();

        $oSubj->render();
        $oSubj->getMetaKeywords();
        $this->assertEquals("aaa", $oSubj->getMetaKeywords());
    }

    /**
     * Test get active category getter.
     */
    public function testGetActiveCategory()
    {
        $oArticleList = oxNew('aList');
        $oArticleList->setActiveCategory('aaa');
        $this->assertEquals('aaa', $oArticleList->getActiveCategory());
    }


    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oCat1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat1->expects($this->once())->method('getLink')->will($this->returnValue('linkas1'));
        $oCat1->oxcategories__oxtitle = new oxField('title1');

        $oCat2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, ['getLink']);
        $oCat2->expects($this->once())->method('getLink')->will($this->returnValue('linkas2'));
        $oCat2->oxcategories__oxtitle = new oxField('title2');

        $oCategoryList = $this->getMock(\OxidEsales\Eshop\Application\Model\CategoryList::class, ['getPath']);
        $oCategoryList->expects($this->once())->method('getPath')->will($this->returnValue([$oCat1, $oCat2]));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getCategoryTree"]);
        $oView->expects($this->once())->method('getCategoryTree')->will($this->returnValue($oCategoryList));

        $this->assertTrue(count($oView->getBreadCrumb()) == 2);
    }

    /**
     * Testing allist::getBreadCrumb()
     */
    public function testGetBreadCrumbForMorePage()
    {
        $this->setRequestParameter('cnid', 'oxmore');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ArticleListController::class, ["getCategoryTree", "getLink"]);
        $oView->expects($this->never())->method('getCategoryTree');
        $oView->expects($this->once())->method('getLink')->will($this->returnValue("moreLink"));

        $aPath = $oView->getBreadCrumb();
        $this->assertEquals(1, count($aPath));
        $this->assertNotNull($aPath[0]['title']);
        $this->assertEquals("moreLink", $aPath[0]['link']);
    }

    /**
     * Test can display type selector getter
     */
    public function testCanSelectDisplayType()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->will($this->returnValue(true));

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
        $oList->expects($this->once())->method('getRequestPageNr')->will($this->returnValue("10"));

        $this->assertEquals(10, $oList->getActPage());
    }

    /**
     * Test get list pages count
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
