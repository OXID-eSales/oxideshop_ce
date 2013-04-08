<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: articleaccessoriesajaxTest.php 31986 2010-12-17 14:03:45Z sarunas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';
require_once getShopBasePath().'/admin/oxajax.php';

/**
 * Tests for Actions_Order_Ajax class
 */
class Unit_Admin_ArticleAccessoriesAjaxTest extends OxidTestCase
{
    protected $_sArticleView = 'oxv_oxarticles_1_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';
    
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        
        
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testArticle1', oxshopid='1', oxtitle='_testArticle1'" );
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testArticle2', oxshopid='1', oxtitle='_testArticle2'" );
            
            $this->setArticleViewTable( 'oxv_oxarticles_de' );
            $this->setObject2CategoryViewTable( 'oxobject2category' );
        
        oxDb::getDb()->execute( "insert into oxaccessoire2article set oxid='_testArticle1', OXOBJECTID='_testArticle1', OXARTICLENID='_testArticleAccessories', OXSORT='9'" );
        oxDb::getDb()->execute( "insert into oxaccessoire2article set oxid='_testArticle2', OXOBJECTID='_testArticle2', OXARTICLENID='_testArticleAccessories', OXSORT='9'" );
    }
    
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle1'" );
        oxDb::getDb()->execute( "delete from oxarticles where oxid='_testArticle2'" );        
        oxDb::getDB()->execute( "delete from oxaccessoire2article where oxarticlenid='_testArticleAccessories'" );
        oxDb::getDb()->execute( "delete from oxaccessoire2article where oxarticlenid='_testArticle1'" );
        
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

    public function getArticleViewTable()
    {
        return $this->_sArticleView;
    }
    
    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }

    
    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew( 'article_accessories_ajax' );
        $this->assertEquals( "from ".$this->getArticleViewTable()." where 1  and ".$this->getArticleViewTable().".oxparentid = ''  and ".$this->getArticleViewTable().".oxid != ''", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        modconfig::getInstance()->setConfigParam( "blVariantsSelection", true );
        $oView = oxNew( 'article_accessories_ajax' );
        $this->assertEquals( "from ".$this->getArticleViewTable()." where 1  and ".$this->getArticleViewTable().".oxid != ''", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */    
    public function testGetQueryOxid()
    {
        $sOxid = '_testArticleAccessoriesOxid';
        modConfig::setParameter( "oxid", $sOxid );
        
        $oView = oxNew( 'article_accessories_ajax' );
        $this->assertEquals( "from oxaccessoire2article left join ".$this->getArticleViewTable()." on oxaccessoire2article.oxobjectid=".$this->getArticleViewTable().".oxid  where oxaccessoire2article.oxarticlenid = '$sOxid'  and ".$this->getArticleViewTable().".oxid != '$sOxid'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */    
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testArticleAccessoriesSynchoxid';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'article_accessories_ajax' );
        $this->assertEquals( "from ".$this->getArticleViewTable()." where 1  and ".$this->getArticleViewTable().".oxparentid = ''  and ".$this->getArticleViewTable().".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$sSynchoxid'  )  and ".$this->getArticleViewTable().".oxid != '$sSynchoxid'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testArticleAccessoriesOxid';
        $sSynchoxid = '_testArticleAccessoriesSynchoxid';
        modConfig::setParameter( "oxid", $sOxid );
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        
        $oView = oxNew( 'article_accessories_ajax' );
        $this->assertEquals( "from ".$this->getObject2CategoryViewTable()." left join ".$this->getArticleViewTable()." on  ".$this->getArticleViewTable().".oxid=".$this->getObject2CategoryViewTable().".oxobjectid  where ".$this->getObject2CategoryViewTable().".oxcatnid = '$sOxid'  and ".$this->getArticleViewTable().".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$sSynchoxid'  )  and ".$this->getArticleViewTable().".oxid != '$sSynchoxid'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleAccessoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidSynchoxidVariantsSelectionTrue()
    {
        $sOxid = '_testArticleAccessoriesOxid';
        $sSynchoxid = '_testArticleAccessoriesSynchoxid';
        modConfig::setParameter( "oxid", $sOxid );
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        modconfig::getInstance()->setConfigParam( "blVariantsSelection", true );
        
        $oView = oxNew( 'article_accessories_ajax' );
        $this->assertEquals( "from ".$this->getObject2CategoryViewTable()." left join ".$this->getArticleViewTable()." on  ( ".$this->getArticleViewTable().".oxid=".$this->getObject2CategoryViewTable().".oxobjectid or ".$this->getArticleViewTable().".oxparentid=".$this->getObject2CategoryViewTable().".oxobjectid ) where ".$this->getObject2CategoryViewTable().".oxcatnid = '$sOxid'  and ".$this->getArticleViewTable().".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$sSynchoxid'  )  and ".$this->getArticleViewTable().".oxid != '$sSynchoxid'", trim( $oView->UNITgetQuery() ) );
    }
    
    /**
     * ArticleAccessoriesAjax::removeArticleAcc() test case
     *
     * @return null
     */
    public function testRemoveArticleAcc()
    {       
        $oView = $this->getMock( "article_accessories_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testArticle1', '_testArticle2' ) ) );
        $this->assertEquals( 2, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'" ) );
        
        $oView->removearticleacc();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'" ) );
    }
    
    /**
     * ArticleAccessoriesAjax::removeArticleAcc() test case
     *
     * @return null
     */
    public function testRemoveArticleAccAll()
    {
        modConfig::setParameter( "all", true );
        modConfig::setParameter( "oxid", '_testArticleAccessories' );
        
        $this->assertEquals( 2, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'" ) );
        
        $oView = oxNew( 'article_accessories_ajax' );
        $oView->removearticleacc();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where OXARTICLENID='_testArticleAccessories'" ) );
    }
    
    /**
     * ArticleAccessoriesAjax::addArticleAcc() test case
     *
     * @return null
     */
    public function testAddArticleAcc()
    {
        $oView = $this->getMock( "article_accessories_ajax", array( "_getActionIds" ) );
        modConfig::setParameter( "synchoxid", '_testArticle1' );
        
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testGroupAdd1', '_testGroupAdd2' ) ) );
        
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'" ) );
        $oView->addarticleacc();
        $this->assertEquals( 2, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'" ) );
    }
    
    /**
     * ArticleAccessoriesAjax::addArticleAcc() test case
     *
     * @return null
     */
    public function testAddArticleAccAll()
    {
        $oView = $this->getMock( "article_accessories_ajax", array( "_getActionIds" ) );
        $sSynchoxid = '_testArticle1';
        modConfig::setParameter( "synchoxid", $sSynchoxid );
        modConfig::setParameter( "all", true );
        
        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne( "select count(oxid) from ".$this->getArticleViewTable()." where 1  and ".$this->getArticleViewTable().".oxparentid = ''  and ".$this->getArticleViewTable().".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$sSynchoxid'  ) and ".$this->getArticleViewTable().".oxid != '$sSynchoxid'" );
        
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testGroupAdd1', '_testGroupAdd2' ) ) );
        
        $this->assertGreaterThan( 0, $iCount );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'" ) );
        $oView->addarticleacc();
        $this->assertEquals( $iCount, oxDb::getDb()->getOne( "select count(oxid) from oxaccessoire2article where oxarticlenid='_testArticle1'" ) );
    }
}