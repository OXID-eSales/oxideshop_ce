<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Delivery_Payment_Ajax class
 */
class DeliverysetPaymentAjaxTest extends \OxidTestCase
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

        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testDeliverysetPayment1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testDeliverysetPayment2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testDeliverysetPaymentDelAll1', oxpaymentid='_testPayment1', oxobjectid='_testDeliverysetPaymentRemoveAll', oxtype='oxdelset'");
        oxDb::getDb()->execute("insert into oxobject2payment set oxid='_testDeliverysetPaymentDelAll2', oxpaymentid='_testPayment2', oxobjectid='_testDeliverysetPaymentRemoveAll', oxtype='oxdelset'");

        oxDb::getDb()->execute("insert into oxpayments set oxid='_testPayment1', oxdesc='_testPayment1'");
        oxDb::getDb()->execute("insert into oxpayments set oxid='_testPayment2', oxdesc='_testPayment2'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2payment where oxid='_testDeliverysetPayment1'");
        oxDb::getDb()->execute("delete from oxobject2payment where oxid='_testDeliverysetPayment2'");

        oxDb::getDb()->execute("delete from oxobject2payment where oxid='_testDeliverysetPaymentDelAll1'");
        oxDb::getDb()->execute("delete from oxobject2payment where oxid='_testDeliverysetPaymentDelAll2'");

        oxDb::getDb()->execute("delete from oxpayments where oxid='_testPayment1'");
        oxDb::getDb()->execute("delete from oxpayments where oxid='_testPayment2'");

        oxDb::getDb()->execute("delete from oxobject2payment where oxobjectid='_testActionAddPayment'");
        oxDb::getDb()->execute("delete from oxobject2payment where oxobjectid='_testActionAddPaymentAll'");

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
     * DeliverysetPaymentAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('deliveryset_payment_ajax');
        $this->assertEquals("from oxv_oxpayments_de where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetPaymentAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_payment_ajax');
        $this->assertEquals("from oxv_oxpayments_de where 1 and oxv_oxpayments_de.oxid not in ( select oxv_oxpayments_de.oxid from oxobject2payment, oxv_oxpayments_de where oxobject2payment.oxobjectid = '" . $sSynchoxid . "'and oxobject2payment.oxpaymentid = oxv_oxpayments_de.oxid and oxobject2payment.oxtype = 'oxdelset' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetPaymentAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('deliveryset_payment_ajax');
        $this->assertEquals("from oxobject2payment, oxv_oxpayments_de where oxobject2payment.oxobjectid = '" . $sOxid . "' and oxobject2payment.oxpaymentid = oxv_oxpayments_de.oxid and oxobject2payment.oxtype = 'oxdelset'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetPaymentAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_payment_ajax');
        $this->assertEquals("from oxobject2payment, oxv_oxpayments_de where oxobject2payment.oxobjectid = '" . $sOxid . "' and oxobject2payment.oxpaymentid = oxv_oxpayments_de.oxid and oxobject2payment.oxtype = 'oxdelset' and oxv_oxpayments_de.oxid not in ( select oxv_oxpayments_de.oxid from oxobject2payment, oxv_oxpayments_de where oxobject2payment.oxobjectid = '" . $sSynchoxid . "'and oxobject2payment.oxpaymentid = oxv_oxpayments_de.oxid and oxobject2payment.oxtype = 'oxdelset' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetPaymentAjax::removePayFromSet() test case
     *
     * @return null
     */
    public function testRemovePayFromSet()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetPaymentAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliverysetPayment1', '_testDeliverysetPayment2')));

        $sSql = "select count(oxid) from oxobject2payment where oxid in ('_testDeliverysetPayment1', '_testDeliverysetPayment2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removePayFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetPaymentAjax::removePayFromSet() test case
     *
     * @return null
     */
    public function testRemovePayFromSetAll()
    {
        $sOxid = '_testDeliverysetPaymentRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2payment.oxid) from oxobject2payment, oxv_oxpayments_de where oxobject2payment.oxobjectid = '" . $sOxid . "' and oxobject2payment.oxpaymentid = oxv_oxpayments_de.oxid and oxobject2payment.oxtype = 'oxdelset'";
        $oView = oxNew('deliveryset_payment_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removePayFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetPaymentAjax::addPayToSet() test case
     *
     * @return null
     */
    public function testAddPayToset()
    {
        $sSynchoxid = '_testActionAddPayment';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2payment where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetPaymentAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addPayToSet();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetPaymentAjax::addPayToSet() test case
     *
     * @return null
     */
    public function testAddPayToSetAll()
    {
        $sSynchoxid = '_testActionAddPaymentAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxv_oxpayments_de.oxid) from oxv_oxpayments_de where 1 and oxv_oxpayments_de.oxid not in ( select oxv_oxpayments_de.oxid from oxobject2payment, oxv_oxpayments_de where oxobject2payment.oxobjectid = '" . $sSynchoxid . "'and oxobject2payment.oxpaymentid = oxv_oxpayments_de.oxid and oxobject2payment.oxtype = 'oxdelset' )");

        $sSql = "select count(oxid) from oxobject2payment where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetPaymentAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addPayToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
