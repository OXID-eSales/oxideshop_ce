<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * Tests for Category_Order_Ajax class
 */
class CategoryOrderAjaxTest extends \PHPUnit\Framework\TestCase
{
    protected $_sArticleView = 'oxv_oxarticles_1_de';

    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';

    protected $_sShopId = '1';

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if ((new Facts())->getEdition() !== 'EE') :
            $this->setArticleViewTable('oxv_oxarticles_de');
            $this->setObject2CategoryViewTable('oxobject2category');
        endif;

        $this->addToDatabase("replace into oxcategories set oxid='_testCategory', oxtitle='_testCategory', oxshopid='" . $this->getShopIdTest() . "'", 'oxcategories');
        $this->addToDatabase("replace into oxobject2category set oxid='_testObject2Category1', oxcatnid='_testCategory', oxobjectid = '_testOxid1'", 'oxobject2category');
        $this->addToDatabase("replace into oxobject2category set oxid='_testObject2Category2', oxcatnid='_testCategory', oxobjectid = '_testOxid2'", 'oxobject2category');

        $this->addToDatabase("replace into oxarticles set oxid='_testObjectRemove1', oxtitle='_testArticle1', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testObjectRemove2', oxtitle='_testArticle2', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testObjectRemove3', oxtitle='_testArticle3', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');

        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove1', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove1'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove2', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove2'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove3', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove3'", 'oxobject2category');

        $this->addTeardownSql("delete from oxobject2category where oxobjectid like '_test%'");
        $this->addTeardownSql("delete from oxarticles where oxid like '_test%'");
        $this->addTeardownSql("delete from oxcategories where oxid like '_test%'");
    }

    public function setArticleViewTable($sParam)
    {
        $this->_sArticleView = $sParam;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function setShopIdTest($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getArticleViewTable()
    {
        return $this->_sArticleView;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }

    public function getShopIdTest()
    {
        return $this->_sShopId;
    }

    /**
     * CategoryOrderAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('category_order_ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where  1 = 0", trim((string) $oView->getQuery()));
    }

    /**
     * CategoryOrderAjax::getQuery() test case
     */
    public function testGetQueryNewOrderSess()
    {
        $aOxid = ['_testOxid1', '_testOxid2'];
        $this->setSessionParam("neworder_sess", $aOxid);
        $sArticleTable = $this->getArticleViewTable();

        $oView = oxNew('category_order_ajax');
        $this->assertSame("from " . $sArticleTable . sprintf(" where  %s.oxid in ( '_testOxid1', '_testOxid2' )", $sArticleTable), trim((string) $oView->getQuery()));
    }

    /**
     * CategoryOrderAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $aOxid = ['_testOxid1', '_testOxid2'];
        $this->setSessionParam("neworder_sess", $aOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sO2CView = $this->getObject2CategoryViewTable();
        $sArticleTable = $this->getArticleViewTable();

        $sReturn = sprintf("from %s left join %s on %s.oxid=%s.oxobjectid where %s.oxcatnid = '_testSynchoxid'", $sArticleTable, $sO2CView, $sArticleTable, $sO2CView, $sO2CView);
        $sReturn .= sprintf(" and %s.oxid not in ( '_testOxid1', '_testOxid2' )", $sArticleTable);

        $oView = oxNew('category_order_ajax');
        $this->assertSame($sReturn, trim((string) $oView->getQuery()));
    }

    /**
     * CategoryOrderAjax::_getSorting() test case
     */
    public function testGetSorting()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = oxNew('category_order_ajax');
        $this->assertSame("order by _0 asc", trim((string) $oView->getSorting()));
    }

    /**
     * CategoryOrderAjax::_getSorting() test case
     */
    public function testGetSortingAfterArticleIds()
    {
        $sArticleTable = $this->getArticleViewTable();
        $aOxid = ['_testOxid1', '_testOxid2'];
        $this->setSessionParam("neworder_sess", $aOxid);
        $oView = oxNew('category_order_ajax');
        $this->assertSame(sprintf("order by  %s.oxid='_testOxid2' ,  %s.oxid='_testOxid1'", $sArticleTable, $sArticleTable), trim((string) $oView->getSorting()));
    }

    /**
     * CategoryOrderAjax::saveNewOrder() test case
     */
    public function testSaveNewOrder()
    {
        $sOxid = '_testCategory';
        $this->setRequestParameter("oxid", $sOxid);
        $aOxid = ['_testOxid1', '_testOxid2'];
        $this->setSessionParam("neworder_sess", $aOxid);
        $this->assertSame(0, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid1'"));
        $this->assertSame(0, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid2'"));

        $oView = oxNew('category_order_ajax');
        $oView->saveNewOrder();
        $this->assertSame(0, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid1'"));
        $this->assertSame(1, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid2'"));
        $this->assertNull($this->getSessionParam("neworder_sess"));
    }

    /**
     * CategoryOrderAjax::remNewOrder() test case
     */
    public function testRemNewOrder()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testCategory';
        $this->setRequestParameter("oxid", $sOxid);
        $aOxid = ['_testOxid1', '_testOxid2'];
        $this->setSessionParam("neworder_sess", $aOxid);
        // updating oxtime values
        $sQ = "update oxobject2category set oxpos = 1 where oxobjectid = '_testOxid1' ";
        $oDb->execute($sQ);
        $sQ = "update oxobject2category set oxpos = 2 where oxobjectid = '_testOxid2' ";
        $oDb->execute($sQ);
        $this->assertSame(1, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid1'"));
        $this->assertSame(2, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid2'"));

        $oView = oxNew('category_order_ajax');
        $oView->remNewOrder();
        $this->assertSame(0, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid1'"));
        $this->assertSame(0, oxDb::getDb()->getOne("select oxpos from oxobject2category where oxobjectid='_testOxid2'"));
    }
}
