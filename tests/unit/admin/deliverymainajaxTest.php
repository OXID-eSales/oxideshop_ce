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
class Unit_Admin_DeliveryMainAjaxTest extends OxidTestCase
{

    protected $_sCountryView = 'oxv_oxcountry_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCountry1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCountry2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCountryDeleteAll1', oxdeliveryid='_testDeliveryCountryRemoveAll', oxobjectid='_testCountry1', oxtype='oxcountry'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCountryDeleteAll2', oxdeliveryid='_testDeliveryCountryRemoveAll', oxobjectid='_testCountry2', oxtype='oxcountry'");

        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry1', oxtitle='_testCountry1'");
        oxDb::getDb()->execute("insert into oxcountry set oxid='_testCountry2', oxtitle='_testCountry2'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCountry1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCountry2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCountryDeleteAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryCountryDeleteAll2'");

        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry1'");
        oxDb::getDb()->execute("delete from oxcountry where oxid='_testCountry2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddCountry'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddCountryAll'");

        parent::tearDown();
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
     * DeliveryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_main_ajax');
        $this->assertEquals("from " . $this->getCountryViewTable() . " where " . $this->getCountryViewTable() . ".oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_main_ajax');
        $this->assertEquals("from " . $this->getCountryViewTable() . " where " . $this->getCountryViewTable() . ".oxactive = '1'  and " . $this->getCountryViewTable() . ".oxid not in ( select " . $this->getCountryViewTable() . ".oxid from oxobject2delivery left join " . $this->getCountryViewTable() . " on " . $this->getCountryViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcountry' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_main_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getCountryViewTable() . " on " . $this->getCountryViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcountry'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_main_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getCountryViewTable() . " on " . $this->getCountryViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcountry'  and " . $this->getCountryViewTable() . ".oxid not in ( select " . $this->getCountryViewTable() . ".oxid from oxobject2delivery left join " . $this->getCountryViewTable() . " on " . $this->getCountryViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcountry' )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryMainAjax::removeCountryFromDel() test case
     *
     * @return null
     */
    public function testRemoveCountryFromDel()
    {
        $oView = $this->getMock("delivery_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliveryCountry1', '_testDeliveryCountry2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryCountry1', '_testDeliveryCountry2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCountryFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryMainAjax::removeGroupFromDel() test case
     *
     * @return null
     */
    public function testRemoveGroupFromDelAll()
    {
        $sOxid = '_testDeliveryCountryRemoveAll';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join " . $this->getCountryViewTable() . " on " . $this->getCountryViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcountry'";
        $oView = oxNew('delivery_main_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCountryFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryMainAjax::addGroupToDel() test case
     *
     * @return null
     */
    public function testAddCountryToDel()
    {
        $sSynchoxid = '_testActionAddCountry';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("delivery_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addCountryToDel();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryMainAjax::addGroupToDel() test case
     *
     * @return null
     */
    public function testAddCountryToDelAll()
    {
        $sSynchoxid = '_testActionAddCountryAll';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getCountryViewTable() . ".oxid) from " . $this->getCountryViewTable() . " where " . $this->getCountryViewTable() . ".oxactive = '1'  and " . $this->getCountryViewTable() . ".oxid not in ( select " . $this->getCountryViewTable() . ".oxid from oxobject2delivery left join " . $this->getCountryViewTable() . " on " . $this->getCountryViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcountry' )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("delivery_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addCountryToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}