<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for News_Main_Ajax class
 */
class NewsMainAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemove1', oxobjectid='_testPayRemove1', oxgroupsid='_testRemoveGroup1'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemove2', oxobjectid='_testPayRemove2', oxgroupsid='_testRemoveGroup2'");

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemoveAll1', oxgroupsid='_testGroup1', oxobjectid='_testPayRemoveAll'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemoveAll2', oxgroupsid='_testGroup2', oxobjectid='_testPayRemoveAll'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testPayRemoveAll3', oxgroupsid='_testGroup3', oxobjectid='_testPayRemoveAll'");

        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup1', oxtitle='_testGroup1', oxactive=1");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup2', oxtitle='_testGroup2', oxactive=1");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup3', oxtitle='_testGroup3', oxactive=1");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2group where oxid LIKE '\_testPayRemove%'");

        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup1'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup2'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup3'");

        parent::tearDown();
    }

    /**
     * NewsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('news_main_ajax');
        $this->assertEquals("from oxv_oxgroups_de where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('news_main_ajax');
        $this->assertEquals("from oxv_oxgroups_de where 1  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('news_main_ajax');
        $this->assertEquals("from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sOxid . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('news_main_ajax');
        $this->assertEquals("from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sOxid . "' and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsMainAjax::removeGroupFromNews() test case
     *
     * @return null
     */
    public function testRemoveGroupFromNews()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testPayRemove1', '_testPayRemove2')));

        $sSql = "select count(oxid) from oxobject2group where oxid in ('_testPayRemove1', '_testPayRemove2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromNews();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * NewsMainAjax::removeGroupFromNews() test case
     *
     * @return null
     */
    public function testRemoveGroupFromNewsAll()
    {
        $sOxid = '_testPayRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid = '" . $sOxid . "'";
        $oView = oxNew('news_main_ajax');
        $this->assertEquals(3, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromNews();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * NewsMainAjax::addGroupToNews() test case
     *
     * @return null
     */
    public function testAddGroupToNews()
    {
        $sSynchoxid = '_testGroupAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $oView->addGroupToNews();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * NewsMainAjax::addGroupToNews() test case
     *
     * @return null
     */
    public function testAddGroupToNewsAll()
    {
        $sSynchoxid = '_testGroupAddAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxv_oxgroups_de.oxid) from oxv_oxgroups_de where 1  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )");

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $oView->addGroupToNews();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
