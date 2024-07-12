<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Tests for Discount_Main_Ajax class
 */
class DiscountMainAjaxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = 'a7c40f631fc920687.20179984', oxtype = 'oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = 'a7c40f6320aeb2ec2.72885259', oxtype = 'oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = 'a7c40f6321c6f6109.43859248', oxtype = 'oxcountry'");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxobject2discount where oxdiscountid like '_test%'");

        parent::tearDown();
    }

    /**
     * DiscountMainAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName("oxcountry");

        $oView = oxNew('discount_main_ajax');
        $sQuery = sprintf("from %s where %s.oxactive = '1'", $sTable, $sTable);
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountMainAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName("oxcountry");

        $oView = oxNew('discount_main_ajax');
        $sQuery = sprintf('from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid', $sTable, $sTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxcountry'";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sTable = $tableViewNameGenerator->getViewName("oxcountry");

        $oView = oxNew('discount_main_ajax');
        $sQuery = sprintf("from %s where %s.oxactive = '1' and", $sTable, $sTable);
        $sQuery .= sprintf(' %s.oxid not in ( select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid', $sTable, $sTable, $sTable, $sTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcountry' )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountMainAjax::removeDiscCountry() test case
     */
    public function testRemoveDiscCountry()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testO2DRemove1', '_testO2DRemove2']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscCountry();
        $this->assertSame(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountMainAjax::removeDiscCountry() test case
     */
    public function testRemoveDiscCountryAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_main_ajax');
        $oView->removeDiscCountry();
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountMainAjax::addDiscCountry() test case
     */
    public function testAddDiscCountry()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['oxidmiddlecust', 'oxidgoodcust']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscCountry();
        $this->assertSame(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountMainAjax::addDiscCountry() test case
     */
    public function testAddDiscCountryAll()
    {
        $sSynchoxid = 'testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxcountry where oxactive='1'");

        $oView = oxNew('discount_main_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertSame(0, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));

        $oView->addDiscCountry();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));
    }
}
