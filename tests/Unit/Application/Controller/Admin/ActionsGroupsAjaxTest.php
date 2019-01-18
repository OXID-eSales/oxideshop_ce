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
class ActionsGroupsAjaxTest extends \OxidTestCase
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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsGroupsAjax::class, array("_getActionIds"));
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
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("oxid", '_testGroupDeleteAll');

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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsGroupsAjax::class, array("_getActionIds"));
        $this->setRequestParameter("synchoxid", '_testActionAdd');

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
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsGroupsAjax::class, array("_getActionIds"));
        $this->setRequestParameter("synchoxid", '_testActionAdd');
        $this->setRequestParameter("all", true);

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testActionAdd'"));
        $oView->addPromotionGroup();

        $count = $this->getTestConfig()->getShopEdition() == 'EE' ? 18 : 17;
        $this->assertEquals($count, oxDb::getDb()->getOne("select count(oxid) from oxobject2action where oxactionid='_testActionAdd'"));
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
        $this->setRequestParameter("synchoxid", $sSynchoxid);

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
        $this->setRequestParameter("oxid", $sOxid);

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
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('actions_groups_ajax');
        $this->assertEquals("from oxobject2action, oxv_oxgroups_de where oxv_oxgroups_de.oxid=oxobject2action.oxobjectid  and oxobject2action.oxactionid = '$sOxid' and oxobject2action.oxclass = 'oxgroups'  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2action, oxv_oxgroups_de where oxv_oxgroups_de.oxid=oxobject2action.oxobjectid  and oxobject2action.oxactionid = '$sSynchoxid' and oxobject2action.oxclass = 'oxgroups' )", trim($oView->UNITgetQuery()));
    }
}
