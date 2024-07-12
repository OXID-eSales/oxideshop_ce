<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;

/**
 * Tests for Discount_Article_Ajax class
 */
class DiscountArticlesAjaxTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove1', oxtitle='_testArticle1', oxshopid=" . ShopIdCalculator::BASE_SHOP_ID, 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove2', oxtitle='_testArticle2', oxshopid=" . ShopIdCalculator::BASE_SHOP_ID, 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove3', oxtitle='_testArticle3', oxshopid=" . ShopIdCalculator::BASE_SHOP_ID, 'oxarticles');

        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove1', oxtype = 'oxarticles'", 'oxobject2discount');
        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove2', oxtype = 'oxarticles'", 'oxobject2discount');
        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove3', oxtype = 'oxarticles'", 'oxobject2discount');

        $this->addTeardownSql("delete from oxobject2discount where oxobjectid like '_test%'");
        $this->addTeardownSql("delete from oxarticles where oxid like '_test%'");
    }

    /**
     * DiscountArticlesAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName("oxarticles");

        $oView = oxNew('discount_articles_ajax');
        $sQuery = sprintf('from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sArticleTable, $sArticleTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxarticles'";
        $this->assertEquals($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountArticlesAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName("oxarticles");
        $sO2CView = $tableViewNameGenerator->getViewName("oxobject2category");

        $oView = oxNew('discount_articles_ajax');
        $sQuery = sprintf('from %s left join %s on  %s.oxid=%s.oxobjectid ', $sO2CView, $sArticleTable, $sArticleTable, $sO2CView);
        $sQuery .= sprintf(' where %s.oxcatnid = \'_testOxid\' and %s.oxid is not null  and ', $sO2CView, $sArticleTable);
        $sQuery .= sprintf(' %s.oxid not in (  select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sArticleTable, $sArticleTable, $sArticleTable, $sArticleTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxarticles'  )";
        $this->assertEquals($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountArticlesAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sArticleTable = $tableViewNameGenerator->getViewName("oxarticles");

        $oView = oxNew('discount_articles_ajax');
        $sQuery = sprintf('from %s where 1 and %s.oxparentid = \'\'  and ', $sArticleTable, $sArticleTable);
        $sQuery .= sprintf(' %s.oxid not in (  select %s.oxid from oxobject2discount, %s where %s.oxid=oxobject2discount.oxobjectid ', $sArticleTable, $sArticleTable, $sArticleTable, $sArticleTable);
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxarticles'  )";
        $this->assertEquals($sQuery, trim((string) $oView->getQuery()));
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     */
    public function testRemoveDiscArt()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountArticlesAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testO2DRemove1', '_testO2DRemove2']));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscArt();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     */
    public function testRemoveDiscArtAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_articles_ajax');
        $oView->removeDiscArt();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::addDiscArt() test case
     */
    public function testAddDiscArt()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DiscountArticlesAjax::class, ["getActionIds"]);
        $oView->expects($this->any())->method('getActionIds')->will($this->returnValue(['_testArticleAdd1', '_testArticleAdd2']));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscArt();
        $this->assertEquals(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::addDiscArt() test case
     */
    public function testAddDiscArtAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxparentid = ''");

        $oView = oxNew('discount_articles_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2discount where oxdiscountid=\'%s\'', $sSynchoxid)));

        $oView->addDiscArt();
        $this->assertEquals($iCount, oxDb::getDb()->getOne(sprintf('select count(oxid) from oxobject2discount where oxdiscountid=\'%s\'', $sSynchoxid)));
    }
}
