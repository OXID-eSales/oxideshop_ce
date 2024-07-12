<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\Facts\Facts;

/**
 * Tests for Attribute_Category_Ajax class
 */
class VendorMainAjaxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $sShopId = $this->getShopId();

        $this->addToDatabase(sprintf("insert into oxarticles set oxid='_testArticle1', oxshopid='%s', oxtitle='testArticle1', oxvendorid='_testVendorId'", $sShopId), 'oxarticles');
        $this->addToDatabase(sprintf("insert into oxarticles set oxid='_testArticle2', oxshopid='%s', oxtitle='testArticle2', oxvendorid='_testVendorId'", $sShopId), 'oxarticles');
        $this->addToDatabase(sprintf("insert into oxarticles set oxid='_testArticle3', oxshopid='%s', oxtitle='testArticle3', oxvendorid=''", $sShopId), 'oxarticles');
        $this->addToDatabase(sprintf("insert into oxarticles set oxid='_testArticle4', oxshopid='%s', oxtitle='testArticle4', oxvendorid=''", $sShopId), 'oxarticles');

        $this->addToDatabase("insert into oxobject2category set oxid='_testOxid1', oxobjectid='_testArticle1', oxcatnid='_testCat1'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testOxid2', oxobjectid='_testArticle1', oxcatnid='_testCat2'", 'oxobject2category');

        $this->addTeardownSql("delete from oxobject2category where oxid like '\_test%'");
        $this->addTeardownSql("delete from oxarticles where oxid like '\_test%'");
    }

    public function getArticleViewTable()
    {
        return (new Facts())->getEdition() === 'EE' ? 'oxv_oxarticles_1_de' : 'oxv_oxarticles_de';
    }

    public function getObject2CategoryViewTable()
    {
        return (new Facts())->getEdition() === 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }

    public function getShopId()
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testGetQuery_selectingMainArticles()
    {
        $this->setConfigParam("blVariantsSelection", false);
        $this->setRequestParameter("synchoxid", "_testSyncOxId");

        $oView = oxNew('vendor_main_ajax');
        $sQuery = 'from ' . $this->getArticleViewTable() . ' where ' . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopId() . '" and 1 ';
        $sQuery .= "and " . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxvendorid != '_testSyncOxId'";
        $sQuery = trim((string) preg_replace("/\s+/", " ", $sQuery));
        $this->assertSame($sQuery, preg_replace("/\s+/", " ", trim((string) $oView->getQuery())));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testGetQuery_selectingVariants()
    {
        $this->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('vendor_main_ajax');
        $sQuery = 'from ' . $this->getArticleViewTable() . ' where ' . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopId() . '" and 1';
        $sQuery = preg_replace("/\s+/", " ", $sQuery);
        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim((string) $oView->getQuery())));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testGetQuery_OxId_variantsOff()
    {
        $this->setConfigParam("blVariantsSelection", false);
        $this->setRequestParameter("oxid", "_testVendorId");
        $this->setRequestParameter("synchoxid", "_testSyncOxId");

        $oView = oxNew('vendor_main_ajax');

        $sQuery = "from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on ";
        $sQuery .= $this->getArticleViewTable() . ".oxid = " . $this->getObject2CategoryViewTable() . ".oxobjectid ";
        $sQuery .= 'where ' . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopId() . '" and ' . $this->getObject2CategoryViewTable() . ".oxcatnid = '_testVendorId' and " . $this->getArticleViewTable() . ".oxvendorid != '_testSyncOxId' ";
        $sQuery .= "and " . $this->getArticleViewTable() . ".oxparentid = '' ";
        $sQuery = trim((string) preg_replace("/\s+/", " ", $sQuery));

        $this->assertSame($sQuery, preg_replace("/\s+/", " ", trim((string) $oView->getQuery())));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testGetQuery_OxId_variantsOn()
    {
        $this->setConfigParam("blVariantsSelection", true);
        $this->setRequestParameter("oxid", "_testVendorId");
        $this->setRequestParameter("synchoxid", "_testSyncOxId");

        $oView = oxNew('vendor_main_ajax');

        $sQuery = "from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on ";
        $sQuery .= "( " . $this->getArticleViewTable() . ".oxid = " . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticleViewTable() . ".oxparentid = oxobject2category.oxobjectid )";
        $sQuery .= 'where ' . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopId() . '" and ' . $this->getObject2CategoryViewTable() . ".oxcatnid = '_testVendorId' and " . $this->getArticleViewTable() . ".oxvendorid != '_testSyncOxId' ";
        $sQuery = trim((string) preg_replace("/\s+/", " ", $sQuery));

        $this->assertSame($sQuery, preg_replace("/\s+/", " ", trim((string) $oView->getQuery())));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testGetQuery_OxId_EqalTo_SyncId_variantsOff()
    {
        $this->setConfigParam("blVariantsSelection", false);
        $this->setRequestParameter("oxid", "_testVendorId");
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = oxNew('vendor_main_ajax');

        $sQuery = "from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxvendorid = '_testVendorId' ";
        $sQuery .= "and " . $this->getArticleViewTable() . ".oxparentid = ''";
        $sQuery = trim((string) preg_replace("/\s+/", " ", $sQuery));

        $this->assertSame($sQuery, preg_replace("/\s+/", " ", trim((string) $oView->getQuery())));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testGetQuery_OxId_EqalTo_SyncId_variantsOn()
    {
        $this->setConfigParam("blVariantsSelection", true);
        $this->setRequestParameter("oxid", "_testVendorId");
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = oxNew('vendor_main_ajax');

        $sQuery = "from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxvendorid = '_testVendorId' ";
        $sQuery = trim((string) preg_replace("/\s+/", " ", $sQuery));

        $this->assertSame($sQuery, preg_replace("/\s+/", " ", trim((string) $oView->getQuery())));
    }


    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testAddFilter()
    {
        $oView = oxNew('vendor_main_ajax');
        $this->assertSame("", trim((string) $oView->addFilter('')));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testAddFilter_VariantsOff()
    {
        $this->setConfigParam("blVariantsSelection", false);

        $oView = oxNew('vendor_main_ajax');
        $this->assertSame("select * from oxarticles", trim((string) $oView->addFilter('select * from oxarticles')));
    }

    /**
     * AttributeMainAjax::getQuery() test case
     */
    public function testAddFilter_VariantsOn()
    {
        $this->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('vendor_main_ajax');
        $this->assertSame("select * from oxarticles group by " . $this->getArticleViewTable() . ".oxid", trim((string) $oView->addFilter('select * from oxarticles')));
    }

    /**
     * AttributeMainAjax::removeVendor() test case
     */
    public function testRemoveVendor_oneArticle()
    {
        $this->setRequestParameter("oxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testArticle1']);

        $oDb = oxDb::getDb();
        $this->assertSame(2, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));

        $oView->removeVendor();
        $this->assertSame(1, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));
        $this->assertSame("_testArticle2", $oDb->getOne("select oxid from oxarticles where oxvendorid='_testVendorId' "));
    }

    /**
     * AttributeMainAjax::removeVendor() test case
     */
    public function testRemoveVendor_allArticles()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("oxid", "_testVendorId");

        $oDb = oxDb::getDb();
        $this->assertSame(2, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));

        $oView = oxNew("vendor_main_ajax");
        $oView->removeVendor();

        $this->assertSame(0, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));
    }

    /**
     * AttributeMainAjax::removeVendor() test case
     */
    public function testRemoveVendor_resetingCounter()
    {
        $this->setRequestParameter("oxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, ["getActionIds", "resetCounter"]);
        $oView->method('getActionIds')->willReturn(['_testArticle1']);
        $oView->method('resetCounter')->with("vendorArticle", "_testVendorId");

        $oView->removeVendor();
    }

    /**
     * AttributeMainAjax::addVendor() test case
     */
    public function testAddVendor_oneArticle()
    {
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testArticle3']);

        $oDb = oxDb::getDb();
        $this->assertSame("", $oDb->getOne("select oxvendorid from oxarticles where oxid='_testArticle3' "));

        $oView->addVendor();
        $this->assertSame("_testVendorId", $oDb->getOne("select oxvendorid from oxarticles where oxid='_testArticle3' "));
    }

    /**
     * AttributeMainAjax::addVendor() test case
     */
    public function testAddVendor_allArticles()
    {
        $this->setRequestParameter("synchoxid", "_testVendorId");
        $this->setRequestParameter("all", true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, ["getQuery"]);
        $oView->expects($this->once())->method('getQuery')->willReturn(sprintf('from %s where oxid like \'\_test%%\' and oxvendorid=\'\' ', $this->getArticleViewTable()));

        $oDb = oxDb::getDb();
        $this->assertSame("2", $oDb->getOne("select count(oxid) from oxarticles where oxid like '\_test%' and oxvendorid='_testVendorId' "));
        $this->assertSame("2", $oDb->getOne("select count(oxid) from oxarticles where oxid like '\_test%' and oxvendorid='' "));

        $oView->addVendor();
        $this->assertSame("4", $oDb->getOne("select count(oxid) from oxarticles where oxid like '\_test%' and oxvendorid='_testVendorId' "));
    }

    /**
     * AttributeMainAjax::addVendor() test case
     */
    public function testAddVendor_resetingCounter()
    {
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, ["getActionIds", "resetCounter"]);
        $oView->method('getActionIds')->willReturn(['_testArticle3']);
        $oView->method('resetCounter')->with("vendorArticle", "_testVendorId");

        $oView->addVendor();
    }
}
