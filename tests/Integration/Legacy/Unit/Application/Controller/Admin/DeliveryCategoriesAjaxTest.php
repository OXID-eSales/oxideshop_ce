<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * Tests for Delivery_Categories_Ajax class
 */
class DeliveryCategoriesAjaxTest extends \OxidTestCase
{
    protected $_sCategoriesView = 'oxv_oxcategories_1_de';

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCat1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCat2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCatDeleteAll1', oxdeliveryid='_testDeliveryCatRemoveAll', oxobjectid='_testCategory1', oxtype='oxcategories'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCatDeleteAll2', oxdeliveryid='_testDeliveryCatRemoveAll', oxobjectid='_testCategory2', oxtype='oxcategories'");

        $shopId = '1';

        oxDb::getDb()->execute("insert into oxcategories set oxid='_testCategory1', oxshopid='" . $shopId . "', oxtitle='_testCategory1'");
        oxDb::getDb()->execute("insert into oxcategories set oxid='_testCategory2', oxshopid='" . $shopId . "', oxtitle='_testCategory2'");

        if ((new Facts())->getEdition() !== 'EE') {
            $this->setCategoriesViewTable('oxv_oxcategories_de');
        }
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCat1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCat2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCatDeleteAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCatDeleteAll2'");

        oxDb::getDb()->execute("delete from oxcategories where oxid='_testCategory1'");
        oxDb::getDb()->execute("delete from oxcategories where oxid='_testCategory2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddCat'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddCatAll'");

        parent::tearDown();
    }

    public function setCategoriesViewTable($sParam)
    {
        $this->_sCategoriesView = $sParam;
    }

    public function getCategoriesViewTable()
    {
        return $this->_sCategoriesView;
    }

    /**
     * DeliveryCategoriessAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from " . $this->getCategoriesViewTable(), trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryCategoriessAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from " . $this->getCategoriesViewTable() . "  where  " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcategories'  )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryCategoriessAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcategories'", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryCategoriessAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcategories'  and  " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcategories'  )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryCategoriessAjax::removeCatFromDel() test case
     */
    public function testRemoveCatFromDel()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryCategoriesAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testDeliveryCat1', '_testDeliveryCat2']));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryCat1', '_testDeliveryCat2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCatFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryCategoriessAjax::removeCatFromDel() test case
     */
    public function testRemoveCatFromDelAll()
    {
        $sOxid = '_testDeliveryCatRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcategories'";
        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCatFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryCategoriessAjax::addCatToDel() test case
     */
    public function testAddCatToDel()
    {
        $sSynchoxid = '_testActionAddCat';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = sprintf('select count(oxid) from oxobject2delivery where oxdeliveryid=\'%s\'', $sSynchoxid);
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryCategoriesAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testActionAdd1', '_testActionAdd2']));

        $oView->addCatToDel();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryCategoriessAjax::addCatToDel() test case
     */
    public function testAddCatToDelAll()
    {
        $sSynchoxid = '_testActionAddCatAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getCategoriesViewTable() . ".oxid) from " . $this->getCategoriesViewTable() . "  where  " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcategories'  )");

        $sSql = sprintf('select count(oxid) from oxobject2delivery where oxdeliveryid=\'%s\'', $sSynchoxid);
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryCategoriesAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testActionAdd1', '_testActionAdd2']));

        $oView->addCatToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
