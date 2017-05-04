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
 * Tests for Delivery_Categories_Ajax class
 */
class Unit_Admin_DeliveryCategoriesAjaxTest extends OxidTestCase
{

    protected $_sCategoriesView = 'oxv_oxcategories_1_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCat1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCat2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCatDeleteAll1', oxdeliveryid='_testDeliveryCatRemoveAll', oxobjectid='_testCategory1', oxtype='oxcategories'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryCatDeleteAll2', oxdeliveryid='_testDeliveryCatRemoveAll', oxobjectid='_testCategory2', oxtype='oxcategories'");



        oxDb::getDb()->execute("insert into oxcategories set oxid='_testCategory1', oxshopid='oxbaseshop', oxtitle='_testCategory1'");
        oxDb::getDb()->execute("insert into oxcategories set oxid='_testCategory2', oxshopid='oxbaseshop', oxtitle='_testCategory2'");

        $this->setCategoriesViewTable('oxv_oxcategories_de');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
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
     * DeliveryCategoriessAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from " . $this->getCategoriesViewTable(), trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryCategoriessAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from " . $this->getCategoriesViewTable() . "  where  " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcategories'  )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryCategoriessAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcategories'", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryCategoriessAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcategories'  and  " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcategories'  )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryCategoriessAjax::removeCatFromDel() test case
     *
     * @return null
     */
    public function testRemoveCatFromDel()
    {
        $oView = $this->getMock("delivery_categories_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDeliveryCat1', '_testDeliveryCat2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDeliveryCat1', '_testDeliveryCat2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCatFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryCategoriessAjax::removeCatFromDel() test case
     *
     * @return null
     */
    public function testRemoveCatFromDelAll()
    {
        $sOxid = '_testDeliveryCatRemoveAll';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = 'oxcategories'";
        $oView = oxNew('delivery_categories_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeCatFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryCategoriessAjax::addCatToDel() test case
     *
     * @return null
     */
    public function testAddCatToDel()
    {
        $sSynchoxid = '_testActionAddCat';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("delivery_categories_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addCatToDel();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryCategoriessAjax::addCatToDel() test case
     *
     * @return null
     */
    public function testAddCatToDelAll()
    {
        $sSynchoxid = '_testActionAddCatAll';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getCategoriesViewTable() . ".oxid) from " . $this->getCategoriesViewTable() . "  where  " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from oxobject2delivery left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2delivery.oxobjectid  where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxcategories'  )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("delivery_categories_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addCatToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}