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

use \oxRegistry;
use \oxTestModules;

class SearchTest extends \OxidTestCase
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
     * search::_processListArticles() when seo is off
     *
     * @return null
     */
    public function testProcessListArticlesSeoOff()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return false; }');

        $oArticle = $this->getMock('oxarticle', array("appendStdLink", "appendLink"));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testStdParams'));
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock('oxarticle', array("appendStdLink", "appendLink"));
        $oArticle->expects($this->once())->method('appendStdLink')->with($this->equalto('testStdParams'));
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testStdParams'));
        $aArticleList[] = $oArticle;

        $oSearchView = $this->getMock('search', array('getArticleList', "getAddUrlParams"));
        $oSearchView->expects($this->once())->method('getArticleList')->will($this->returnValue($aArticleList));
        $oSearchView->expects($this->once())->method('getAddUrlParams')->will($this->returnValue('testStdParams'));

        $oSearchView->UNITprocessListArticles();
    }

    /**
     * search::_processListArticles() when seo is on
     *
     * @return null
     */
    public function testProcessListArticlesSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{ return true; }');

        $oArticle = $this->getMock('oxarticle', array("appendStdLink", "appendLink"));
        $oArticle->expects($this->never())->method('appendStdLink');
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testStdParams'));
        $aArticleList[] = $oArticle;

        $oArticle = $this->getMock('oxarticle', array("appendStdLink", "appendLink"));
        $oArticle->expects($this->never())->method('appendStdLink');
        $oArticle->expects($this->once())->method('appendLink')->with($this->equalto('testStdParams'));
        $aArticleList[] = $oArticle;

        $oSearchView = $this->getMock('search', array('getArticleList', "getAddUrlParams"));
        $oSearchView->expects($this->once())->method('getArticleList')->will($this->returnValue($aArticleList));
        $oSearchView->expects($this->once())->method('getAddUrlParams')->will($this->returnValue('testStdParams'));

        $oSearchView->UNITprocessListArticles();
    }

    public function testGetArticleList()
    {
        $this->setRequestParameter('searchparam', 'bar');

        $search = oxNew('Search');
        $search->init();

        $expectedArticles = array(1126, 1127, 1131, 1142, 1351, 1849, 2080, 'd8842e3cbf9290351.59301740');
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
            $expectedArticles = array(1126, 1849, 1876, 2080);
        }

        $result = array_keys($search->getArticleList()->getArray());
        sort($result);
        sort($expectedArticles);

        $this->assertEquals($expectedArticles, $result);
    }

    public function testGetSimilarRecommListIds()
    {
        $aArrayKeys = array("articleId");
        $oArtList = $this->getMock("oxarticlelist", array("count", "arrayKeys"));
        $oArtList->expects($this->once())->method("count")->will($this->returnValue(1));
        $oArtList->expects($this->once())->method("arrayKeys")->will($this->returnValue($aArrayKeys));


        $oSearch = $this->getMock("search", array("getArticleList"));
        $oSearch->expects($this->once())->method("getArticleList")->will($this->returnValue($oArtList));
        $this->assertEquals($aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getArticleList()");
    }

    public function testGetSearchParamForHtml()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchparam', 'ü  a');

        $this->assertEquals('ü  a', $oSearch->getSearchParamForHtml());
    }

    public function testGetSearchParam()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchparam', 'ü  a');

        $this->assertEquals('%FC%20%20a', $oSearch->getSearchParam());
    }

    public function testGetSearchCatId()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchcnid', 'test');

        $this->assertEquals('test', $oSearch->getSearchCatId());
    }

    public function testGetSearchVendor()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar("_blSearchClass", true);
        $this->setRequestParameter('searchvendor', 'test');

        $this->assertEquals('test', $oSearch->getSearchVendor());
    }

    public function testGetPageNavigation()
    {
        $oSearch = $this->getMock('search', array('generatePageNavigation'));
        $oSearch->expects($this->any())->method('generatePageNavigation')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oSearch->getPageNavigation());
    }

    public function testGetActiveCategory()
    {
        $oSearch = $this->getMock('search', array('getActSearch'));
        $oSearch->expects($this->any())->method('getActSearch')->will($this->returnValue("aaa"));
        $this->assertEquals('aaa', $oSearch->getActiveCategory());
    }

    public function testRender()
    {
        $this->getConfig()->setConfigParam('bl_rssSearch', false);
        $n = $this->getMock(
            'search', array(
                           '_processListArticles'
                      )
        );
        $n->expects($this->once())->method('_processListArticles');

        $this->assertEquals('page/search/search.tpl', $n->render());
    }

    public function testRenderRss()
    {
        $oRss = $this->getMock('oxrssfeed', array('getSearchArticlesTitle', 'getSearchArticlesUrl'));
        $oRss->expects($this->once())->method('getSearchArticlesTitle')
            ->with(
                $this->equalTo('ysearchparam'),
                $this->equalTo('ysearchcnid'),
                $this->equalTo('ysearchvendor'),
                $this->equalTo('ysearchmanufacturer')
            )->will($this->returnValue('rss1title'));
        $oRss->expects($this->once())->method('getSearchArticlesUrl')
            ->with(
                $this->equalTo('ysearchparam'),
                $this->equalTo('ysearchcnid'),
                $this->equalTo('ysearchvendor'),
                $this->equalTo('ysearchmanufacturer')
            )->will($this->returnValue('rss1url'));
        oxTestModules::addModuleObject('oxrssfeed', $oRss);

        $this->getConfig()->setConfigParam('bl_rssSearch', 1);
        $this->setRequestParameter('searchparam', 'ysearchparam');
        $this->setRequestParameter('searchcnid', 'ysearchcnid');
        $this->setRequestParameter('searchvendor', 'ysearchvendor');
        $this->setRequestParameter('searchmanufacturer', 'ysearchmanufacturer');

        $n = $this->getMock(
            'search', array(
                           '_processListArticles',
                           'addRssFeed'
                      )
        );
        $n->expects($this->once())->method('_processListArticles');
        $n->expects($this->once())->method('addRssFeed')->with($this->equalTo('rss1title'), $this->equalTo('rss1url'), $this->equalTo('searchArticles'));

        $this->assertEquals('page/search/search.tpl', $n->render());
    }

    public function testGetAddUrlParams()
    {
        $this->setRequestParameter('searchparam', 'ysearchparam');
        $this->setRequestParameter('searchcnid', 'ysearchcnid');
        $this->setRequestParameter('searchvendor', 'ysearchvendor');
        $this->setRequestParameter('searchmanufacturer', 'ysearchmanufacturer');
        $this->assertEquals('listtype=search&amp;searchparam=ysearchparam&amp;searchcnid=ysearchcnid&amp;searchvendor=ysearchvendor&amp;searchmanufacturer=ysearchmanufacturer', oxNew('search')->getAddUrlParams());
    }

    public function testIsSearchClass()
    {
        $this->setRequestParameter('cl', 'ysearchcnid');
        $this->assertEquals(false, oxNew('search')->UNITisSearchClass());
        $this->setRequestParameter('cl', 'search');
        $this->assertEquals(true, oxNew('search')->UNITisSearchClass());

    }

    public function testGetSearchManufacturer()
    {
        $oSearch = $this->getMock("search", array("_isSearchClass"));
        $oSearch->expects($this->once())->method('_isSearchClass')->will($this->returnValue(true));
        $this->setRequestParameter('searchmanufacturer', 'gsearchmanufacturer&');
        $this->assertSame('gsearchmanufacturer&amp;', $oSearch->getSearchManufacturer());
    }

    public function testGetSearchManufacturerNotInSearch()
    {
        $oSearch = $this->getMock("search", array("_isSearchClass"));
        $oSearch->expects($this->once())->method('_isSearchClass')->will($this->returnValue(false));
        $this->setRequestParameter('searchmanufacturer', 'gsearchmanufacturer&');
        $this->assertSame(false, $oSearch->getSearchManufacturer());
    }

    public function testGetBreadCrumb()
    {
        $oSearch = oxNew('Search');

        $this->assertEquals(1, count($oSearch->getBreadCrumb()));
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


    public function testCanRedirect()
    {
        $oSearch = oxNew('search');
        $this->assertFalse($oSearch->UNITcanRedirect());
    }

    public function testGetArticleCount()
    {
        $oSearch = $this->getProxyClass('search');
        $oSearch->setNonPublicVar('_iAllArtCnt', 3);

        $this->assertEquals(3, $oSearch->getArticleCount());
    }

    /**
     * Test get title.
     */
    public function testGetTitle()
    {
        $oView = $this->getMock("search", array('getArticleCount', 'getSearchParamForHtml'));
        $oView->expects($this->any())->method('getArticleCount')->will($this->returnValue(6));
        $oView->expects($this->any())->method('getSearchParamForHtml')->will($this->returnValue('searchStr'));

        $this->assertEquals('6 ' . oxRegistry::getLang()->translateString('HITS_FOR', oxRegistry::getLang()->getBaseLanguage(), false) . ' "searchStr"', $oView->getTitle());
    }
}
