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
namespace Unit\Application\Controller\Admin;

use \OxidEsales\EshopCommunity\Application\Model\Category;
use \oxField;
use \oxDb;
use \OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use \oxRegistry;
use \OxidEsales\EshopCommunity\Application\Model\SeoEncoderArticle;
use \oxTestModules;
use \OxidEsales\EshopCommunity\Application\Model\Vendor;

/**
 * Tests for Article_Seo class
 */
class ArticleSeoTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $sQ = "delete from oxvendor where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        $sQ = "delete from oxmanufacturers where oxid like '_test%'";
        oxDb::getDb()->execute($sQ);

        $sQ = "delete from oxseo where oxobjectid='objectid'";
        oxDb::getDb()->execute($sQ);
        parent::tearDown();
    }

    /**
     * Article_Seo::getEntryUri() test case, with oxvendor as active category type given.
     */
    public function testGetEntryUriOxVendorCase()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock("oxSeoEncoderCategory", array("getArticleVendorUri"));
        $seoEncoder->expects($this->at(0))->method('getArticleVendorUri')->will($this->returnValue("ArticleVendorUri"));

        $oView = $this->getMock("Article_Seo", array("getEditObjectId", "_getEncoder", "getActCatType", "getEditLang"));

        $oView->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue($productId));
        $oView->expects($this->at(1))->method('_getEncoder')->will($this->returnValue($seoEncoder));
        $oView->expects($this->at(2))->method('getActCatType')->will($this->returnValue("oxvendor"));
        $oView->expects($this->at(3))->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals("ArticleVendorUri", $oView->getEntryUri());
    }

    /**
     * Article_Seo::getEntryUri() test case, with the oxmanufacturer as active category type given.
     */
    public function testGetEntryUriOxManufacturerCase()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock("oxSeoEncoderCategory", array("getArticleManufacturerUri"));
        $seoEncoder->expects($this->at(0))->method('getArticleManufacturerUri')->will($this->returnValue("ArticleManufacturerUri"));

        $view = $this->getMock("Article_Seo", array("getEditObjectId", "_getEncoder", "getActCatType", "getEditLang"));

        $view->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue($productId));
        $view->expects($this->at(1))->method('_getEncoder')->will($this->returnValue($seoEncoder));
        $view->expects($this->at(2))->method('getActCatType')->will($this->returnValue("oxmanufacturer"));
        $view->expects($this->at(3))->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals("ArticleManufacturerUri", $view->getEntryUri());
    }

    /**
     * Article_Seo::getEntryUri() test case, with given active category id.
     */
    public function testGetEntryUriDefaultWithActiveCategoryId()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock("oxSeoEncoderCategory", array("getArticleUri"));
        $seoEncoder->expects($this->at(0))->method('getArticleUri')->will($this->returnValue("ArticleUri"));

        $view = $this->getMock("Article_Seo", array("getEditObjectId", "_getEncoder", "getActCatType", "getEditLang", "getActCatId"));

        $view->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue($productId));
        $view->expects($this->at(1))->method('_getEncoder')->will($this->returnValue($seoEncoder));
        $view->expects($this->at(2))->method('getActCatType')->will($this->returnValue("oxsomething"));
        $view->expects($this->at(3))->method('getActCatId')->will($this->returnValue(true));
        $view->expects($this->at(4))->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals("ArticleUri", $view->getEntryUri());
    }

    /**
     * Article_Seo::getEntryUri() test case, without given active category id.
     */
    public function testGetEntryUriDefaultWithoutActiveCategoryId()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock("oxSeoEncoderCategory", array("getArticleMainUri"));
        $seoEncoder->expects($this->at(0))->method('getArticleMainUri')->will($this->returnValue("ArticleMainUri"));

        $view = $this->getMock("Article_Seo", array("getEditObjectId", "_getEncoder", "getActCatType", "getEditLang", "getActCatId"));

        $view->expects($this->at(0))->method('getEditObjectId')->will($this->returnValue($productId));
        $view->expects($this->at(1))->method('_getEncoder')->will($this->returnValue($seoEncoder));
        $view->expects($this->at(2))->method('getActCatType')->will($this->returnValue("oxsomething"));
        $view->expects($this->at(3))->method('getActCatId')->will($this->returnValue(false));
        $view->expects($this->at(4))->method('getEditLang')->will($this->returnValue(0));

        $this->assertEquals("ArticleMainUri", $view->getEntryUri());
    }

    /**
     * Testing Article_Seo::showCatSelect()
     */
    public function showCatSelect()
    {
        $oView = oxNew('Article_Seo');
        $this->assertTrue($oView->showCatSelect());
    }

    /**
     * Article_Seo::_getEncoder() test case
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Article_Seo');
        $this->assertTrue($oView->UNITgetEncoder() instanceof SeoEncoderArticle);
    }


    /**
     * Article_Seo::Render() test case
     */
    public function testRender()
    {
        $oView = oxNew('Article_Seo');
        $this->assertEquals("object_seo.tpl", $oView->render());
    }

    /**
     * Article_Seo::_getVendorList() test case (regular)
     */
    public function testGetVendorList()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId("_test1");
        $oVendor->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxvendorid = new oxField("_test1");

        $oView = oxNew('Article_Seo');
        $aList = $oView->UNITgetVendorList($oArticle);

        $this->assertTrue(is_array($aList));

        $oArtVendor = reset($aList);
        $this->assertTrue($oArtVendor instanceof Vendor);
        $this->assertEquals($oVendor->getId(), $oArtVendor->getId());
    }

    /**
     * Article_Seo::_getManufacturerList() test case (regular)
     */
    public function testGetManufacturerList()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("_test1");
        $oManufacturer->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxmanufacturerid = new oxField("_test1");

        $oView = oxNew('Article_Seo');
        $aList = $oView->UNITgetManufacturerList($oArticle);

        $this->assertTrue(is_array($aList));

        $oArtManufacturer = reset($aList);
        $this->assertTrue($oArtManufacturer instanceof Manufacturer);
        $this->assertEquals($oManufacturer->getId(), $oArtManufacturer->getId());
    }


    /**
     * Article_Seo::getActCategory() test case (category)
     */
    public function testGetActCategory()
    {
        oxTestModules::addFunction('oxcategory', 'load', '{ return true; }');

        $oView = oxNew('Article_Seo');
        $this->assertTrue($oView->getActCategory() instanceof Category);
    }

    /**
     * Article_Seo::getActVendor() test case (manufacturer)
     */
    public function testGetActVendor()
    {
        oxTestModules::addFunction('oxvendor', 'load', '{ return true; }');

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxvendor"));
        $this->assertTrue($oView->getActVendor() instanceof Vendor);
    }

    /**
     * Article_Seo::getActManufacturer() test case (manufacturer)
     */
    public function testGetActManufacturer()
    {
        oxTestModules::addFunction('oxmanufacturer', 'load', '{ return true; }');

        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxmanufacturer"));
        $this->assertTrue($oView->getActManufacturer() instanceof Manufacturer);
    }

    /**
     * Test, that the method 'getListType' returns null, if the active category type is oxany.
     */
    public function testGetListTypeCaseAny()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxany"));
        $this->assertNull($oView->getListType());
    }

    /**
     * Test, that the method 'getListType' returns vendor, if the active category type is oxvendor.
     */
    public function testGetListTypeCaseVendor()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxvendor"));
        $this->assertEquals("vendor", $oView->getListType());
    }

    /**
     * Test, that the method 'getListType' returns manufacturer, if the active category type is oxmanufacturer.
     */
    public function testGetListTypeCaseManufacturer()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatType"));
        $oView->expects($this->any())->method('getActCatType')->will($this->returnValue("oxmanufacturer"));
        $this->assertEquals("manufacturer", $oView->getListType());
    }


    /**
     * Article_Seo::_getAltSeoEntryId() test case
     */
    public function testGetAltSeoEntryId()
    {
        $oView = $this->getMock("Article_Seo", array("getEditObjectId"));
        $oView->expects($this->once())->method('getEditObjectId')->will($this->returnValue(999));
        $this->assertEquals(999, $oView->UNITgetAltSeoEntryId());
    }

    /**
     * Article_Seo::getEditLang() test case
     */
    public function testGetEditLang()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatLang"));
        $oView->expects($this->once())->method('getActCatLang')->will($this->returnValue(999));
        $this->assertEquals(999, $oView->getEditLang());
    }

    /**
     * Article_Seo::_getSeoEntryType() test case (default)
     */
    public function testGetSeoEntryType()
    {
        $view = oxNew("Article_Seo");

        $this->assertEquals('oxarticle', $view->UNITgetSeoEntryType());
    }

    /**
     * Article_Seo::getType() test case (manufacturer)
     */
    public function testGetType()
    {
        $oView = oxNew('Article_Seo');
        $this->assertEquals('oxarticle', $oView->UNITgetType());
    }

    /**
     * Article_Seo::getActCatType() test case
     */
    public function testGetActCatType()
    {
        $this->setRequestParameter("aSeoData", null);

        $oView = $this->getMock("Article_Seo", array("getSelectionList"));
        $oView->expects($this->once())->method("getSelectionList")->will($this->returnValue(array("type" => array(999 => "value"))));
        $this->assertEquals("type", $oView->getActCatType());

        $this->setRequestParameter("aSeoData", array("oxparams" => "type#value#999"));
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertEquals("type", $oView->getActCatType());
    }

    /**
     * Article_Seo::getActCatLang() test case
     */
    public function testGetActCatLang()
    {
        $this->setRequestParameter("aSeoData", null);

        $oView = $this->getMock("Article_Seo", array("getSelectionList"));
        $oView->expects($this->once())->method("getSelectionList")->will($this->returnValue(array("type" => array(999 => "value"))));
        $this->assertEquals(999, $oView->getActCatLang());

        $this->setRequestParameter("aSeoData", array("oxparams" => "type#value#999"));
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertEquals(999, $oView->getActCatLang());
    }

    /**
     * Article_Seo::getActCatId() test case
     */
    public function testGetActCatId()
    {
        $this->setRequestParameter("aSeoData", null);

        $oItem = $this->getMock("oxManufacturer", array("getId"));
        $oItem->expects($this->once())->method("getId")->will($this->returnValue("value"));

        $oView = $this->getMock("Article_Seo", array("getSelectionList", "getActCatType", "getActCatLang"));
        $oView->expects($this->once())->method("getSelectionList")->will($this->returnValue(array("type" => array(999 => array($oItem)))));
        $oView->expects($this->once())->method("getActCatType")->will($this->returnValue("type"));
        $oView->expects($this->once())->method("getActCatLang")->will($this->returnValue(999));
        $this->assertEquals("value", $oView->getActCatId());

        $this->setRequestParameter("aSeoData", array("oxparams" => "type#value#999"));
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertEquals("value", $oView->getActCatId());
    }

    /**
     * Article_Seo::_getCategoryList() test case
     */
    public function testGetCategoryList()
    {
        $sO2CView = getViewName('oxobject2category');
        $sQ = "select oxarticles.oxid from oxarticles left join {$sO2CView} on
               oxarticles.oxid={$sO2CView}.oxobjectid where
               oxarticles.oxactive='1' and {$sO2CView}.oxobjectid is not null";

        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sProdId = $oDb->getOne($sQ);

        // must be existing
        $this->assertTrue((bool) $sProdId);

        $oProduct = oxNew('oxArticle');
        $oProduct->load($sProdId);

        $sQ = "select oxobject2category.oxcatnid as oxid from {$sO2CView} as oxobject2category where oxobject2category.oxobjectid="
              . $oDb->quote($oProduct->getId()) . " union " . $oProduct->getSqlForPriceCategories('oxid');

        $sQ = "select count(*) from ( $sQ ) as _tmp";
        $iCount = $oDb->getOne($sQ);

        $oView = oxNew('Article_Seo');
        $aList = $oView->UNITgetCategoryList($oProduct);

        // must be have few assignments
        $this->assertTrue($iCount > 0);
        $this->assertEquals($iCount, count($aList));
    }

    /**
     * Article_Seo::getSelectionList() test case
     */
    public function testGetSelectionList()
    {
        $productId = oxDb::getDb()->getOne("select oxid from oxarticles");
        $editingLanguageId = oxRegistry::getLang()->getEditLanguage();

        $product = oxNew('oxArticle');
        $product->load($productId);

        $view = $this->getMock("Article_Seo", array("getEditObjectId", "_getCategoryList", "_getVendorList", "_getManufacturerList", "_getTagList"));
        $view->expects($this->any())->method("getEditObjectId")->will($this->returnValue($productId));
        $view->expects($this->any())->method("_getCategoryList")->will($this->returnValue("CategoryList"));
        $view->expects($this->any())->method("_getVendorList")->will($this->returnValue("VendorList"));
        $view->expects($this->any())->method("_getManufacturerList")->will($this->returnValue("ManufacturerList"));
        $view->expects($this->any())->method("_getTagList")->will($this->returnValue("TagList"));

        $expectedList = array();
        $expectedList["oxcategory"][$editingLanguageId] = "CategoryList";
        $expectedList["oxvendor"][$editingLanguageId] = "VendorList";
        $expectedList["oxmanufacturer"][$editingLanguageId] = "ManufacturerList";

        $this->assertEquals($expectedList, $view->getSelectionList());
    }

    /**
     * Article_Seo::processParam() test case (any other than tag)
     */
    public function testProcessParam()
    {
        $oView = $this->getMock("Article_Seo", array("getActCatId"));

        $oView->expects($this->once())->method("getActCatId")->will($this->returnValue("testParam2"));

        $this->assertEquals("testParam2", $oView->processParam("testParam1#testParam2#0"));
    }

    /**
     * Vendor_Seo::isEntryFixed() test case
     */
    public function testIsEntryFixed()
    {
        $ShopId = $this->getConfig()->getShopId();
        $iLang = 0;
        $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxparams ) values
                                 ( 'objectid', 'ident', '{$ShopId}', '{$iLang}', 'stdurl', 'seourl', 'type', 1, 'catid' )";
        oxDb::getDb()->execute($sQ);

        $oView = $this->getMock("Article_Seo", array("_getSaveObjectId", "getActCatId", "getEditLang", "processParam"));
        $oView->expects($this->at(0))->method('_getSaveObjectId')->will($this->returnValue("objectid"));
        $oView->expects($this->at(1))->method('getEditLang')->will($this->returnValue(0));
        $oView->expects($this->at(2))->method('getActCatId')->will($this->returnValue("catid"));
        $oView->expects($this->at(3))->method('processParam')->will($this->returnValue("catid"));

        $oView->expects($this->at(4))->method('_getSaveObjectId')->will($this->returnValue("nonexistingobjectid"));
        $oView->expects($this->at(5))->method('getEditLang')->will($this->returnValue(0));
        $oView->expects($this->at(6))->method('getActCatId')->will($this->returnValue("catid"));
        $oView->expects($this->at(7))->method('processParam')->will($this->returnValue("catid"));

        $this->assertTrue($oView->isEntryFixed());
        $this->assertFalse($oView->isEntryFixed());
    }

    /**
     * @return string The product id.
     */
    protected function ensureProductIdExists()
    {
        $objectToCategoryViewName = getViewName('oxobject2category');
        $query = "select oxarticles.oxid from oxarticles left join {$objectToCategoryViewName} on
               oxarticles.oxid={$objectToCategoryViewName}.oxobjectid where
               oxarticles.oxactive='1' and {$objectToCategoryViewName}.oxobjectid is not null";

        $produdtId = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($query);

        // must be existing
        $this->assertTrue((bool) $produdtId);

        return $produdtId;
    }
}
