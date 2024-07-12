<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Article;
use \oxField;
use \oxDb;
use \oxTestModules;

/**
 * Tests for Article_Review class
 */
class ArticleReviewTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Article_Review test setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        $articleId = $this->getTestArticleId();

        oxDb::getDb()->execute(
            'replace into oxreviews (OXID, OXACTIVE, OXOBJECTID, OXTYPE, OXTEXT, OXUSERID, OXCREATE, OXLANG, OXRATING)
                        values ("_test_i1", 1, "' . $articleId . '", "oxarticle", "aa", "' . \OXADMIN_LOGIN . '", "0000-00-00 00:00:00", "0", "3")'
        );
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxreviews');
        parent::tearDown();
    }

    /**
     * Article_Review::render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", $this->getTestArticleId());
        $this->setRequestParameter("rev_oxid", "_test_i1");
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = oxNew('Article_Review');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["edit"]);

        $this->assertSame('article_review', $sTplName);
    }

    /**
     * Article_Review::delete() test case
     */
    public function testSave()
    {
        $oReview = oxNew("oxreview");
        $oReview->setId("_testReviewId");

        $oReview->oxreviews__oxactive = new oxField(1);
        $oReview->oxreviews__oxobjectid = new oxField("_testObjectId");
        $oReview->oxreviews__oxtype = new oxField("oxarticle");
        $oReview->save();

        $oDb = oxDb::getDb();

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxreviews where oxid = '_testReviewId'"));

        $this->setRequestParameter("rev_oxid", "_testReviewId");
        $this->setRequestParameter("editval", ['oxreviews__oxtext' => 6, 'oxreviews__oxrating' => 6]);
        $this->getConfig()->setConfigParam("blGBModerate", "_testReviewId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleReview::class, ["resetContentCache"]);
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxreviews where oxtext = '6' and oxrating = '6'"));
    }

    /**
     * Article_Review::delete() test case
     */
    public function testDelete()
    {
        $oReview = oxNew("oxreview");
        $oReview->setId("testReviewId");

        $oReview->oxreviews__oxactive = new oxField(1);
        $oReview->oxreviews__oxobjectid = new oxField("testObjectId");
        $oReview->oxreviews__oxtype = new oxField("oxarticle");
        $oReview->save();

        $oDb = oxDb::getDb();

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxreviews where oxid = 'testReviewId'"));
        $this->setRequestParameter("rev_oxid", "testReviewId");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleReview::class, ["resetContentCache"]);
        $oView->expects($this->once())->method('resetContentCache');

        $oView->delete();

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxreviews where oxid = 'testReviewId'"));
    }

    /**
     * Article_Review::getReviewList() test case
     */
    public function testGetReviewList()
    {
        $articleVariantId = $this->getTestArticleVariantId();
        oxDb::getDb()->execute(
            'replace into oxreviews (OXID, OXACTIVE, OXOBJECTID, OXTYPE, OXTEXT, OXUSERID, OXCREATE, OXLANG, OXRATING)
                        values ("_test_i2", 1, "' . $articleVariantId . '", "oxarticle", "aa", "' . \OXADMIN_LOGIN . '", "0000-00-00 00:00:00", "0", "3")'
        );
        $o = oxNew('article_review');
        $oA = oxNew('oxArticle');
        $oA->load($this->getTestArticleId());
        $this->getConfig()->setConfigParam('blShowVariantReviews', false);
        $this->assertCount(1, $o->getReviewList($oA));
        $this->getConfig()->setConfigParam('blShowVariantReviews', true);
        $this->assertCount(2, $o->getReviewList($oA));
    }

    /**
     * @return string Test Article Id
     */
    protected function getTestArticleId()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? '2363' : '2077';
    }

    /**
     * @return string Test Article Variant Id
     */
    protected function getTestArticleVariantId()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? '2363-01' : '8a142c4100e0b2f57.59530204';
    }
}
