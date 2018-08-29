<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Tests for Attribute_Category_Ajax class
 */
class VendorMainAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $sShopId = $this->getShopId();

        $this->addToDatabase("insert into oxarticles set oxid='_testArticle1', oxshopid='{$sShopId}', oxtitle='testArticle1', oxvendorid='_testVendorId'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle2', oxshopid='{$sShopId}', oxtitle='testArticle2', oxvendorid='_testVendorId'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle3', oxshopid='{$sShopId}', oxtitle='testArticle3', oxvendorid=''", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle4', oxshopid='{$sShopId}', oxtitle='testArticle4', oxvendorid=''", 'oxarticles');

        $this->addToDatabase("insert into oxobject2category set oxid='_testOxid1', oxobjectid='_testArticle1', oxcatnid='_testCat1'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testOxid2', oxobjectid='_testArticle1', oxcatnid='_testCat2'", 'oxobject2category');

        $this->addTeardownSql("delete from oxobject2category where oxid like '\_test%'");
        $this->addTeardownSql("delete from oxarticles where oxid like '\_test%'");
    }

    public function getArticleViewTable()
    {
        return $this->getConfig()->getEdition() === 'EE' ? 'oxv_oxarticles_1_de' : 'oxv_oxarticles_de';
    }

    public function getObject2CategoryViewTable()
    {
        return $this->getConfig()->getEdition() === 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }

    public function getShopId()
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery_selectingMainArticles()
    {
        $this->setConfigParam("blVariantsSelection", false);
        $this->setRequestParameter("synchoxid", "_testSyncOxId");

        $oView = oxNew('vendor_main_ajax');
        $sQuery = 'from ' . $this->getArticleViewTable() . ' where ' . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopId() . '" and 1 ';
        $sQuery .= "and " . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxvendorid != '_testSyncOxId'";
        $sQuery = trim(preg_replace("/\s+/", " ", $sQuery));
        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim($oView->UNITgetQuery())));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery_selectingVariants()
    {
        $this->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('vendor_main_ajax');
        $sQuery = 'from ' . $this->getArticleViewTable() . ' where ' . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopId() . '" and 1';
        $sQuery = preg_replace("/\s+/", " ", $sQuery);
        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim($oView->UNITgetQuery())));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
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
        $sQuery = trim(preg_replace("/\s+/", " ", $sQuery));

        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim($oView->UNITgetQuery())));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
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
        $sQuery = trim(preg_replace("/\s+/", " ", $sQuery));

        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim($oView->UNITgetQuery())));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery_OxId_EqalTo_SyncId_variantsOff()
    {
        $this->setConfigParam("blVariantsSelection", false);
        $this->setRequestParameter("oxid", "_testVendorId");
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = oxNew('vendor_main_ajax');

        $sQuery = "from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxvendorid = '_testVendorId' ";
        $sQuery .= "and " . $this->getArticleViewTable() . ".oxparentid = ''";
        $sQuery = trim(preg_replace("/\s+/", " ", $sQuery));

        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim($oView->UNITgetQuery())));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery_OxId_EqalTo_SyncId_variantsOn()
    {
        $this->setConfigParam("blVariantsSelection", true);
        $this->setRequestParameter("oxid", "_testVendorId");
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = oxNew('vendor_main_ajax');

        $sQuery = "from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxvendorid = '_testVendorId' ";
        $sQuery = trim(preg_replace("/\s+/", " ", $sQuery));

        $this->assertEquals($sQuery, preg_replace("/\s+/", " ", trim($oView->UNITgetQuery())));
    }


    /**
     * AttributeMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $oView = oxNew('vendor_main_ajax');
        $this->assertEquals("", trim($oView->UNITaddFilter('')));
    }

    /**
     * AttributeMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter_VariantsOff()
    {
        $this->setConfigParam("blVariantsSelection", false);

        $oView = oxNew('vendor_main_ajax');
        $this->assertEquals("select * from oxarticles", trim($oView->UNITaddFilter('select * from oxarticles')));
    }

    /**
     * AttributeMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter_VariantsOn()
    {
        $this->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('vendor_main_ajax');
        $this->assertEquals("select * from oxarticles group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter('select * from oxarticles')));
    }

    /**
     * AttributeMainAjax::removeVendor() test case
     *
     * @return null
     */
    public function testRemoveVendor_oneArticle()
    {
        $this->setRequestParameter("oxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle1')));

        $oDb = oxDb::getDb();
        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));

        $oView->removeVendor();
        $this->assertEquals(1, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));
        $this->assertEquals("_testArticle2", $oDb->getOne("select oxid from oxarticles where oxvendorid='_testVendorId' "));
    }

    /**
     * AttributeMainAjax::removeVendor() test case
     *
     * @return null
     */
    public function testRemoveVendor_allArticles()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("oxid", "_testVendorId");

        $oDb = oxDb::getDb();
        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));

        $oView = oxNew("vendor_main_ajax");
        $oView->removeVendor();

        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxarticles where oxvendorid='_testVendorId' "));
    }

    /**
     * AttributeMainAjax::removeVendor() test case
     *
     * @return null
     */
    public function testRemoveVendor_resetingCounter()
    {
        $this->setRequestParameter("oxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, array("_getActionIds", "resetCounter"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle1')));
        $oView->expects($this->any())->method('resetCounter')->with($this->equalTo("vendorArticle"), $this->equalTo("_testVendorId"));

        $oView->removeVendor();
    }

    /**
     * AttributeMainAjax::addVendor() test case
     *
     * @return null
     */
    public function testAddVendor_oneArticle()
    {
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle3')));

        $oDb = oxDb::getDb();
        $this->assertEquals("", $oDb->getOne("select oxvendorid from oxarticles where oxid='_testArticle3' "));

        $oView->addVendor();
        $this->assertEquals("_testVendorId", $oDb->getOne("select oxvendorid from oxarticles where oxid='_testArticle3' "));
    }

    /**
     * AttributeMainAjax::addVendor() test case
     *
     * @return null
     */
    public function testAddVendor_allArticles()
    {
        $this->setRequestParameter("synchoxid", "_testVendorId");
        $this->setRequestParameter("all", true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, array("_getQuery"));
        $oView->expects($this->once())->method('_getQuery')->will($this->returnValue("from {$this->getArticleViewTable()} where oxid like '\_test%' and oxvendorid='' "));

        $oDb = oxDb::getDb();
        $this->assertEquals("2", $oDb->getOne("select count(oxid) from oxarticles where oxid like '\_test%' and oxvendorid='_testVendorId' "));
        $this->assertEquals("2", $oDb->getOne("select count(oxid) from oxarticles where oxid like '\_test%' and oxvendorid='' "));

        $oView->addVendor();
        $this->assertEquals("4", $oDb->getOne("select count(oxid) from oxarticles where oxid like '\_test%' and oxvendorid='_testVendorId' "));
    }

    /**
     * AttributeMainAjax::addVendor() test case
     *
     * @return null
     */
    public function testAddVendor_resetingCounter()
    {
        $this->setRequestParameter("synchoxid", "_testVendorId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VendorMainAjax::class, array("_getActionIds", "resetCounter"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle3')));
        $oView->expects($this->any())->method('resetCounter')->with($this->equalTo("vendorArticle"), $this->equalTo("_testVendorId"));

        $oView->addVendor();
    }
}
