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
class VoucherseriegroupsajaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $shopId = ShopIdCalculator::BASE_SHOP_ID;

        oxDb::getDb()->execute("replace into oxobject2group set oxid='_testId1', oxshopid='$shopId', oxobjectid='_testVoucherId1', oxgroupsid='_testGroupId1'");
        oxDb::getDb()->execute("replace into oxobject2group set oxid='_testId2', oxshopid='$shopId', oxobjectid='_testVoucherId1', oxgroupsid='_testGroupId2'");

        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId1', oxactive=1, oxtitle='_testGroup1', oxtitle_1='_testGroup1_en'");
        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId2', oxactive=1, oxtitle='_testGroup2', oxtitle_1='_testGroup2_en'");
        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId3', oxactive=1, oxtitle='_testGroup3', oxtitle_1='_testGroup3_en'");
        oxDb::getDb()->execute("replace into oxgroups set oxid='_testGroupId4', oxactive=1, oxtitle='_testGroup4', oxtitle_1='_testGroup4_en'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDB()->execute("delete from oxobject2group where oxid like '\_test%'");
        oxDb::getDB()->execute("delete from oxobject2group where oxobjectid like '\_test%'");
        oxDb::getDB()->execute("delete from oxgroups where oxid like '\_test%'");

        parent::tearDown();
    }

    /**
     * voucherserie_groups_ajax::removeGroupFromVoucher() test case
     *
     * @return null
     */
    public function testRemoveGroupFromVoucher_allRecords()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("oxid", "_testVoucherId1");

        $oDb = oxDb::getDb();

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView = oxNew('voucherserie_groups_ajax');
        $oView->removeGroupFromVoucher();

        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));
    }

    /**
     * voucherserie_groups_ajax::removeGroupFromVoucher() test case
     *
     * @return null
     */
    public function testRemoveGroupFromVoucher_oneRecords()
    {
        $this->setRequestParameter("oxid", "_testVoucherId1");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testId1')));

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView->removeGroupFromVoucher();

        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));
        $this->assertEquals("_testVoucherId1", oxDb::getDb()->getOne("select oxobjectid from oxobject2group where oxid like '_test%'"));
    }

    /**
     * voucherserie_groups_ajax::addGroupToVoucher() test case
     *
     * @return null
     */
    public function testAddGroupToVoucher_allGroups()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("synchoxid", "_testVoucherId1");

        oxDb::getDB()->execute("delete from oxobject2group where oxid like '\_test%'");
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class, array("_getQuery"));
        $oView->expects($this->once())->method('_getQuery')->will($this->returnValue("from oxv_oxgroups_de where oxid like '\_test%'"));

        $oView->addGroupToVoucher();

        $this->assertEquals(4, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxobjectid = '_testVoucherId1'"));
    }

    /**
     * voucherserie_groups_ajax::addGroupToVoucher() test case
     *
     * @return null
     */
    public function testAddGroupToVoucher_someGroups()
    {
        $this->setRequestParameter("synchoxid", "_testVoucherId1");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\VoucherSerieGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->once())->method('_getActionIds')->will($this->returnValue(array('_testGroupId3')));

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxid like '_test%'"));

        $oView->addGroupToVoucher();

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2group where oxobjectid = '_testVoucherId1'"));
    }


    /**
     * voucherserie_groups_ajax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertEquals('from oxv_oxgroups_de where 1', trim($oView->UNITgetQuery()));
    }

    /**
     * voucherserie_groups_ajax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testGroupGetQuerySynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sResult = "from oxv_oxgroups_de where 1 and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '$sSynchoxid' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid )";
        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertEquals($sResult, trim(preg_replace("/\s+/", " ", $oView->UNITgetQuery())));
    }

    /**
     * voucherserie_groups_ajax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testGroupGetQuery';
        $this->setRequestParameter("oxid", $sOxid);

        $sResult = "from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '$sOxid' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid";
        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertEquals($sResult, trim(preg_replace("/\s+/", " ", $oView->UNITgetQuery())));
    }

    /**
     * voucherserie_groups_ajax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testGroupGetQuery';
        $sSynchoxid = '_testGroupGetQuerySynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sResult = "from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '$sOxid' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid";
        $sResult .= " and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxv_oxgroups_de, oxobject2group where oxobject2group.oxobjectid = '$sSynchoxid' and oxv_oxgroups_de.oxid = oxobject2group.oxgroupsid )";

        $oView = oxNew('voucherserie_groups_ajax');
        $this->assertEquals($sResult, trim(preg_replace("/\s+/", " ", $oView->UNITgetQuery())));
    }
}
