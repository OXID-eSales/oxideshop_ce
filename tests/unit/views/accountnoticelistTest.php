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
 * @version   SVN: $Id: accountTest.php 25505 2010-02-02 02:12:13Z alfonsas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for Account class
 */
class Unit_Views_accountNoticelistTest extends OxidTestCase
{
    /**
     * Testing Account_Noticelist::getSimilarRecommLists()
     *
     * @return null
     */
    public function testGetSimilarRecommListsEmptyNoticeProductList()
    {
        $oView = $this->getMock( "Account_Noticelist", array( "getNoticeProductList" ) );
        $oView->expects( $this->any() )->method( 'getNoticeProductList')->will( $this->returnValue( false ) );
        $this->assertFalse( $oView->getSimilarRecommLists() );
    }

    /**
     * Testing Account_Noticelist::getSimilarRecommLists()
     *
     * @return null
     */
    public function testGetSimilarRecommLists()
    {
        oxTestModules::addFunction('oxrecommlist', 'getRecommListsByIds', '{ return "testList"; }' );

        $oView = $this->getMock( "Account_Noticelist", array( "getNoticeProductList" ) );
        $oView->expects( $this->any() )->method( 'getNoticeProductList')->will( $this->returnValue( array( "1126" ) ) );
        $this->assertEquals( "testList", $oView->getSimilarRecommLists() );
    }

    /**
     * Testing Account_Noticelist::getSimilarProducts()
     *
     * @return null
     */
    public function testGetSimilarProductsEmptyProductList()
    {
        $oView = $this->getMock( "Account_Noticelist", array( "getNoticeProductList" ) );
        $oView->expects( $this->any() )->method( 'getNoticeProductList')->will( $this->returnValue( array() ) );
        $this->assertNull( $oView->getSimilarProducts() );
    }

    /**
     * Testing Account_Noticelist::getSimilarProducts()
     *
     * @return null
     */
    public function testGetSimilarProducts()
    {
        $oProduct = $this->getMock( "oxStdClass", array( "getSimilarProducts" ) );
        $oProduct->expects( $this->any() )->method( 'getSimilarProducts')->will( $this->returnValue( "testSimilarProducts" ) );

        $oView = $this->getMock( "Account_Noticelist", array( "getNoticeProductList" ) );
        $oView->expects( $this->any() )->method( 'getNoticeProductList')->will( $this->returnValue( array( $oProduct ) ) );
        $this->assertEquals( "testSimilarProducts", $oView->getSimilarProducts() );
    }

    /**
     * Testing Account_Noticelist::getNoticeProductList()
     *
     * @return null
     */
    public function testGetNoticeProductListNoSessionUser()
    {
        $oView = $this->getMock( "Account_Noticelist", array( "getUser" ) );
        $oView->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( false ) );
        $this->assertNull( $oView->getNoticeProductList() );
    }

    /**
     * Testing Account_Noticelist::getNoticeProductList()
     *
     * @return null
     */
    public function testGetNoticeProductList()
    {
        $oBasket = $this->getMock( "oxStdClass", array( "getArticles" ) );
        $oBasket->expects( $this->once() )->method( 'getArticles')->will( $this->returnValue( "articles" ) );

        $oUser = $this->getMock( "oxStdClass", array( "getBasket" ) );
        $oUser->expects( $this->once() )->method( 'getBasket')->with( $this->equalTo( "noticelist" ))->will( $this->returnValue( $oBasket ) );

        $oView = $this->getMock( "Account_Noticelist", array( "getUser" ) );
        $oView->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
        $this->assertEquals( "articles", $oView->getNoticeProductList() );
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock( "Account_Noticelist", array( "getUser" ) );
        $oView->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( false ) );
        $this->assertEquals( 'page/account/login.tpl', $oView->render() );
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oUser = new oxuser;
        $oUser->oxuser__oxpassword = new oxField( "testPassword" );

        $oView = $this->getMock( "Account_Noticelist", array( "getUser" ) );
        $oView->expects( $this->any() )->method( 'getUser')->will( $this->returnValue( $oUser ) );
        $this->assertEquals( 'page/account/noticelist.tpl', $oView->render() );
    }

	/**
     * Testing Account_Newsletter::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccNoticeList = new Account_Noticelist();

        $this->assertEquals(2, count($oAccNoticeList->getBreadCrumb()));
    }


}