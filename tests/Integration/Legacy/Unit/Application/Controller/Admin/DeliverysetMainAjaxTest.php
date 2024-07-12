<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * Tests for Delivery_Groups_Ajax class
 */
class DeliverysetMainAjaxTest extends \PHPUnit\Framework\TestCase
{
    protected $_sDeliveryView = 'oxv_oxdelivery_1_de';

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
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

        if ((new Facts())->getEdition() !== 'EE') :
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
     * DeliverysetMainAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('deliveryset_main_ajax');
        $this->assertSame("from " . $this->getDeliveryViewTable() . " where 1", trim((string) $oView->getQuery()));
    }

    /**
     * DeliverysetMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertSame("from " . $this->getDeliveryViewTable() . " where 1 and " . $this->getDeliveryViewTable() . ".oxid not in ( select " . $this->getDeliveryViewTable() . ".oxid from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sSynchoxid . "' )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliverysetMainAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertSame("from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sOxid . "'", trim((string) $oView->getQuery()));
    }

    /**
     * DeliverysetMainAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertSame("from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sOxid . "'and " . $this->getDeliveryViewTable() . ".oxid not in ( select " . $this->getDeliveryViewTable() . ".oxid from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sSynchoxid . "' )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliverysetMainAjax::removeFromSet() test case
     */
    public function testRemoveFromSet()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testDeliverysetMain1', '_testDeliverysetMain2']);

        $sSql = "select count(oxid) from oxdel2delset where oxid in ('_testDeliverysetMain1', '_testDeliverysetMain2')";
        $this->assertSame(2, oxDb::getDb()->getOne($sSql));
        $oView->removeFromSet();
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetMainAjax::removeFromSet() test case
     */
    public function testRemoveFromSetAll()
    {
        $sOxid = '_testDeliverysetMainRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxdel2delset.oxid) from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sOxid . "'";

        $oView = oxNew('deliveryset_main_ajax');
        $this->assertSame(2, oxDb::getDb()->getOne($sSql));
        $oView->removeFromSet();
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetMainAjax::addToSet() test case
     */
    public function testAddToset()
    {
        $sSynchoxid = '_testActionAddMain';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = sprintf("select count(oxid) from oxdel2delset where oxdelsetid='%s'", $sSynchoxid);
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testActionAdd1', '_testActionAdd2']);

        $oView->addToSet();
        $this->assertSame(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetMainAjax::addToSet() test case
     */
    public function testAddToSetAll()
    {
        $sSynchoxid = '_testActionAddMainAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getDeliveryViewTable() . ".oxid) from " . $this->getDeliveryViewTable() . " where 1 and " . $this->getDeliveryViewTable() . ".oxid not in ( select " . $this->getDeliveryViewTable() . ".oxid from " . $this->getDeliveryViewTable() . " left join oxdel2delset on oxdel2delset.oxdelid=" . $this->getDeliveryViewTable() . ".oxid where oxdel2delset.oxdelsetid = '" . $sSynchoxid . "' )");

        $sSql = sprintf("select count(oxid) from oxdel2delset where oxdelsetid='%s'", $sSynchoxid);
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetMainAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testActionAdd1', '_testActionAdd2']);

        $oView->addToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
