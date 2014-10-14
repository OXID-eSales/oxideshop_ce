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
 * Tests for Discount_Categories_Ajax class
 */
class Unit_Admin_DiscountCategoriesAjaxTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testObjectRemove1', oxtitle='_testCat1', oxshopid='oxbaseshop'" );
            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testObjectRemove2', oxtitle='_testCat2', oxshopid='oxbaseshop'" );
            oxDb::getDb()->execute( "insert into oxcategories set oxid='_testObjectRemove3', oxtitle='_testCat3', oxshopid='oxbaseshop'" );


        oxDb::getDb()->execute( "insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove1', oxtype = 'oxcategories'" );
        oxDb::getDb()->execute( "insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove2', oxtype = 'oxcategories'" );
        oxDb::getDb()->execute( "insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove3', oxtype = 'oxcategories'" );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxobject2discount where oxobjectid like '_test%'" );
        oxDb::getDb()->execute( "delete from oxcategories where oxid like '_test%'" );


        parent::tearDown();
    }

    /**
     * DiscountCategoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sCategoryTable = getViewName( "oxcategories" );

        $oView = oxNew( 'discount_categories_ajax' );
        $sQuery  = "from $sCategoryTable";
        $this->assertEquals( $sQuery, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * DiscountCategoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam( "oxid", $sOxid );
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $sCategoryTable = getViewName( "oxcategories" );

        $oView = oxNew( 'discount_categories_ajax' );
        $sQuery  = "from oxobject2discount, $sCategoryTable where $sCategoryTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxcategories'  and ";
        $sQuery .= " $sCategoryTable.oxid not in (  select $sCategoryTable.oxid from oxobject2discount, $sCategoryTable where $sCategoryTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcategories'  )";
        $this->assertEquals( $sQuery, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * DiscountCategoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $sCategoryTable = getViewName( "oxcategories" );

        $oView = oxNew( 'discount_categories_ajax' );
        $sQuery  = "from $sCategoryTable where ";
        $sQuery .= " $sCategoryTable.oxid not in (  select $sCategoryTable.oxid from oxobject2discount, $sCategoryTable where $sCategoryTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcategories'  )";
        $this->assertEquals( $sQuery, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * DiscountArticlesAjax::removeDiscCat() test case
     *
     * @return null
     */
    public function testRemoveDiscCat()
    {
        $oView = $this->getMock( "discount_categories_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testO2DRemove1', '_testO2DRemove2' ) ) );
        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );

        $oView->removeDiscCat();
        $this->assertEquals( 1, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     *
     * @return null
     */
    public function testRemoveDiscCatAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParam( "oxid", $sOxid );
        $this->setRequestParam( "all", true );

        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );

        $oView = oxNew( 'discount_categories_ajax' );
        $oView->removeDiscCat();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );
    }

    /**
     * DiscountArticlesAjax::addDiscCat() test case
     *
     * @return null
     */
    public function testAddDiscCat()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $oView = $this->getMock( "discount_categories_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testCatAdd1', '_testCatAdd1' ) ) );
        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );

        $oView->addDiscCat();
        $this->assertEquals( 5, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );
    }

    /**
     * DiscountArticlesAjax::addDiscCat() test case
     *
     * @return null
     */
    public function testAddDiscCatAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $this->setRequestParam( "all", true );

        $iCount = oxDb::getDb()->getOne( "select count(oxid) from oxcategories" );

        $oView = oxNew( 'discount_categories_ajax' );
        $this->assertGreaterThan( 0, $iCount );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'" ) );

        $oView->addDiscCat();
        $this->assertEquals( $iCount, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'" ) );
    }

}