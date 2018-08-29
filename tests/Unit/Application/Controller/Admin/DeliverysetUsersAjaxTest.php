<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Tests for Delivery_Groups_Ajax class
 */
class DeliverysetUsersAjaxTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryUser1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryUser2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryUserDeleteAll1', oxdeliveryid='_testDeliveryUserRemoveAll', oxobjectid='_testUser1', oxtype='oxdelsetu'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryUserDeleteAll2', oxdeliveryid='_testDeliveryUserRemoveAll', oxobjectid='_testUser2', oxtype='oxdelsetu'");

        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser1', oxusername='_testUser1'");
        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser2', oxusername='_testUser2'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryUser1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryUser2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryUserDeleteAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryUserDeleteAll2'");

        oxDb::getDb()->execute("delete from oxuser where oxid='_testUser1'");
        oxDb::getDb()->execute("delete from oxuser where oxid='_testUser2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddUser'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddUserAll'");

        parent::tearDown();
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxuser where 1 and oxuser.oxshopid = '" . $this->getShopIdTest() . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryMallUsers()
    {
        $this->getConfig()->setConfigParam("blMallUsers", true);
        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxuser where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxuser where 1 and oxuser.oxshopid = '" . $this->getShopIdTest() . "' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidMallUsers()
    {
        $this->getConfig()->setConfigParam("blMallUsers", true);
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxuser where 1 and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sOxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxobject2group left join oxuser on oxuser.oxid = oxobject2group.oxobjectid  where oxobject2group.oxgroupsid = '" . $sOxid . "'and oxuser.oxshopid = '" . $this->getShopIdTest() . "' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxidMallUsers()
    {
        $this->getConfig()->setConfigParam("blMallUsers", true);
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals("from oxobject2group left join oxuser on oxuser.oxid = oxobject2group.oxobjectid  where oxobject2group.oxgroupsid = '" . $sOxid . "'and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetUsersAjax::removeUserFromSet() test case
     *
     * @return null
     */
    public function testRemoveUserFromSet()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetUsersAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliveryUser1', '_testDeliveryUser2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryUser1', '_testDeliveryUser2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeUserFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetUsersAjax::removeUserFromSet() test case
     *
     * @return null
     */
    public function testRemoveUserFromSetAll()
    {
        $sOxid = '_testDeliveryUserRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sOxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu'";
        $oView = oxNew('deliveryset_users_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeUserFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetUsersAjax::addUserToSet() test case
     *
     * @return null
     */
    public function testAddUserToSet()
    {
        $sSynchoxid = '_testActionAddUser';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetUsersAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addUserToSet();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetUsersAjax::addUserToSet() test case
     *
     * @return null
     */
    public function testAddUserToSetAll()
    {
        $sSynchoxid = '_testActionAddUserAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxuser.oxid) from oxuser where 1 and oxuser.oxshopid = '" . $this->getShopIdTest() . "' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery, oxuser where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = oxuser.oxid and oxobject2delivery.oxtype = 'oxdelsetu' )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetUsersAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addUserToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }

    /**
     * Returns shop id based on edition.
     *
     * @return string
     */
    protected function getShopIdTest()
    {
        return ShopIdCalculator::BASE_SHOP_ID;
    }
}
