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
 * Tests for Discount_Article_Ajax class
 */
class Unit_Admin_DiscountArticlesAjaxTest extends OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testObjectRemove1', oxtitle='_testArticle1', oxshopid='oxbaseshop'" );
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testObjectRemove2', oxtitle='_testArticle2', oxshopid='oxbaseshop'" );
            oxDb::getDb()->execute( "insert into oxarticles set oxid='_testObjectRemove3', oxtitle='_testArticle3', oxshopid='oxbaseshop'" );


        oxDb::getDb()->execute( "insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove1', oxtype = 'oxarticles'" );
        oxDb::getDb()->execute( "insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove2', oxtype = 'oxarticles'" );
        oxDb::getDb()->execute( "insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove3', oxtype = 'oxarticles'" );
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute( "delete from oxobject2discount where oxobjectid like '_test%'" );
        oxDb::getDb()->execute( "delete from oxarticles where oxid like '_test%'" );


        parent::tearDown();
    }

    /**
     * DiscountArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testOxid';
        $this->setRequestParam( "oxid", $sOxid );
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $sArticleTable = getViewName( "oxarticles" );
        $sO2CView = getViewName( "oxobject2category" );

        $oView = oxNew( 'discount_articles_ajax' );
        $sQuery  = "from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxarticles'";
        $this->assertEquals( $sQuery, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * DiscountArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam( "oxid", $sOxid );
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $sArticleTable = getViewName( "oxarticles" );
        $sO2CView = getViewName( "oxobject2category" );

        $oView = oxNew( 'discount_articles_ajax' );
        $sQuery  = "from $sO2CView left join $sArticleTable on  $sArticleTable.oxid=$sO2CView.oxobjectid ";
        $sQuery .= " where $sO2CView.oxcatnid = '_testOxid' and $sArticleTable.oxid is not null  and ";
        $sQuery .= " $sArticleTable.oxid not in (  select $sArticleTable.oxid from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxarticles'  )";
        $this->assertEquals( $sQuery, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * DiscountArticlesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $sArticleTable = getViewName( "oxarticles" );

        $oView = oxNew( 'discount_articles_ajax' );
        $sQuery  = "from $sArticleTable where 1 and $sArticleTable.oxparentid = ''  and ";
        $sQuery .= " $sArticleTable.oxid not in (  select $sArticleTable.oxid from oxobject2discount, $sArticleTable where $sArticleTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxarticles'  )";
        $this->assertEquals( $sQuery, trim( $oView->UNITgetQuery() ) );
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     *
     * @return null
     */
    public function testRemoveDiscArt()
    {
        $oView = $this->getMock( "discount_articles_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testO2DRemove1', '_testO2DRemove2' ) ) );
        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );

        $oView->removeDiscArt();
        $this->assertEquals( 1, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     *
     * @return null
     */
    public function testRemoveDiscArtAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParam( "oxid", $sOxid );
        $this->setRequestParam( "all", true );

        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );

        $oView = oxNew( 'discount_articles_ajax' );
        $oView->removeDiscArt();
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );
    }

    /**
     * DiscountArticlesAjax::addDiscArt() test case
     *
     * @return null
     */
    public function testAddDiscArt()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $oView = $this->getMock( "discount_articles_ajax", array( "_getActionIds" ) );
        $oView->expects( $this->any() )->method( '_getActionIds')->will( $this->returnValue( array( '_testArticleAdd1', '_testArticleAdd2' ) ) );
        $this->assertEquals( 3, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );

        $oView->addDiscArt();
        $this->assertEquals( 5, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'" ) );
    }

    /**
     * DiscountArticlesAjax::addDiscArt() test case
     *
     * @return null
     */
    public function testAddDiscArtAll()
    {
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParam( "synchoxid", $sSynchoxid );
        $this->setRequestParam( "all", true );

        $iCount = oxDb::getDb()->getOne( "select count(oxid) from oxarticles where oxparentid = ''" );

        $oView = oxNew( 'discount_articles_ajax' );
        $this->assertGreaterThan( 0, $iCount );
        $this->assertEquals( 0, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'" ) );

        $oView->addDiscArt();
        $this->assertEquals( $iCount, oxDb::getDb()->getOne( "select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'" ) );
    }

}