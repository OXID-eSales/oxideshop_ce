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

class Unit_Views_wishlistTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $this->tearDown();
        parent::setUp();

        $this->_oUser = oxNew( 'oxuser' );
        $this->_oUser->setId('_testId' );
        $this->_oUser->oxuser__oxusername = new oxField( 'testUserName', oxField::T_RAW );
        $this->_oUser->oxuser__oxactive   = new oxField( 1, oxField::T_RAW );
        $this->_oUser->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxuserbaskets');
        $this->cleanUpTable('oxuserbasketitems');

        parent::tearDown();
    }

    /*
     * Checking getting wishlist user
     */
    public function testGetWishUser()
    {
        modConfig::setParameter( 'wishid', '_testId' );
        $oWishList = oxNew( "Wishlist" );

        $oWishUser = $oWishList->getWishUser();

        $this->assertEquals( $this->_oUser->getId(), $oWishUser->getId() );
        $this->assertEquals( '_testId', oxSession::getVar('wishid') );
    }

    /*
     * Checking getting wishlist
     */
    public function testGetWishList()
    {
        modConfig::setParameter( 'wishid', '_testId' );
        $oWishList = oxNew( "Wishlist" );
        $myDB      = oxDb::getDB();

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle, oxpublic ) values ( "_testBasketId1", "'.$this->_oUser->getId().'", "wishlist", 1 ) ';
        $myDB->Execute( $sQ );

        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "_testId1", "_testBasketId1", "1126", "1" ) ';
        $myDB->Execute( $sQ );

        $oList = $oWishList->getWishList();
        $this->assertEquals( 1, count( $oList ) );
        $oArticle = array_pop( $oList );
        $this->assertEquals( '1126', $oArticle->getId() );
    }


    /*
     * Checking getting wishlist
     */
    public function testGetWishListIactive()
    {
        modConfig::setParameter( 'wishid', '_testId' );
        $oWishList = oxNew( "Wishlist" );
        $myDB      = oxDb::getDB();

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle, oxpublic ) values ( "_testBasketId1", "'.$this->_oUser->getId().'", "wishlist", 0 ) ';
        $myDB->Execute( $sQ );

        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "_testId1", "_testBasketId1", "1126", "1" ) ';
        $myDB->Execute( $sQ );

        $oList = $oWishList->getWishList();
        $this->assertFalse($oList);
    }

    /*
     * Checking searching wishlist of another user
     */
    public function testSearchForWishList()
    {

        modConfig::setParameter( 'search', 'testUserName' );

        $oWishList = $this->getProxyClass("Wishlist");
        $myDB      = oxDb::getDB( oxDB::FETCH_MODE_ASSOC );

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle ) values ( "_testBasketId1", "'.$this->_oUser->getId().'", "wishlist" ) ';
        $myDB->Execute( $sQ );

        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "_testId1", "_testBasketId1", "1126", "1" ) ';
        $myDB->Execute( $sQ );

        $oWishList->searchForWishList();
        $oUsersList = $oWishList->getNonPublicVar( '_oWishListUsers' );
        $oUser = $oUsersList->current();
        $this->assertEquals( '_testId', $oUser->getId() );
        $this->assertEquals( 'testUserName', $oWishList->getNonPublicVar( '_sSearchParam' ) );
    }

    /*
     * Checking getter of wishlist user
     */
    public function testGetWishListUsers()
    {
        $oWishList = $this->getProxyClass("Wishlist");

        $oWishList->setNonPublicVar( '_oWishListUsers', 'testValue' );
        $this->assertEquals( 'testValue', $oWishList->getWishListUsers() );
    }

    /*
     * Checking getter of wishlist search parameter
     */
    public function testGetWishListSearchParam()
    {
        $oWishList = $this->getProxyClass("Wishlist");

        $oWishList->setNonPublicVar( '_sSearchParam', 'testValue' );
        $this->assertEquals( 'testValue', $oWishList->getWishListSearchParam() );
    }

    /*
     * Testing render method
     */
    public function testRender()
    {
        $oWishList = $this->getProxyClass("Wishlist");

        $this->assertEquals( 'page/wishlist/wishlist.tpl', $oWishList->render() );
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oWishList = new Wishlist();
        $aResults  = array();
        $aResult   = array();

        $aResult["title"] = oxLang::getInstance()->translateString( 'PUBLIC_GIFT_REGISTRIES', oxLang::getInstance()->getBaseLanguage(), false );
        $aResult["link"]  = $oWishList->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oWishList->getBreadCrumb());
    }

}
