<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Delivery_Groups_Ajax class
 */
class Unit_Admin_DeliverysetGroupsAjaxTest extends OxidTestCase
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

        $this->setShopIdTest('oxbaseshop');
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
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

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
        $this->getConfig()->setRequestParameter("oxid", $sOxid);

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
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

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
        $oView = $this->getMock("deliveryset_groups_ajax", array("_getActionIds"));
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
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("all", true);

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
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("deliveryset_groups_ajax", array("_getActionIds"));
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
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getGroupsViewTable() . ".oxid) from " . $this->getGroupsViewTable() . " where 1  and " . $this->getGroupsViewTable() . ".oxid not in ( select " . $this->getGroupsViewTable() . ".oxid from oxobject2delivery, " . $this->getGroupsViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxobjectid = " . $this->getGroupsViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelsetg' )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("deliveryset_groups_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addGroupToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}