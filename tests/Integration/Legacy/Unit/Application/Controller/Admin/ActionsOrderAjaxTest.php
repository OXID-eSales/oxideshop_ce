<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Actions_Order_Ajax class
 */
class ActionsOrderAjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * ActionsOrderAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $sOxid = '_testOrder';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('actions_order_ajax');

        $sViewTable = $this->getSelectListViewTable();

        $this->assertSame(sprintf("from %s left join oxobject2selectlist on oxobject2selectlist.oxselnid = %s.oxid where oxobjectid = '%s'", $sViewTable, $sViewTable, $sOxid), trim((string) $oView->getQuery()));
    }

    /**
     * ActionsOrderAjax::_getSorting() test case
     */
    public function testGetSorting()
    {
        $oView = oxNew('actions_order_ajax');
        $this->assertSame("order by oxobject2selectlist.oxsort", trim((string) $oView->getSorting()));
    }

    /**
     * ActionsOrderAjax::setSorting() test case
     */
    public function testSetSorting()
    {
        $this->getConfig()->setConfigParam("iDebug", 1);

        $sViewTable = $this->getSelectListViewTable();
        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => sprintf("select count( * )  from %s left join oxobject2selectlist on oxobject2selectlist.oxselnid = %s.oxid where oxobjectid = '%s'  ", $sViewTable, $sViewTable, $sOxid), 'records' => [], 'totalRecords' => 0];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsOrderAjax::class, ["output"]);
        $oView->method('output')->with(json_encode($aData));
        $oView->setsorting();
    }

    /**
     * ActionsOrderAjax::setSorting() test case
     */
    public function testSetSortingOxid()
    {
        $sOxid = '_testOrder';
        $this->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setConfigParam("iDebug", 1);

        $sViewTable = $this->getSelectListViewTable();
        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => sprintf("select count( * )  from %s left join oxobject2selectlist on oxobject2selectlist.oxselnid = %s.oxid where oxobjectid = '%s'  ", $sViewTable, $sViewTable, $sOxid), 'records' => [], 'totalRecords' => 0];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsOrderAjax::class, ["output"]);
        $oView->method('output')->with(json_encode($aData));
        $oView->setsorting();
    }

    public function getSelectListViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxselectlist_1_de' : 'oxv_oxselectlist_de';
    }
}
