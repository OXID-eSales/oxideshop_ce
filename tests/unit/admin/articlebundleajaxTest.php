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
 * Tests for Actions_Order_Ajax class
 */
class Unit_Admin_ArticleBundleAjaxTest extends OxidTestCase
{

    protected $_sArticleView = 'oxv_oxarticles_1_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';
    protected $_sShopId = '1';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->setArticleViewTable('oxv_oxarticles_de');
        $this->setObject2CategoryViewTable('oxobject2category');
        $this->setShopId('oxbaseshop');

        oxDb::getDb()->execute("insert into oxarticles set oxid='_testArticleBundle', oxshopid='" . $this->getShopId() . "', oxtitle='_testArticleBundle', oxbundleid='_testBundleId'");

    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticleBundle'");

        parent::tearDown();
    }

    public function setArticleViewTable($sParam)
    {
        $this->_sArticleView = $sParam;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function setShopId($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getArticleViewTable()
    {
        return $this->_sArticleView;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }

    public function getShopId()
    {
        return $this->_sShopId;
    }

    /**
     * ArticleBundleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleBundleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleBundleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testBundleOxid';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals("and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleBundleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxid()
    {
        $sSynchoxid = '_testBundleSynchoxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        $sOxid = '_testBundleOxid';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " as oxobject2category left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid=oxobject2category.oxobjectid  where oxobject2category.oxcatnid = '$sOxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleBundleAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testBundleSynchoxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        $sOxid = '_testBundleOxid';
        modConfig::setRequestParameter("oxid", $sOxid);
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " as oxobject2category left join " . $this->getArticleViewTable() . " on  (" . $this->getArticleViewTable() . ".oxid=oxobject2category.oxobjectid or " . $this->getArticleViewTable() . ".oxparentid=oxobject2category.oxobjectid) where oxobject2category.oxcatnid = '$sOxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleBundleAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $sParam = 'parameter';
        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals($sParam, trim($oView->UNITaddFilter($sParam)));
    }

    /**
     * ArticleBundleAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelectionTrue()
    {
        $sParam = 'parameter';
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('article_bundle_ajax');
        $this->assertEquals("$sParam group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter($sParam)));
    }


    /**
     * ArticleBundleAjax::removeArticleBundle() test case
     *
     * @return null
     */
    public function testRemoveArticleBundle()
    {
        $sOxid = '_testArticleBundle';
        modConfig::setRequestParameter("oxid", $sOxid);
        modconfig::getInstance()->setConfigParam("blVariantsSelection", true);

        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxid='$sOxid' and oxbundleid!=''"));
        $oView = oxNew('article_bundle_ajax');
        $oView->removeArticleBundle();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxid='$sOxid' and oxbundleid!=''"));
    }

    /**
     * ArticleBundleAjax::addArticleBundle() test case
     *
     * @return null
     */
    public function testAddArticleBundle()
    {
        $sOxid = '_testArticleBundle';
        modConfig::setRequestParameter("oxid", $sOxid);
        $sBundleId = '_testArticleBundle';
        modConfig::setRequestParameter("oxbundleid", $sBundleId);

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxid='$sOxid' and oxbundleid='$sBundleId'"));
        $oView = oxNew('article_bundle_ajax');
        $oView->addArticleBundle();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxid='$sOxid' and oxbundleid='$sBundleId'"));
    }


}