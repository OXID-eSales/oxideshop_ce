<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\EshopCommunity\Application\Model\ArticleList;
use \oxField;
use \oxTestModules;

/**
 * Testing start class
 */
class StartTest extends \OxidTestCase
{
    public function testGetTitleSuffix()
    {
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxstarttitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ['__get']);
        $oShop->oxshops__oxstarttitle->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveShop']);
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\StartController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertEquals('testsuffix', $oView->getTitleSuffix());
    }

    public function testGetCanonicalUrl()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getHomeLink"]);
        $oViewConfig->expects($this->once())->method('getHomeLink')->will($this->returnValue("testHomeLink"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\StartController::class, ["getViewConfig"]);
        $oView->expects($this->once())->method('getViewConfig')->will($this->returnValue($oViewConfig));

        $this->assertEquals('testHomeLink', $oView->getCanonicalUrl());
    }

    public function testGetRealSeoCanonicalUrl()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oView = oxNew('start');
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL"), $oView->getCanonicalUrl());
    }

    public function testGetTopArticleList()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getTopArticleList();
        $this->assertTrue($aList instanceof articlelist);
        $this->assertEquals(1, $aList->count());

        $expectedId = $this->getTestConfig()->getShopEdition() == 'EE' ? "2275" : "1849";
        $this->assertEquals($expectedId, $aList->current()->getId());
    }

    public function testGetNewestArticles()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getNewestArticles();
        $this->assertTrue($aList instanceof articlelist);
        $this->assertEquals(4, $aList->count());
    }

    public function testGetCatOfferArticle()
    {
        $oStart = $this->getProxyClass('start');

        $oArt = $oStart->getCatOfferArticle();

        $expectedId = $this->getTestConfig()->getShopEdition() == 'EE' ? "1351" : "1126";
        $this->assertEquals($expectedId, $oArt->getId());
    }

    public function testGetCatOfferArticleList()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getCatOfferArticleList();
        $this->assertTrue($aList instanceof articlelist);
        $this->assertEquals(2, $aList->count());
    }

    public function testPrepareMetaKeyword()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadAktion', 1);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getLongDescription']);
        $oArticle->expects($this->once())->method('getLongDescription')->will($this->returnValue(new oxField('testlongdesc')));

        $oStart = $this->getMock(\OxidEsales\Eshop\Application\Controller\StartController::class, ['getFirstArticle']);
        $oStart->expects($this->once())->method('getFirstArticle')->will($this->returnValue($oArticle));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->prepareMetaKeyword('testlongdesc'), $oStart->prepareMetaKeyword(null));
    }

    public function testViewMetaKeywords()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $oStart = $this->getProxyClass('start');
        $oStart->render();

        $aMetaKeywords = $oStart->getMetaKeywords();

        $this->assertTrue(strlen((string) $aMetaKeywords) > 0);
    }

    public function testPrepareMetaDescription()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadAktion', 1);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ['getLongDescription']);
        $oArticle->expects($this->once())->method('getLongDescription')->will($this->returnValue(new oxField('testlongdesc')));

        $oStart = $this->getMock(\OxidEsales\Eshop\Application\Controller\StartController::class, ['getFirstArticle']);
        $oStart->expects($this->once())->method('getFirstArticle')->will($this->returnValue($oArticle));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->prepareMetaDescription('- testlongdesc'), $oStart->prepareMetaDescription(null));
    }

    public function testViewMetaDescritpion()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $oStart = $this->getProxyClass('start');
        $oStart->render();

        $aMetaKeywords = $oStart->getMetaDescription();

        $this->assertTrue(strlen((string) $aMetaKeywords) > 0);
    }

    public function testGetBanners()
    {
        $oArticleList = $this->getMock(\OxidEsales\Eshop\Application\Model\ActionList::class, ['loadBanners']);
        $oArticleList->expects($this->once())->method('loadBanners');

        oxTestModules::addModuleObject('oxActionList', $oArticleList);

        $oView = oxNew('start');
        $oView->getBanners();
    }
}
