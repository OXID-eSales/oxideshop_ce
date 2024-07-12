<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Tests for Manufacturer_Main_Ajax class
 */
class ManufacturerMainAjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->addToDatabase("insert into oxarticles set oxid='_testArticle1', oxtitle='_testArticle1', oxmanufacturerid='_testRemove1'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle2', oxtitle='_testArticle2', oxmanufacturerid='_testRemove2'", 'oxarticles');

        $this->addToDatabase("insert into oxarticles set oxid='_testArticle3', oxtitle='_testArticle3', oxmanufacturerid='_testRemoveAll'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle4', oxtitle='_testArticle4', oxmanufacturerid='_testRemoveAll'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle5', oxtitle='_testArticle5', oxmanufacturerid='_testRemoveAll'", 'oxarticles');

        $this->addToDatabase("insert into oxmanufacturers set oxid='_testManufacturer1', oxtitle='_testManufacturer1'", 'oxmanufacturers');
        $this->addToDatabase("insert into oxmanufacturers set oxid='_testManufacturer2', oxtitle='_testManufacturer2'", 'oxmanufacturers');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle1'");
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle2'");

        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle3'");
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle4'");
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle5'");

        oxDb::getDb()->execute("delete from oxmanufacturers where oxid='_testManufacturer1'");
        oxDb::getDb()->execute("delete from oxmanufacturers where oxid='_testManufacturer2'");

        parent::tearDown();
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopIdTest() . '" and 1  and ' . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxmanufacturerid != ''", trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopIdTest() . '" and 1', trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopIdTest() . '" and 1  and ' . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "'", trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopIdTest() . '" and 1', trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxmanufacturerid = '" . $sOxid . "' and " . $this->getArticleViewTable() . ".oxparentid = ''", trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid = " . $this->getObject2CategoryViewTable() . ".oxobjectid where " . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopIdTest() . '" and ' . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "' and " . $this->getArticleViewTable() . ".oxparentid = ''", trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxidVariantsSelection()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  ( " . $this->getArticleViewTable() . ".oxid = " . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticleViewTable() . ".oxparentid = " . $this->getObject2CategoryViewTable() . ".oxobjectid )where " . $this->getArticleViewTable() . '.oxshopid="' . $this->getShopIdTest() . '" and ' . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "'", trim((string) $oView->getQuery()));
    }

    /**
     * ManufacturerMainAjax::_addFilter() test case
     */
    public function testAddFilter()
    {
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("", trim((string) $oView->addFilter('')));
    }

    /**
     * ManufacturerMainAjax::_addFilter() test case
     */
    public function testAddFilterVariantsSelection()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("group by " . $this->getArticleViewTable() . ".oxid", trim((string) $oView->addFilter('')));
    }

    /**
     * ManufacturerMainAjax::_addFilter() test case
     */
    public function testAddFilterVariantsSelection2()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('Manufacturer_Main_Ajax');
        $this->assertSame("select count( * ) group by " . $this->getArticleViewTable() . ".oxid", trim((string) $oView->addFilter('select count( * )')));
    }

    /**
     * ManufacturerMainAjax::removeManufacturer() test case
     */
    public function testRemoveManufacturer()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerMainAjax::class, ["getActionIds", "resetCounter"]);
        $oView->method('getActionIds')->willReturn(['_testArticle1', '_testArticle2']);
        $oView->expects($this->once())->method('resetCounter')->with("manufacturerArticle");
        $this->assertSame(2, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid in('_testRemove1', '_testRemove2')"));
        $oView->removeManufacturer();
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid in('_testRemove1', '_testRemove2')"));
    }

    /**
     * ManufacturerMainAjax::removeManufacturer() test case
     */
    public function testRemoveManufacturerAll()
    {
        $sOxid = '_testRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '_testRemoveAll'"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerMainAjax::class, ["resetCounter"]);
        $oView->expects($this->once())->method('resetCounter')->with("manufacturerArticle", $sOxid);
        $oView->removeManufacturer();
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '_testRemoveAll'"));
    }

    /**
     * ManufacturerMainAjax::addManufacturer() test case
     */
    public function testAddManufacturer()
    {
        $sSynchoxid = '_testAddManufacturer';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerMainAjax::class, ["getActionIds", "resetCounter"]);
        $oView->method('getActionIds')->willReturn(['_testArticle1', '_testArticle2']);
        $oView->expects($this->once())->method('resetCounter')->with("manufacturerArticle");

        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));
        $oView->addManufacturer();
        $this->assertSame(2, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));
    }

    /**
     * ManufacturerMainAjax::addManufacturer() test case
     */
    public function testAddManufacturerAll()
    {
        $sSynchoxid = '_testAddManufacturerAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne(" select count(oxid) from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxshopid='" . $this->getShopIdTest() . "' and 1  and " . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "'");
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ManufacturerMainAjax::class, ["resetCounter"]);
        $oView->expects($this->once())->method('resetCounter')->with("manufacturerArticle", $sSynchoxid);
        $oView->addManufacturer();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));
    }

    /**
     * Returns oxarticle view table name.
     *
     * @return string
     */
    protected function getArticleViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxarticles_1_de' : 'oxv_oxarticles_de';
    }

    /**
     * Returns oxobject2category view table name.
     *
     * @return string
     */
    protected function getObject2CategoryViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }

    /**
     * Returns shop id.
     *
     * @return string
     */
    protected function getShopIdTest()
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }
}
