<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax;

/**
 * Tests for Actions_Order_Ajax class
 */
class ArticleAccessoriesAjaxTest extends \OxidTestCase
{

   /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addToDatabase("replace into oxarticles set oxid='_testArticle1', oxshopid='1', oxtitle='_testArticle1'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testArticle2', oxshopid='1', oxtitle='_testArticle2'", 'oxarticles');

        oxDb::getDb()->execute("insert into oxaccessoire2article set oxid='_testArticle1', OXOBJECTID='_testArticle1', OXARTICLENID='_testArticleAccessories', OXSORT='9'");
        oxDb::getDb()->execute("insert into oxaccessoire2article set oxid='_testArticle2', OXOBJECTID='_testArticle2', OXARTICLENID='_testArticleAccessories', OXSORT='9'");

        $this->addTeardownSql("delete from oxarticles where oxid like '%_testArt%'");
        $this->addTeardownSql("delete from oxaccessoire2article where oxarticlenid like '%_testArticle%'");
    }

    public function getArticleViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxarticles_1_de' : 'oxv_oxarticles_de';
    }

    public function getObject2CategoryViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxobject2category_1' : 'oxobject2category';
    }

    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_accessories_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('article_accessories_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testArticleAccessoriesOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_accessories_ajax');
        $this->assertEquals("from oxaccessoire2article left join " . $this->getArticleViewTable() . " on oxaccessoire2article.oxobjectid=" . $this->getArticleViewTable() . ".oxid  where oxaccessoire2article.oxarticlenid = '$sOxid'  and " . $this->getArticleViewTable() . ".oxid != '$sOxid'", trim($oView->UNITgetQuery()));
    }

    public function testGetQuerySynchoxid()
    {
        $synchoxid = '_testArticleAccessoriesSynchoxid';
        $this->setRequestParameter('synchoxid', $synchoxid);

        $view = oxNew(ArticleAccessoriesAjax::class);
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$synchoxid'  ) and " . $this->getArticleViewTable() . ".oxid != '$synchoxid'", trim($view->UNITgetQuery()));
    }

    public function testGetQueryOxidSynchoxid()
    {
        $oxid = '_testArticleAccessoriesOxid';
        $synchoxid = '_testArticleAccessoriesSynchoxid';
        $this->setRequestParameter('oxid', $oxid);
        $this->setRequestParameter('synchoxid', $synchoxid);

        $view = oxNew(ArticleAccessoriesAjax::class);
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid  where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '$oxid'  and " . $this->getArticleViewTable() . ".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$synchoxid'  ) and " . $this->getArticleViewTable() . ".oxid != '$synchoxid'", trim($view->UNITgetQuery()));
    }

    public function testGetQueryOxidSynchoxidVariantsSelectionTrue()
    {
        $oxid = '_testArticleAccessoriesOxid';
        $synchoxid = '_testArticleAccessoriesSynchoxid';
        $this->setRequestParameter("oxid", $oxid);
        $this->setRequestParameter("synchoxid", $synchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $view = oxNew(ArticleAccessoriesAjax::class);
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticleViewTable() . " on  ( " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticleViewTable() . ".oxparentid=" . $this->getObject2CategoryViewTable() . ".oxobjectid ) where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '$oxid'  and " . $this->getArticleViewTable() . ".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$synchoxid'  ) and " . $this->getArticleViewTable() . ".oxid != '$synchoxid'", trim($view->UNITgetQuery()));
    }

    /**
     * ArticleAccessoriesAjax::removeArticleAcc() test case
     *
     * @return null
     */
    public function testRemoveArticleAcc()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticle1', '_testArticle2')));
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'"));

        $oView->removearticleacc();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'"));
    }

    /**
     * ArticleAccessoriesAjax::removeArticleAcc() test case
     *
     * @return null
     */
    public function testRemoveArticleAccAll()
    {
        $this->setRequestParameter("all", true);
        $this->setRequestParameter("oxid", '_testArticleAccessories');

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'"));
        /** @var \OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax $oView */
        $oView = oxNew('article_accessories_ajax');
        $oView->removearticleacc();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'"));
    }

    /**
     * ArticleAccessoriesAjax::addArticleAcc() test case
     *
     * @return null
     */
    public function testAddArticleAcc()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax::class, array("_getActionIds"));
        $this->setRequestParameter("synchoxid", '_testArticle1');

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'"));
        $oView->addarticleacc();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'"));
    }

    /**
     * ArticleAccessoriesAjax::addArticleAcc() test case
     *
     * @return null
     */
    public function testAddArticleAccAll()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleAccessoriesAjax::class, array("_getActionIds"));
        $sSynchoxid = '_testArticle1';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$sSynchoxid'  )  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'");

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testGroupAdd1', '_testGroupAdd2')));

        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'"));
        $oView->addarticleacc();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'"));
    }
}
