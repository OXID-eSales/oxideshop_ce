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

use \OxidEsales\EshopCommunity\Application\Model\ArticleList;
use oxField;
use oxTestModules;

/**
 * Testing start class
 */
class StartControllerTest extends \OxidTestCase
{

    public function testGetTitleSuffix()
    {
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxstarttitle = $this->getMock('oxField', array('__get'));
        $oShop->oxshops__oxstarttitle->expects($this->once())->method('__get')->will($this->returnValue('testsuffix'));

        $oConfig = $this->getMock('oxconfig', array('getActiveShop'));
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oView = $this->getMock('start', array('getConfig'));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals('testsuffix', $oView->getTitleSuffix());
    }

    public function testGetCanonicalUrl()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oViewConfig = $this->getMock("oxviewconfig", array("getHomeLink"));
        $oViewConfig->expects($this->once())->method('getHomeLink')->will($this->returnValue("testHomeLink"));

        $oView = $this->getMock("start", array("getViewConfig"));
        $oView->expects($this->once())->method('getViewConfig')->will($this->returnValue($oViewConfig));

        $this->assertEquals('testHomeLink', $oView->getCanonicalUrl());
    }

    public function testGetRealSeoCanonicalUrl()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return true;}");

        $oView = oxNew('start');
        $this->assertEquals($this->getConfig()->getConfigParam("sShopURL"), $oView->getCanonicalUrl());
    }

    public function testGetArticleList()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getArticleList();
        $this->assertTrue($aList instanceof ArticleList);
        $this->assertEquals(2, $aList->count());
    }

    public function testGetTopArticleList()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getTopArticleList();
        $this->assertTrue($aList instanceof ArticleList);
        $this->assertEquals(1, $aList->count());

        $expectedId = $this->getTestConfig()->getShopEdition() == 'EE'? "2275" : "1849";
        $this->assertEquals($expectedId, $aList->current()->getId());
    }

    public function testGetNewestArticles()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getNewestArticles();
        $this->assertTrue($aList instanceof ArticleList);
        $this->assertEquals(4, $aList->count());
    }

    public function testGetCatOfferArticle()
    {
        $oStart = $this->getProxyClass('start');

        $oArt = $oStart->getCatOfferArticle();

        $expectedId = $this->getTestConfig()->getShopEdition() == 'EE'? "1351" : "1126";
        $this->assertEquals($expectedId, $oArt->getId());
    }

    public function testGetCatOfferArticleList()
    {
        $oStart = $this->getProxyClass('start');

        $aList = $oStart->getCatOfferArticleList();
        $this->assertTrue($aList instanceof ArticleList);
        $this->assertEquals(2, $aList->count());
    }

    public function testPrepareMetaKeyword()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadAktion', 1);

        $oArticle = $this->getMock('oxarticle', array('getLongDescription'));
        $oArticle->expects($this->once())->method('getLongDescription')->will($this->returnValue(new oxField('testlongdesc')));

        $oStart = $this->getMock('start', array('getFirstArticle'));
        $oStart->expects($this->once())->method('getFirstArticle')->will($this->returnValue($oArticle));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->UNITprepareMetaKeyword('testlongdesc'), $oStart->UNITprepareMetaKeyword(null));
    }

    public function testViewMetaKeywords()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $oStart = $this->getProxyClass('start');
        $oStart->render();
        $aMetaKeywords = $oStart->getMetaKeywords();

        $this->assertTrue(strlen($aMetaKeywords) > 0);
    }

    public function testPrepareMetaDescription()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadAktion', 1);

        $oArticle = $this->getMock('oxarticle', array('getLongDescription'));
        $oArticle->expects($this->once())->method('getLongDescription')->will($this->returnValue(new oxField('testlongdesc')));

        $oStart = $this->getMock('start', array('getFirstArticle'));
        $oStart->expects($this->once())->method('getFirstArticle')->will($this->returnValue($oArticle));

        $oView = oxNew('oxubase');
        $this->assertEquals($oView->UNITprepareMetaDescription('- testlongdesc'), $oStart->UNITprepareMetaDescription(null));
    }

    public function testViewMetaDescritpion()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $oStart = $this->getProxyClass('start');
        $oStart->render();
        $aMetaKeywords = $oStart->getMetaDescription();

        $this->assertTrue(strlen($aMetaKeywords) > 0);
    }

    public function testGetBanners()
    {
        $oArticleList = $this->getMock('oxActionList', array('loadBanners'));
        $oArticleList->expects($this->once())->method('loadBanners');

        oxTestModules::addModuleObject('oxActionList', $oArticleList);

        $oView = oxNew('start');
        $oView->getBanners();
    }
}
