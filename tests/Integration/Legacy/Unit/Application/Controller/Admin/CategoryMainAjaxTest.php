<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * Tests for Category_Main_Ajax class
 */
class CategoryMainAjaxTest extends \OxidTestCase
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

        if ((new Facts())->getEdition() !== 'EE') {
            $this->setArticleViewTable('oxv_oxarticles_de');
            $this->setObject2CategoryViewTable('oxobject2category');
        }

        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove1', oxtitle='_testArticle1', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove2', oxtitle='_testArticle2', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');

        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove1', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove1'", 'oxcategories');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove2', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove2'", 'oxcategories');

        $this->addToDatabase(
            "insert into oxarticles set
                                            oxid='_testObjectRemoveChild1',
                                            oxparentid='_testObjectRemove1',
                                            oxtitle='_testArticleChild1',
                                            oxshopid='" . $this->getShopIdTest() . "'",
            'oxarticles'
        );
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemoveChild1', oxcatnid='_testCategory', oxobjectid = '_testObjectRemoveChild1'", 'oxcategories');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->addTeardownSql("delete from oxarticles where oxid like '_test%'");
        $this->addTeardownSql("delete from oxobject2category where oxobjectid like '_test%'");

        parent::tearDown();
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
     * CategoryMainAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('category_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " join " . $this->getArticleViewTable() . "  on  " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid  where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '' and " . $this->getArticleViewTable() . ".oxid is not null", trim((string) $oView->getQuery()));
    }

    /**
     * CategoryMainAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sArticleTable = $this->getArticleViewTable();
        $sO2CView = $this->getObject2CategoryViewTable();

        $oView = oxNew('category_main_ajax');
        $sQuery = "from " . $sO2CView . " join " . $sArticleTable . "  on  " . $sArticleTable . ".oxid=" . $sO2CView . ".oxobjectid";
        $sQuery .= "  where " . $sO2CView . ".oxcatnid = '_testOxid' and " . $sArticleTable . ".oxid is not null";
        $sQuery .= "  and " . $sArticleTable . sprintf('.oxid not in ( select %s.oxid from %s left join %s ', $sArticleTable, $sO2CView, $sArticleTable);
        $sQuery .= sprintf('on  %s.oxid=%s.oxobjectid  where %s.oxcatnid =  \'_testSynchoxid\' and ', $sArticleTable, $sO2CView, $sO2CView) . $sArticleTable . ".oxid is not null )";
        $this->assertEquals($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * CategoryMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('category_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1", trim((string) $oView->getQuery()));
    }

    /**
     * CategoryMainAjax::removeArticle() test case
     */
    public function testRemoveArticle()
    {
        $sOxid = '_testCategory';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\CategoryMainAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testObjectRemove1']));
        $this->assertEquals(3, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sOxid)));

        $oView->removeArticle();
        $this->assertEquals(1, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sOxid)));
    }

    /**
     * CategoryMainAjax::removeArticle() test case
     */
    public function testRemoveArticleAll()
    {
        $sOxid = '_testCategory';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sOxid)));

        $oView = oxNew('category_main_ajax');
        $oView->removeArticle();
        $this->assertEquals(0, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sOxid)));
    }

    /**
     * CategoryMainAjax::addArticle() test case
     */
    public function testAddArticle()
    {
        $sSynchoxid = '_testCategory';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\CategoryMainAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testArticleAdd1', '_testArticleAdd2']));
        $this->assertEquals(3, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sSynchoxid)));

        $oView->addArticle();
        $this->assertEquals(5, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sSynchoxid)));
    }

    /**
     * CategoryMainAjax::addArticle() test case
     */
    public function testAddArticleAll()
    {
        $sSynchoxid = '_testCategoryNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxparentid = '' and oxshopid='" . $this->getShopIdTest() . "'");

        $oView = oxNew('category_main_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sSynchoxid)));

        $oView->addArticle();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2category where oxcatnid=\'%s\'', $sSynchoxid)));
    }

    /**
     * CategoryMainAjax::updateOxTime() test case
     */
    public function testUpdateOxTime()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testObjectRemove1';

        // updating oxtime values
        $sQ = sprintf('update oxobject2category set oxtime = 1 where oxobjectid = \'%s\' ', $sOxid);
        $oDb->execute($sQ);

        $oView = oxNew('category_main_ajax');
        $oView->updateOxTime($oDb->quote($sOxid));
        $this->assertEquals(1, $oDb->getOne(sprintf('select count(oxid) from oxobject2category where oxtime=0 and oxobjectid = \'%s\'', $sOxid)));
    }
}
