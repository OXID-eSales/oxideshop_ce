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
class NewsletterSelectionAjaxTest extends \OxidTestCase
{
    protected $_sGroupsView = 'oxv_oxgroups_de';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemove1', oxobjectid='_testGroupRemove', oxgroupsid='_testGroup1'");
        oxDb::getDb()->execute("insert into oxobject2group set oxid='_testGroupRemove2', oxobjectid='_testGroupRemove', oxgroupsid='_testGroup2'");

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
        oxDb::getDb()->execute("delete from oxobject2group where oxid LIKE '\_testGroupRemove%'");

        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup1'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup2'");
        oxDb::getDb()->execute("delete from oxgroups where oxid='_testGroup3'");

        parent::tearDown();
    }

    public function setGroupsViewTable($sParam)
    {
        $this->_sGroupsView = $sParam;
    }

    public function getGroupsViewTable()
    {
        return $this->_sGroupsView;
    }

    /**
     * NewsletterSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('newsletter_selection_ajax');
        $this->assertEquals("from oxv_oxgroups_de where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsletterSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('newsletter_selection_ajax');
        $this->assertEquals("from oxv_oxgroups_de where 1  and oxv_oxgroups_de.oxid not in (  select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsletterSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('newsletter_selection_ajax');
        $this->assertEquals("from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sOxid . "'", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsletterSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('newsletter_selection_ajax');
        $this->assertEquals("from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sOxid . "' and oxv_oxgroups_de.oxid not in (  select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )", trim($oView->UNITgetQuery()));
    }

    /**
     * NewsletterSelectionAjax::removeGroupFromNewsletter() test case
     *
     * @return null
     */
    public function testRemoveGroupFromNewsletter()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsletterSelectionAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupRemove1', '_testGroupRemove2')));

        $sSql = "select count(oxid) from oxobject2group where oxid in ('_testGroupRemove1', '_testGroupRemove2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromNewsletter();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * NewsletterSelectionAjax::removeGroupFromNewsletter() test case
     *
     * @return null
     */
    public function testRemoveGroupFromNewsletterAll()
    {
        $sOxid = '_testGroupRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid = '" . $sOxid . "'";
        $oView = oxNew('newsletter_selection_ajax');
        $this->assertEquals(3, oxDb::getDb()->getOne($sSql));
        $oView->removeGroupFromNewsletter();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * NewsletterSelectionAjax::addGroupToNewsletter() test case
     *
     * @return null
     */
    public function testAddGroupToNewsletter()
    {
        $sSynchoxid = '_testGroupAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsletterSelectionAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $oView->addGroupToNewsletter();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * NewsletterSelectionAjax::addGroupToNewsletter() test case
     *
     * @return null
     */
    public function testAddGroupToNewsletterAll()
    {
        $sSynchoxid = '_testGroupAddAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxv_oxgroups_de.oxid) from oxv_oxgroups_de where 1  and oxv_oxgroups_de.oxid not in ( select oxv_oxgroups_de.oxid from oxobject2group left join oxv_oxgroups_de on oxobject2group.oxgroupsid=oxv_oxgroups_de.oxid  where oxobject2group.oxobjectid = '" . $sSynchoxid . "' )");

        $sSql = "select count(oxid) from oxobject2group where oxobjectid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NewsletterSelectionAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $oView->addGroupToNewsletter();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
