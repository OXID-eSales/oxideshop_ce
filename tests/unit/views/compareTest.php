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
 * Tests for compate class
 */
class Unit_Views_compareTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $myDB = oxDb::getDB();
        $sShopId = oxConfig::getInstance()->getShopId();
        // adding article to recommendlist
        $sQ = 'insert into oxrecommlists ( oxid, oxuserid, oxtitle, oxdesc, oxshopid ) values ( "testlist", "oxdefaultadmin", "oxtest", "oxtest", "'.$sShopId.'" ) ';
        $myDB->Execute( $sQ );
        $sQ = 'insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( "testlist", "2000", "testlist", "test" ) ';
        $myDB->Execute( $sQ );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();
        $sDelete = 'delete from oxrecommlists where oxid like "testlist%" ';
        $myDB->execute( $sDelete );

        $sDelete = 'delete from oxobject2list where oxlistid like "testlist%" ';
        $myDB->execute( $sDelete );

        $this->cleanUpTable( 'oxreviews' );
        parent::tearDown();
    }

    /**
     * compare::moveLeft() test case
     *
     * @return null
     */
    public function testMoveLeft()
    {
        modConfig::setParameter( 'aid', "testId2" );
        $aItems  = array( "testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3" );
        $aResult = array( "testId1" => true, "testId2" => true, "testId3" => true );

        $oView = $this->getMock( "compare", array( "getCompareItems", "setCompareItems" ) );
        $oView->expects( $this->once() )->method( 'getCompareItems')->will( $this->returnValue( $aItems ) );
        $oView->expects( $this->once() )->method( 'setCompareItems')->with( $this->equalTo( $aResult ) );
        $oView->moveLeft();
    }

    /**
     * bug #0001566
     */
    public function testMoveLeftSkipsIfNoAnid()
    {
        modConfig::setParameter( 'aid', "" );

        $oView = $this->getMock( "compare", array( "getCompareItems", "setCompareItems" ) );
        $oView->expects( $this->never() )->method( 'getCompareItems');
        $oView->expects( $this->never() )->method( 'setCompareItems');
        $oView->moveLeft();
    }

    /**
     * compare::moveRight() test case
     *
     * @return null
     */
    public function testMoveRight()
    {
        modConfig::setParameter( 'aid', "testId2" );
        $aItems  = array( "testId1" => "testVal1", "testId2" => "testVal2", "testId3" => "testVal3" );
        $aResult = array( "testId1" => true, "testId2" => true, "testId3" => true );

        $oView = $this->getMock( "compare", array( "getCompareItems", "setCompareItems" ) );
        $oView->expects( $this->once() )->method( 'getCompareItems')->will( $this->returnValue( $aItems ) );
        $oView->expects( $this->once() )->method( 'setCompareItems')->with( $this->equalTo( $aResult ) );
        $oView->moveRight();
    }

    /**
     * bug #0001566
     */
    public function testMoveRightSkipsIfNoAnid()
    {
        modConfig::setParameter( 'aid', "" );

        $oView = $this->getMock( "compare", array( "getCompareItems", "setCompareItems" ) );
        $oView->expects( $this->never() )->method( 'getCompareItems');
        $oView->expects( $this->never() )->method( 'setCompareItems');
        $oView->moveRight();
    }

    /**
     * compare::render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = new compare();

        $this->assertEquals( "page/compare/compare.tpl", $oView->render() );

    }

    /**
     * compare::render() & compare::inPopup() test case
     *
     * @return null
     */
    public function testRenderInPopup()
    {
        $oView = new compare();

        $oView->inPopup();
        $this->assertEquals( "compare_popup.tpl", $oView->render() );

    }

    /**
     * compare::getOrderCnt() test case
     *
     * @return null
     */
    public function testGetOrderCnt()
    {
        $oUser = $this->getMock( "oxuser", array( "getOrderCount" ) );
        $oUser->expects( $this->once() )->method( 'getOrderCount')->will( $this->returnValue( 999 ) );

        $oView = $this->getMock( "compare", array( "getUser" ) );
        $oView->expects( $this->once() )->method( 'getUser')->will( $this->returnValue( $oUser ) );

        $this->assertEquals( 999, $oView->getOrderCnt() );
    }

    public function testSetCompareItemsgetCompareItems()
    {
        modSession::getInstance()->setVar( 'aFiltcompproducts', array( "testItems1" ) );
        $oView = new compare();
        $this->assertEquals( array( "testItems1" ), $oView->getCompareItems() );

        $oView = new compare();
        $oView->setCompareItems( array( "testItems2" ) );
        $this->assertEquals( array( "testItems2" ), $oView->getCompareItems() );
        $this->assertEquals( array( "testItems2" ), oxSession::getVar( 'aFiltcompproducts' ) );
    }

    /**
     * Test get compare article list.
     *
     * @return null
     */
    public function testGetCompArtList()
    {
        $oCompare = $this->getProxyClass( "compare" );
        $oArticle = oxNew("oxarticle");
        $oArticle->load('1672');
        $oCompare->setNonPublicVar( "_aCompItems", array ( '1672' => $oArticle) );
        $aArtList = $oCompare->getCompArtList();
        $this->assertEquals(  array('1672'), array_keys($aArtList));
    }

    /**
     * Test get compare article count.
     *
     * @return null
     */
    public function testGetCompareItemsCnt()
    {
        $oCompare = $this->getProxyClass( "compare" );
        $oArticle = oxNew("oxarticle");
        $oCompare->setNonPublicVar( "_aCompItems", array ( '1672' => $oArticle, '2000' => $oArticle) );
        $this->assertEquals(  2, $oCompare->getCompareItemsCnt());
    }

    public function testGetSetCompareItemsCnt()
    {
        $oView = $this->getProxyClass( 'compare' );
        $oView->setCompareItemsCnt( 10 );
        $this->assertEquals( 10, $oView->getCompareItemsCnt() );
    }

    /**
     * Test get attribute list.
     *
     * @return null
     */
    public function testGetAttributeList()
    {
        $oCompare = $this->getProxyClass( "compare" );
        $oArticle = oxNew("oxarticle");
        $oCompare->setNonPublicVar( "_oArtList", array ( '1672' => $oArticle) );
        $aAttributes = $oCompare->getAttributeList();

        $sSelect = "select oxattrid, oxvalue from oxobject2attribute where oxobjectid = '1672'";
        $rs = oxDb::getDB()->execute($sSelect);
        $sSelect = "select oxtitle from oxattribute where oxid = '".$rs->fields[0]."'";
        $sTitle = oxDb::getDB()->getOne($sSelect);
        $this->assertEquals( $rs->fields[1], $aAttributes[$rs->fields[0]]->aProd['1672']->value);
        $this->assertEquals( $sTitle, $aAttributes[$rs->fields[0]]->title);
    }

    /**
     * Test get ids for similar recommendation list.
     *
     * @return null
     */
    public function testGetSimilarRecommListIds()
    {
        $sArrayKey = "articleId";
        $aArrayKeys = array( $sArrayKey );
        $oArtList = array( $sArrayKey => "zyyy" );

        $oSearch = $this->getMock( "compare", array( "getCompArtList" ) );
        $oSearch->expects( $this->once() )->method( "getCompArtList" )->will( $this->returnValue( $oArtList ) );
        $this->assertEquals( $aArrayKeys, $oSearch->getSimilarRecommListIds(), "getSimilarRecommListIds() should return array of keys from result of getCompArtList()" );
    }

    /**
     * Test get page navigation.
     *
     * @return null
     */
    public function testGetPageNavigation()
    {
        $oCompare = $this->getMock( 'compare', array( 'generatePageNavigation' ));
        $oCompare->expects( $this->any() )->method( 'generatePageNavigation')->will($this->returnValue( "aaa" ) );
        $this->assertEquals( 'aaa', $oCompare->getPageNavigation() );
    }

    /**
     * Test paging off
     *
     * @return null
     */
    public function testSetNoPaging()
    {
        $oCompare = $this->getMock( 'compare', array( '_setArticlesPerPage' ));

        $oCompare->expects( $this->once() )->method('_setArticlesPerPage')->with( $this->equalTo(0) );
        $oCompare->setNoPaging();
    }

    /**
     * Test number of item in compare list
     *
     * @return null
     */
    public function testSetArticlesPerPage()
    {
        $cl = oxTestModules::addFunction('compare', '_getArticlesPerPage', '{return $this->_iArticlesPerPage;}');
        $oCompare = new $cl;

        $oCompare->UNITsetArticlesPerPage(5);
        $this->assertEquals(5, $oCompare->_getArticlesPerPage());
        $oCompare->UNITsetArticlesPerPage(50);
        $this->assertEquals(50, $oCompare->_getArticlesPerPage());
        $oCompare->UNITsetArticlesPerPage(-50);
        $this->assertEquals(-50, $oCompare->_getArticlesPerPage());

    }

    public function testGetBreadCrumb()
    {
        $oCompare = new Compare();
        $aCatPath = array();
        $aResult  = array();

        $aCatPath['title'] = oxLang::getInstance()->translateString( 'MY_ACCOUNT', 0, false );
        $aCatPath['link']  = oxSeoEncoder::getInstance()->getStaticUrl( $oCompare->getViewConfig()->getSelfLink() . 'cl=account' );

        $aResult[] = $aCatPath;

        $aCatPath['title'] = oxLang::getInstance()->translateString( 'PRODUCT_COMPARISON', 0, false );
        $aCatPath['link']  = $oCompare->getLink();

        $aResult[] = $aCatPath;

        $this->assertEquals( $aResult, $oCompare->getBreadCrumb() );
    }

    /**
     * Testing #4391 fix
     */
    public function testChangeArtListOrderWithNotExistingProduct()
    {
        $oSubj = $this->getProxyClass("Compare");
        $aItems = array("1126" => true, "nonExistingVal" => true, "1127" => true);
        $oArtList = new oxArticleList();
        $oArtList->loadIds( array_keys( $aItems) );

        $oResList = $oSubj->UNITchangeArtListOrder($aItems, $oArtList);

        $this->assertArrayHasKey("1126", $oResList);
        $this->assertArrayNotHasKey("nonExistingVal", $oResList);
        $this->assertArrayHasKey("1127", $oResList);

    }

}
