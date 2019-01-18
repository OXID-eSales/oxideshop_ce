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
     *
     * @return null
     */
    protected function setUp()
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
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = '$sSynchoxid' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = '$sSynchoxid' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '$sOxid' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid  where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '$sOxid' and " . $this->getArticleViewTable() . ".oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = '$sSynchoxid' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxidVariantsSelection()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  ( " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticleViewTable() . ".oxparentid=" . $this->getObject2CategoryViewTable() . ".oxobjectid)  where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '$sOxid' and " . $this->getArticleViewTable() . ".oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = '$sSynchoxid' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("", trim($oView->UNITaddFilter('')));
    }

    /**
     * ActionsMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelection()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter('')));
    }

    /**
     * ActionsMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelection2()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("select count( * ) from ( select count( * ) group by " . $this->getArticleViewTable() . ".oxid  ) as _cnttable", trim($oView->UNITaddFilter('select count( * )')));
    }

    /**
     * ActionsMainAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSorting()
    {
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("order by _0 asc", trim($oView->UNITgetSorting()));
    }

    /**
     * ActionsMainAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSortingOxid()
    {
        $this->setRequestParameter("oxid", 'oxid');
        $oView = oxNew('actions_main_ajax');
        $this->assertEquals("order by oxactions2article.oxsort", trim($oView->UNITgetSorting()));
    }

    /**
     * ActionsMainAjax::removeArtFromAct() test case
     *
     * @return null
     */
    public function testRemoveArtFromAct()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));
        $oView->removeartfromact();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));
    }

    /**
     * ActionsMainAjax::removeArtFromAct() test case
     *
     * @return null
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
     * Check, that the method 'removeArtFromAct' expires the file cache.
     */
    public function testRemoveArtFromActExpiresFileCache()
    {
        $oRssFeed = $this->getMock(\OxidEsales\Eshop\Application\Model\RssFeed::class, array('removeCacheFile'));
        $oRssFeed->expects($this->once())->method('removeCacheFile');

        $oActionsMainAjax = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, array('_getOxRssFeed'));

        $oActionsMainAjax->expects($this->once())
            ->method('_getOxRssFeed')
            ->will($this->returnValue($oRssFeed));

        $oActionsMainAjax->removeArtFromAct();
    }

    /**
     * ActionsMainAjax::addArtToAct() test case
     *
     * @return null
     */
    public function testAddArtToAct()
    {
        $sSynchoxid = '_testActionAddAct';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='$sSynchoxid'"));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addarttoact();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='$sSynchoxid'"));
    }

    /**
     * Check, that the method 'addArtToAct' expires the file cache.
     */
    public function testAddArtToActExpiresFileCache()
    {
        $oActionsMainAjax = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, array('addArtToAct'));

        $oActionsMainAjax->expects($this->once())
            ->method('addArtToAct');

        $oActionsMainAjax->addArtToAct();
    }

    /**
     * ActionsMainAjax::addArtToAct() test case
     *
     * @return null
     */
    public function testAddArtToActAll()
    {
        $sSynchoxid = '_testActionAddAct';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getArticleViewTable() . ".oxid)  from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in ( select oxactions2article.oxartid from oxactions2article  where oxactions2article.oxactionid = '$sSynchoxid' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' )");

        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='$sSynchoxid'"));

        $oView = oxNew('actions_main_ajax');
        $oView->addarttoact();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='$sSynchoxid'"));
    }

    /**
     * ActionsMainAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSorting()
    {
        $aData = array('startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => "select count( * )  from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '_testSetSorting' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' ", 'records' => array(), 'totalRecords' => 0);

        $this->getConfig()->setConfigParam("iDebug", 1);
        $sOxid = '_testSetSorting';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }

    /**
     * ActionsMainAjax::setSorting() test case
     *
     * @return null
     */
    public function testSetSortingOxid()
    {
        $sOxid = '_testActionAddAct';
        $this->setRequestParameter("oxid", $sOxid);
        $aData = array('startIndex' => 0, 'sort' => '_0', 'dir' => 'asc', 'countsql' => "select count( * )  from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '_testSetSorting' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' ", 'records' => array(), 'totalRecords' => 0);

        $sOxid = '_testSetSorting';
        $this->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setConfigParam("iDebug", 1);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class, array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
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
