<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Tests for Actions_List class
 */
class VoucherseriegroupsajaxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $shopId = ShopIdCalculator::BASE_SHOP_ID;

        oxDb::getDb()->execute(sprintf("replace into oxobject2group set oxid='_testId1', oxshopid='%d', oxobjectid='_testVoucherId1', oxgroupsid='_testGroupId1'", $shopId));
        oxDb::getDb()->execute(sprintf("replace into oxobject2group set oxid='_testId2', oxshopid='%d', oxobjectid='_testVoucherId1', oxgroupsid='_testGroupId2'", $shopId));

        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId1', oxactive=1, oxtitle='_testGroup1', oxtitle_1='_testGroup1_en'");
        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId2', oxactive=1, oxtitle='_testGroup2', oxtitle_1='_testGroup2_en'");
        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId3', oxactive=1, oxtitle='_testGroup3', oxtitle_1='_testGroup3_en'");
        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId4', oxactive=1, oxtitle='_testGroup4', oxtitle_1='_testGroup4_en'");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDB()->execute("delete from oxobject2group where oxid like '\_test%'");
        oxDb::getDB()->execute("delete from oxobject2group where oxobjectid like '\_test%'");
        oxDb::getDB()->execute("delete from oxgroups where oxid like '\_test%'");

        parent::tearDown();
    }

    /**
     * voucherserie_groups_ajax::removeGroupFromVoucher() test case
     */
    public function testRemoveGroupFromVoucher_allRecords()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("oxid", "_testVoucherId1");

        $oDb = oxDb::getDb();

        $this->assertSame(2, $oDb->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView = oxNew('voucherserie_groups_ajax');
        $oView->removeGroupFromVoucher();

        $this->assertSame(0, $oDb->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));
    }

    /**
     * voucherserie_groups_ajax::removeGroupFromVoucher() test case
     */
    public function testRemoveGroupFromVoucher_oneRecords()
    {
        $this->setRequestParameter("oxid", "_testVoucherId1");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testId1']);

        $this->assertSame(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView->removeGroupFromVoucher();

        $this->assertSame(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));
        $this->assertSame("_testVoucherId1", oxDb::getDb()->getOne("select oxobjectid from oxobject2group where oxid like '_test%'"));
    }

    /**
     * voucherserie_groups_ajax::addGroupToVoucher() test case
     */
    public function testAddGroupToVoucher_allGroups()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("synchoxid", "_testVoucherId1");

        oxDb::getDB()->execute("delete from oxobject2group where oxid like '\_test%'");
        $this->assertSame(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class, ["getQuery"]);
        $oView->expects($this->once())->method('getQuery')->willReturn("from oxv_oxgroups_de where oxid like '\_test%'");

        $oView->addGroupToVoucher();

        $this->assertSame(4, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxobjectid = '_testVoucherId1'"));
    }

    /**
     * voucherserie_groups_ajax::addGroupToVoucher() test case
     */
    public function testAddGroupToVoucher_someGroups()
    {
        $this->setRequestParameter("synchoxid", "_testVoucherId1");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class, ["getActionIds"]);
        $oView->expects($this->once())->method('getActionIds')->willReturn(['_testGroupId3']);

        $this->assertSame(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView->addGroupToVoucher();

        $this->assertSame(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxobjectid = '_testVoucherId1'"));
    }


    /**
     * voucherserie_groups_ajax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertSame('from oxv_oxgroups_de where 1', trim((string) $oView->getQuery()));
    }

    /**
     * voucherserie_groups_ajax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testGroupGetQuerySynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sResult = sprintf("from oxv_oxgroups_de where 1 and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '%s' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid )", $sSynchoxid);
        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertSame($sResult, trim(preg_replace("/\s+/", " ", $oView->getQuery())));
    }

    /**
     * voucherserie_groups_ajax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testGroupGetQuery';
        $this->setRequestParameter("oxid", $sOxid);

        $sResult = sprintf("from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '%s' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid", $sOxid);
        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertSame($sResult, trim(preg_replace("/\s+/", " ", $oView->getQuery())));
    }

    /**
     * voucherserie_groups_ajax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testGroupGetQuery';
        $sSynchoxid = '_testGroupGetQuerySynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sResult = sprintf("from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '%s' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid", $sOxid);
        $sResult .= sprintf(" and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '%s' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid )", $sSynchoxid);

        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertSame($sResult, trim(preg_replace("/\s+/", " ", $oView->getQuery())));
    }
}
