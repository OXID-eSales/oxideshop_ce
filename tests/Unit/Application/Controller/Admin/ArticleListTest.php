<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\CategoryList;
use \oxField;
use \oxDb;
use OxidEsales\EshopCommunity\Application\Model\ManufacturerList;
use \oxTestModules;
use OxidEsales\EshopCommunity\Application\Model\VendorList;

/**
 * Tests for Article_List class
 */
class ArticleListTest extends \OxidTestCase
{

    /**
     * Test building sql where with specified "folder" param
     *  for oxarticles, oxorder, oxcontents tables
     *
     * @return null
     */
    public function testBuildWhereWithSpecifiedFolderParam()
    {
        $sObjects = 'oxArticle';

        $this->setRequestParameter('folder', $sObjects . 'TestFolderName');

        $oAdminList = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleList::class, array("getItemList"));
        $oAdminList->expects($this->once())->method('getItemList')->will($this->returnValue(null));
        $aBuildWhere = $oAdminList->buildWhere();
        $this->assertEquals('oxArticleTestFolderName', $aBuildWhere[getViewName('oxarticles') . '.oxfolder']);
    }

    /**
     * Article_List::Render() test case
     *
     * @return null
     */
    public function testRenderSelectingProductCategory()
    {
        $this->setRequestParameter("where", array("oxarticles" => array("oxtitle" => "testValue")));

        $sCatId = oxDb::getDb()->getOne("select oxid from oxcategories");
        $this->setRequestParameter("art_category", "cat@@" . $sCatId);
        // testing..
        $oView = oxNew('Article_List');
        $this->assertEquals('article_list.tpl', $oView->render());

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["cattree"] instanceof CategoryList);
        $this->assertTrue($aViewData["cattree"]->offsetExists($sCatId));
        $this->assertEquals(1, $aViewData["cattree"]->offsetGet($sCatId)->selected);
        $this->assertTrue($aViewData["mnftree"] instanceof ManufacturerList);
        $this->assertTrue($aViewData["vndtree"] instanceof VendorList);
        $this->assertTrue(isset($aViewData["pwrsearchinput"]));
        $this->assertEquals("testValue", $aViewData["pwrsearchinput"]);
    }

    /**
     * Article_List::Render() test case
     *
     * @return null
     */
    public function testRenderSelectingProductManufacturer()
    {
        $sManId = oxDb::getDb()->getOne("select oxid from oxmanufacturers");
        $this->setRequestParameter("art_category", "mnf@@" . $sManId);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleList::class, array("getItemList"));
        $oView->expects($this->any())->method('getItemList')->will($this->returnValue(oxNew('oxarticlelist')));
        $this->assertEquals('article_list.tpl', $oView->render());

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["cattree"] instanceof CategoryList);
        $this->assertTrue($aViewData["mnftree"] instanceof ManufacturerList);
        $this->assertTrue($aViewData["mnftree"]->offsetExists($sManId));
        $this->assertEquals(1, $aViewData["mnftree"]->offsetGet($sManId)->selected);
        $this->assertTrue($aViewData["vndtree"] instanceof VendorList);
    }

    /**
     * Article_List::Render() test case
     *
     * @return null
     */
    public function testRenderSelectingProductVendor()
    {
        $sVndId = oxDb::getDb()->getOne("select oxid from oxvendor");
        $this->setRequestParameter("art_category", "vnd@@" . $sVndId);
        $this->getConfig()->setConfigParam("blSkipFormatConversion", false);

        $oArticle1 = oxNew('oxArticle');
        $oArticle1->oxarticles__oxtitle = new oxField("title1");
        $oArticle1->oxarticles__oxtitle->fldtype = "datetime";

        $oArticle2 = oxNew('oxArticle');
        $oArticle2->oxarticles__oxtitle = new oxField("title2");
        $oArticle2->oxarticles__oxtitle->fldtype = "timestamp";

        $oArticle3 = oxNew('oxArticle');
        $oArticle3->oxarticles__oxtitle = new oxField("title3");
        $oArticle3->oxarticles__oxtitle->fldtype = "date";

        $oList = oxNew('oxList');
        $oList->offsetSet("1", $oArticle1);
        $oList->offsetSet("2", $oArticle2);
        $oList->offsetSet("3", $oArticle3);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleList::class, array("getItemList"));
        $oView->expects($this->any())->method('getItemList')->will($this->returnValue($oList));
        $this->assertEquals('article_list.tpl', $oView->render());

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["cattree"] instanceof CategoryList);
        $this->assertTrue($aViewData["mnftree"] instanceof ManufacturerList);
        $this->assertTrue($aViewData["vndtree"] instanceof VendorList);
        $this->assertTrue($aViewData["vndtree"]->offsetExists($sVndId));
        $this->assertEquals(1, $aViewData["vndtree"]->offsetGet($sVndId)->selected);
    }

    /**
     * Article_List::_buildSelectString() test case
     *
     * @return null
     */
    public function testBuildSelectStringCategory()
    {
        $sTable = getViewName("oxarticles");
        $sO2CView = getViewName("oxobject2category");
        $this->setRequestParameter("art_category", "cat@@testCategory");

        $oProduct = oxNew('oxArticle');
        $sQ = $oProduct->buildSelectString(null);
        $sQ = str_replace(" from $sTable where 1 ", " from $sTable left join $sO2CView on $sTable.oxid = $sO2CView.oxobjectid where $sO2CView.oxcatnid = 'testCategory' and  1  and $sTable.oxparentid = '' ", $sQ);

        $oView = oxNew('Article_List');
        $this->assertEquals($sQ, $oView->UNITbuildSelectString($oProduct));
    }

    /**
     * Article_List::_buildSelectString() test case
     *
     * @return null
     */
    public function testBuildSelectStringManufacturer()
    {
        $sTable = getViewName("oxarticles");
        $this->setRequestParameter("art_category", "mnf@@testManufacturer");

        $oProduct = oxNew('oxArticle');
        $sQ = $oProduct->buildSelectString(null);

        $oView = oxNew('Article_List');
        $this->assertEquals($sQ . " and $sTable.oxparentid = ''  and $sTable.oxmanufacturerid = 'testManufacturer'", $oView->UNITbuildSelectString($oProduct));
    }

    /**
     * Article_List::_buildSelectString() test case
     *
     * @return null
     */
    public function testBuildSelectStringVendor()
    {
        $sTable = getViewName("oxarticles");
        $this->setRequestParameter("art_category", "vnd@@testVendor");

        $oProduct = oxNew('oxArticle');
        $sQ = $oProduct->buildSelectString(null);

        $oView = oxNew('Article_List');
        $this->assertEquals($sQ . " and $sTable.oxparentid = ''  and $sTable.oxvendorid = 'testVendor'", $oView->UNITbuildSelectString($oProduct));
    }

    /**
     * Article_List::BuildWhere() test case
     *
     * @return null
     */
    public function testBuildWhere()
    {
        $this->setRequestParameter("folder", "testFolder");
        $sViewName = getViewName('oxarticles');

        $oView = oxNew('Article_List');
        $this->assertEquals(array("$sViewName.oxfolder" => "testFolder"), $oView->buildWhere());
    }

    /**
     * Article_List::_buildSelectString() test case
     *
     * @return null
     */
    public function testBuildSelectString()
    {
        $oProduct = oxNew('oxArticle');
        $sQ = $oProduct->buildSelectString(null);

        $oView = oxNew('Article_List');
        $this->assertEquals($sQ . " and " . getViewName('oxarticles') . ".oxparentid = '' ", $oView->UNITbuildSelectString($oProduct));
    }

    /**
     * Article_List::DeleteEntry() test case
     *
     * @return null
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");
        oxTestModules::addFunction('oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'delete', '{ return true; }');

        $this->setRequestParameter("oxid", "testId");

        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleList::class, array("_authorize", 'getSession'));
        $oView->expects($this->any())->method('_authorize')->will($this->returnValue(true));
        $oView->expects($this->any())->method('getSession')->will($this->returnValue($oSess));
        $oView->deleteEntry();
    }

    /**
     * Test case for Article_List::getSearchFields()() getter
     *
     * @return null
     */
    public function testGetSearchFields()
    {
        $aSkipFields = array("oxblfixedprice", "oxvarselect", "oxamitemid", "oxamtaskid", "oxpixiexport", "oxpixiexported");
        $oView = oxNew('Article_List');

        $oArticle = oxNew('oxArticle');
        $this->assertEquals(array_diff($oArticle->getFieldNames(), $aSkipFields), $oView->getSearchFields());
    }
}
