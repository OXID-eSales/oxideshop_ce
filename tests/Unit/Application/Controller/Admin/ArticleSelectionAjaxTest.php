<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Article_Rights_Visible_Ajax class
 */
class ArticleSelectionAjaxTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addToDatabase("replace into oxarticles set oxid='_testArticle', oxparentid='_testArticlePArent', oxshopid='1', oxtitle='_testArticle'", 'oxarticles');
        $this->addToDatabase("replace into oxselectlist set oxid='_testSelectList', oxshopid='1', oxtitle='_testSelectList'", 'oxselectlist');
        $this->addTeardownSql("delete from oxarticles where oxid = '_testArticles'");
        $this->addTeardownSql("delete from oxselectlist where oxid = '_testSelectList'");

        $this->addToDatabase("replace into oxobject2article set oxid='_testOxid', oxobjectid='_testArtcle', oxarticlenid=''", 'oxobject2article');
        $this->addTeardownSql("delete from oxobject2article where oxid = '_testOxid'");

        $this->addToDatabase("replace into oxobject2selectlist set oxid='_testOxid1', oxobjectid='_testRemove', oxselnid='_testRemove'", 'oxobject2selectlist');
        $this->addToDatabase("replace into oxobject2selectlist set oxid='_testOxid2', oxobjectid='_testRemove', oxselnid='_testRemove'", 'oxobject2selectlist');
        $this->addTeardownSql("delete from oxobject2selectlist where oxobjectid like '%_test%' or oxid = '_testOxid'");
    }

    public function getSelectListViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxselectlist_1_de' : 'oxv_oxselectlist_de';
    }

    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_selection_ajax');
        $this->assertEquals("from oxobject2selectlist left join " . $this->getSelectListViewTable() . " on " . $this->getSelectListViewTable() . ".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testArticleSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('article_selection_ajax');
        $this->assertEquals("from " . $this->getSelectListViewTable() . "  where " . $this->getSelectListViewTable() . ".oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join " . $this->getSelectListViewTable() . " on " . $this->getSelectListViewTable() . ".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sSynchoxid'  )", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidParent()
    {
        $sOxid = '_testArticle';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_selection_ajax');
        $this->assertEquals("from oxobject2selectlist left join " . $this->getSelectListViewTable() . " on " . $this->getSelectListViewTable() . ".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sOxid' or oxobject2selectlist.oxobjectid = '_testArticlePArent'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxid()
    {
        $sSynchoxid = '_testArticleSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sOxid = '_testArticleOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_selection_ajax');
        $this->assertEquals("from " . $this->getSelectListViewTable() . "  where " . $this->getSelectListViewTable() . ".oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join " . $this->getSelectListViewTable() . " on " . $this->getSelectListViewTable() . ".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sOxid'  )", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxidparent()
    {
        $sSynchoxid = '_testArticleSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sOxid = '_testArticle';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_selection_ajax');
        $this->assertEquals("from " . $this->getSelectListViewTable() . "  where " . $this->getSelectListViewTable() . ".oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join " . $this->getSelectListViewTable() . " on " . $this->getSelectListViewTable() . ".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sOxid' or oxobject2selectlist.oxobjectid = '_testArticlePArent'  )", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleSelectionAjax::removeSel() test case
     *
     * @return null
     */
    public function testRemoveSel()
    {
        $oDb = oxDb::getDb();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSelectionAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testOxid1', '_testOxid2')));

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'"));
        $oView->removeSel();
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'"));
    }

    /**
     * ArticleSelectionAjax::removeSel() test case
     *
     * @return null
     */
    public function testRemoveSelAll()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testRemove';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'"));
        $oView = oxNew('article_selection_ajax');
        $oView->removeSel();
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'"));
    }

    /**
     * ArticleSelectionAjax::addSel() test case
     *
     * @return null
     */
    public function testAddSel()
    {
        $oDb = oxDb::getDb();

        $sSynchoxid = '_testAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleSelectionAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testAdd1', '_testAdd2')));

        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'"));
        $oView->addSel();
        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'"));
    }

    /**
     * ArticleSelectionAjax::addSel() test case
     *
     * @return null
     */
    public function testAddSelAll()
    {
        $oDb = oxDb::getDb();

        $sSynchoxid = '_testAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $selectListTable = $this->getSelectListViewTable();
        $iCount = $oDb->getOne(
            "select count({$selectListTable}.oxid) from {$selectListTable}
                where {$selectListTable}.oxid not in (
                    select oxobject2selectlist.oxselnid
                        from oxobject2selectlist
                        left join {$selectListTable} on {$selectListTable}.oxid=oxobject2selectlist.oxselnid
                        where oxobject2selectlist.oxobjectid = '_testAdd'  )"
        );

        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'"));
        $oView = oxNew('article_selection_ajax');
        $oView->addSel();
        $this->assertEquals(1, $oDb->getOne("select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'"));
    }
}
