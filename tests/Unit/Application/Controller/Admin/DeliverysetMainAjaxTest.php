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
class DeliverysetMainAjaxTest extends \OxidTestCase
{
    protected $_sDeliveryView = 'oxv_oxdelivery_1_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addToDatabase("insert into oxdel2delset set oxid='_testDeliverysetMain1', oxdelsetid='_testObjectId'", 'oxdel2delset');
        $this->addToDatabase("insert into oxdel2delset set oxid='_testDeliverysetMain2', oxdelsetid='_testObjectId'", 'oxdel2delset');
        //for delete all
        $this->addToDatabase("insert into oxdel2delset set oxid='_testDeliverysetMainDelAll1', oxdelsetid='_testDeliverysetMainRemoveAll', oxdelid='_testMain1'", 'oxdel2delset');
        $this->addToDatabase("insert into oxdel2delset set oxid='_testDeliverysetMainDelAll2', oxdelsetid='_testDeliverysetMainRemoveAll', oxdelid='_testMain2'", 'oxdel2delset');

        $this->addToDatabase("insert into oxdelivery set oxid='_testMain1', oxtitle='_testMain1'", 'oxdelivery');
        $this->addToDatabase("insert into oxdelivery set oxid='_testMain2', oxtitle='_testMain2'", 'oxdelivery');

        $this->addTeardownSql("delete from oxdel2delset where oxid like '%_testDelivery%'");
        $this->addTeardownSql("delete from oxdelivery where oxid like '%_testMain%'");

        if ($this->getConfig()->getEdition() !== 'EE') :
            $this->setDeliveryViewTable('oxv_oxdelivery_de');
        endif;
    }

    public function setDeliveryViewTable($sParam)
    {
        $this->_sDeliveryView = $sParam;
    }

    public function getDeliveryViewTable()
    {
        return $this->_sDeliveryView;
    }

    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('deliveryset_main_ajax');
        $this->assertEquals("from " . $this->getDeliveryViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertEquals("from " . $this->getDeliveryViewTable() . " where 1 and " . $this->getDeliveryViewTable() . ".oxid not in ( select " . $this->getDeliveryViewTable() . ".oxid from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sSynchoxid . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertEquals("from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sOxid . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertEquals("from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sOxid . "'and " . $this->getDeliveryViewTable() . ".oxid not in ( select " . $this->getDeliveryViewTable() . ".oxid from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sSynchoxid . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetMainAjax::removeFromSet() test case
     *
     * @return null
     */
    public function testRemoveFromSet()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliverysetMain1', '_testDeliverysetMain2')));

        $sSql = "select count(oxid) from oxdel2delset where oxid in ('_testDeliverysetMain1', '_testDeliverysetMain2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetMainAjax::removeFromSet() test case
     *
     * @return null
     */
    public function testRemoveFromSetAll()
    {
        $sOxid = '_testDeliverysetMainRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxdel2delset.oxid) from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sOxid . "'";

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetMainAjax::addToSet() test case
     *
     * @return null
     */
    public function testAddToset()
    {
        $sSynchoxid = '_testActionAddMain';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxdel2delset where oxdelsetid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addToSet();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetMainAjax::addToSet() test case
     *
     * @return null
     */
    public function testAddToSetAll()
    {
        $sSynchoxid = '_testActionAddMainAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getDeliveryViewTable() . ".oxid) from " . $this->getDeliveryViewTable() . " where 1 and " . $this->getDeliveryViewTable() . ".oxid not in ( select " . $this->getDeliveryViewTable() . ".oxid from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sSynchoxid . "' )");

        $sSql = "select count(oxid) from oxdel2delset where oxdelsetid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
