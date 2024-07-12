<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * Tests for Attribute_Order_Ajax class
 */
class AttributeOrderAjaxTest extends \PHPUnit\Framework\TestCase
{
    protected $_sArticleView = 'oxv_oxarticles_1_de';

    protected $_sObject2AttributeView = 'oxv_oxobject2attribute_de';

    protected $_sObject2CategoryView = 'oxv_oxobject2category_de';

    protected $_sShopId = '1';

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxcategory2attribute set oxid='_testOxid1', oxobjectid='_testObject', oxattrid='_testAttribute', oxsort='99'");
        oxDb::getDb()->execute("insert into oxcategory2attribute set oxid='_testOxid2', oxobjectid='_testObject', oxattrid='_testAttribute', oxsort='99'");
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxcategory2attribute where oxobjectid='_testObject'");

        parent::tearDown();
    }

    /**
     * AttributeOrderAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $sOxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('attribute_order_ajax');
        $sViewTable = $this->getVieTableName();

        $this->assertSame(sprintf("from %s left join oxcategory2attribute on oxcategory2attribute.oxattrid = %s.oxid where oxobjectid = '%s'", $sViewTable, $sViewTable, $sOxid), trim((string) $oView->getQuery()));
    }

    /**
     * AttributeOrderAjax::getSorting() test case
     */
    public function testGetSorting()
    {
        $oView = oxNew('attribute_order_ajax');
        $this->assertSame("order by oxcategory2attribute.oxsort", trim((string) $oView->getSorting()));
    }

    /**
     * AttributeOrderAjax::setSorting() test case
     */
    public function testSetSorting()
    {
        $this->getConfig()->setConfigParam("iDebug", 1);

        $sViewTable = $this->getVieTableName();

        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => sprintf("select count( * )  from %s left join oxcategory2attribute on oxcategory2attribute.oxattrid = %s.oxid where oxobjectid = '%s' ", $sViewTable, $sViewTable, $sOxid), 'records' => [], 'totalRecords' => 0];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AttributeOrderAjax::class, ["output"]);
        $oView->method('output')->with(json_encode($aData));
        $oView->setsorting();
    }

    /**
     * AttributeOrderAjax::setSorting() test case
     */
    public function testSetSortingOxid()
    {
        $sOxid = '_testObject';
        $this->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setConfigParam("iDebug", 1);
        $this->setRequestParameter("sortoxid", 0);

        $sViewTable = $this->getVieTableName();

        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => sprintf("select count( * )  from %s left join oxcategory2attribute on oxcategory2attribute.oxattrid = %s.oxid where oxobjectid = '%s' ", $sViewTable, $sViewTable, $sOxid), 'records' => [], 'totalRecords' => 0];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AttributeOrderAjax::class, ["output"]);
        $oView->method('output')->with(json_encode($aData));
        $oView->setsorting();

        $this->assertSame(1, oxDb::getDb()->getOne("select sum(oxsort) from oxcategory2attribute where oxobjectid='_testObject'"));
    }

    /**
     * @return string
     */
    private function getVieTableName()
    {
        if ((new Facts())->getEdition() === 'EE') {
            return "oxv_oxattribute_1_de";
        }

        return "oxv_oxattribute_de";
    }
}
