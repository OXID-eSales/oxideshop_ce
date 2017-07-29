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
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxTestModules;

class RssTest extends \OxidTestCase
{

    public function testGetChannel()
    {
        oxTestModules::addFunction('oxrssfeed', 'setChannel', '{$this->_aChannel = $aA[0];}');

        $o = $this->getProxyClass("rss");
        $oRss = oxNew("oxRssFeed");
        $oRss->setChannel('asd');
        $o->setNonPublicVar("_oRss", $oRss);
        $this->assertEquals('asd', $o->getChannel());
    }

    public function testProcessOutput()
    {
        $oRss = oxNew('rss');
        $this->assertEquals("äöüÄÖÜß", $oRss->UNITprocessOutput('&auml;&ouml;&uuml;&Auml;&Ouml;&Uuml;&szlig;'));
    }

    public function testInit()
    {
        $oRss = oxNew('rss');

        $this->setRequestParameter('cur', 2);
        $this->getSession()->setVariable('currency', 4);

        $this->assertSame(null, $oRss->init());

        $this->assertEquals(2, $this->getSession()->getVariable('currency'));
    }

    public function testGetRssFeed()
    {
        $oRssFeed = (object) array('x' => 'a');
        oxTestModules::addModuleObject('oxRssFeed', $oRssFeed);

        $this->assertSame($oRssFeed, oxNew('rss')->UNITgetRssFeed());
    }

    public function testRender()
    {
        $oSmarty = $this->getMock('Smarty', array('assign_by_ref', 'assign', 'fetch'));
        $oSmarty->expects($this->any())->method('assign_by_ref');
        $oSmarty->expects($this->once())->method('fetch')->with($this->equalTo('widget/rss.tpl'), $this->equalTo('viewid'))->will($this->returnValue('smarty processed xml'));
        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('getSmarty'));
        $oUtilsView->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('setHeader', 'showMessageAndExit'));
        $oUtils->expects($this->once())->method('setHeader')->with($this->equalTo('Content-Type: text/xml; charset=XCHARSET'));
        $oUtils->expects($this->once())->method('showMessageAndExit')->with($this->equalTo('smarty processed xml'));

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('translateString'));
        $oLang->expects($this->once())->method('translateString')->with($this->equalTo('charset'))->will($this->returnValue('XCHARSET'));

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getViewId'));
        $oRss->expects($this->once())->method('getViewId')->will($this->returnValue('viewid'));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);
        oxTestModules::addModuleObject('oxLang', $oLang);

        $this->assertSame(null, $oRss->render());
    }

    public function testTopShopDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssTopShop'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->topshop();
    }


    public function testTopShopEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssTopShop'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('loadTopInShop'));
        $oRssFeed->expects($this->once())->method('loadTopInShop');

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $oRss->topshop();
    }


    public function testNewArtsDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssNewest'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->newarts();
    }


    public function testNewArtsEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssNewest'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('loadNewestArticles'));
        $oRssFeed->expects($this->once())->method('loadNewestArticles');

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $oRss->newarts();
    }


    public function testSearchArtsDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssSearch'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->searcharts();
    }


    public function testSearchArtsEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssSearch'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);


        $this->setRequestParameter('searchparam', 'x&searchparam');
        $this->setRequestParameter('searchcnid', 'x&searchcnid');
        $this->setRequestParameter('searchvendor', 'x&searchvendor');
        $this->setRequestParameter('searchmanufacturer', 'x&searchmanufacturer');

        $oRssFeed = $this->getMock('stdclass', array('loadSearchArticles'));
        $oRssFeed->expects($this->once())->method('loadSearchArticles')->with(
            $this->equalTo('x&searchparam'),
            $this->equalTo('x&amp;searchcnid'),
            $this->equalTo('x&amp;searchvendor'),
            $this->equalTo('x&amp;searchmanufacturer')
        );

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $oRss->searcharts();
    }


    public function testBargainDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssBargain'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->bargain();
    }


    public function testBargainEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssBargain'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('loadBargain'));
        $oRssFeed->expects($this->once())->method('loadBargain');

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $oRss->bargain();
    }


    public function testCatArtsDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssCategories'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->catarts();
    }


    public function testCatArtsEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssCategories'))->will($this->returnValue(true));

        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Model\Category::class, array('load'));
        $oObj->expects($this->once())->method('load')->with($this->equalTo('x&amp;objid'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('loadCategoryArticles'));
        $oRssFeed->expects($this->once())->method('loadCategoryArticles')->with($this->equalTo($oObj));

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $this->setRequestParameter('cat', 'x&objid');
        oxTestModules::addModuleObject('oxCategory', $oObj);

        $oRss->catarts();
    }


    public function testRecommListsDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssRecommLists'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->recommlists();
    }

    public function testRecommListsEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssRecommLists'))->will($this->returnValue(true));

        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('load'));
        $oObj->expects($this->once())->method('load')->with($this->equalTo('x&amp;objid'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('loadRecommLists'));
        $oRssFeed->expects($this->once())->method('loadRecommLists')->with($this->equalTo($oObj));

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $this->setRequestParameter('anid', 'x&objid');
        oxTestModules::addModuleObject('oxarticle', $oObj);

        $oRss->recommlists();
    }


    public function testRecommListArtsDisabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssRecommListArts'))->will($this->returnValue(false));
        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError')->with($this->equalTo(''));
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss->recommlistarts();
    }


    public function testRecommListArtsEnabled()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssRecommListArts'))->will($this->returnValue(true));

        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array('load'));
        $oObj->expects($this->once())->method('load')->with($this->equalTo('x&amp;objid'))->will($this->returnValue(true));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->never())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('loadRecommListArticles'));
        $oRssFeed->expects($this->once())->method('loadRecommListArticles')->with($this->equalTo($oObj));

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->once())->method('_getRssFeed')->will($this->returnValue($oRssFeed));

        $this->setRequestParameter('recommid', 'x&objid');
        oxTestModules::addModuleObject('oxrecommlist', $oObj);

        $oRss->recommlistarts();
    }

    public function testRecommListArtsEnabledNoList()
    {
        $oCfg = $this->getMock('oxCofig', array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssRecommListArts'))->will($this->returnValue(true));

        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array('load'));
        $oObj->expects($this->once())->method('load')->with($this->equalTo('x&amp;objid'))->will($this->returnValue(false));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->never())->method('_getRssFeed');

        $this->setRequestParameter('recommid', 'x&objid');
        oxTestModules::addModuleObject('oxrecommlist', $oObj);

        $oRss->recommlistarts();
    }

    public function testRecommListsEnabledNoList()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getConfigParam'));
        $oCfg->expects($this->once())->method('getConfigParam')->with($this->equalTo('bl_rssRecommLists'))->will($this->returnValue(true));

        $oObj = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('load'));
        $oObj->expects($this->once())->method('load')->with($this->equalTo('x&amp;objid'))->will($this->returnValue(false));

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('handlePageNotFoundError'));
        $oUtils->expects($this->once())->method('handlePageNotFoundError');
        oxTestModules::addModuleObject('oxutils', $oUtils);

        $oRss = $this->getMock(\OxidEsales\Eshop\Application\Controller\RssController::class, array('getConfig', '_getRssFeed'));
        $oRss->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $oRss->expects($this->never())->method('_getRssFeed');

        $this->setRequestParameter('anid', 'x&objid');
        oxTestModules::addModuleObject('oxarticle', $oObj);

        $oRss->recommlists();
    }


}

