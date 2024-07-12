<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Actions_List class
 */
class ActionsMainAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        $this->addToDatabase("replace into oxarticles set oxid='_testArticle1', oxshopid='" . $this->getShopIdTest() . "', oxtitle='_testArticle1'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testArticle2', oxshopid='" . $this->getShopIdTest() . "', oxtitle='_testArticle2'", 'oxarticles');

        parent::setUp();

        oxDb::getDb()->execute("replace into oxactions2article set oxid='_testActionAdd1', oxactionid='_testActionAdd', oxshopid='" . $this->getShopIdTest() . "', oxartid='_testArticle1'");
        oxDb::getDb()->execute("replace into oxactions2article set oxid='_testActionAdd2', oxactionid='_testActionAdd', oxshopid='" . $this->getShopIdTest() . "', oxartid='_testArticle2'");

        $this->addTeardownSql("delete from oxarticles where oxid like '%_testArt%'");
        $this->addTeardownSql("delete from oxactions2article where oxactionid like '%_testActionAdd%'");
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . sprintf('.oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = \'%s\' and oxactions2article.oxshopid = \'', $sSynchoxid) . $this->getShopIdTest() . "' )", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQuerySynchoxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . sprintf('.oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = \'%s\' and oxactions2article.oxshopid = \'', $sSynchoxid) . $this->getShopIdTest() . "' )", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . sprintf('.oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = \'%s\' and oxactions2article.oxshopid = \'', $sOxid) . $this->getShopIdTest() . "'", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid  where " . $this->getObject2CategoryViewTable() . sprintf('.oxcatnid = \'%s\' and ', $sOxid) . $this->getArticleViewTable() . sprintf('.oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = \'%s\' and oxactions2article.oxshopid = \'', $sSynchoxid) . $this->getShopIdTest() . "' )", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxidVariantsSelection()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  ( " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticleViewTable() . ".oxparentid=" . $this->getObject2CategoryViewTable() . ".oxobjectid)  where " . $this->getObject2CategoryViewTable() . sprintf('.oxcatnid = \'%s\' and ', $sOxid) . $this->getArticleViewTable() . sprintf('.oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = \'%s\' and oxactions2article.oxshopid = \'', $sSynchoxid) . $this->getShopIdTest() . "' )", trim((string) $oView->getQuery()));
    }

    /**
     * ActionsMainAjax::_addFilter() test case
     */
    public function testAddFilter()
    {
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("", trim((string) $oView->addFilter('')));
    }

    /**
     * ActionsMainAjax::_addFilter() test case
     */
    public function testAddFilterVariantsSelection()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("group by " . $this->getArticleViewTable() . ".oxid", trim((string) $oView->addFilter('')));
    }

    /**
     * ActionsMainAjax::_addFilter() test case
     */
    public function testAddFilterVariantsSelection2()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("select count( * ) from ( select count( * ) group by " . $this->getArticleViewTable() . ".oxid  ) as _cnttable", trim((string) $oView->addFilter('select count( * )')));
    }

    /**
     * ActionsMainAjax::_getSorting() test case
     */
    public function testGetSorting()
    {
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("order by _0 asc", trim((string) $oView->getSorting()));
    }

    /**
     * ActionsMainAjax::_getSorting() test case
     */
    public function testGetSortingOxid()
    {
        $this->setRequestParameter("oxid", 'oxid');
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("order by oxactions2article.oxsort", trim((string) $oView->getSorting()));
    }

    /**
     * ActionsMainAjax::removeArtFromAct() test case
     */
    public function testRemoveArtFromAct()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testActionAdd1', '_testActionAdd2']));
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));
        $oView->removeartfromact();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));
    }

    /**
     * ActionsMainAjax::removeArtFromAct() test case
     */
    public function testRemoveArtFromActAll()
    {
        $this->setRequestParameter("all", true);

        $sOxid = '_testActionAdd';
        $this->setRequestParameter("oxid", $sOxid);

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));

        /** @var \OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax $oView */
        $oView = oxNew('actions_main_ajax');
        $oView->removeartfromact();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));
    }

    /**
     * ActionsMainAjax::addArtToAct() test case
     */
    public function testAddArtToAct()
    {
        $sSynchoxid = '_testActionAddAct';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $this->assertEquals(0, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxactions2article where oxactionid=\'%s\'', $sSynchoxid)));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testActionAdd1', '_testActionAdd2']));

        $oView->addarttoact();
        $this->assertEquals(2, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxactions2article where oxactionid=\'%s\'', $sSynchoxid)));
    }

    /**
     * Check, that the method 'addArtToAct' expires the file cache.
     */
    public function testAddArtToActExpiresFileCache()
    {
        $oActionsMainAjax = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, ['addArtToAct']);

        $oActionsMainAjax->expects($this->once())
            ->method('addArtToAct');

        $oActionsMainAjax->addArtToAct();
    }

    /**
     * ActionsMainAjax::addArtToAct() test case
     */
    public function testAddArtToActAll()
    {
        $sSynchoxid = '_testActionAddAct';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getArticleViewTable() . ".oxid)  from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . sprintf('.oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = \'%s\' and oxactions2article.oxshopid = \'', $sSynchoxid) . $this->getShopIdTest() . "' )");

        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxactions2article where oxactionid=\'%s\'', $sSynchoxid)));

        $oView = oxNew('actions_main_ajax');
        $oView->addarttoact();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxactions2article where oxactionid=\'%s\'', $sSynchoxid)));
    }

    /**
     * ActionsMainAjax::setSorting() test case
     */
    public function testSetSorting()
    {
        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => "select count( * )  from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '_testSetSorting' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' ", 'records' => [], 'totalRecords' => 0];

        $this->getConfig()->setConfigParam("iDebug", 1);
        $sOxid = '_testSetSorting';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, ["output"]);
        $oView->expects($this->any())->method('output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }

    /**
     * ActionsMainAjax::setSorting() test case
     */
    public function testSetSortingOxid()
    {
        $sOxid = '_testActionAddAct';
        $this->setRequestParameter("oxid", $sOxid);
        $aData = ['startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => "select count( * )  from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '_testSetSorting' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' ", 'records' => [], 'totalRecords' => 0];

        $sOxid = '_testSetSorting';
        $this->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setConfigParam("iDebug", 1);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, ["output"]);
        $oView->expects($this->any())->method('output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }

    public function getArticleViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxarticles_1_de' : 'oxv_oxarticles_de';
    }

    public function getObject2CategoryViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }

    public function getShopIdTest()
    {
        return 1;
    }
}
