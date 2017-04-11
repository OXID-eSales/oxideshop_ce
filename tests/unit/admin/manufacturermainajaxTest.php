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
 * Tests for Manufacturer_Main_Ajax class
 */
class Unit_Admin_ManufacturerMainAjaxTest extends OxidTestCase
{

    protected $_sArticleView = 'oxv_oxarticles_1_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';
    protected $_sShopId = '1';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setShopIdTest('oxbaseshop');
        $this->setArticleViewTable('oxv_oxarticles_de');
        $this->setObject2CategoryViewTable('oxobject2category');

        $this->addToDatabase("insert into oxarticles set oxid='_testArticle1', oxtitle='_testArticle1', oxmanufacturerid='_testRemove1'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle2', oxtitle='_testArticle2', oxmanufacturerid='_testRemove2'", 'oxarticles');

        $this->addToDatabase("insert into oxarticles set oxid='_testArticle3', oxtitle='_testArticle3', oxmanufacturerid='_testRemoveAll'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle4', oxtitle='_testArticle4', oxmanufacturerid='_testRemoveAll'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testArticle5', oxtitle='_testArticle5', oxmanufacturerid='_testRemoveAll'", 'oxarticles');

        $this->addToDatabase("insert into oxmanufacturers set oxid='_testManufacturer1', oxtitle='_testManufacturer1'", 'oxmanufacturers');
        $this->addToDatabase("insert into oxmanufacturers set oxid='_testManufacturer2', oxtitle='_testManufacturer2'", 'oxmanufacturers');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle1'" );
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle2'" );

        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle3'" );
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle4'" );
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle5'" );

        oxDb::getDb()->execute( "delete from oxmanufacturers where oxid='_testManufacturer1'" );
        oxDb::getDb()->execute( "delete from oxmanufacturers where oxid='_testManufacturer2'" );

        parent::tearDown();
    }

    public function setArticleViewTable($sParam)
    {
        $this->_sArticleView = $sParam;
    }

    public function getArticleViewTable()
    {
        return $this->_sArticleView;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
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
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxshopid=\"" . $this->getShopIdTest() . "\" and 1  and " . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxmanufacturerid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setRequestParameter("blVariantsSelection", true);
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxshopid=\"" . $this->getShopIdTest() . "\" and 1", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxshopid=\"" . $this->getShopIdTest() . "\" and 1  and " . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("blVariantsSelection", true);
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxshopid=\"" . $this->getShopIdTest() . "\" and 1", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxmanufacturerid = '" . $sOxid . "' and " . $this->getArticleViewTable() . ".oxparentid = ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid = " . $this->getObject2CategoryViewTable() . ".oxobjectid where " . $this->getArticleViewTable() . ".oxshopid=\"" . $this->getShopIdTest() . "\" and " . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "' and " . $this->getArticleViewTable() . ".oxparentid = ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxidVariantsSelection()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("blVariantsSelection", true);

        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  ( " . $this->getArticleViewTable() . ".oxid = " . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticleViewTable() . ".oxparentid = " . $this->getObject2CategoryViewTable() . ".oxobjectid )where " . $this->getArticleViewTable() . ".oxshopid=\"" . $this->getShopIdTest() . "\" and " . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * ManufacturerMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("", trim($oView->UNITaddFilter('')));
    }

    /**
     * ManufacturerMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelection()
    {
        $this->getConfig()->setRequestParameter("blVariantsSelection", true);
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter('')));
    }

    /**
     * ManufacturerMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelection2()
    {
        $this->getConfig()->setRequestParameter("blVariantsSelection", true);
        $oView = oxNew('manufacturer_main_ajax');
        $this->assertEquals("select count( * ) group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter('select count( * )')));
    }

    /**
     * ManufacturerMainAjax::removeManufacturer() test case
     *
     * @return null
     */
    public function testRemoveManufacturer()
    {
        $oView = $this->getMock("manufacturer_main_ajax", array("_getActionIds", "resetCounter"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle1', '_testArticle2')));
        $oView->expects($this->once())->method('resetCounter')->with($this->equalTo("manufacturerArticle"));
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid in('_testRemove1', '_testRemove2')"));
        $oView->removeManufacturer();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid in('_testRemove1', '_testRemove2')"));
    }

    /**
     * ManufacturerMainAjax::removeManufacturer() test case
     *
     * @return null
     */
    public function testRemoveManufacturerAll()
    {
        $sOxid = '_testRemoveAll';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '_testRemoveAll'"));

        $oView = $this->getMock("manufacturer_main_ajax", array("resetCounter"));
        $oView->expects($this->once())->method('resetCounter')->with($this->equalTo("manufacturerArticle"), $this->equalTo($sOxid));
        $oView->removeManufacturer();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '_testRemoveAll'"));
    }

    /**
     * ManufacturerMainAjax::addManufacturer() test case
     *
     * @return null
     */
    public function testAddManufacturer()
    {
        $sSynchoxid = '_testAddManufacturer';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = $this->getMock("manufacturer_main_ajax", array("_getActionIds", "resetCounter"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle1', '_testArticle2')));
        $oView->expects($this->once())->method('resetCounter')->with($this->equalTo("manufacturerArticle"));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));
        $oView->addManufacturer();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));
    }

    /**
     * ManufacturerMainAjax::addManufacturer() test case
     *
     * @return null
     */
    public function testAddManufacturerAll()
    {
        $sSynchoxid = '_testAddManufacturerAll';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne(" select count(oxid) from " . $this->getArticleViewTable() . " where " . $this->getArticleViewTable() . ".oxshopid='" . $this->getShopIdTest() . "' and 1  and " . $this->getArticleViewTable() . ".oxparentid = '' and " . $this->getArticleViewTable() . ".oxmanufacturerid != '" . $sSynchoxid . "'");
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));

        $oView = $this->getMock("manufacturer_main_ajax", array("resetCounter"));
        $oView->expects($this->once())->method('resetCounter')->with($this->equalTo("manufacturerArticle"), $this->equalTo($sSynchoxid));
        $oView->addManufacturer();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where oxmanufacturerid = '" . $sSynchoxid . "'"));
    }

}
