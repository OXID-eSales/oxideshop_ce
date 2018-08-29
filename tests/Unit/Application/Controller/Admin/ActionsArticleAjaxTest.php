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
class ActionsArticleAjaxTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2action set oxid='_testId', oxactionid='_testActionDelete', oxobjectid='_testObject', oxclass='oxarticle'");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDB()->execute("delete from oxobject2action where oxactionid='_testActionDelete'");
        oxDb::getDB()->execute("delete from oxobject2action where oxactionid='_testActionSet'");

        parent::tearDown();
    }

    /**
     * ActionsArticleAjax::removeActionArticle() test case
     *
     * @return null
     */
    public function testRemoveActionArticle()
    {
        $this->setRequestParameter("oxid", '_testActionDelete');

        $this->assertTrue((bool) oxDb::getDb()->getOne("select oxid from oxobject2action where oxactionid='_testActionDelete' limit 1"));
        $oView = oxNew('actions_article_ajax');
        $oView->removeactionarticle();
        $this->assertFalse((bool) oxDb::getDb()->getOne("select oxid from oxobject2action where oxactionid='_testActionDelete' limit 1"));
    }

    /**
     * ActionsArticleAjax::setActionArticle() test case
     *
     * @return null
     */
    public function testSetActionArticle()
    {
        $this->setRequestParameter("oxid", '_testActionSet');
        $this->setRequestParameter("oxarticleid", '_testObject');

        $oView = oxNew('actions_article_ajax');
        $oView->setactionarticle();
        $this->assertTrue((bool) oxDb::getDb()->getOne("select oxid from oxobject2action where oxactionid='_testActionSet' limit 1"));
    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('actions_article_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_article_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsArticleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidTrue()
    {
        $this->setRequestParameter("oxid", 'oxid');
        $this->setRequestParameter("synchoxid", true);
        $oView = oxNew('actions_article_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " as oxobject2category left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid=oxobject2category.oxobjectid  where oxobject2category.oxcatnid = 'oxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * ActionsArticleAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $sParam = 'param';
        $oView = oxNew('actions_article_ajax');
        $this->assertEquals($sParam, $oView->UNITaddFilter($sParam));
    }

    /**
     * ActionsArticleAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantSelectionTrue()
    {
        $sParam = 'param';
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('actions_article_ajax');
        $this->assertEquals("$sParam group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter($sParam)));
    }

    private function getArticleViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxarticles_1_de' : 'oxv_oxarticles_de';
    }

    private function getObject2CategoryViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }
}
