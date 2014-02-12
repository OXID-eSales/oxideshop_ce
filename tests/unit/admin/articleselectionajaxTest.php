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
 * Tests for Article_Rights_Visible_Ajax class
 */
class Unit_Admin_ArticleSelectionAjaxTest extends OxidTestCase
{
    protected $_sSelectListView = 'oxv_oxselectlist_1_de';
    
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        
            $this->setSelectListViewTable( 'oxv_oxselectlist_de' );
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testArticle', oxparentid='_testArticlePArent', oxshopid='1', oxtitle='_testArticle'" );
            oxDb::getDb()->execute( "insert into oxselectlist set oxid='_testSelectList', oxshopid='1', oxtitle='_testSelectList'" );
        
        
        oxDb::getDb()->execute( "insert into oxobject2article set oxid='_testOxid', oxobjectid='_testArtcle', oxarticlenid=''" );
        
        oxDb::getDb()->execute( "insert into oxobject2selectlist set oxid='_testOxid1', oxobjectid='_testRemove', oxselnid='_testRemove'" );
        oxDb::getDb()->execute( "insert into oxobject2selectlist set oxid='_testOxid2', oxobjectid='_testRemove', oxselnid='_testRemove'" );
    }
    
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle'" );
        oxDb::getDb()->execute( "delete from oxobject2selectlist where oxid='_testOxid'" );
        oxDb::getDb()->execute( "delete from oxobject2selectlist where oxobjectid='_testRemove'" );
        oxDb::getDb()->execute( "delete from oxobject2selectlist where oxobjectid='_testAdd'" );
        oxDb::getDb()->execute( "delete from oxselectlist where oxid='_testSelectList'" );
        oxDb::getDb()->execute( "delete from oxobject2article where oxid='_testOxid'" );
        
        parent::tearDown();
    }
    
    public function setSelectListViewTable( $sParam )
    {
        $this->_sSelectListView = $sParam;
    }
    
    public function getSelectListViewTable()
    {
        return $this->_sSelectListView;
    }
    
    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew( 'article_selection_ajax' );
        $this->assertEquals( "from oxobject2selectlist left join ".$this->getSelectListViewTable()." on ".$this->getSelectListViewTable().".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = ''", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testArticleSynchoxid';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'article_selection_ajax' );
        $this->assertEquals( "from ".$this->getSelectListViewTable()."  where ".$this->getSelectListViewTable().".oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join ".$this->getSelectListViewTable()." on ".$this->getSelectListViewTable().".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sSynchoxid'  )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidParent()
    {
        $sOxid = '_testArticle';
        modConfig::setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'article_selection_ajax' );
        $this->assertEquals( "from oxobject2selectlist left join ".$this->getSelectListViewTable()." on ".$this->getSelectListViewTable().".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sOxid' or oxobject2selectlist.oxobjectid = '_testArticlePArent'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxid()
    {
        $sSynchoxid = '_testArticleSynchoxid';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        $sOxid = '_testArticleOxid';
        modConfig::setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'article_selection_ajax' );
        $this->assertEquals( "from ".$this->getSelectListViewTable()."  where ".$this->getSelectListViewTable().".oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join ".$this->getSelectListViewTable()." on ".$this->getSelectListViewTable().".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sOxid'  )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleSelectionAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidOxidparent()
    {
        $sSynchoxid = '_testArticleSynchoxid';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        $sOxid = '_testArticle';
        modConfig::setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'article_selection_ajax' );
        $this->assertEquals( "from ".$this->getSelectListViewTable()."  where ".$this->getSelectListViewTable().".oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join ".$this->getSelectListViewTable()." on ".$this->getSelectListViewTable().".oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '$sOxid' or oxobject2selectlist.oxobjectid = '_testArticlePArent'  )", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleSelectionAjax::removeSel() test case
     *
     * @return null
     */
    public function testRemoveSel()
    {
        $oDb = oxDb::getDb();
        
        $oView = $this->getMock( "article_selection_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testOxid1', '_testOxid2' ) ) );
        
        $this->assertEquals( 2, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'" ) );
        $oView->removeSel();
        $this->assertEquals( 0, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'" ) );
    }
    
    /**
     * ArticleSelectionAjax::removeSel() test case
     *
     * @return null
     */
    public function testRemoveSelAll()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testRemove';
        modConfig::setParameter( "oxid", $sOxid );
        modConfig::setParameter( "all", true );
        
        $this->assertEquals( 2, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'" ) );
        $oView = oxNew( 'article_selection_ajax' );
        $oView->removeSel();
        $this->assertEquals( 0, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testRemove'" ) );
    }
    
    /**
     * ArticleSelectionAjax::addSel() test case
     *
     * @return null
     */
    public function testAddSel()
    {
        $oDb = oxDb::getDb();
        
        $sSynchoxid = '_testAdd';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        
        $oView = $this->getMock( "article_selection_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testAdd1', '_testAdd2' ) ) );
        
        $this->assertEquals( 0, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'" ) );
        $oView->addSel();
        $this->assertEquals( 2, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'" ) );
    }
    
    /**
     * ArticleSelectionAjax::addSel() test case
     *
     * @return null
     */
    public function testAddSelAll()
    {
        $oDb = oxDb::getDb();
        
        $sSynchoxid = '_testAdd';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        modConfig::setParameter( "all", true );
        
        
            $iCount = $oDb->getOne( "select count(oxv_oxselectlist_de.oxid)  from oxv_oxselectlist_de  where oxv_oxselectlist_de.oxid not in ( select oxobject2selectlist.oxselnid  from oxobject2selectlist left join oxv_oxselectlist_de on oxv_oxselectlist_de.oxid=oxobject2selectlist.oxselnid  where oxobject2selectlist.oxobjectid = '_testAdd'  )" );
        
        $this->assertGreaterThan( 0, $iCount );
        $this->assertEquals( 0, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'" ) );
        $oView = oxNew( 'article_selection_ajax' );
        $oView->addSel();
        $this->assertEquals( 1, $oDb->getOne( "select count(oxid) from oxobject2selectlist where oxobjectid='_testAdd'" ) );
    }
    
   
   
}