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
class DeliverysetGroupsAjaxTest extends \OxidTestCase
{
    protected $_sShopId = '1';
    protected $_sGroupsView = 'oxv_oxgroups_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetGroup1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetGroup2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetGroupDelAll1', oxdeliveryid='_testDeliverysetGroupRemoveAll', oxobjectid='_testGroup1', oxtype='oxdelsetg'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetGroupDelAll2', oxdeliveryid='_testDeliverysetGroupRemoveAll', oxobjectid='_testGroup2', oxtype='oxdelsetg'");

        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup1', oxtitle='_testGroup1'");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testgroup2', oxtitle='_testGroup2'");

        if ($this->getConfig()->getEdition() === 'EE') {
            $this->setShopIdTest('1');
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetGroup1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetGroup2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetGroupDelAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetGroupDelAll2'");

        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup1'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddGroup'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddGroupAll'");

        parent::tearDown();
    }

    public function setShopIdTest($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getShopIdTest()
    {
        return $this->_sShopId;
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
     * DeliverysetGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('deliveryset_groups_ajax');
        $this->assertEquals("from " . $this->getGroupsViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_groups_ajax');
        $this->assertEquals("from " . $this->getGroupsViewTable() . " where 1  and " . $this->getGroupsViewTable() . ".oxid not in ( select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('deliveryset_groups_ajax');
        $this->assertEquals("from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetGroupsAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_groups_ajax');
        $this->assertEquals("from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg'  and " . $this->getGroupsViewTable() . ".oxid not in ( select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetGroupsAjax::removeGroupFromSet() test case
     *
     * @return null
     */
    public function testRemoveGroupFromSet()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliverysetGroup1', '_testDeliverysetGroup2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliverysetGroup1', '_testDeliverysetGroup2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetGroupsAjax::removeGroupFromSet() test case
     *
     * @return null
     */
    public function testRemoveGroupFromSetAll()
    {
        $sOxid = '_testDeliverysetGroupRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg'";
        $oView = oxNew('deliveryset_groups_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetGroupsAjax::addGroupToSet() test case
     *
     * @return null
     */
    public function testAddGroupToset()
    {
        $sSynchoxid = '_testActionAddGroup';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addGroupToSet();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetGroupsAjax::addGroupToSet() test case
     *
     * @return null
     */
    public function testAddGroupToSetAll()
    {
        $sSynchoxid = '_testActionAddGroupAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getGroupsViewTable() . ".oxid) from " . $this->getGroupsViewTable() . " where 1  and " . $this->getGroupsViewTable() . ".oxid not in ( select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg' )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetGroupsAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addGroupToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
