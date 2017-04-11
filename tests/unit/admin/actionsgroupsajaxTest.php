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
class Unit_Admin_ActionsGroupsAjaxTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2action set oxid='_testId1', oxactionid='_testGroupDelete', oxobjectid='_testGroup', oxclass='oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2action set oxid='_testId2', oxactionid='_testGroupDelete', oxobjectid='_testGroup', oxclass='oxgroups'");

        oxDb::getDb()->execute("insert into oxobject2action set oxid='_testId3', oxactionid='_testGroupDeleteAll', oxobjectid='_testGroupAll', oxclass='oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2action set oxid='_testId4', oxactionid='_testGroupDeleteAll', oxobjectid='_testGroupAll', oxclass='oxgroups'");
        oxDb::getDb()->execute("insert into oxobject2action set oxid='_testId5', oxactionid='_testGroupDeleteAll', oxobjectid='_testGroupAll', oxclass='oxgroups'");
        oxDb::getDb()->execute("insert into oxgroups set oxid='_testGroupAll', oxactive=1, oxtitle='_testGroupAll', oxtitle_1='_testGroupAll1'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2action where oxactionid='_testGroupDelete'");
        oxDb::getDb()->execute("delete from oxobject2action where oxactionid='_testGroupDeleteAll'");
        oxDb::getDb()->execute("delete from oxobject2action where oxactionid='_testActionAdd'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroupAll'");

        parent::tearDown();
    }

    /**
     * ActionsArticleAjax::removeActionArticle() test case
     *
     * @return null
     */
    public function testRemovePromotionGroup()
    {
        $oView = $this->getMock("actions_groups_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testId1', '_testId2')));

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testGroupDelete'"));
        $oView->removePromotionGroup();
        $this->assertFalse((bool) oxDb::getDb()->getOne("select oxid from oxobject2action where oxactionid='_testGroupDelete' limit 1"));
    }

    /**
     * ActionsArticleAjax::removeActionArticle() test case
     *
     * @return null
     */
    public function testRemovePromotionGroupAll()
    {
        modConfig::setRequestParameter("all", true);
        modConfig::setRequestParameter("oxid", '_testGroupDeleteAll');

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testGroupDeleteAll'"));

        $oView = oxNew('actions_groups_ajax');
        $oView->removePromotionGroup();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testGroupDeleteAll'"));
    }

    /**
     * ActionsArticleAjax::addPromotionGroup() test case
     *
     * @return null
     */
    public function testAddPromotionGroup()
    {
        $oView = $this->getMock("actions_groups_ajax", array("_getActionIds"));
        modConfig::setRequestParameter("synchoxid", '_testActionAdd');

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testActionAdd'"));
        $oView->addPromotionGroup();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testActionAdd'"));
    }

    /**
     * ActionsArticleAjax::addPromotionGroup() test case
     *
     * @return null
     */
    public function testAddPromotionGroupAll()
    {
        $oView = $this->getMock("actions_groups_ajax", array("_getActionIds"));
        modConfig::setRequestParameter("synchoxid", '_testActionAdd');
        modConfig::setRequestParameter("all", true);

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testActionAdd'"));
        $oView->addPromotionGroup();

        $this->assertEquals(17, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testActionAdd'"));

    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('actions_groups_ajax');
        $this->assertEquals('from oxv_oxgroups_de where 1', trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testGroupGetQuerySynchoxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('actions_groups_ajax');
        $this->assertEquals("from oxv_oxgroups_de where 1  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2action, oxv_oxgroups_de where oxv_oxgroups_de.oxid=oxobject2action.oxobjectid  and oxobject2action.oxactionid = '$sSynchoxid' and oxobject2action.oxclass = 'oxgroups' )", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testGroupGetQuery';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('actions_groups_ajax');
        $this->assertEquals("from oxobject2action, oxv_oxgroups_de where oxv_oxgroups_de.oxid=oxobject2action.oxobjectid  and oxobject2action.oxactionid = '$sOxid' and oxobject2action.oxclass = 'oxgroups'", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testGroupGetQuery';
        $sSynchoxid = '_testGroupGetQuerySynchoxid';
        modConfig::setRequestParameter("oxid", $sOxid);
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('actions_groups_ajax');
        $this->assertEquals("from oxobject2action, oxv_oxgroups_de where oxv_oxgroups_de.oxid=oxobject2action.oxobjectid  and oxobject2action.oxactionid = '$sOxid' and oxobject2action.oxclass = 'oxgroups'  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2action, oxv_oxgroups_de where oxv_oxgroups_de.oxid=oxobject2action.oxobjectid  and oxobject2action.oxactionid = '$sSynchoxid' and oxobject2action.oxclass = 'oxgroups' )", trim($oView->UNITgetQuery()));
    }
}