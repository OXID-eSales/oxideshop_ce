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
class Unit_Admin_ArticleCrosssellingAjaxTest extends OxidTestCase
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

        $this->addToDatabase("replace into oxarticles set oxid='_testArticleCrossselling', oxshopid='" . $this->getShopId() . "', oxtitle='_testArticleCrossselling'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testArticleCrosssellingAdd', oxshopid='" . $this->getShopId() . "', oxtitle='_testArticleCrosssellingAdd'", 'oxarticles');
        $this->addToDatabase("replace into oxarticles set oxid='_testArticleCrosssellingAddAll', oxshopid='" . $this->getShopId() . "', oxtitle='_testArticleCrosssellingAddAll'", 'oxarticles');

        oxDb::getDb()->execute("insert into oxobject2article set oxid='_testCrosssellingOxid1', oxobjectid='_testCrosselling', oxarticlenid='_testArticleCrossselling'");
        oxDb::getDb()->execute("insert into oxobject2article set oxid='_testCrosssellingOxid2', oxobjectid='_testCrosselling', oxarticlenid='_testArticleCrossselling'");

        oxDb::getDb()->execute("insert into oxobject2article set oxid='_testCrosssellingOxid3', oxobjectid='_testArticleCrossselling', oxarticlenid='_testCrosssellingRemoveAll'");
        oxDb::getDb()->execute("insert into oxobject2article set oxid='_testCrosssellingOxid4', oxobjectid='_testArticleCrossselling', oxarticlenid='_testCrosssellingRemoveAll'");

        $this->addTeardownSql("delete from oxarticles where oxid like '%_testArticleCrossselling%'");
        $this->addTeardownSql("delete from oxobject2article where oxobjectid like '%_testCrosselling%'");
        $this->addTeardownSql("delete from oxobject2article where oxarticlenid like '%_testArticleCrossselling%'");
        $this->addTeardownSql("delete from oxobject2article where oxobjectid like '%_testArticleCrossselling%'");
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
     * ArticleCrosssellingAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_crossselling_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != ''", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleCrosssellingAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('article_crossselling_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in ( select " . $this->getArticleViewTable() . ".oxid from oxobject2article left join " . $this->getArticleViewTable() . " on oxobject2article.oxobjectid=" . $this->getArticleViewTable() . ".oxid where oxobject2article.oxarticlenid = '$sSynchoxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  )  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleCrosssellingAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $sOxid = '_testOxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_crossselling_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " as oxobject2category left join " . $this->getArticleViewTable() . " on  " . $this->getArticleViewTable() . ".oxid=oxobject2category.oxobjectid  where oxobject2category.oxcatnid = '$sOxid'  and " . $this->getArticleViewTable() . ".oxid not in ( select " . $this->getArticleViewTable() . ".oxid from oxobject2article left join " . $this->getArticleViewTable() . " on oxobject2article.oxobjectid=" . $this->getArticleViewTable() . ".oxid where oxobject2article.oxarticlenid = '$sSynchoxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  )  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleCrosssellingAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidBidirectCross()
    {
        $sOxid = '_testOxid';
        modConfig::setRequestParameter("oxid", $sOxid);
        modconfig::getInstance()->setConfigParam("blBidirectCross", true);

        $oView = oxNew('article_crossselling_ajax');
        $this->assertEquals("from oxobject2article  inner join " . $this->getArticleViewTable() . " on ( oxobject2article.oxobjectid = " . $this->getArticleViewTable() . ".oxid  or oxobject2article.oxarticlenid = " . $this->getArticleViewTable() . ".oxid )  where ( oxobject2article.oxarticlenid = '$sOxid' or oxobject2article.oxobjectid = '$sOxid' )  and " . $this->getArticleViewTable() . ".oxid != '$sOxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sOxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleCrosssellingAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        modConfig::setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_crossselling_ajax');
        $this->assertEquals("from oxobject2article left join " . $this->getArticleViewTable() . " on oxobject2article.oxobjectid=" . $this->getArticleViewTable() . ".oxid  where oxobject2article.oxarticlenid = '$sOxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sOxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleCrosssellingAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidBidirectCross()
    {
        $sSynchoxid = '_testSynchoxid';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modconfig::getInstance()->setConfigParam("blBidirectCross", true);

        $oView = oxNew('article_crossselling_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in ( select " . $this->getArticleViewTable() . ".oxid from oxobject2article left join " . $this->getArticleViewTable() . " on (oxobject2article.oxobjectid=" . $this->getArticleViewTable() . ".oxid or oxobject2article.oxarticlenid=" . $this->getArticleViewTable() . ".oxid) where (oxobject2article.oxarticlenid = '$sSynchoxid' or oxobject2article.oxobjectid = '$sSynchoxid' ) and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  )  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleCrosssellingAjax::removeArticleCross() test case
     *
     * @return null
     */
    public function testRemoveArticleCross()
    {
        $oView = $this->getMock("article_crossselling_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testCrosssellingOxid1', '_testCrosssellingOxid2')));

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxobjectid='_testCrosselling'"));
        $oView->removeArticleCross();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxobjectid='_testCrosselling'"));
    }

    /**
     * ArticleCrosssellingAjax::removeArticleCross() test case
     *
     * @return null
     */
    public function testRemoveArticleCrossAll()
    {
        modConfig::setRequestParameter("all", true);

        $sOxid = '_testCrosssellingRemoveAll';
        modConfig::setRequestParameter("oxid", $sOxid);

        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxarticlenid='_testCrosssellingRemoveAll'"));

        $oView = oxNew('article_crossselling_ajax');
        $oView->removeArticleCross();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxarticlenid='_testCrosssellingRemoveAll'"));
    }

    /**
     * ArticleCrosssellingAjax::addArticleCross() test case
     *
     * @return null
     */
    public function testAddArticleCross()
    {
        $sSynchoxid = '_testArticleCrosssellingAdd';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxarticlenid='$sSynchoxid'"));

        $oView = $this->getMock("article_crossselling_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testObjectId1', '_testObjectId2')));

        $oView->addArticleCross();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxarticlenid='$sSynchoxid'"));
    }

    /**
     * ArticleCrosssellingAjax::addArticleCross() test case
     *
     * @return null
     */
    public function testAddArticleCrossAll()
    {
        $sSynchoxid = '_testArticleCrosssellingAddAll';
        modConfig::setRequestParameter("synchoxid", $sSynchoxid);
        modConfig::setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getArticleViewTable() . ".oxid)  from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in ( select " . $this->getArticleViewTable() . ".oxid from oxobject2article left join " . $this->getArticleViewTable() . " on oxobject2article.oxobjectid=" . $this->getArticleViewTable() . ".oxid where oxobject2article.oxarticlenid = '$sSynchoxid'  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  )  and " . $this->getArticleViewTable() . ".oxid IS NOT NULL  and " . $this->getArticleViewTable() . ".oxid != '$sSynchoxid'");
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxarticlenid='$sSynchoxid'"));

        $oView = oxNew('article_crossselling_ajax');
        $oView->addArticleCross();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2article where oxarticlenid='$sSynchoxid'"));
    }
}