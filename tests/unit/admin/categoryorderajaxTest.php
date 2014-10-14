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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for Category_Order_Ajax class
 */
class Unit_Admin_CategoryOrderAjaxTest extends OxidTestCase
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

            $this->setArticleViewTable( 'oxv_oxarticles_de' );
            $this->setObject2CategoryViewTable( 'oxobject2category' );
            $this->setShopIdTest( 'oxbaseshop' );

            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testCategory', oxtitle='_testCategory', oxshopid='".$this->getShopIdTest()."'" );
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2Category1', oxcatnid='_testCategory', oxobjectid = '_testOxid1'" );
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2Category2', oxcatnid='_testCategory', oxobjectid = '_testOxid2'" );

            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testObjectRemove1', oxtitle='_testArticle1', oxshopid='".$this->getShopIdTest()."'" );
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testObjectRemove2', oxtitle='_testArticle2', oxshopid='".$this->getShopIdTest()."'" );
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testObjectRemove3', oxtitle='_testArticle3', oxshopid='".$this->getShopIdTest()."'" );


        oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemove1', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove1'" );
        oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemove2', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove2'" );
        oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemove3', oxcatnid='_testCategory', oxobjectid = '_testObjectRemove3'" );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxobject2category where oxobjectid like '_test%'" );
        oxDb::getDb()->execute( "delete from oxarticles where oxid like '_test%'" );
        oxDb::getDb()->execute( "delete from oxcategories where oxid like '_test%'" );

        parent::tearDown();
    }

    public function setArticleViewTable( $sParam )
    {
        $this->_sArticleView = $sParam;
    }

    public function setObject2CategoryViewTable( $sParam )
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function setShopIdTest( $sParam )
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
     * CategoryOrderAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew( 'category_order_ajax' );
        $this->assertEquals( "from ".$this->getArticleViewTable()." where  1 = 0", trim( $oView->UNITgetQuery() ) );
    }

    /**
     * CategoryOrderAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryNewOrderSess()
    {
        $aOxid = array('_testOxid1', '_testOxid2');
        $this->setSessionParam( "neworder_sess", $aOxid );
        $sArticleTable = $this->getArticleViewTable();

        $oView = oxNew( 'category_order_ajax' );
        $this->assertEquals( "from ".$sArticleTable." where  $sArticleTable.oxid in ( '_testOxid1', '_testOxid2' )", trim( $oView->UNITgetQuery() ) );
    }

    /**
     * CategoryOrderAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $aOxid = array('_testOxid1', '_testOxid2');
        $this->setSessionParam( "neworder_sess", $aOxid );
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $sO2CView = $this->getObject2CategoryViewTable();
        $sArticleTable = $this->getArticleViewTable();

        $sReturn  = "from $sArticleTable left join $sO2CView on $sArticleTable.oxid=$sO2CView.oxobjectid where $sO2CView.oxcatnid = '_testSynchoxid'";
        $sReturn .= " and $sArticleTable.oxid not in ( '_testOxid1', '_testOxid2' )";

        $oView = oxNew( 'category_order_ajax' );
        $this->assertEquals( $sReturn, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * CategoryOrderAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSorting()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $oView = oxNew( 'category_order_ajax' );
        $this->assertEquals( "order by _0 asc", trim( $oView->UNITgetSorting() ) );
    }

    /**
     * CategoryOrderAjax::_getSorting() test case
     *
     * @return null
     */
    public function testGetSortingAfterArticleIds()
    {
        $sArticleTable = $this->getArticleViewTable();
        $aOxid = array('_testOxid1', '_testOxid2');
        $this->setSessionParam( "neworder_sess", $aOxid );
        $oView = oxNew( 'category_order_ajax' );
        $this->assertEquals( "order by  $sArticleTable.oxid='_testOxid2' ,  $sArticleTable.oxid='_testOxid1'", trim( $oView->UNITgetSorting() ) );
    }

    /**
     * CategoryOrderAjax::saveNewOrder() test case
     *
     * @return null
     */
    public function testSaveNewOrder()
    {
        $sOxid = '_testCategory';
        $this->setRequestParam( "oxid", $sOxid );
        $aOxid = array('_testOxid1', '_testOxid2');
        $this->setSessionParam( "neworder_sess", $aOxid );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid1'" ) );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid2'" ) );

        $oView = oxNew( 'category_order_ajax' );
        $oView->saveNewOrder();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid1'" ) );
        $this->assertEquals( 1, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid2'" ) );
        $this->assertNull( $this->getSessionParam( "neworder_sess" ) );
    }

    /**
     * CategoryOrderAjax::remNewOrder() test case
     *
     * @return null
     */
    public function testRemNewOrder()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testCategory';
        $this->setRequestParam( "oxid", $sOxid );
        $aOxid = array('_testOxid1', '_testOxid2');
        $this->setSessionParam( "neworder_sess", $aOxid );
        // updating oxtime values
        $sQ  = "update oxobject2category set oxpos = 1 where oxobjectid = '_testOxid1' ";
        $oDb->execute( $sQ );
        $sQ  = "update oxobject2category set oxpos = 2 where oxobjectid = '_testOxid2' ";
        $oDb->execute( $sQ );
        $this->assertEquals( 1, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid1'" ) );
        $this->assertEquals( 2, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid2'" ) );

        $oView = oxNew( 'category_order_ajax' );
        $oView->remNewOrder();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid1'" ) );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select oxpos from oxobject2category where oxobjectid='_testOxid2'" ) );
    }

}