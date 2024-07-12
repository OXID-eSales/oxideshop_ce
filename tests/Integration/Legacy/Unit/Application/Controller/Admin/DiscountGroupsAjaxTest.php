<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Tests for Discount_Groups_Ajax class
 */
class DiscountGroupsAjaxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = 'oxidsmallcust', oxtype = 'oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = 'oxidmiddlecust', oxtype = 'oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = 'oxidgoodcust', oxtype = 'oxgroups'");
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
     * DiscountGroupsAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sGroupTable = $tableViewNameGenerator->getViewName("oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $sQuery = sprintf('from %s where 1', $sGroupTable);
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountGroupsAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sGroupTable = $tableViewNameGenerator->getViewName("oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $sQuery = sprintf('from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sGroupTable, $sGroupTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxgroups'  and";
        $sQuery .= sprintf(' %s.oxid not in ( select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sGroupTable, $sGroupTable, $sGroupTable, $sGroupTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxgroups' )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountGroupsAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sGroupTable = $tableViewNameGenerator->getViewName("oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $sQuery = sprintf('from %s where 1  and', $sGroupTable);
        $sQuery .= sprintf(' %s.oxid not in ( select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sGroupTable, $sGroupTable, $sGroupTable, $sGroupTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxgroups' )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountGroupsAjax::removeDiscGroup() test case
     */
    public function testRemoveDiscGroup()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountGroupsAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testO2DRemove1', '_testO2DRemove2']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscGroup();
        $this->assertSame(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountGroupsAjax::removeDiscGroup() test case
     */
    public function testRemoveDiscGroupAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_groups_ajax');
        $oView->removeDiscGroup();
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountGroupsAjax::addDiscGroup() test case
     */
    public function testAddDiscGroup()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountGroupsAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['oxidmiddlecust', 'oxidgoodcust']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscGroup();
        $this->assertSame(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountGroupsAjax::addDiscGroup() test case
     */
    public function testAddDiscGroupAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxgroups");

        $oView = oxNew('discount_groups_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertSame(0, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));

        $oView->addDiscGroup();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));
    }
}
