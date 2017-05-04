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
 * Tests for Actions_Order_Ajax class
 */
class Unit_Admin_ActionsOrderAjaxTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * ActionsOrderAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sOxid = '_testOrder';
        modConfig::setRequestParameter("oxid", $sOxid);
        $oView = oxNew('actions_order_ajax');


        $this->assertEquals("from oxv_oxselectlist_de left join oxobject2selectlist on oxobject2selectlist.oxselnid = oxv_oxselectlist_de.oxid where oxobjectid = '$sOxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsOrderAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSorting()
    {
        $oView = oxNew('actions_order_ajax');
        $this->assertEquals("order by oxobject2selectlist.oxsort", trim($oView->UNITgetSorting()));
    }

    /**
     * ActionsOrderAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSorting()
    {
        modconfig::getInstance()->setConfigParam("iDebug", 1);


        $sViewTable = "oxv_oxselectlist_de";

        $aData = array('startIndex' => 0, 'sort' => _0, 'dir' => asc, 'countsql' => "select count( * )  from $sViewTable left join oxobject2selectlist on oxobject2selectlist.oxselnid = $sViewTable.oxid where oxobjectid = '$sOxid'  ", 'records' => array(), 'totalRecords' => 0);

        $oView = $this->getMock("actions_order_ajax", array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }

    /**
     * ActionsOrderAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSortingOxid()
    {
        $sOxid = '_testOrder';
        modConfig::setRequestParameter("oxid", $sOxid);
        modconfig::getInstance()->setConfigParam("iDebug", 1);


        $sViewTable = "oxv_oxselectlist_de";

        $aData = array('startIndex' => 0, 'sort' => _0, 'dir' => asc, 'countsql' => "select count( * )  from $sViewTable left join oxobject2selectlist on oxobject2selectlist.oxselnid = $sViewTable.oxid where oxobjectid = '$sOxid'  ", 'records' => array(), 'totalRecords' => 0);

        $oView = $this->getMock("actions_order_ajax", array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }


}