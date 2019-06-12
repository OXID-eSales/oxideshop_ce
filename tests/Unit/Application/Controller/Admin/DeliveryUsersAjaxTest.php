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
class DeliveryUsersAjaxTest extends \OxidTestCase
{
    protected $_sShopId = ShopIdCalculator::BASE_SHOP_ID;

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
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryUserDeleteAll1', oxdeliveryid='_testDeliveryUserRemoveAll', oxobjectid='_testUser1', oxtype='oxuser'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryUserDeleteAll2', oxdeliveryid='_testDeliveryUserRemoveAll', oxobjectid='_testUser2', oxtype='oxuser'");

        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser1', oxusername='_testUser1'");
        oxDb::getDb()->execute("insert into oxuser set oxid='_testUser2', oxusername='_testUser2'");

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

    public function setShopIdTest($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getShopIdTest()
    {
        return $this->_sShopId;
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxuser where 1  and oxuser.oxshopid = '" . $this->getShopIdTest() . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryMallUsers()
    {
        $this->getConfig()->setConfigParam("blMallUsers", true);
        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxuser where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxuser where 1  and oxuser.oxshopid = '" . $this->getShopIdTest() . "'  and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxuser' and oxuser.oxid IS NOT NULL )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidMallUsers()
    {
        $this->getConfig()->setConfigParam("blMallUsers", true);
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxuser where 1  and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxuser' and oxuser.oxid IS NOT NULL )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxuser' and oxuser.oxid IS NOT NULL", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxobject2group left join oxuser on oxuser.oxid = oxobject2group.oxobjectid  where oxobject2group.oxgroupsid = '" . $sOxid . "' and oxuser.oxshopid = '" . $this->getShopIdTest() . "'  and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxuser' and oxuser.oxid IS NOT NULL )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::_getQuery() test case
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

        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals("from oxobject2group left join oxuser on oxuser.oxid = oxobject2group.oxobjectid  where oxobject2group.oxgroupsid = '" . $sOxid . "' and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxuser' and oxuser.oxid IS NOT NULL )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryUsersAjax::removeUserFromDel() test case
     *
     * @return null
     */
    public function testRemoveUserFromDel()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryUsersAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliveryUser1', '_testDeliveryUser2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryUser1', '_testDeliveryUser2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeUserFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryUsersAjax::removeUserFromDel() test case
     *
     * @return null
     */
    public function testRemoveUserFromDelAll()
    {
        $sOxid = '_testDeliveryUserRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxuser'";
        $oView = oxNew('delivery_users_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeUserFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryUsersAjax::addUserToDel() test case
     *
     * @return null
     */
    public function testAddUserToDel()
    {
        $sSynchoxid = '_testActionAddUser';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryUsersAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addUserToDel();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryUsersAjax::addUserToDel() test case
     *
     * @return null
     */
    public function testAddUserToDelAll()
    {
        $sSynchoxid = '_testActionAddUserAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxuser.oxid) from oxuser where 1  and oxuser.oxshopid = '" . $this->getShopIdTest() . "'  and oxuser.oxid not in ( select oxuser.oxid from oxobject2delivery left join oxuser on oxuser.oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxuser' and oxuser.oxid IS NOT NULL )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryUsersAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addUserToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
