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
 * Tests for Category_Main_Ajax class
 */
class Unit_Admin_CategoryMainAjaxTest extends OxidTestCase
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
        $this->setShopIdTest('oxbaseshop');

        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove1', oxtitle='_testArticle1', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove2', oxtitle='_testArticle2', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');
        $this->addToDatabase("insert into oxarticles set oxid='_testObjectRemove3', oxtitle='_testArticle3', oxshopid='" . $this->getShopIdTest() . "'", 'oxarticles');

        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove1', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove1'", 'oxcategories');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove2', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove2'", 'oxcategories');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove3', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove3'", 'oxcategories');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->addTeardownSql("delete from oxarticles where oxid like '_test%'");
        $this->addTeardownSql("delete from oxobject2category where oxobjectid like '_test%'");

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

    public function setShopIdTest($sParam)
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

    public function getShopIdTest()
    {
        return $this->_sShopId;
    }

    /**
     * CategoryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('category_main_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " join " . $this->getArticleViewTable() . "  on  " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid  where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '' and " . $this->getArticleViewTable() . ".oxid is not null", trim($oView->UNITgetQuery()));
    }

    /**
     * CategoryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam("oxid", $sOxid);
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $sArticleTable = $this->getArticleViewTable();
        $sO2CView = $this->getObject2CategoryViewTable();

        $oView = oxNew('category_main_ajax');
        $sQuery = "from " . $sO2CView . " join " . $sArticleTable . "  on  " . $sArticleTable . ".oxid=" . $sO2CView . ".oxobjectid";
        $sQuery .= "  where " . $sO2CView . ".oxcatnid = '_testOxid' and " . $sArticleTable . ".oxid is not null";
        $sQuery .= "  and " . $sArticleTable . ".oxid not in ( select $sArticleTable.oxid from $sO2CView left join $sArticleTable ";
        $sQuery .= "on  $sArticleTable.oxid=$sO2CView.oxobjectid  where $sO2CView.oxcatnid =  '_testSynchoxid' and " . $sArticleTable . ".oxid is not null )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * CategoryMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam("synchoxid", $sSynchoxid);

        $oView = oxNew('category_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * CategoryMainAjax::removeArticle() test case
     *
     * @return null
     */
    public function testRemoveArticle()
    {
        $sOxid = '_testCategory';
        $this->setRequestParam("oxid", $sOxid);
        $oView = $this->getMock("category_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testObjectRemove1', '_testObjectRemove2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sOxid'"));

        $oView->removeArticle();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sOxid'"));
    }

    /**
     * CategoryMainAjax::removeArticle() test case
     *
     * @return null
     */
    public function testRemoveArticleAll()
    {
        $sOxid = '_testCategory';
        $this->setRequestParam("oxid", $sOxid);
        $this->setRequestParam("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sOxid'"));

        $oView = oxNew('category_main_ajax');
        $oView->removeArticle();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sOxid'"));
    }

    /**
     * CategoryMainAjax::addArticle() test case
     *
     * @return null
     */
    public function testAddArticle()
    {
        $sSynchoxid = '_testCategory';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $oView = $this->getMock("category_main_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticleAdd1', '_testArticleAdd2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sSynchoxid'"));

        $oView->addArticle();
        $this->assertEquals(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sSynchoxid'"));
    }

    /**
     * CategoryMainAjax::addArticle() test case
     *
     * @return null
     */
    public function testAddArticleAll()
    {
        $sSynchoxid = '_testCategoryNew';
        $this->setRequestParam("synchoxid", $sSynchoxid);
        $this->setRequestParam("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxarticles where oxparentid = '' and oxshopid='" . $this->getShopIdTest() . "'");

        $oView = oxNew('category_main_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sSynchoxid'"));

        $oView->addArticle();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxcatnid='$sSynchoxid'"));
    }

    /**
     * CategoryMainAjax::_updateOxTime() test case
     *
     * @return null
     */
    public function testUpdateOxTime()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testObjectRemove1';

        // updating oxtime values
        $sQ = "update oxobject2category set oxtime = 1 where oxobjectid = '$sOxid' ";
        $oDb->execute($sQ);

        $oView = oxNew('category_main_ajax');
        $oView->UNITupdateOxTime($oDb->quote($sOxid));
        $this->assertEquals(1, $oDb->getOne("select count(oxid) from oxobject2category where oxtime=0 and oxobjectid = '$sOxid'"));
    }

}