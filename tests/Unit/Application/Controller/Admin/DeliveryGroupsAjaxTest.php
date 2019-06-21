<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Delivery_Groups_Ajax class
 */
class DeliveryGroupsAjaxTest extends \OxidTestCase
{
    protected $_sGroupsView = 'oxv_oxgroups_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryGroup1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryGroup2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryGroupDeleteAll1', oxdeliveryid='_testDeliveryGroupRemoveAll', oxobjectid='_testGroup1', oxtype='oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryGroupDeleteAll2', oxdeliveryid='_testDeliveryGroupRemoveAll', oxobjectid='_testGroup2', oxtype='oxgroups'");

        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup1', oxtitle='_testGroup1'");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup2', oxtitle='_testGroup2'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryGroup1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryGroup2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryGroupDeleteAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryGroupDeleteAll2'");

        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup1'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddGroup'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddGroupAll'");

        parent::tearDown();
    }

    public function setGroupsViewTable($sParam)
    {
        $this->_sGroupsView = $sParam;
    }

    public function getGroupsViewTable()
    {
        return $this->_sGroupsView;
    }

    /**
     * DeliveryGroupssAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_groups_ajax');
        $this->assertEquals("from " . $this->getGroupsViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryGroupssAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_groups_ajax');
        $this->assertEquals("from " . $this->getGroupsViewTable() . " where 1  and " . $this->getGroupsViewTable() . ".oxid not in ( select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery left join " . $this->getGroupsViewTable() . " on " . $this->getGroupsViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxgroups' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryGroupssAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_groups_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getGroupsViewTable() . " on " . $this->getGroupsViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxgroups'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryGroupssAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_groups_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getGroupsViewTable() . " on " . $this->getGroupsViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxgroups'  and " . $this->getGroupsViewTable() . ".oxid not in ( select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery left join " . $this->getGroupsViewTable() . " on " . $this->getGroupsViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxgroups' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryGroupssAjax::removeGroupFromDel() test case
     *
     * @return null
     */
    public function testRemoveGroupFromDel()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliveryGroup1', '_testDeliveryGroup2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryGroup1', '_testDeliveryGroup2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryGroupssAjax::removeGroupFromDel() test case
     *
     * @return null
     */
    public function testRemoveGroupFromDelAll()
    {
        $sOxid = '_testDeliveryGroupRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join " . $this->getGroupsViewTable() . " on " . $this->getGroupsViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxgroups'";
        $oView = oxNew('delivery_groups_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryGroupssAjax::addGroupToDel() test case
     *
     * @return null
     */
    public function testAddGroupToDel()
    {
        $sSynchoxid = '_testActionAddGroup';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addGroupToDel();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryGroupssAjax::addGroupToDel() test case
     *
     * @return null
     */
    public function testAddCatToDelAll()
    {
        $sSynchoxid = '_testActionAddGroupAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getGroupsViewTable() . ".oxid) from " . $this->getGroupsViewTable() . "  where  " . $this->getGroupsViewTable() . ".oxid not in (  select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery left join " . $this->getGroupsViewTable() . " on " . $this->getGroupsViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxgroups'  )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addGroupToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
