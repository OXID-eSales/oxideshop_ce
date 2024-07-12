<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Tests for Discount_Users_Ajax class
 */
class DiscountUsersAjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $shopId = ShopIdCalculator::BASE_SHOP_ID;
        oxDb::getDb()->execute(sprintf("insert into oxuser set oxid='_testUser1', oxusername='_testUserName1', oxshopid='%d'", $shopId));
        oxDb::getDb()->execute(sprintf("insert into oxuser set oxid='_testUser2', oxusername='_testUserName2', oxshopid='%d'", $shopId));
        oxDb::getDb()->execute(sprintf("insert into oxuser set oxid='_testUser3', oxusername='_testUserName3', oxshopid='%d'", $shopId));

        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testUser1', oxtype = 'oxuser'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testUser2', oxtype = 'oxuser'");
        oxDb::getDb()->execute("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testUser3', oxtype = 'oxuser'");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxobject2discount where oxdiscountid like '_test%'");
        oxDb::getDb()->execute("delete from oxuser where oxid like '_test%'");

        parent::tearDown();
    }

    /**
     * DiscountUsersAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sUserTable = $tableViewNameGenerator->getViewName("oxuser");

        $oView = oxNew('discount_users_ajax');
        $sQuery = sprintf("from %s where 1  and oxshopid = '", $sUserTable) . $this->getShopId() . "'";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountUsersAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sUserTable = $tableViewNameGenerator->getViewName("oxuser");

        $oView = oxNew('discount_users_ajax');
        $sQuery = sprintf('from oxobject2group left join %s on %s.oxid = oxobject2group.oxobjectid', $sUserTable, $sUserTable);
        $sQuery .= sprintf(" where oxobject2group.oxgroupsid = '_testOxid' and %s.oxshopid = '", $sUserTable) . $this->getShopId() . "'  and";
        $sQuery .= sprintf(' %s.oxid not in ( select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sUserTable, $sUserTable, $sUserTable, $sUserTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxuser' )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountUsersAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sUserTable = $tableViewNameGenerator->getViewName("oxuser");

        $oView = oxNew('discount_users_ajax');
        $sQuery = sprintf("from %s where 1  and oxshopid = '", $sUserTable) . $this->getShopId() . "'  and";
        $sQuery .= sprintf(' %s.oxid not in ( select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sUserTable, $sUserTable, $sUserTable, $sUserTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxuser' )";
        $this->assertSame($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountUsersAjax::removeDiscUser() test case
     */
    public function testRemoveDiscUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountUsersAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testO2DRemove1', '_testO2DRemove2']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscUser();
        $this->assertSame(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountUsersAjax::removeDiscUser() test case
     */
    public function testRemoveDiscUserAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_users_ajax');
        $oView->removeDiscUser();
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountUsersAjax::addDiscUser() test case
     */
    public function testAddDiscUser()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountUsersAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testNewUser1', '_testNewUser2']);
        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscUser();
        $this->assertSame(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountUsersAjax::addDiscUser() test case
     */
    public function testAddDiscUserAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxuser");

        $oView = oxNew('discount_users_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertSame(0, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));

        $oView->addDiscUser();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxobject2discount where oxdiscountid='%s'", $sSynchoxid)));
    }
}
