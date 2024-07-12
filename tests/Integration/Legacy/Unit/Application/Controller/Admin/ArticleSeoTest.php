<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Application\Model\Category;
use \oxField;
use \oxDb;
use OxidEsales\EshopCommunity\Application\Model\Manufacturer;
use \oxRegistry;
use OxidEsales\EshopCommunity\Application\Model\SeoEncoderArticle;
use \oxTestModules;
use OxidEsales\EshopCommunity\Application\Model\Vendor;

/**
 * Tests for Article_Seo class
 */
class ArticleSeoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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

        $seoEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, ["getArticleVendorUri"]);
        $seoEncoder->method('getArticleVendorUri')->willReturn("ArticleVendorUri");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getEditObjectId", "getEncoder", "getActCatType", "getEditLang"]);

        $oView->method('getEditObjectId')->willReturn($productId);
        $oView->method('getEncoder')->willReturn($seoEncoder);
        $oView->method('getActCatType')->willReturn("oxvendor");
        $oView->method('getEditLang')->willReturn(0);

        $this->assertSame("ArticleVendorUri", $oView->getEntryUri());
    }

    /**
     * Article_Seo::getEntryUri() test case, with the oxmanufacturer as active category type given.
     */
    public function testGetEntryUriOxManufacturerCase()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, ["getArticleManufacturerUri"]);
        $seoEncoder->method('getArticleManufacturerUri')->willReturn("ArticleManufacturerUri");

        $view = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getEditObjectId", "getEncoder", "getActCatType", "getEditLang"]);

        $view->method('getEditObjectId')->willReturn($productId);
        $view->method('getEncoder')->willReturn($seoEncoder);
        $view->method('getActCatType')->willReturn("oxmanufacturer");
        $view->method('getEditLang')->willReturn(0);

        $this->assertSame("ArticleManufacturerUri", $view->getEntryUri());
    }

    /**
     * Article_Seo::getEntryUri() test case, with given active category id.
     */
    public function testGetEntryUriDefaultWithActiveCategoryId()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, ["getArticleUri"]);
        $seoEncoder->method('getArticleUri')->willReturn("ArticleUri");

        $view = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getEditObjectId", "getEncoder", "getActCatType", "getEditLang", "getActCatId"]);

        $view->method('getEditObjectId')->willReturn($productId);
        $view->method('getEncoder')->willReturn($seoEncoder);
        $view->method('getActCatType')->willReturn("oxsomething");
        $view->method('getActCatId')->willReturn(true);
        $view->method('getEditLang')->willReturn(0);

        $this->assertSame("ArticleUri", $view->getEntryUri());
    }

    /**
     * Article_Seo::getEntryUri() test case, without given active category id.
     */
    public function testGetEntryUriDefaultWithoutActiveCategoryId()
    {
        $productId = $this->ensureProductIdExists();

        $seoEncoder = $this->getMock(\OxidEsales\Eshop\Application\Model\SeoEncoderCategory::class, ["getArticleMainUri"]);
        $seoEncoder->method('getArticleMainUri')->willReturn("ArticleMainUri");

        $view = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getEditObjectId", "getEncoder", "getActCatType", "getEditLang", "getActCatId"]);

        $view->method('getEditObjectId')->willReturn($productId);
        $view->method('getEncoder')->willReturn($seoEncoder);
        $view->method('getActCatType')->willReturn("oxsomething");
        $view->method('getActCatId')->willReturn(false);
        $view->method('getEditLang')->willReturn(0);

        $this->assertSame("ArticleMainUri", $view->getEntryUri());
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
     * Article_Seo::getEncoder() test case
     */
    public function testGetEncoder()
    {
        $oView = oxNew('Article_Seo');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\SeoEncoderArticle::class, $oView->getEncoder());
    }


    /**
     * Article_Seo::Render() test case
     */
    public function testRender()
    {
        $oView = oxNew('Article_Seo');
        $this->assertSame("object_seo", $oView->render());
    }

    /**
     * Article_Seo::getVendorList() test case (regular)
     */
    public function testGetVendorList()
    {
        $oVendor = oxNew('oxVendor');
        $oVendor->setId("_test1");
        $oVendor->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxvendorid = new oxField("_test1");

        $oView = oxNew('Article_Seo');
        $aList = $oView->getVendorList($oArticle);

        $this->assertTrue(is_array($aList));

        $oArtVendor = reset($aList);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Vendor::class, $oArtVendor);
        $this->assertEquals($oVendor->getId(), $oArtVendor->getId());
    }

    /**
     * Article_Seo::getManufacturerList() test case (regular)
     */
    public function testGetManufacturerList()
    {
        $oManufacturer = oxNew('oxManufacturer');
        $oManufacturer->setId("_test1");
        $oManufacturer->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->oxarticles__oxmanufacturerid = new oxField("_test1");

        $oView = oxNew('Article_Seo');
        $aList = $oView->getManufacturerList($oArticle);

        $this->assertTrue(is_array($aList));

        $oArtManufacturer = reset($aList);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Manufacturer::class, $oArtManufacturer);
        $this->assertEquals($oManufacturer->getId(), $oArtManufacturer->getId());
    }


    /**
     * Article_Seo::getActCategory() test case (category)
     */
    public function testGetActCategory()
    {
        oxTestModules::addFunction('oxcategory', 'load', '{ return true; }');

        $oView = oxNew('Article_Seo');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Category::class, $oView->getActCategory());
    }

    /**
     * Article_Seo::getActVendor() test case (manufacturer)
     */
    public function testGetActVendor()
    {
        oxTestModules::addFunction('oxvendor', 'load', '{ return true; }');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatType"]);
        $oView->method('getActCatType')->willReturn("oxvendor");
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Vendor::class, $oView->getActVendor());
    }

    /**
     * Article_Seo::getActManufacturer() test case (manufacturer)
     */
    public function testGetActManufacturer()
    {
        oxTestModules::addFunction('oxmanufacturer', 'load', '{ return true; }');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatType"]);
        $oView->method('getActCatType')->willReturn("oxmanufacturer");
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Manufacturer::class, $oView->getActManufacturer());
    }

    /**
     * Test, that the method 'getListType' returns null, if the active category type is oxany.
     */
    public function testGetListTypeCaseAny()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatType"]);
        $oView->method('getActCatType')->willReturn("oxany");
        $this->assertNull($oView->getListType());
    }

    /**
     * Test, that the method 'getListType' returns vendor, if the active category type is oxvendor.
     */
    public function testGetListTypeCaseVendor()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatType"]);
        $oView->method('getActCatType')->willReturn("oxvendor");
        $this->assertSame("vendor", $oView->getListType());
    }

    /**
     * Test, that the method 'getListType' returns manufacturer, if the active category type is oxmanufacturer.
     */
    public function testGetListTypeCaseManufacturer()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatType"]);
        $oView->method('getActCatType')->willReturn("oxmanufacturer");
        $this->assertSame("manufacturer", $oView->getListType());
    }


    /**
     * Article_Seo::getAltSeoEntryId() test case
     */
    public function testGetAltSeoEntryId()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getEditObjectId"]);
        $oView->expects($this->once())->method('getEditObjectId')->willReturn(999);
        $this->assertSame(999, $oView->getAltSeoEntryId());
    }

    /**
     * Article_Seo::getEditLang() test case
     */
    public function testGetEditLang()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatLang"]);
        $oView->expects($this->once())->method('getActCatLang')->willReturn(999);
        $this->assertSame(999, $oView->getEditLang());
    }

    /**
     * Article_Seo::_getSeoEntryType() test case (default)
     */
    public function testGetSeoEntryType()
    {
        $view = oxNew("Article_Seo");

        $this->assertSame('oxarticle', $view->getSeoEntryType());
    }

    /**
     * Article_Seo::getType() test case (manufacturer)
     */
    public function testGetType()
    {
        $oView = oxNew('Article_Seo');
        $this->assertSame('oxarticle', $oView->getType());
    }

    /**
     * Article_Seo::getActCatType() test case
     */
    public function testGetActCatType()
    {
        $this->setRequestParameter("aSeoData", null);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getSelectionList"]);
        $oView->expects($this->once())->method("getSelectionList")->willReturn(["type" => [999 => "value"]]);
        $this->assertSame("type", $oView->getActCatType());

        $this->setRequestParameter("aSeoData", ["oxparams" => "type#value#999"]);
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertSame("type", $oView->getActCatType());
    }

    /**
     * Article_Seo::getActCatLang() test case
     */
    public function testGetActCatLang()
    {
        $this->setRequestParameter("aSeoData", null);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getSelectionList"]);
        $oView->expects($this->once())->method("getSelectionList")->willReturn(["type" => [999 => "value"]]);
        $this->assertSame(999, $oView->getActCatLang());

        $this->setRequestParameter("aSeoData", ["oxparams" => "type#value#999"]);
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertSame(999, $oView->getActCatLang());
    }

    /**
     * Article_Seo::getActCatId() test case
     */
    public function testGetActCatId()
    {
        $this->setRequestParameter("aSeoData", null);

        $oItem = $this->getMock(\OxidEsales\Eshop\Application\Model\Manufacturer::class, ["getId"]);
        $oItem->expects($this->once())->method("getId")->willReturn("value");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getSelectionList", "getActCatType", "getActCatLang"]);
        $oView->expects($this->once())->method("getSelectionList")->willReturn(["type" => [999 => [$oItem]]]);
        $oView->expects($this->once())->method("getActCatType")->willReturn("type");
        $oView->expects($this->once())->method("getActCatLang")->willReturn(999);
        $this->assertSame("value", $oView->getActCatId());

        $this->setRequestParameter("aSeoData", ["oxparams" => "type#value#999"]);
        $oView->expects($this->never())->method("getSelectionList");
        $this->assertSame("value", $oView->getActCatId());
    }

    /**
     * Article_Seo::getQuery() test case
     */
    public function testGetCategoryList()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');
        $sQ = "select oxarticles.oxid from oxarticles left join {$sO2CView} on
               oxarticles.oxid={$sO2CView}.oxobjectid where
               oxarticles.oxactive='1' and {$sO2CView}.oxobjectid is not null";

        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $sProdId = $oDb->getOne($sQ);

        // must be existing
        $this->assertTrue((bool) $sProdId);

        $oProduct = oxNew('oxArticle');
        $oProduct->load($sProdId);

        $sQ = sprintf('select oxobject2category.oxcatnid as oxid from %s as oxobject2category where oxobject2category.oxobjectid=', $sO2CView)
              . $oDb->quote($oProduct->getId()) . " union " . $oProduct->getSqlForPriceCategories('oxid');

        $sQ = sprintf('select count(*) from ( %s ) as _tmp', $sQ);

        $iCount = $oDb->getOne($sQ);

        $oView = oxNew('Article_Seo');
        $aList = $oView->getCategoryList($oProduct);

        // must be have few assignments
        $this->assertGreaterThan(0, $iCount);
        $this->assertCount($iCount, $aList);
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

        $view = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getEditObjectId", "getCategoryList", "getVendorList", "getManufacturerList", "getTagList"]);
        $view->method("getEditObjectId")->willReturn($productId);
        $view->method("getCategoryList")->willReturn("CategoryList");
        $view->method("getVendorList")->willReturn("VendorList");
        $view->method("getManufacturerList")->willReturn("ManufacturerList");
        $view->method("getTagList")->willReturn("TagList");

        $expectedList = [];
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getActCatId"]);

        $oView->expects($this->once())->method("getActCatId")->willReturn("testParam2");

        $this->assertSame("testParam2", $oView->processParam("testParam1#testParam2#0"));
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSeo::class, ["getSaveObjectId", "getActCatId", "getEditLang", "processParam"]);
        $oView
            ->method('getSaveObjectId')
            ->willReturnOnConsecutiveCalls('objectid', 'nonexistingobjectid');

        $oView->method('getEditLang')->willReturn(0);
        $oView->method('getActCatId')->willReturn("catid");
        $oView->method('processParam')->willReturn("catid");

        $this->assertTrue($oView->isEntryFixed());
        $this->assertFalse($oView->isEntryFixed());
    }

    /**
     * @return string The product id.
     */
    protected function ensureProductIdExists()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $objectToCategoryViewName = $tableViewNameGenerator->getViewName('oxobject2category');
        $query = "select oxarticles.oxid from oxarticles left join {$objectToCategoryViewName} on
               oxarticles.oxid={$objectToCategoryViewName}.oxobjectid where
               oxarticles.oxactive='1' and {$objectToCategoryViewName}.oxobjectid is not null";

        $produdtId = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($query);

        // must be existing
        $this->assertTrue((bool) $produdtId);

        return $produdtId;
    }
}
