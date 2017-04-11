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
 * Tests for Actions_List class
 */
class Unit_Admin_ActionsMainAjaxTest extends OxidTestCase
{

    protected $_sArticleView = 'oxv_oxarticles_1_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';
    protected $_sShopId = '1';


    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $this->setArticleViewTable('oxv_oxarticles_de');
        $this->setObject2CategoryViewTable('oxobject2category');
        $this->setShopIdTest('oxbaseshop');

        $this->addToDatabase("replace into oxarticles set oxid='_testArticle1', oxshopid='" . $this->getShopIdTest() . "', oxtitle='_testArticle1'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testArticle2', oxshopid='" . $this->getShopIdTest() . "', oxtitle='_testArticle2'", 'oxarticles');

        parent::setUp();

        oxDb::getDb()->execute("replace into oxactions2article set oxid='_testActionAdd1', oxactionid='_testActionAdd', oxshopid='" . $this->getShopIdTest() . "', oxartid='_testArticle1'");
        oxDb::getDb()->execute("replace into oxactions2article set oxid='_testActionAdd2', oxactionid='_testActionAdd', oxshopid='" . $this->getShopIdTest() . "', oxartid='_testArticle2'");

        $this->addTeardownSql("delete from oxarticles where oxid like '%_testArt%'");
        $this->addTeardownSql("delete from oxactions2article where oxactionid like '%_testActionAdd%'");
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
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);
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
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
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
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);
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
        modConfig::setRequestParameter("oxid", $sOxid);
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
        modConfig::setRequestParameter("oxid", $sOxid);
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

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
        modConfig::setRequestParameter("oxid", $sOxid);
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);

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
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);
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
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);
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
        modConfig::setRequestParameter("oxid", 'oxid');
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
        $oView = $this->getMock("actions_main_ajax", array("_getActionIds"));
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
        modConfig::setRequestParameter("all", true);

        $sOxid = '_testActionAdd';
        modConfig::setRequestParameter("oxid", $sOxid);

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));

        /** @var actions_main_ajax $oView */
        $oView = oxNew('actions_main_ajax');
        $oView->removeartfromact();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='_testActionAdd'"));
    }

    /**
     * Check, that the method 'removeArtFromAct' expires the file cache.
     */
    public function testRemoveArtFromActExpiresFileCache()
    {
        $oRssFeed = $this->getMock('oxRssFeed', array('removeCacheFile'));
        $oRssFeed->expects($this->once())->method('removeCacheFile');

        $oActionsMainAjax = $this->getMock('actions_main_ajax', array('_getOxRssFeed'));

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
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='$sSynchoxid'"));

        $oView = $this->getMock("actions_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addarttoact();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxactions2article where oxactionid='$sSynchoxid'"));
    }

    /**
     * Check, that the method 'addArtToAct' expires the file cache.
     */
    public function testAddArtToActExpiresFileCache()
    {
        $oActionsMainAjax = $this->getMock('actions_main_ajax', array('addArtToAct'));

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
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modConfig::setRequestParameter("all", true);

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
        $aData = array('startIndex' => 0, 'sort' => _0, 'dir' => asc, 'countsql' => "select count( * )  from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '_testSetSorting' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' ", 'records' => array(), 'totalRecords' => 0);

        modconfig::getInstance()->setConfigParam("iDebug", 1);
        $sOxid = '_testSetSorting';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = $this->getMock("actions_main_ajax", array("_output"));
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
        modConfig::setRequestParameter("oxid", $sOxid);
        $aData = array('startIndex' => 0, 'sort' => _0, 'dir' => asc, 'countsql' => "select count( * )  from " . $this->getArticleViewTable() . " left join oxactions2article on " . $this->getArticleViewTable() . ".oxid=oxactions2article.oxartid  where oxactions2article.oxactionid = '_testSetSorting' and oxactions2article.oxshopid = '" . $this->getShopIdTest() . "' ", 'records' => array(), 'totalRecords' => 0);

        $sOxid = '_testSetSorting';
        modConfig::setRequestParameter("oxid", $sOxid);
        modconfig::getInstance()->setConfigParam("iDebug", 1);

        $oView = $this->getMock("actions_main_ajax", array("_output"));
        $oView->expects($this->any())->method('_output')->with($this->equalTo(json_encode($aData)));
        $oView->setsorting();
    }
}