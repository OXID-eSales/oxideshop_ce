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
 * Testing tag class
 */
class Unit_Views_tagTest extends OxidTestCase
{

    public function testSetItemSorting()
    {
        $oView = new tag();
        $oView->setItemSorting('alist', "testSortBy", "testSortOrder");

        $aSorting = $this->getSession()->getVar("aSorting");

        $this->assertNotNull($aSorting);
        $this->assertTrue(isset($aSorting["alist"]));
        $this->assertEquals("testSortBy", $aSorting["alist"]["sortby"]);
        $this->assertEquals("testSortOrder", $aSorting["alist"]["sortdir"]);
    }

    public function testRender()
    {
        modConfig::setRequestParameter("searchtag", "kuyichi");
        $oView = new tag();

        $this->assertEquals("page/list/list.tpl", $oView->render());
    }

    /**
     * Testing if render method calls empty category should be outputted
     */
    public function testRender_noArticlesForTag()
    {
        modConfig::setRequestParameter("pgNr", 999);
        modConfig::setRequestParameter("searchtag", "notexistingtag");

        $oView = new tag();
        $this->assertEquals("page/list/list.tpl", $oView->render());
    }

    public function testGetAddUrlParams()
    {
        modConfig::setRequestParameter("searchtag", "testSearchTag");

        $oListView = new aList();
        $sListViewParams = $oListView->getAddUrlParams();
        $sListViewParams .= "listtype=tag&amp;searchtag=testSearchTag";

        $oView = new tag();
        $this->assertEquals($sListViewParams, $oView->getAddUrlParams());
    }

    public function testGetTitlePageSuffix()
    {
        $oView = $this->getMock("tag", array("getActPage"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(0));

        $this->assertNull($oView->getTitlePageSuffix());

        $oView = $this->getMock("tag", array("getActPage"));
        $oView->expects($this->once())->method('getActPage')->will($this->returnValue(1));

        $this->assertEquals(oxRegistry::getLang()->translateString('PAGE') . " " . 2, $oView->getTitlePageSuffix());
    }

    public function testGetTreePath()
    {
        $sTag = "testTag";

        $oStr = getStr();

        $aPath[0] = oxNew("oxcategory");
        $aPath[0]->setLink(false);
        $aPath[0]->oxcategories__oxtitle = new oxField(oxRegistry::getLang()->translateString('TAGS'));

        $aPath[1] = oxNew("oxcategory");
        $aPath[1]->setLink(false);
        $aPath[1]->oxcategories__oxtitle = new oxField($oStr->htmlspecialchars($oStr->ucfirst($sTag)));

        $oView = $this->getMock("tag", array("getTag"));
        $oView->expects($this->once())->method('getTag')->will($this->returnValue($sTag));

        $this->assertEquals($aPath, $oView->getTreePath());
    }

    public function testNoIndex()
    {
        $oTagView = new tag();
        $this->assertTrue(0 === $oTagView->noIndex());
    }

    public function testGetCanonicalUrlForPageNumberTwo()
    {
        $oTagView = $this->getMock("tag", array("getActPage", "_addPageNrParam", "generatePageNavigationUrl", "getTag"));
        $oTagView->expects($this->once())->method('getActPage')->will($this->returnValue(1));
        $oTagView->expects($this->once())->method('generatePageNavigationUrl')->will($this->returnValue("testUrl"));
        $oTagView->expects($this->once())->method('_addPageNrParam')->with($this->equalTo("testUrl"), $this->equalTo(1))->will($this->returnValue("testUrlWithPagePAram"));
        $oTagView->expects($this->never())->method('getTag');

        $this->assertEquals("testUrlWithPagePAram", $oTagView->getCanonicalUrl());
    }

    public function testGetCanonicalUrlForPageNumberOne()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $oTagView = $this->getMock("tag", array("getActPage", "_addPageNrParam", "generatePageNavigationUrl", "getTag"));
        $oTagView->expects($this->never())->method('generatePageNavigationUrl');
        $oTagView->expects($this->never())->method('_addPageNrParam');
        $oTagView->expects($this->once())->method('getActPage')->will($this->returnValue(0));
        $oTagView->expects($this->once())->method('getTag')->will($this->returnValue('testTag'));

        $this->assertEquals(oxRegistry::get("oxSeoEncoderTag")->getTagUrl('testTag'), $oTagView->getCanonicalUrl());
    }

    public function testGeneratePageNavigationUrlSeo()
    {
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{ return true; }");
        oxTestModules::addFunction("oxSeoEncoderTag", "getTagUrl", "{ return 'sTagUrl'; }");

        $oTag = $this->getMock('tag', array('getTag'));
        $oTag->expects($this->once())->method('getTag')->will($this->returnValue('sTag'));

        $this->assertEquals('sTagUrl', $oTag->generatePageNavigationUrl());
    }

    public function testGeneratePageNavigationUrl()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . oxRegistry::getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{ return false; }");

        $oTag = $this->getMock('tag', array('getTag'));
        $oTag->expects($this->never())->method('getTag');

        $sUrl = oxRegistry::getConfig()->getShopHomeURL() . $oTag->UNITgetRequestParams(false);
        $this->assertEquals($sUrl, $oTag->generatePageNavigationUrl());
    }

    public function testAddPageNrParamSeo()
    {
        oxTestModules::addFunction("oxUtils", "seoIsActive", "{ return true; }");
        oxTestModules::addFunction("oxSeoEncoderTag", "getTagPageUrl", "{ return 'sTagPageUrl'; }");

        $oTag = $this->getMock('tag', array('getTag'));
        $oTag->expects($this->once())->method('getTag')->will($this->returnValue('sTag'));

        $this->assertEquals('sTagPageUrl', $oTag->UNITaddPageNrParam('sUrl', 10));
    }

    public function testAddPageNrParam()
    {
        $sUrl = 'sUrl?pgNr=10';

        oxTestModules::addFunction("oxUtils", "seoIsActive", "{ return false; }");

        $oTag = $this->getMock('tag', array('getTag'));
        $oTag->expects($this->never())->method('getTag');

        $this->assertEquals($sUrl, $oTag->UNITaddPageNrParam('sUrl', 10));
    }

    public function testGetProductLinkType()
    {
        $oTagView = new tag();
        $this->assertEquals(OXARTICLE_LINKTYPE_TAG, $oTagView->UNITgetProductLinkType());
    }

    public function testPrepareMetaKeyword()
    {
        $oArticle1 = new oxarticle();
        $oArticle1->setId('oArticle1');
        $oArticle1->oxarticles__oxtitle = new oxField('testoxtitle1');

        $oArticle2 = new oxarticle();
        $oArticle2->setId('oArticle2');
        $oArticle2->oxarticles__oxtitle = new oxField('testoxtitle2');

        $oArtList = new oxlist();
        $oArtList->offsetSet($oArticle1->getId(), $oArticle1);
        $oArtList->offsetSet($oArticle2->getId(), $oArticle2);

        $oTagView = $this->getMock('tag', array('getArticleList'));
        $oTagView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $this->assertEquals("testoxtitle1, testoxtitle2", $oTagView->getMetaKeywords());
    }

    public function testPrepareMetaDescription()
    {
        $oArticle1 = new oxarticle();
        $oArticle1->setId('oArticle1');
        $oArticle1->oxarticles__oxtitle = new oxField('testoxtitle1');

        $oArticle2 = new oxarticle();
        $oArticle2->setId('oArticle2');
        $oArticle2->oxarticles__oxtitle = new oxField('testoxtitle2');

        $oArtList = new oxlist();
        $oArtList->offsetSet($oArticle1->getId(), $oArticle1);
        $oArtList->offsetSet($oArticle2->getId(), $oArticle2);

        $oTagView = $this->getMock('tag', array('getArticleList'));
        $oTagView->expects($this->any())->method('getArticleList')->will($this->returnValue($oArtList));
        $this->assertEquals("testoxtitle1, testoxtitle2", $oTagView->getMetaDescription());
    }


    public function testGetArticleList()
    {
        $sTag = 'wanduhr';
        modConfig::getInstance()->setConfigParam('iNrofCatArticles', 20);
        $oTag = $this->getProxyClass('tag');
        $oTag->setNonPublicVar("_sTag", $sTag);
        $oArtList = $oTag->getArticleList();
        $this->assertEquals(3, $oArtList->count());
    }

    public function testGetTitle()
    {
        $sTag = "wanduhr";
        $oTag = $this->getProxyClass('tag');
        $oTag->setNonPublicVar("_sTag", $sTag);
        $this->assertEquals('Wanduhr', $oTag->getTitle());
    }

    /**
     * Testing tags::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oTag = new Tag();
        $this->assertEquals(2, count($oTag->getBreadCrumb()));
    }

    /**
     * Test get active tag.
     *
     * @return null
     */
    public function testGetTag()
    {
        $oTag = new Tag();

        $this->setRequestParam('searchtag', null);
        $this->assertNull($oTag->getTag());

        $this->setRequestParam('searchtag', 'sometag');
        $this->assertEquals('sometag', $oTag->getTag());
    }

    /**
     * Tests tags with special chars
     */
    public function testGetTagSpecialChars()
    {
        $oTag = new Tag();

        $this->setRequestParam('searchtag', 'sometag<">');
        $this->assertEquals('sometag&lt;&quot;&gt;', $oTag->getTag());
    }

}
