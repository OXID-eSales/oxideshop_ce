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
class DeliverysetCountryAjaxTest extends \OxidTestCase
{
    protected $_sShopId = '1';
    protected $_sCountryView = 'oxv_oxcountry_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetCountry1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetCountry2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetCountryDelAll1', oxdeliveryid='_testDeliverysetCountryRemoveAll', oxobjectid='_testCountry1', oxtype='oxdelset'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliverysetCountryDelAll2', oxdeliveryid='_testDeliverysetCountryRemoveAll', oxobjectid='_testCountry2', oxtype='oxdelset'");

        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry1', oxtitle='_testCountry1'");
        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry2', oxtitle='_testCountry2'");

        if ($this->getConfig()->getEdition() === 'EE') :
            $this->setShopIdTest('1');
        endif;
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetCountry1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetCountry2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetCountryDelAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliverysetCountryDelAll2'");

        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry1'");
        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddCountry'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddCountryAll'");

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

    public function setCountryViewTable($sParam)
    {
        $this->_sCountryView = $sParam;
    }

    public function getCountryViewTable()
    {
        return $this->_sCountryView;
    }

    /**
     * DeliverysetCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('deliveryset_country_ajax');
        $this->assertEquals("from " . $this->getCountryViewTable() . " where oxv_oxcountry_de.oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_country_ajax');
        $this->assertEquals("from " . $this->getCountryViewTable() . " where " . $this->getCountryViewTable() . ".oxactive = '1' and " . $this->getCountryViewTable() . ".oxid not in ( select " . $this->getCountryViewTable() . ".oxid from oxobject2delivery, " . $this->getCountryViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = " . $this->getCountryViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelset' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('deliveryset_country_ajax');
        $this->assertEquals("from oxobject2delivery, " . $this->getCountryViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxobjectid = " . $this->getCountryViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelset'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetCountryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('deliveryset_country_ajax');
        $this->assertEquals("from oxobject2delivery, " . $this->getCountryViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxobjectid = " . $this->getCountryViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelset' and " . $this->getCountryViewTable() . ".oxid not in ( select " . $this->getCountryViewTable() . ".oxid from oxobject2delivery, " . $this->getCountryViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = " . $this->getCountryViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelset' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliverysetCountryAjax::removeCountryFromSet() test case
     *
     * @return null
     */
    public function testRemoveCountryFromSet()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetCountryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliverysetCountry1', '_testDeliverysetCountry2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliverysetCountry1', '_testDeliverysetCountry2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCountryFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetCountryAjax::removeCountryFromSet() test case
     *
     * @return null
     */
    public function testRemoveCountryFromSetAll()
    {
        $sOxid = '_testDeliverysetCountryRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery, " . $this->getCountryViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxobjectid = " . $this->getCountryViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelset'";
        $oView = oxNew('deliveryset_country_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCountryFromSet();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetCountryAjax::addCountryToSet() test case
     *
     * @return null
     */
    public function testAddCountryToset()
    {
        $sSynchoxid = '_testActionAddCountry';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetCountryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addCountryToSet();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliverysetCountryAjax::addCountryToSet() test case
     *
     * @return null
     */
    public function testAddCountryToSetAll()
    {
        $sSynchoxid = '_testActionAddCountryAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getCountryViewTable() . ".oxid) from " . $this->getCountryViewTable() . " where " . $this->getCountryViewTable() . ".oxactive = '1' and " . $this->getCountryViewTable() . ".oxid not in ( select " . $this->getCountryViewTable() . ".oxid from oxobject2delivery, " . $this->getCountryViewTable() . " where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "'and oxobject2delivery.oxobjectid = " . $this->getCountryViewTable() . ".oxid and oxobject2delivery.oxtype = 'oxdelset' )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliverySetCountryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addCountryToSet();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
