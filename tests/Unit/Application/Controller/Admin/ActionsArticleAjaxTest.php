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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

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