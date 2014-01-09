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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * OxDiscountList tester
 */
class Unit_Core_oxDiscountlistTest extends OxidTestCase
{

    public $aDiscountIds    = array();
    public $aDiscountArtIds = array();
    public $aTransparentDiscountArtIds = array();

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule( 'modOxUtilsDate' );
        $this->cleanUpTable( 'oxobject2discount' );
        $this->cleanUpTable( 'oxcategories' );
        parent::tearDown();
    }

    // just SQL cleaner ..
    protected function cleanSQL( $sQ )
    {
        return preg_replace( array( '/[^\w\'\:\-\.\*]/' ), '', $sQ );
    }

    /**
     * Testing if discount list will be build even some data is wrong
     */
    public function testGetDiscountListWithSomeWrongData()
    {
        $oUser = new oxUser();
        $oUser->load( "oxdefaultadmin" );

        $oArticle = new oxArticle();
        $oArticle->load( "1431" );

        $oBasket = new oxBasket();
        $oBasket->addToBasket( "1431", 1 );
        $oBasket->calculateBasket();

        $oDiscountList = new oxDiscountList();
        $oDiscountList = $oDiscountList->getBasketItemDiscounts( $oArticle, $oBasket, $oUser );
        $iListCOunt = count( $oDiscountList );

        // list must contain at least one item
        $this->assertTrue( $iListCOunt > 0 );

        $oDiscount = current( $oDiscountList );

        // adding garbage
        $oGarbage = new oxbase();
        $oGarbage->init( "oxobject2discount" );
        $oGarbage->setId( "_testoxobject2discount1" );
        $oGarbage->oxobject2discount__oxdiscountid = new oxField( $oDiscount->getId() );
        $oGarbage->oxobject2discount__oxobjectid   = new oxField( "yyy" );
        $oGarbage->oxobject2discount__oxtype       = new oxField( "oxcountry" );
        $oGarbage->save();

        $oGarbage = new oxbase();
        $oGarbage->init( "oxobject2discount" );
        $oGarbage->setId( "_testoxobject2discount2" );
        $oGarbage->oxobject2discount__oxdiscountid = new oxField( $oDiscount->getId() );
        $oGarbage->oxobject2discount__oxobjectid   = new oxField( "yyy" );
        $oGarbage->oxobject2discount__oxtype       = new oxField( "oxuser" );
        $oGarbage->save();

        $oGarbage = new oxbase();
        $oGarbage->init( "oxobject2discount" );
        $oGarbage->setId( "_testoxobject2discount3" );
        $oGarbage->oxobject2discount__oxdiscountid = new oxField( $oDiscount->getId() );
        $oGarbage->oxobject2discount__oxobjectid   = new oxField( "yyy" );
        $oGarbage->oxobject2discount__oxtype       = new oxField( "oxgroups" );
        $oGarbage->save();

        $oDiscountList = new oxDiscountList();
        $oDiscountList = $oDiscountList->getBasketItemDiscounts( $oArticle, $oBasket, $oUser );
        $iNewListCount = count( $oDiscountList );

        // list must contain at least one item
        $this->assertTrue( $iNewListCount > 0 );
        $this->assertTrue( $iNewListCount === $iListCOunt );

        $blFound = false;
        foreach ( $oDiscountList as $oDisc ) {
            if ( $oDiscount->getId() == $oDisc->getId() ) {
                $blFound = true;
                break;
            }
        }
        $this->assertTrue( $blFound, "Error, delivery set not found" );
    }

    public function testApplyDiscounts()
    {
        //$oDiscount = $this->getMock( 'oxdiscount', array( 'getAbsValue') );
        //$oDiscount->expects( $this->any() )->method( 'getAbsValue')->will( $this->returnValue( 13 ) );

        $oDiscount = new oxdiscount();
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(13, oxField::T_RAW);

        $aDiscounts = array($oDiscount);
        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice(123);

        $oDiscountList = new oxDiscountList();
        $oDiscountList->applyDiscounts( $oPrice, $aDiscounts );
        $this->assertEquals( 110, $oPrice->getBruttoPrice() );
    }



    /**
     * testing applaying basket discounts
     */
    public function testApplyBasketDiscounts()
    {
        $oDiscount = oxNew( 'oxDiscount' );
        $oDiscount->setId( '_testDiscountId' );
        $oDiscount->oxdiscount__oxtitle = new oxField('testDiscountTitle', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(5, oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $aDiscounts[] = $oDiscount;

        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice( 25 );

        $oDiscountList = new oxDiscountList();
        $aResult = $oDiscountList->applyBasketDiscounts( $oPrice, $aDiscounts );

        $oResDiscount = reset( $aResult );
        $aResDiscountKeys = array_keys( $aResult );

        $this->assertEquals( 1, count($oResDiscount) );
        $this->assertEquals( '_testDiscountId', $aResDiscountKeys[0] );
        $this->assertEquals( '_testDiscountId', $oResDiscount->sOXID );
        $this->assertEquals( 'testDiscountTitle', $oResDiscount->sDiscount );
        $this->assertEquals( 5, $oResDiscount->dDiscount );
        $this->assertEquals( 20, $oPrice->getBruttoPrice());
    }

    /**
     * testing applaying basket discounts
     */
    public function testApplyBasketDiscountsWithAmount()
    {
        $oDiscount = oxNew( 'oxDiscount' );
        $oDiscount->setId( '_testDiscountId' );
        $oDiscount->oxdiscount__oxtitle = new oxField('testDiscountTitle', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(5, oxField::T_RAW);
        $aDiscounts[] = $oDiscount;

        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice( 25 );

        $oDiscountList = new oxDiscountList();
        $aResult = $oDiscountList->applyBasketDiscounts( $oPrice, $aDiscounts, 2 );

        $oResDiscount = reset( $aResult );
        $aResDiscountKeys = array_keys( $aResult );

        $this->assertEquals( 1, count($oResDiscount) );
        $this->assertEquals( '_testDiscountId', $aResDiscountKeys[0] );
        $this->assertEquals( '_testDiscountId', $oResDiscount->sOXID);
        $this->assertEquals( 'testDiscountTitle', $oResDiscount->sDiscount );
        $this->assertEquals( 2.5, $oResDiscount->dDiscount );
        $this->assertEquals( 23.75, $oPrice->getBruttoPrice());
    }

    /**
     * testing applaying basket discounts does not affects price parameter
     */
    public function testApplyBasketDiscountsDoesNotAffectsPriceParam()
    {
        $oDiscount = oxNew( 'oxDiscount' );
        $oDiscount->setId( '_testDiscountId' );
        $oDiscount->oxdiscount__oxtitle = new oxField('testDiscountTitle', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(5, oxField::T_RAW);
        $aDiscounts[] = $oDiscount;

        $oPrice = oxNew( 'oxPrice' );
        $oPrice->setPrice( 25 );

        $oDiscountList = new oxDiscountList();
        $oDiscountList->applyBasketDiscounts( $oPrice, $aDiscounts );

        $this->assertEquals( 23.75, $oPrice->getBruttoPrice() );
    }

    public function testApplyBasketDiscountsAbsWithAmount()
    {
        $oPrice = new oxPrice();
        $oPrice->setPrice( 100 );

        $oDiscount = new oxDiscount();
        $oDiscount->setId( 'xxx' );
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField(5, oxField::T_RAW);

        $oDiscountList = new oxDiscountList();
        $aUsedDiscounts = $oDiscountList->applyBasketDiscounts( $oPrice, array( $oDiscount ), 5 );

        $this->assertTrue( isset( $aUsedDiscounts['xxx'] ) );
        $this->assertEquals( 25, $aUsedDiscounts['xxx']->dDiscount );
        $this->assertEquals( 95, $oPrice->getBruttoPrice());
    }

    /**
     * Testing #3587 case. We are using price information reported in bug entry.
     * %2 from 2.98*200 is 11.92 and not 12.00 like it was before the bug fix
     */
    /*public function testApplyBaskketDiscountsLargeAmountDiscountPrecission()
    {
        //INIT
        $dInitialPrice = 2.98;
        $dDiscountValue = 2;//%
        $dAmount = 200;
        $dExpectedDiscountValue = 11.92;
        $dExpectedDiscountedPrice = 2.92;

        //IMPLEMENT
        //price information
        $oPrice = new oxPrice();
        $oPrice->setPrice($dInitialPrice);

        $oDiscount = new oxDiscount();
        $oDiscount->setId( 'testDiscount' );
        $oDiscount->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDiscount->oxdiscount__oxaddsum = new oxField($dDiscountValue, oxField::T_RAW);

        $oDiscountList = new oxDiscountList();
        $aUsedDiscounts = $oDiscountList->applyBasketDiscounts( $oPrice, array( $oDiscount ), $dAmount );

        $this->assertTrue( isset( $aUsedDiscounts['testDiscount'] ) );
        //rounding the result due to strange double conversion error:
        //"Failed asserting that <double:11.92> matches expected <double:11.92>."
        $this->assertEquals( $dExpectedDiscountValue, round($aUsedDiscounts['testDiscount']->dDiscount, 12) );
        $this->assertEquals( $dExpectedDiscountedPrice, $oPrice->getBruttoPrice());
    }*/

    /**
     * Testing #3587 case. Here we are using price information reported in bug entry.
     * %2 from 2.98*200 is 11.92 and not 12.00 like it was before the bug fix.
     *
     * Also adding additional discount for discount combination testing
     */
    /*public function testApplyBaskketDiscountsLargeAmountDiscountPrecissionCombined()
    {
        //INIT
        $dInitialPrice = 2.98;
        $dDiscountValue1 = 2;//%
        $dDiscountValue2 = 1;//abs
        $dAmount = 200;
        $dExpectedDiscountValue1 = 11.92;
        $dExpectedDiscountValue2 = 200.0;
        $dExpectedDiscountedPrice1 = 1.92;

        //IMPLEMENT
        //price information
        $oPrice = new oxPrice();
        $oPrice->setPrice($dInitialPrice);

        $oDiscount1 = new oxDiscount();
        $oDiscount1->setId( 'testDiscount1' );
        $oDiscount1->oxdiscount__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oDiscount1->oxdiscount__oxaddsum = new oxField($dDiscountValue1, oxField::T_RAW);

        $oDiscount2 = new oxDiscount();
        $oDiscount2->setId( 'testDiscount2' );
        $oDiscount2->oxdiscount__oxaddsumtype = new oxField('abs', oxField::T_RAW);
        $oDiscount2->oxdiscount__oxaddsum = new oxField($dDiscountValue2, oxField::T_RAW);

        $oDiscountList = new oxDiscountList();
        $aUsedDiscounts = $oDiscountList->applyBasketDiscounts( $oPrice, array( $oDiscount1, $oDiscount2 ), $dAmount );

        $this->assertTrue( isset( $aUsedDiscounts['testDiscount1'] ) );
        $this->assertTrue( isset( $aUsedDiscounts['testDiscount2'] ) );
        //rounding the result due to strange double conversion error:
        //"Failed asserting that <double:11.92> matches expected <double:11.92>."
        $this->assertEquals( $dExpectedDiscountValue1, round($aUsedDiscounts['testDiscount1']->dDiscount, 12) );
        $this->assertEquals( $dExpectedDiscountValue2, round($aUsedDiscounts['testDiscount2']->dDiscount, 12) );
        $this->assertEquals( $dExpectedDiscountedPrice1, $oPrice->getBruttoPrice());
    }*/

    /**
     * Checking dicount list initializer
     */
    // with user
    public function testGetList()
    {
        $oUser = $this->getMock( 'oxuser', array( 'getId' ) );
        $oUser->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'xxx' ) );

        $oList = $this->getMock( 'oxdiscountlist', array( 'selectString', '_getFilterSelect' ) );
        $oList->expects( $this->once() )->method( 'selectString' );
        $oList->expects( $this->once() )->method( '_getFilterSelect' );
        $oList->UNITgetList( $oUser );
    }

    // testing returned data
    public function testGetListDataCheckNoUser()
    {
        $oList = new oxdiscountlist();
        $oList->UNITgetList();

        // checking using demo data
        $this->assertEquals( 1, $oList->count() );
        $this->assertEquals( array( '4e542e4e8dd127836.00288451' ),
                             array_keys( $oList->aList ) );
    }
    public function testGetListDataCheckAdminUser()
    {
        $oUser = new oxuser();
        $oUser->load( 'oxdefaultadmin' );

        $oList = new oxdiscountlist();
        $oList->UNITgetList( $oUser );

        // checking using demo data
        $this->assertEquals( 1, $oList->count() );
        $this->assertEquals( array( '4e542e4e8dd127836.00288451' ),
                             array_keys( $oList->aList ) );
    }

    /**
     * Testing country ID getter
     */
    // no user, will be taken from config
    public function testGetCountryIdNoUserExpectsConfigCountry()
    {
        $oList = new oxdiscountlist();
        $this->assertNull( $oList->getCountryId( null ) );
    }
    // taking user country id
    public function testGetCountryIdAdminUser()
    {
        $oUser = new oxuser();
        $oUser->load( 'oxdefaultadmin' );

        $oList = new oxdiscountlist();
        $this->assertEquals( 'a7c40f631fc920687.20179984', $oList->getCountryId( $oUser ) );
    }

    /**
     * Testing discount filter SQL getter
     */
    // no user
    public function testGetFilterSelectNoUser()
    {
        $iCurrTime = time();
        oxTestModules::addFunction( "oxUtilsDate", "getTime", "{ return $iCurrTime; }");
        $sUserTable    = getViewName( 'oxuser' );
        $sGroupTable   = getViewName( 'oxgroups' );
        $sCountryTable = getViewName( 'oxcountry' );

        $oList = new oxdiscountlist();

        // default oxConfig country check.
        $sTable = getViewName( 'oxdiscount' );
        $sQ = "select ".$oList->getBaseObject()->getSelectFields()." from $sTable where ( ( $sTable.oxactive = 1 or ( $sTable.oxactivefrom < '".date( 'Y-m-d H:i:s', $iCurrTime )."' and $sTable.oxactiveto > '".date( 'Y-m-d H:i:s', $iCurrTime )."')) ) and (
            select
                if(EXISTS(select 1 from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1),
                        0,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1),
                        0,
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1),
                        0,
                        1)
            )";

        $this->assertEquals( $this->cleanSQL( $sQ ), $this->cleanSQL( $oList->UNITgetFilterSelect( null ) ) );
    }
    // admin user
    public function testGetFilterSelectAdminUser()
    {
        oxTestModules::addFunction("oxUtilsDate", "getTime", "{return 0;}");
        $sUserTable    = getViewName( 'oxuser' );
        $sGroupTable   = getViewName( 'oxgroups' );
        $sCountryTable = getViewName( 'oxcountry' );

        $oUser = new oxuser();
        $oUser->load( 'oxdefaultadmin' );

        $sGroupIds = '';
        foreach ( $oUser->getUserGroups() as $oGroup ) {
            if ( $sGroupIds )
                $sGroupIds .= ', ';
            $sGroupIds .= "'".$oGroup->getId()."'";
        }

        $oList = new oxdiscountlist();

        // default oxConfig country check.
        $sTable = getViewName( 'oxdiscount' );
        $sQ = "select ".$oList->getBaseObject()->getSelectFields()." from $sTable where ".$oList->getBaseObject()->getSqlActiveSnippet()." and (
            select
                if(EXISTS(select 1 from oxobject2discount, $sCountryTable where $sCountryTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' LIMIT 1),
                        EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxcountry' and oxobject2discount.OXOBJECTID='a7c40f631fc920687.20179984'),
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sUserTable where $sUserTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' LIMIT 1),
                        EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxuser' and oxobject2discount.OXOBJECTID='oxdefaultadmin'),
                        1) &&
                if(EXISTS(select 1 from oxobject2discount, $sGroupTable where $sGroupTable.oxid=oxobject2discount.oxobjectid and oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' LIMIT 1),
                        EXISTS(select oxobject2discount.oxid from oxobject2discount where oxobject2discount.OXDISCOUNTID=$sTable.OXID and oxobject2discount.oxtype='oxgroups' and oxobject2discount.OXOBJECTID in ($sGroupIds) ),
                        1)
            )";

        $this->assertEquals( $this->cleanSQL( $sQ ), $this->cleanSQL( $oList->UNITgetFilterSelect( $oUser ) ) );
    }

    /**
     * Testing discount loader by type
     */
    // article discounts
    public function testGetArticleDiscounts()
    {
        // just simulating article
        $oArticle = new oxArticle();
        $oArticle->xxx = 'yyy';

        $aDiscounts[0] = $this->getMock( 'oxdiscount', array( 'isForArticle', 'getId' ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForArticle' )->will( $this->returnValue( true ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'xxx' ) );

        $aDiscounts[1] = $this->getMock( 'oxdiscount', array( 'isForArticle', 'getId' ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForArticle' )->will( $this->returnValue( true ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'yyy' ) );

        $aDiscounts[2] = $this->getMock( 'oxdiscount', array( 'isForArticle', 'getId' ) );
        $aDiscounts[2]->expects( $this->once() )->method( 'isForArticle' )->will( $this->returnValue( false ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'zzz' ) );

        $aDiscounts[3] = $this->getMock( 'oxdiscount', array( 'isForArticle', 'getId' ) );
        $aDiscounts[3]->expects( $this->once() )->method( 'isForArticle' )->will( $this->returnValue( false ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'www' ) );

        $oDList = $this->getMock( 'oxList', array( 'getArray' ) );
        $oDList->expects( $this->once() )->method( 'getArray' )->will( $this->returnValue( $aDiscounts ) );

        $oList = $this->getMock( 'oxdiscountlist', array( '_getList' ) );
        $oList->expects( $this->once() )->method( '_getList' )->will( $this->returnValue( $oDList ) );

        // now proceeding to disocunt id check
        $this->assertEquals( array( 'xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1] ), $oList->getArticleDiscounts( $oArticle ) );
    }
    // basket item
    public function testGetBasketItemDiscounts()
    {
        // just simulating article
        $oArticle = new oxArticle();
        $oArticle->xxx = 'yyy';

        // simulating basket
        $oBasket = new oxBasket();
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock( 'oxdiscount', array( 'isForBasketItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForBasketItem' )->will( $this->returnValue( true ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'xxx' ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForBasketAmount' )->will( $this->returnValue( true ) );

        $aDiscounts[1] = $this->getMock( 'oxdiscount', array( 'isForBasketItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForBasketItem' )->will( $this->returnValue( true ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'yyy' ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForBasketAmount' )->will( $this->returnValue( true ) );

        $aDiscounts[2] = $this->getMock( 'oxdiscount', array( 'isForBasketItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[2]->expects( $this->once() )->method( 'isForBasketItem' )->will( $this->returnValue( false ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'zzz' ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'isForBasketAmount' );

        $aDiscounts[3] = $this->getMock( 'oxdiscount', array( 'isForBasketItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[3]->expects( $this->once() )->method( 'isForBasketItem' )->will( $this->returnValue( false ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'www' ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'isForBasketAmount' );

        $aDiscounts[4] = $this->getMock( 'oxdiscount', array( 'isForBasketItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[4]->expects( $this->once() )->method( 'isForBasketItem' )->will( $this->returnValue( false ) );
        $aDiscounts[4]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'www' ) );
        $aDiscounts[4]->oxdiscount__oxaddsumtype = new oxField('itm', oxField::T_RAW);
        $aDiscounts[4]->expects( $this->never() )->method( 'isForBasketAmount' );

        $oDList = $this->getMock( 'oxList', array( 'getArray' ) );
        $oDList->expects( $this->once() )->method( 'getArray' )->will( $this->returnValue( $aDiscounts ) );

        $oList = $this->getMock( 'oxdiscountlist', array( '_getList' ) );
        $oList->expects( $this->once() )->method( '_getList' )->will( $this->returnValue( $oDList ) );

        // now proceeding to disocunt id check
        $this->assertEquals( array( 'xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1] ), $oList->getBasketItemDiscounts( $oArticle, $oBasket ) );
    }
    // basket discounts
    public function testGetBasketDiscounts()
    {
        // simulating basket
        $oBasket = new oxBasket();
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock( 'oxdiscount', array( 'isForBasket', 'getId' ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForBasket' )->will( $this->returnValue( true ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'xxx' ) );

        $aDiscounts[1] = $this->getMock( 'oxdiscount', array( 'isForBasket', 'getId' ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForBasket' )->will( $this->returnValue( true ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'yyy' ) );

        $aDiscounts[2] = $this->getMock( 'oxdiscount', array( 'isForBasket', 'getId' ) );
        $aDiscounts[2]->expects( $this->once() )->method( 'isForBasket' )->will( $this->returnValue( false ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'zzz' ) );

        $aDiscounts[3] = $this->getMock( 'oxdiscount', array( 'isForBasket', 'getId' ) );
        $aDiscounts[3]->expects( $this->once() )->method( 'isForBasket' )->will( $this->returnValue( false ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'www' ) );

        $oDList = $this->getMock( 'oxList', array( 'getArray' ) );
        $oDList->expects( $this->once() )->method( 'getArray' )->will( $this->returnValue( $aDiscounts ) );

        $oList = $this->getMock( 'oxdiscountlist', array( '_getList' ) );
        $oList->expects( $this->once() )->method( '_getList' )->will( $this->returnValue( $oDList ) );

        // now proceeding to disocunt id check
        $this->assertEquals( array( 'xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1] ), $oList->getBasketDiscounts( $oBasket ) );
    }
    // basket item
    public function testGetBasketItemBundleDiscounts()
    {
        // just simulating article
        $oArticle = new oxArticle();
        $oArticle->xxx = 'yyy';

        // simulating basket
        $oBasket = new oxBasket();
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock( 'oxdiscount', array( 'isForBundleItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForBundleItem' )->will( $this->returnValue( true ) );
        $aDiscounts[0]->expects( $this->never() )->method( 'getId' );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForBasketAmount' )->will( $this->returnValue( false ) );

        $aDiscounts[1] = $this->getMock( 'oxdiscount', array( 'isForBundleItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForBundleItem' )->will( $this->returnValue( true ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForBasketAmount' )->will( $this->returnValue( true ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'yyy' ) );

        $aDiscounts[2] = $this->getMock( 'oxdiscount', array( 'isForBundleItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[2]->expects( $this->once() )->method( 'isForBundleItem' )->will( $this->returnValue( false ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'zzz' ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'isForBasketAmount' );

        $aDiscounts[3] = $this->getMock( 'oxdiscount', array( 'isForBundleItem', 'isForBasketAmount', 'getId' ) );
        $aDiscounts[3]->expects( $this->once() )->method( 'isForBundleItem' )->will( $this->returnValue( false ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'www' ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'isForBasketAmount' );

        $oDList = $this->getMock( 'oxList', array( 'getArray' ) );
        $oDList->expects( $this->once() )->method( 'getArray' )->will( $this->returnValue( $aDiscounts ) );

        $oList = $this->getMock( 'oxdiscountlist', array( '_getList' ) );
        $oList->expects( $this->once() )->method( '_getList' )->will( $this->returnValue( $oDList ) );

        // now proceeding to disocunt id check
        $this->assertEquals( array( 'yyy' => $aDiscounts[1] ), $oList->getBasketItemBundleDiscounts( $oArticle, $oBasket ) );
    }
    // basket bundle
    public function testGetBasketBundleDiscounts()
    {
        // simulating basket
        $oBasket = new oxBasket();
        $oBasket->zzz = 'www';

        $aDiscounts[0] = $this->getMock( 'oxdiscount', array( 'isForBundleBasket', 'getId' ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'isForBundleBasket' )->will( $this->returnValue( true ) );
        $aDiscounts[0]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'xxx' ) );

        $aDiscounts[1] = $this->getMock( 'oxdiscount', array( 'isForBundleBasket', 'getId' ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'isForBundleBasket' )->will( $this->returnValue( true ) );
        $aDiscounts[1]->expects( $this->once() )->method( 'getId' )->will( $this->returnValue( 'yyy' ) );

        $aDiscounts[2] = $this->getMock( 'oxdiscount', array( 'isForBundleBasket', 'getId' ) );
        $aDiscounts[2]->expects( $this->once() )->method( 'isForBundleBasket' )->will( $this->returnValue( false ) );
        $aDiscounts[2]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'zzz' ) );

        $aDiscounts[3] = $this->getMock( 'oxdiscount', array( 'isForBundleBasket', 'getId' ) );
        $aDiscounts[3]->expects( $this->once() )->method( 'isForBundleBasket' )->will( $this->returnValue( false ) );
        $aDiscounts[3]->expects( $this->never() )->method( 'getId' )->will( $this->returnValue( 'www' ) );

        $oDList = $this->getMock( 'oxList', array( 'getArray' ) );
        $oDList->expects( $this->once() )->method( 'getArray' )->will( $this->returnValue( $aDiscounts ) );

        $oList = $this->getMock( 'oxdiscountlist', array( '_getList' ) );
        $oList->expects( $this->once() )->method( '_getList' )->will( $this->returnValue( $oDList ) );

        // now proceeding to disocunt id check
        $this->assertEquals( array( 'xxx' => $aDiscounts[0], 'yyy' => $aDiscounts[1] ), $oList->getBasketBundleDiscounts( $oBasket ) );
    }

    //tests forceReload setter
    public function testForceReload()
    {
        $oList = $this->getProxyClass( 'oxDiscountList' );
        $oList->forceReload( true );
        $this->assertTrue( $oList->getNonPublicVar( '_blReload' ) );
    }

    public function testHasSkipDiscountCategories()
    {
           // making category
        $oCategory = oxNew( 'oxcategory' );
        $oCategory->setId( '_testCat' );
        $oCategory->oxcategories__oxparentid = new oxField('oxrootid', oxField::T_RAW);
        $oCategory->oxcategories__oxrootid = new oxField('_testCat', oxField::T_RAW);
        $oCategory->oxcategories__oxactive = new oxField(1, oxField::T_RAW);
        $oCategory->oxcategories__oxshopid = new oxField(oxConfig::getInstance()->getShopId(), oxField::T_RAW);
        $oCategory->oxcategories__oxtitle = new oxField('Test category 1', oxField::T_RAW);
        $oCategory->oxcategories__oxskipdiscounts = new oxField('1', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertTrue( oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories() );

        $oCategory->oxcategories__oxskipdiscounts = new oxField('0', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertFalse( oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories() );


        $oCategory->oxcategories__oxskipdiscounts = new oxField('1', oxField::T_RAW);
        $oCategory->save();

        oxRegistry::get("oxDiscountList")->forceReload();
        $this->assertTrue( oxRegistry::get("oxDiscountList")->hasSkipDiscountCategories() );
    }


}