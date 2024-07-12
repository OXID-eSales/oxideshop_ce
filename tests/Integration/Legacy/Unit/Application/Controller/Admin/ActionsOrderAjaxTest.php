<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Actions_Order_Ajax class
 */
class ActionsOrderAjaxTest extends \OxidTestCase
{
    /**
     * ActionsOrderAjax::getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sOxid = '_testOrder';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('actions_order_ajax');

        $sViewTable = $this->getSelectListViewTable();

        $this->assertEquals("from $sViewTable left join oxobject2selectlist on oxobject2selectlist.oxselnid = $sViewTable.oxid where oxobjectid = '$sOxid'", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsOrderAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSorting()
    {
        $oView = oxNew('actions_order_ajax');
        $this->assertEquals("order by oxobject2selectlist.oxsort", trim((string) $oView->getSorting()));
    }

    /**
     * ActionsOrderAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSorting()
    {
        $this->getConfig()->setConfigParam("iDebug", 1);

        $sViewTable = $this->getSelectListViewTable();
        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => "select count( * )  from $sViewTable left join oxobject2selectlist on oxobject2selectlist.oxselnid = $sViewTable.oxid where oxobjectid = '$sOxid'  ", 'records' => [], 'totalRecords' => 0];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsOrderAjax::class, ["output"]);
        $oView->expects($this->any())->method('output')->with($this->equalTo(json_encode($aData)));
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
        $this->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setConfigParam("iDebug", 1);

        $sViewTable = $this->getSelectListViewTable();
        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => "select count( * )  from $sViewTable left join oxobject2selectlist on oxobject2selectlist.oxselnid = $sViewTable.oxid where oxobjectid = '$sOxid'  ", 'records' => [], 'totalRecords' => 0];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsOrderAjax::class, ["output"]);
        $oView->expects($this->any())->method('output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }

    public function getSelectListViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxselectlist_1_de' : 'oxv_oxselectlist_de';
    }
}
