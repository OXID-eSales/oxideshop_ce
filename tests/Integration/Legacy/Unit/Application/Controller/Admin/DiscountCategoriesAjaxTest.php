<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Tests for Discount_Categories_Ajax class
 */
class DiscountCategoriesAjaxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->addToDatabase("insert into oxcategories set oxid='_testObjectRemove1', oxtitle='_testCat1', oxshopid='1'", 'oxcategories');
        $this->addToDatabase("insert into oxcategories set oxid='_testObjectRemove2', oxtitle='_testCat2', oxshopid='1'", 'oxcategories');
        $this->addToDatabase("insert into oxcategories set oxid='_testObjectRemove3', oxtitle='_testCat3', oxshopid='1'", 'oxcategories');

        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove1', oxtype = 'oxcategories'", 'oxobject2discount');
        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove2', oxtype = 'oxcategories'", 'oxobject2discount');
        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove3', oxtype = 'oxcategories'", 'oxobject2discount');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxobject2discount');
        $this->cleanUpTable('oxcategories');

        parent::tearDown();
    }

    /**
     * DiscountCategoriesAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCategoryTable = $tableViewNameGenerator->getViewName("oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $sQuery = 'from ' . $sCategoryTable;
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountCategoriesAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCategoryTable = $tableViewNameGenerator->getViewName("oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $sQuery = sprintf('from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sCategoryTable, $sCategoryTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxcategories'  and ";
        $sQuery .= sprintf(' %s.oxid not in (  select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sCategoryTable, $sCategoryTable, $sCategoryTable, $sCategoryTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcategories'  )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountCategoriesAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sCategoryTable = $tableViewNameGenerator->getViewName("oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $sQuery = sprintf('from %s where ', $sCategoryTable);
        $sQuery .= sprintf(' %s.oxid not in (  select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sCategoryTable, $sCategoryTable, $sCategoryTable, $sCategoryTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcategories'  )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountArticlesAjax::removeDiscCat() test case
     */
    public function testRemoveDiscCat()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountCategoriesAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testO2DRemove1', '_testO2DRemove2']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscCat();
        $this->assertSame(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     */
    public function testRemoveDiscCatAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_categories_ajax');
        $oView->removeDiscCat();
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::addDiscCat() test case
     */
    public function testAddDiscCat()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountCategoriesAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testCatAdd1', '_testCatAdd1']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscCat();
        $this->assertSame(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::addDiscCat() test case
     */
    public function testAddDiscCatAll()
    {
        $this->cleanUpTable('oxobject2discount', 'oxdiscountid');
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertSame(0, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));

        $oView->addDiscCat();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));
    }
}
