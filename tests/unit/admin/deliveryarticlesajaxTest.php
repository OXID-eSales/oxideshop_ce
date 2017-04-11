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
 * Tests for Delivery_Articles_Ajax class
 */
class Unit_Admin_DeliveryArticlesAjaxTest extends OxidTestCase
{

    protected $_sArticlesView = 'oxv_oxarticles_1_de';
    protected $_sCategoriesView = 'oxv_oxcategories_1_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDelivery1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDelivery2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryDeleteAll1', oxdeliveryid='_testDelieveryRemoveAll', oxobjectid='_testObjectId', oxtype='oxarticles'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryDeleteAll2', oxdeliveryid='_testDelieveryRemoveAll', oxobjectid='_testObjectId', oxtype='oxarticles'");



        oxDb::getDb()->execute("insert into oxarticles set oxid='_testArticle1', oxshopid='oxbaseshop', oxtitle='_testArticle1'");
        oxDb::getDb()->execute("insert into oxarticles set oxid='_testArticle2', oxshopid='oxbaseshop', oxtitle='_testArticle2'");

        $this->setArticlesViewTable('oxv_oxarticles_de');
        $this->setCategoriesViewTable('oxv_oxcategories_de');
        $this->setObject2CategoryViewTable('oxobject2category');

    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDelivery1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDelivery2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryDeleteAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryDeleteAll2'");

        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle1'");
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddArt'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddArtAll'");

        parent::tearDown();
    }

    public function setArticlesViewTable($sParam)
    {
        $this->_sArticlesView = $sParam;
    }

    public function setCategoriesViewTable($sParam)
    {
        $this->_sCategoriesView = $sParam;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function getArticlesViewTable()
    {
        return $this->_sArticlesView;
    }

    public function getCategoriesViewTable()
    {
        return $this->_sCategoriesView;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }


    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxparentid = ''", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from " . $this->getArticlesViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxparentid = '' and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidSelectionTrue()
    {
        $sSynchoxid = '_testAction';

        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from oxobject2delivery left join " . $this->getArticlesViewTable() . " on " . $this->getArticlesViewTable() . ".oxid=oxobject2delivery.oxobjectid where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = \"oxarticles\"", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticlesViewTable() . " on  " . $this->getArticlesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "'and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxidVariantsSelection()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticlesViewTable() . " on  ( " . $this->getArticlesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticlesViewTable() . ".oxparentid=" . $this->getObject2CategoryViewTable() . ".oxobjectid)where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "'and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim($oView->UNITgetQuery()));
    }

    /**
     * DeliveryArticlesAjax::removeArtFromDel() test case
     *
     * @return null
     */
    public function testRemoveArtFromDel()
    {
        $oView = $this->getMock("delivery_articles_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testDelivery1', '_testDelivery2')));

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDelivery1', '_testDelivery2')";
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeArtFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryArticlesAjax::removeArtFromDel() test case
     *
     * @return null
     */
    public function testRemoveArtFromDelAll()
    {
        $sOxid = '_testDelieveryRemoveAll';
        $this->getConfig()->setRequestParameter("oxid", $sOxid);
        $this->getConfig()->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join " . $this->getArticlesViewTable() . " on " . $this->getArticlesViewTable() . ".oxid=oxobject2delivery.oxobjectid where oxobject2delivery.oxdeliveryid = '_testDelieveryRemoveAll' and oxobject2delivery.oxtype = 'oxarticles'";
        $oView = oxNew('delivery_articles_ajax');
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
        $oView->removeArtFromDel();
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryArticlesAjax::addArtToDel() test case
     *
     * @return null
     */
    public function testAddArtToDel()
    {
        $sSynchoxid = '_testActionAddArt';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("delivery_articles_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addArtToDel();
        $this->assertEquals(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryArticlesAjax::addArtToDel() test case
     *
     * @return null
     */
    public function testAddArtToDelAll()
    {
        $sSynchoxid = '_testActionAddArtAll';
        $this->getConfig()->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getArticlesViewTable() . ".oxid) from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxparentid = '' and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxarticles' )");

        $sSql = "select count(oxid) from oxobject2delivery where oxdeliveryid='$sSynchoxid'";
        $this->assertEquals(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock("delivery_articles_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testActionAdd1', '_testActionAdd2')));

        $oView->addArtToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}