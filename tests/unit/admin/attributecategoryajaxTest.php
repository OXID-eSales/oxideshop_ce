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
 * Tests for Attribute_Category_Ajax class
 */
class Unit_Admin_AttributeCategoryAjaxTest extends OxidTestCase
{

    protected $_sCategoryView = 'oxv_oxcategories_1_de';
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
        $this->setCategoryViewTable('oxv_oxcategories_de');
        $this->addToDatabase("replace into oxcategories set oxid='_testCategory', oxtitle='_testCategory', oxshopid='" . $this->getgetShopIdTest() . "', oxactive=1", 'oxcategories');
        $this->addToDatabase("replace into oxattribute set oxid='_testAttribute', oxtitle='_testAttribute', oxshopid='" . $this->getgetShopIdTest() . "'", 'oxattribute');
        $this->addToDatabase("replace into oxattribute set oxid='_testAttributeAll', oxtitle='_testAttributeAll', oxshopid='" . $this->getgetShopIdTest() . "'", 'oxattribute');
        $this->addTeardownSql("delete from oxcategories where oxid='_testCategory'");
        $this->addTeardownSql("delete from oxattribute where oxid like '%_testAttribute%'");
        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid1', oxobjectid='_testRemove'", 'oxcategory2attribute');
        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid2', oxobjectid='_testRemove'", 'oxcategory2attribute');

        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid3', oxobjectid='_testCategory', oxattrid='_testRemoveAll'", 'oxcategory2attribute');
        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid4', oxobjectid='_testCategory', oxattrid='_testRemoveAll'", 'oxcategory2attribute');
        $this->addTeardownSql("delete from oxcategory2attribute where oxid like '%_testOxid%'");
    }

    public function setCategoryViewTable($sParam)
    {
        $this->_sCategoryView = $sParam;
    }

    public function setShopIdTest($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getCategoryViewTable()
    {
        return $this->_sCategoryView;
    }

    public function getgetShopIdTest()
    {
        return $this->_sShopId;
    }

    /**
     * AttributeCategoryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('attribute_category_ajax');
        $this->assertEquals("from " . $this->getCategoryViewTable() . " where " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getgetShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeCategoryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('attribute_category_ajax');
        $this->assertEquals("from " . $this->getCategoryViewTable() . " where " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getgetShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'  and " . $this->getCategoryViewTable() . ".oxid not in ( select " . $this->getCategoryViewTable() . ".oxid from " . $this->getCategoryViewTable() . " left join oxcategory2attribute on " . $this->getCategoryViewTable() . ".oxid=oxcategory2attribute.oxobjectid  where oxcategory2attribute.oxattrid = '$sSynchoxid' and " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getgetShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1' )", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeCategoryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('attribute_category_ajax');
        $this->assertEquals("from " . $this->getCategoryViewTable() . " left join oxcategory2attribute on " . $this->getCategoryViewTable() . ".oxid=oxcategory2attribute.oxobjectid  where oxcategory2attribute.oxattrid = '$sOxid' and " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getgetShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeCategoryAjax::removeCatFromAttr() test case
     *
     * @return null
     */
    public function testRemoveCatFromAttr()
    {
        $oDb = oxDb::getDb();

        $oView = $this->getMock("attribute_category_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testOxid1', '_testOxid2')));

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxobjectid='_testRemove'"));
        $oView->removeCatFromAttr();
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxobjectid='_testRemove'"));
    }

    /**
     * AttributeCategoryAjax::removeCatFromAttr() test case
     *
     * @return null
     */
    public function testRemoveCatFromAttrAll()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testRemoveAll';
        modConfig::setRequestParameter("oxid", $sOxid);
        modConfig::setRequestParameter("all", true);

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxattrid='_testRemoveAll' "));
        $oView = oxNew('attribute_category_ajax');
        $oView->removeCatFromAttr();
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxattrid='_testRemoveAll' "));
    }

    /**
     * AttributeCategoryAjax::addCatToAttr() test case
     *
     * @return null
     */
    public function testAddCatToAttr()
    {
        $oView = $this->getMock("attribute_category_ajax", array("_getActionIds"));
        $sSynchoxid = '_testAttribute';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testAdd1', '_testAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
        $oView->addCatToAttr();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
    }

    /**
     * AttributeCategoryAjax::addCatToAttr() test case
     *
     * @return null
     */
    public function testAddCatToAttrAll()
    {
        $sSynchoxid = '_testAttributeAll';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modConfig::setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getCategoryViewTable() . ".oxid)  from " . $this->getCategoryViewTable() . " where " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getgetShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'  and " . $this->getCategoryViewTable() . ".oxid not in ( select " . $this->getCategoryViewTable() . ".oxid from " . $this->getCategoryViewTable() . " left join oxcategory2attribute on " . $this->getCategoryViewTable() . ".oxid=oxcategory2attribute.oxobjectid  where oxcategory2attribute.oxattrid = '$sSynchoxid' and " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getgetShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1' )");

        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
        $oView = oxNew('attribute_category_ajax');
        $oView->addCatToAttr();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
    }
}
