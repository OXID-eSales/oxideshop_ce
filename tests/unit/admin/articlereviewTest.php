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
 * Tests for Article_Review class
 */
class Unit_Admin_ArticleReviewTest extends OxidTestCase
{

    /**
     * Article_Review test setup
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->sTestId = '2077';
        $sVar = '8a142c4100e0b2f57.59530204';

        oxDb::getDb()->Execute(
            'replace into oxreviews (OXID, OXACTIVE, OXOBJECTID, OXTYPE, OXTEXT, OXUSERID, OXCREATE, OXLANG, OXRATING)
                        values ("_test_i1", 1, "' . $this->sTestId . '", "oxarticle", "aa", "' . oxADMIN_LOGIN . '", "0000-00-00 00:00:00", "0", "3")'
        );
        oxDb::getDb()->Execute(
            'replace into oxreviews (OXID, OXACTIVE, OXOBJECTID, OXTYPE, OXTEXT, OXUSERID, OXCREATE, OXLANG, OXRATING)
                        values ("_test_i2", 1, "' . $sVar . '", "oxarticle", "aa", "' . oxADMIN_LOGIN . '", "0000-00-00 00:00:00", "0", "3")'
        );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxreviews');
        parent::tearDown();
    }

    /**
     * Article_Review::render() test case
     *
     * @return null
     */
    public function testRender()
    {
        modConfig::setRequestParameter("oxid", $this->sTestId);
        modConfig::setRequestParameter("rev_oxid", "_test_i1");
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');

        // testing..
        $oView = new Article_Review();
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData["edit"] instanceof oxArticle);

        $this->assertEquals('article_review.tpl', $sTplName);
    }

    /**
     * Article_Review::delete() test case
     *
     * @return null
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

        modConfig::setRequestParameter("rev_oxid", "_testReviewId");
        modConfig::setRequestParameter("editval", array('oxreviews__oxtext' => 6, 'oxreviews__oxrating' => 6));
        modConfig::getInstance()->setConfigParam("blGBModerate", "_testReviewId");

        $oView = $this->getMock("Article_Review", array("resetContentCache"));
        $oView->expects($this->once())->method('resetContentCache');
        $oView->save();

        $this->assertTrue((bool) $oDb->getOne("select 1 from oxreviews where oxtext = '6' and oxrating = '6'"));
    }

    /**
     * Article_Review::delete() test case
     *
     * @return null
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
        modConfig::setRequestParameter("rev_oxid", "testReviewId");

        $oView = $this->getMock("Article_Review", array("resetContentCache"));
        $oView->expects($this->once())->method('resetContentCache');

        $oView->delete();

        $this->assertFalse((bool) $oDb->getOne("select 1 from oxreviews where oxid = 'testReviewId'"));
    }

    /**
     * Article_Review::_getReviewList() test case
     *
     * @return null
     */
    public function testGetReviewList()
    {
        oxTestModules::publicize('article_review', '_getReviewList');
        $o = oxNew('article_review');
        $oA = new oxArticle();
        $oA->load($this->sTestId);
        modConfig::getInstance()->setConfigParam('blShowVariantReviews', false);
        $this->assertEquals(1, count($o->p_getReviewList($oA)));
        modConfig::getInstance()->setConfigParam('blShowVariantReviews', true);
        $this->assertEquals(2, count($o->p_getReviewList($oA)));
    }
}
