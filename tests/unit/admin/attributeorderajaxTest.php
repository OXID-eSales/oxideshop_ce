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
 * Tests for Attribute_Order_Ajax class
 */
class Unit_Admin_AttributeOrderAjaxTest extends OxidTestCase
{

    protected $_sArticleView = 'oxv_oxarticles_1_de';
    protected $_sObject2AttributeView = 'oxv_oxobject2attribute_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_de';
    protected $_sShopId = '1';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxcategory2attribute set oxid='_testOxid1', oxobjectid='_testObject', oxattrid='_testAttribute', oxsort='99'");
        oxDb::getDb()->execute("insert into oxcategory2attribute set oxid='_testOxid2', oxobjectid='_testObject', oxattrid='_testAttribute', oxsort='99'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxcategory2attribute where oxobjectid='_testObject'");

        parent::tearDown();
    }

    /**
     * AttributeOrderAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sOxid = '_testOxid';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('attribute_order_ajax');


        $this->assertEquals("from oxv_oxattribute_de left join oxcategory2attribute on oxcategory2attribute.oxattrid = oxv_oxattribute_de.oxid where oxobjectid = '$sOxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeOrderAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSorting()
    {
        $oView = oxNew('attribute_order_ajax');
        $this->assertEquals("order by oxcategory2attribute.oxsort", trim($oView->UNITgetSorting()));
    }

    /**
     * AttributeOrderAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSorting()
    {
        modconfig::getInstance()->setConfigParam("iDebug", 1);


        $sViewTable = "oxv_oxattribute_de";

        $aData = array('startIndex' => 0, 'sort' => _0, 'dir' => asc, 'countsql' => "select count( * )  from $sViewTable left join oxcategory2attribute on oxcategory2attribute.oxattrid = $sViewTable.oxid where oxobjectid = '$sOxid' ", 'records' => array(), 'totalRecords' => 0);

        $oView = $this->getMock("attribute_order_ajax", array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }

    /**
     * AttributeOrderAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSortingOxid()
    {
        $sOxid = '_testObject';
        modConfig::setRequestParameter("oxid", $sOxid);
        modconfig::getInstance()->setConfigParam("iDebug", 1);
        modConfig::setRequestParameter("sortoxid", 0);


        $sViewTable = "oxv_oxattribute_de";

        $aData = array('startIndex' => 0, 'sort' => _0, 'dir' => asc, 'countsql' => "select count( * )  from $sViewTable left join oxcategory2attribute on oxcategory2attribute.oxattrid = $sViewTable.oxid where oxobjectid = '$sOxid' ", 'records' => array(), 'totalRecords' => 0);

        $oView = $this->getMock("attribute_order_ajax", array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();

        $this->assertEquals(1, oxDb::getDb()->getOne("select sum(oxsort) from oxcategory2attribute where oxobjectid='_testObject'"));
    }

}
