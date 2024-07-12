<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxRegistry;
use \oxTestModules;

class SearchTest extends \PHPUnit\Framework\TestCase
{
    public function testIsEmptySearch()
    {
        $oSearch = oxNew('search');
        $oSearch->init();

        $this->assertTrue($oSearch->isEmptySearch());
    }

    /**
    * Test for bug #5995
    */
    public function testIsEmptySearchWithSpace()
    {
        $this->setRequestParameter('searchparam', ' ');

        $oSearch = oxNew('search');
        $oSearch->init();

        $this->assertTrue($oSearch->isEmptySearch());
    }


    /**
     * search::processListArticles() when seo is off
     */
    public function testProcessListArticlesSeoOff()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["appendStdLink", "appendLink"]);
        $oArticle->expects($this->once())->method('appendStdLink')->with('testStdParams');
        $oArticle->expects($this->once())->method('appendLink')->with('testStdParams');
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["appendStdLink", "appendLink"]);
        $oArticle->expects($this->once())->method('appendStdLink')->with('testStdParams');
        $oArticle->expects($this->once())->method('appendLink')->with('testStdParams');
        $aArticleList[] = $oArticle;

        $oSearchView = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ['getArticleList', "getAddUrlParams"]);
        $oSearchView->expects($this->once())->method('getArticleList')->willReturn($aArticleList);
        $oSearchView->expects($this->once())->method('getAddUrlParams')->willReturn('testStdParams');

        $oSearchView->processListArticles();
    }

    /**
     * search::processListArticles() when seo is on
     */
    public function testProcessListArticlesSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["appendStdLink", "appendLink"]);
        $oArticle->expects($this->never())->method('appendStdLink');
        $oArticle->expects($this->once())->method('appendLink')->with('testStdParams');
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["appendStdLink", "appendLink"]);
        $oArticle->expects($this->never())->method('appendStdLink');
        $oArticle->expects($this->once())->method('appendLink')->with('testStdParams');
        $aArticleList[] = $oArticle;

        $oSearchView = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ['getArticleList', "getAddUrlParams"]);
        $oSearchView->expects($this->once())->method('getArticleList')->willReturn($aArticleList);
        $oSearchView->expects($this->once())->method('getAddUrlParams')->willReturn('testStdParams');

        $oSearchView->processListArticles();
    }

    public function testGetArticleList()
    {
        $this->setRequestParameter('searchparam', 'bar');

        $search = oxNew('Search');
        $search->init();

        $expectedArticles = [1126, 1127, 1131, 1142, 1351, 1849, 2080, 'd8842e3cbf9290351.59301740'];
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
            $expectedArticles = [1126, 1849, 1876, 2080];
        }

        $result = array_keys($search->getArticleList()->getArray());
        sort($result);
        sort($expectedArticles);

        $this->assertEquals($expectedArticles, $result);
    }

    public function testGetSimilarRecommListIds()
    {
        $aArrayKeys = ["articleId"];
        $oArtList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ["count", "arrayKeys"]);
        $oArtList->expects($this->once())->method("count")->willReturn(1);
        $oArtList->expects($this->once())->method("arrayKeys")->willReturn($aArrayKeys);


        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ["getArticleList"]);
        $oSearch->expects($this->once())->method("getArticleList")->willReturn($oArtList);
        $this->assertSame($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getArticleList()");
    }

    public function testGetSearchParamForHtml()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchparam', 'ü  a');

        $this->assertSame('ü  a', $oSearch->getSearchParamForHtml());
    }

    public function testGetSearchParam()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchparam', 'ü  a');

        $this->assertSame('%C3%BC%20%20a', $oSearch->getSearchParam());
    }

    public function testGetSearchCatId()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchcnid', 'test');

        $this->assertSame('test', $oSearch->getSearchCatId());
    }

    public function testGetSearchVendor()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchvendor', 'test');

        $this->assertSame('test', $oSearch->getSearchVendor());
    }

    public function testGetPageNavigation()
    {
        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ['generatePageNavigation']);
        $oSearch->method('generatePageNavigation')->willReturn("aaa");
        $this->assertSame('aaa', $oSearch->getPageNavigation());
    }

    public function testGetActiveCategory()
    {
        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ['getActSearch']);
        $oSearch->method('getActSearch')->willReturn("aaa");
        $this->assertSame('aaa', $oSearch->getActiveCategory());
    }

    public function testRender()
    {
        $n = $this->getMock(
            'search',
            ['processListArticles']
        );
        $n->expects($this->once())->method('processListArticles');

        $this->assertSame('page/search/search', $n->render());
    }

    public function testGetAddUrlParams()
    {
        $this->setRequestParameter('searchparam', 'ysearchparam');
        $this->setRequestParameter('searchcnid', 'ysearchcnid');
        $this->setRequestParameter('searchvendor', 'ysearchvendor');
        $this->setRequestParameter('searchmanufacturer', 'ysearchmanufacturer');
        $this->assertSame('listtype=search&amp;searchparam=ysearchparam&amp;searchcnid=ysearchcnid&amp;searchvendor=ysearchvendor&amp;searchmanufacturer=ysearchmanufacturer', oxNew('search')->getAddUrlParams());
    }

    public function testIsSearchClass()
    {
        $this->setRequestParameter('cl', 'ysearchcnid');
        $this->assertEquals(false, oxNew('search')->isSearchClass());
        $this->setRequestParameter('cl', 'search');
        $this->assertEquals(true, oxNew('search')->isSearchClass());
    }

    public function testGetSearchManufacturer()
    {
        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ["isSearchClass"]);
        $oSearch->expects($this->once())->method('isSearchClass')->willReturn(true);
        $this->setRequestParameter('searchmanufacturer', 'gsearchmanufacturer&');
        $this->assertSame('gsearchmanufacturer&amp;', $oSearch->getSearchManufacturer());
    }

    public function testGetSearchManufacturerNotInSearch()
    {
        $oSearch = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ["isSearchClass"]);
        $oSearch->expects($this->once())->method('isSearchClass')->willReturn(false);
        $this->setRequestParameter('searchmanufacturer', 'gsearchmanufacturer&');
        $this->assertFalse($oSearch->getSearchManufacturer());
    }

    public function testGetBreadCrumb()
    {
        $oSearch = oxNew('Search');

        $this->assertCount(1, $oSearch->getBreadCrumb());
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


    public function testCanRedirect()
    {
        $oSearch = oxNew('search');
        $this->assertFalse($oSearch->canRedirect());
    }

    public function testGetArticleCount()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar('_iAllArtCnt', 3);

        $this->assertSame(3, $oSearch->getArticleCount());
    }

    /**
     * Test get title.
     */
    public function testGetTitle()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\SearchController::class, ['getArticleCount', 'getSearchParamForHtml']);
        $oView->method('getArticleCount')->willReturn(6);
        $oView->method('getSearchParamForHtml')->willReturn('searchStr');

        $this->assertSame('6 ' . oxRegistry::getLang()->translateString('HITS_FOR', oxRegistry::getLang()->getBaseLanguage(), false) . ' "searchStr"', $oView->getTitle());
    }
}
