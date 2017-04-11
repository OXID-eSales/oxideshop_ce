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
 * Tests for News_Main_Ajax class
 */
class Unit_Admin_NewsMainAjaxTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemove1', oxobjectid='_testGroupRemove'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemove2', oxobjectid='_testGroupRemove'");

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemoveAll1', oxobjectid='_testGroupRemoveAll', oxgroupsid='_testGroup1'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemoveAll2', oxobjectid='_testGroupRemoveAll', oxgroupsid='_testGroup2'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemoveAll3', oxobjectid='_testGroupRemoveAll', oxgroupsid='_testGroup3'");

        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup1', oxtitle='_testGroup1'");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup2', oxtitle='_testGroup2'");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroup3', oxtitle='_testGroup3'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2group where oxobjectid='_testGroupRemove'");
        oxDb::getDb()->execute("delete from oxobject2group where oxobjectid='_testGroupRemoveAll'");
        oxDb::getDb()->execute("delete from oxobject2group where oxobjectid='_testGroupAdd'");
        oxDb::getDb()->execute("delete from oxobject2group where oxobjectid='_testGroupAddAll'");

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
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

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
        $this->getConfig()->setRequestParameter("oxid", $sOxid);

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
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

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
        $oView = $this->getMock("news_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupRemove1', '_testGroupRemove2')));

        $sSql = "select count(oxid) from oxobject2group where oxid in ('_testGroupRemove1', '_testGroupRemove2')";
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
        $sOxid = '_testGroupRemoveAll';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("all", true);

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
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("news_main_ajax", array("_getActionIds"));
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
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxv_oxgroups_de.oxid) from oxv_oxgroups_de where 1  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )");

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("news_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $oView->addGroupToNews();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}