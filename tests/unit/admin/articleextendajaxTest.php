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
 * Tests for Article_Extend_Ajax class
 */
class Unit_Admin_ArticleExtendAjaxTest extends OxidTestCase
{
    protected $_sCategoriesView = 'oxv_oxcategories_1_de';
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
        
            $this->setCategoriesViewTable( 'oxv_oxcategories_de' );
            $this->setObject2CategoryViewTable( 'oxobject2category' );
            $this->setShopIdTest( 'oxbaseshop' );
            
            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testCategory', oxtitle='_testCategory', oxshopid='".$this->getShopIdTest()."'" );
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2Category', oxcatnid='_testCategory', oxobjectid = '_testObject'" );
            
            
            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testCategory1', oxtitle='_testCategory1', oxshopid='".$this->getShopIdTest()."'" );
            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testCategory2', oxtitle='_testCategory2', oxshopid='".$this->getShopIdTest()."'" );
            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testCategory3', oxtitle='_testCategory3', oxshopid='".$this->getShopIdTest()."'" );
            
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemove1', oxcatnid='_testCategory1', oxobjectid = '_testObjectRemove'" );
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemove2', oxcatnid='_testCategory2', oxobjectid = '_testObjectRemove'" );
            
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemoveAll1', oxcatnid='_testCategory1', oxobjectid = '_testObjectRemoveAll'" );
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemoveAll2', oxcatnid='_testCategory2', oxobjectid = '_testObjectRemoveAll'" );
            oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryRemoveAll3', oxcatnid='_testCategory3', oxobjectid = '_testObjectRemoveAll'" );
        
        
        oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryUpdateDate', oxcatnid='_testCategory', oxobjectid = '_testObjectUpdateDate'" );
        oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryDefault1', oxcatnid='_testCategory1', oxobjectid = '_testObjectDefault'" );
        oxDb::getDb()->execute( "insert into oxobject2category set oxid='_testObject2CategoryDefault2', oxcatnid='_testCategory2', oxobjectid = '_testObjectDefault'" );
    }
    
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxcategories where oxid='_testCategory'" );
        oxDb::getDb()->execute( "delete from oxobject2category where oxid='_testObject2Category'" );
        
        oxDb::getDb()->execute( "delete from oxcategories where oxid='_testCategory1'" );
        oxDb::getDb()->execute( "delete from oxcategories where oxid='_testCategory2'" );
        oxDb::getDb()->execute( "delete from oxcategories where oxid='_testCategory3'" );
        oxDb::getDb()->execute( "delete from oxobject2category where oxobjectid='_testObjectRemove'" );
        oxDb::getDb()->execute( "delete from oxobject2category where oxobjectid='_testObjectRemoveAll'" );
        
        oxDb::getDb()->execute( "delete from oxobject2category where oxobjectid='_testObjectAdd'" );
        oxDb::getDb()->execute( "delete from oxobject2category where oxobjectid='_testObjectUpdateDate'" );
        
        oxDb::getDb()->execute( "delete from oxobject2category where oxobjectid='_testObjectDefault'" );
        
        parent::tearDown();
    }
    
    public function setCategoriesViewTable( $sParam )
    {
        $this->_sCategoriesView = $sParam;
    }
    
    public function setObject2CategoryViewTable( $sParam )
    {
        $this->_sObject2CategoryView = $sParam;
    }
    
    public function setShopIdTest( $sParam )
    {
        $this->_sShopId = $sParam;
    }
    
    public function getCategoriesViewTable()
    {
        return $this->_sCategoriesView;
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
     * ArticleExtendAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( "from ".$this->getCategoriesViewTable()." where ".$this->getCategoriesViewTable().".oxid not in (  select ".$this->getCategoriesViewTable().".oxid from ".$this->getObject2CategoryViewTable()." left join ".$this->getCategoriesViewTable()." on ".$this->getCategoriesViewTable().".oxid=".$this->getObject2CategoryViewTable().".oxcatnid  where ".$this->getObject2CategoryViewTable().".oxobjectid = '' and ".$this->getCategoriesViewTable().".oxid is not null ) and ".$this->getCategoriesViewTable().".oxpriceto = '0'", trim( $oView->UNITgetQuery() ) );
    }   
    
    /**
     * ArticleExtendAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        modConfig::setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( "from ".$this->getObject2CategoryViewTable()." left join ".$this->getCategoriesViewTable()." on ".$this->getCategoriesViewTable().".oxid=".$this->getObject2CategoryViewTable().".oxcatnid  where ".$this->getObject2CategoryViewTable().".oxobjectid = '$sOxid' and ".$this->getCategoriesViewTable().".oxid is not null", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleExtendAjax::_getQuery() test case
     *
     * @return null
     */    
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( "from ".$this->getCategoriesViewTable()." where ".$this->getCategoriesViewTable().".oxid not in (  select ".$this->getCategoriesViewTable().".oxid from ".$this->getObject2CategoryViewTable()." left join ".$this->getCategoriesViewTable()." on ".$this->getCategoriesViewTable().".oxid=".$this->getObject2CategoryViewTable().".oxcatnid  where ".$this->getObject2CategoryViewTable().".oxobjectid = '$sSynchoxid' and ".$this->getCategoriesViewTable().".oxid is not null ) and ".$this->getCategoriesViewTable().".oxpriceto = '0'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */    
    public function testGetDataFields()
    {
        $aResult = array( array( '_0' => '_testCategory',
                                 '_1' => false,
                                 '_3' => '_testObject2Category',
                                 '_4' => 0,
                                 '_5' => '_testCategory'
                                )
                            );
        
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( $aResult, $oView->UNITgetDataFields( "select  ".$this->getCategoriesViewTable().".oxtitle as _0, ".$this->getCategoriesViewTable().".oxdesc as _1, oxobject2category.oxid as _3, oxobject2category.oxtime as _4, ".$this->getCategoriesViewTable().".oxid as _5  from oxobject2category left join ".$this->getCategoriesViewTable()." on ".$this->getCategoriesViewTable().".oxid=oxobject2category.oxcatnid  where oxobject2category.oxobjectid = '_testObject' and ".$this->getCategoriesViewTable().".oxid is not null  order by _0 asc  limit 0, 25 " ) );
    }
    
    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */    
    public function testGetDataFieldsOxid()
    {
        modConfig::setParameter( "oxid", true );
        $aResult = array( array( '_0' => '_testCategory',
                                 '_1' => false,
                                 '_3' => 0,
                                 '_4' => 0,
                                 '_5' => '_testCategory'
                                )
                            );
        
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( $aResult, $oView->UNITgetDataFields( "select  ".$this->getCategoriesViewTable().".oxtitle as _0, ".$this->getCategoriesViewTable().".oxdesc as _1, oxobject2category.oxid as _3, oxobject2category.oxtime as _4, ".$this->getCategoriesViewTable().".oxid as _5  from oxobject2category left join ".$this->getCategoriesViewTable()." on ".$this->getCategoriesViewTable().".oxid=oxobject2category.oxcatnid  where oxobject2category.oxobjectid = '_testObject' and ".$this->getCategoriesViewTable().".oxid is not null  order by _0 asc  limit 0, 25 " ) );
    }
    
    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */    
    public function testGetDataFieldsFalse()
    {
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( array( array( 'FALSE' => 0 ) ), $oView->UNITgetDataFields( 'select false' ) );
    }
    
    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */    
    public function testGetDataFieldsOxidFalse()
    {
        modConfig::setParameter( "oxid", true );
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertEquals( array( array( 'FALSE' => 0, '_3' => 0 ) ), $oView->UNITgetDataFields( 'select false' ) );
    }
    
    /**
     * ArticleExtendAjax::removeCat() test case
     *
     * @return null
     */    
    public function testRemoveCat()
    {
        $sOxid = '_testObjectRemove';
        modConfig::setParameter( "oxid", $sOxid );
        $oView = $this->getMock( "article_extend_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testCategory1', '_testCategory2' ) ) );
        $this->assertEquals( 2, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sOxid'" ) );
        
        $oView->removeCat();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sOxid'" ) );
    }
    
    /**
     * ArticleExtendAjax::removeCat() test case
     *
     * @return null
     */    
    public function testRemoveCatAll()
    {
        $sOxid = '_testObjectRemoveAll';
        modConfig::setParameter( "oxid", $sOxid );
        modConfig::setParameter( "all", true );
        
        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sOxid'" ) );
        
        $oView = oxNew( 'article_extend_ajax' );
        $oView->removeCat();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sOxid'" ) );
    }
    
    /**
     * ArticleExtendAjax::addCat() test case
     *
     * @return null
     */    
    public function testAddCat()
    {
        $sSynchoxid = '_testObjectAdd';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        $oView = $this->getMock( "article_extend_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testCategoryAdd1', '_testCategoryAdd2' ) ) );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'" ) );
        
        $oView->addCat();
        $this->assertEquals( 2, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'" ) );
    }
    
    /**
     * ArticleExtendAjax::addCat() test case
     *
     * @return null
     */    
    public function testAddCatAll()
    {
        $sSynchoxid = '_testObjectAdd';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        modConfig::setParameter( "all", true );
        
        
            $iCount = oxDb::getDb()->getOne( "select count(oxv_oxcategories_de.oxid)  from oxv_oxcategories_de where oxv_oxcategories_de.oxid not in (  select oxv_oxcategories_de.oxid from oxobject2category left join oxv_oxcategories_de on oxv_oxcategories_de.oxid=oxobject2category.oxcatnid  where oxobject2category.oxobjectid = '$sSynchoxid' and oxv_oxcategories_de.oxid is not null ) and oxv_oxcategories_de.oxpriceto = '0'" );
        
        $oView = oxNew( 'article_extend_ajax' );
        $this->assertGreaterThan( 0, $iCount );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'" ) );
        
        $oView->addCat();
        $this->assertEquals( $iCount, oxDb::getDb()->getOne( "select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'" ) );
    }
    
    /**
     * ArticleExtendAjax::_updateOxTime() test case
     *
     * @return null
     */
    public function testUpdateOxTime()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testObjectUpdateDate';
        
        $oView = oxNew( 'article_extend_ajax' );
        
        $sO2CView = $oView->UNITgetViewName('oxobject2category');
              
        // updating oxtime values
        $sQ  = "update oxobject2category set oxtime = 1 where oxobjectid = '$sOxid' ";
        $oDb->execute( $sQ );
        
        $oView->UNITupdateOxTime( $sOxid );
        $this->assertEquals( 1, $oDb->getOne( "select count(oxid) from oxobject2category where oxtime=0 and oxobjectid = '$sOxid' limit 1" ) );
    }
    
    /**
     * ArticleExtendAjax::setAsDefault() test case
     *
     * @return null
     */
    public function testSetAsDefault()
    {
        $sOxid = '_testObjectDefault';
        $sDefCat = '_testCategory1';
        modConfig::setParameter( "oxid", $sOxid );
        modConfig::setParameter( "defcat", $sDefCat );
        
        $oView = oxNew( 'article_extend_ajax' );
        
        $sShopCheck = "";
        
        $oDb = oxDb::getDb();
        $oDb->execute( "update oxobject2category set oxtime = 1 where oxobjectid = '$sOxid' " );
        
        $oView->setAsDefault();
        
        $this->assertEquals( 11, $oDb->getOne( "select oxtime from oxobject2category where oxobjectid='$sOxid' and oxcatnid!='$sDefCat'" ) );
        $this->assertEquals( 0, $oDb->getOne( "select oxtime from oxobject2category where oxobjectid='$sOxid' and oxcatnid='$sDefCat' $sShopCheck" ) );
    }
    
}