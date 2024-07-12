<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Actions_Order_Ajax class
 */
class ArticleBundleAjaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $setupArticleSql = "insert into oxarticles set oxid='_testArticleBundle', oxshopid=1, oxtitle='_testArticleBundle', oxbundleid='_testBundleId'";

        oxDb::getDb()->execute($setupArticleSql);
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticleBundle'");

        parent::tearDown();
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
     * ArticleBundleAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_bundle_ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim((string) $oView->getQuery()));
    }

    /**
     * ArticleBundleAjax::getQuery() test case
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('article_bundle_ajax');
        $this->assertSame("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim((string) $oView->getQuery()));
    }

    /**
     * ArticleBundleAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testBundleOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_bundle_ajax');
        $this->assertSame("and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim((string) $oView->getQuery()));
    }

    /**
     * ArticleBundleAjax::getQuery() test case
     */
    public function testGetQuerySynchoxidOxid()
    {
        $sSynchoxid = '_testBundleSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sOxid = '_testBundleOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_bundle_ajax');
        $this->assertSame("from " . $this->getObject2CategoryViewTable() . " as oxobject2category left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . sprintf(".oxid=oxobject2category.oxobjectid  where oxobject2category.oxcatnid = '%s'  and ", $sOxid) . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . sprintf(".oxid != '%s'", $sSynchoxid), trim((string) $oView->getQuery()));
    }

    /**
     * ArticleBundleAjax::getQuery() test case
     */
    public function testGetQuerySynchoxidOxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testBundleSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sOxid = '_testBundleOxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('article_bundle_ajax');
        $this->assertSame("from " . $this->getObject2CategoryViewTable() . " as oxobject2category left join " . $this->getArticleViewTable() . " on  (" . $this->getArticleViewTable() . ".oxid=oxobject2category.oxobjectid or " . $this->getArticleViewTable() . sprintf(".oxparentid=oxobject2category.oxobjectid) where oxobject2category.oxcatnid = '%s'  and ", $sOxid) . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . sprintf(".oxid != '%s'", $sSynchoxid), trim((string) $oView->getQuery()));
    }

    /**
     * ArticleBundleAjax::addFilter() test case
     */
    public function testAddFilter()
    {
        $sParam = 'parameter';
        $oView = oxNew('article_bundle_ajax');
        $this->assertSame($sParam, trim((string) $oView->addFilter($sParam)));
    }

    /**
     * ArticleBundleAjax::addFilter() test case
     */
    public function testAddFilterVariantsSelectionTrue()
    {
        $sParam = 'parameter';
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('article_bundle_ajax');
        $this->assertSame($sParam . ' group by ' . $this->getArticleViewTable() . ".oxid", trim((string) $oView->addFilter($sParam)));
    }

    /**
     * ArticleBundleAjax::removeArticleBundle() test case
     */
    public function testRemoveArticleBundle()
    {
        $bundledArticleId = '_testArticleBundle';
        $this->setRequestParameter("oxid", $bundledArticleId);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $this->assertSame(1, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxarticles where oxid='%s' and oxbundleid != ''", $bundledArticleId)));
        $view = oxNew('article_bundle_ajax');
        $view->removeArticleBundle();
        $this->assertSame(0, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxarticles where oxid='%s' and oxbundleid != ''", $bundledArticleId)));
    }

    /**
     * ArticleBundleAjax::addArticleBundle() test case
     */
    public function testAddArticleBundle()
    {
        $bundledArticleId = '_testArticleBundle';
        $this->setRequestParameter("oxid", $bundledArticleId);
        $bundleId = '_testArticleBundle';
        $this->setRequestParameter("oxbundleid", $bundleId);

        $this->assertSame(0, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxarticles where oxid='%s' and oxbundleid='%s'", $bundledArticleId, $bundleId)));
        $view = oxNew('article_bundle_ajax');
        $view->addArticleBundle();
        $this->assertSame(1, oxDb::getDb()->getOne(sprintf("select count(oxid) from oxarticles where oxid='%s' and oxbundleid='%s'", $bundledArticleId, $bundleId)));
    }
}
