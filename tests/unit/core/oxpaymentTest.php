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


class testPayment extends oxPayment
{
    public function unsetGroup($id)
    {
        unset($this->_groups[$id]);
    }
}

class Unit_Core_oxpaymentTest extends OxidTestCase
{

    private $_oPayment;
    private $_oUser;

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function tearDown()
    {
        $this->cleanUpTable( 'oxobject2group', 'oxgroupsid' );

        parent::tearDown();
    }

    /**
     * Get default dynamic data
     */
    protected function getDynValues()
    {
        $aDynvalue['kktype']   = 'vis';
        $aDynvalue['kknumber'] = '4111111111111111';
        $aDynvalue['kkmonth']  = '01';
        $aDynvalue['kkyear']   = date('Y')+1;
        $aDynvalue['kkname']   = 'Hans Mustermann';
        $aDynvalue['kkpruef']  = '333';

        return $aDynvalue;
    }

    public function testGetDynValuesIfAlwaysArrayIsReturned()
    {
        $oPayment = new oxPayment();
        $oPayment->load("oxidcreditcard");

        $this->assertTrue( is_array( $oPayment->getDynValues() ) );

        $oPayment = new oxPayment();
        $oPayment->oxpayments__oxvaldesc = new oxField( 'some unknown format value' );

        $this->assertTrue( is_array( $oPayment->getDynValues() ) );

        $oPayment = new oxPayment();
        $this->assertTrue( is_array( $oPayment->getDynValues() ) );
    }

    /**
     * Test assign data to object
     */
    public function testGetGroups()
    {
        $oPayment = new oxPayment();
        $oPayment->load( 'oxidcreditcard' );

        $aArray = array( 'oxidsmallcust',
                         'oxidmiddlecust',
                         'oxidgoodcust',
                         'oxidforeigncustomer',
                         'oxidnewcustomer',
                         'oxidpowershopper',
                         'oxiddealer',
                         'oxidnewsletter',
                         'oxidadmin',
                         'oxidpriceb',
                         'oxidpricea',
                         'oxidpricec' );

        $this->assertEquals( $aArray, $oPayment->getGroups()->arrayKeys() );
    }

    /**
     * Testing dyn values getter/setter
     */
    public function testGetSetDynValues()
    {
        $oPayment = new oxPayment();
        $oPayment->setDynValues( array( 'field0' => 'val0' ) );
        $oPayment->setDynValue( 'field1', 'val1' );

        $this->assertEquals( array( 'field0' => 'val0', 'field1' => 'val1' ), $oPayment->getDynValues() );
    }

    /**
     * Testing dyn values getter/setter
     */
    public function testGetDynValues()
    {
        $oPayment = new oxPayment();
        $oPayment->load("oxidcreditcard");

        $this->assertEquals( oxUtils::getInstance()->assignValuesFromText( $oPayment->oxpayments__oxvaldesc->value ), $oPayment->getDynValues() );
    }

    /**
     * Test getting payment value
     */
    public function testGetPaymentValue()
    {
        $oPayment = new oxPayment();

        $oPayment->load( 'oxidpayadvance' );
        $dBasePrice = 100.0;
        $this->assertEquals( 0, $oPayment->getPaymentValue( $dBasePrice ) );

        $oPayment->load( 'oxidcashondel' );
        $this->assertEquals( 7.5, $oPayment->getPaymentValue( $dBasePrice ) );

        $oPayment->oxpayments__oxaddsum = new oxField(-105, oxField::T_RAW);
        $this->assertEquals( 100, $oPayment->getPaymentValue( $dBasePrice ) );
    }

    /**
     * Test getting payment value in special currency
     */
    public function testGetPaymentValueSpecCurrency()
    {
        oxConfig::getInstance()->setActShopCurrency(2);
        $oPayment = new oxPayment();

        $oPayment->load( 'oxidpayadvance' );
        $dBasePrice = 100.0;
        $this->assertEquals( 0, $oPayment->getPaymentValue( $dBasePrice ) );

        $oPayment->load( 'oxidcashondel' );
        $this->assertEquals( 10.7445, $oPayment->getPaymentValue( $dBasePrice ) );

        $oPayment->oxpayments__oxaddsum = new oxField(-105, oxField::T_RAW);
        $this->assertEquals( 100, $oPayment->getPaymentValue( $dBasePrice ) );
    }

    /**
     * Test getting payment price
     */
    // positive price
    public function testGetPaymentPricePositivePriceStdBehaviour()
    {
        $oProductsPrice = $this->getMock( "oxpricelist", array( "getBruttoSum" ) );
        $oProductsPrice->expects( $this->once() )->method( 'getBruttoSum' )->will( $this->returnValue( 30 ) );

        $oTotalDiscount = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oTotalDiscount->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        $oDeliveryCosts = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oDeliveryCosts->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        //$oWrappingCosts = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        //$oWrappingCosts->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        // preparing basket for input
        $oBasket = $this->getMock( 'oxbasket', array('getProductsPrice', 'getTotalDiscount', 'getVoucherDiscValue', 'getCosts', 'getMostUsedVatPercent') );
        $oBasket->expects( $this->at( 0 ) )->method( 'getProductsPrice' )->will( $this->returnValue( $oProductsPrice ) );
        $oBasket->expects( $this->at( 1 ) )->method( 'getTotalDiscount' )->will( $this->returnValue( $oTotalDiscount ) );
        $oBasket->expects( $this->at( 2 ) )->method( 'getVoucherDiscValue' )->will( $this->returnValue( 10 ) );
        $oBasket->expects( $this->at( 3 ) )->method( 'getCosts' )->with( 'oxdelivery' )->will( $this->returnValue( $oDeliveryCosts ) );
        //$oBasket->expects( $this->at( 4 ) )->method( 'getCosts' )->with( 'oxwrapping' )->will( $this->returnValue( $oWrappingCosts ) );
        $oBasket->expects( $this->at( 4 ) )->method( 'getMostUsedVatPercent' )->will( $this->returnValue( 50 ) );

        // preparing payment for test
        $oPayment = $this->getMock( 'oxPayment', array('getPaymentValue') );
        $oPayment->expects( $this->once() )->method( 'getPaymentValue')->with( 20 )->will( $this->returnValue( 60 ) );
        $oPayment->oxpayments__oxaddsum = new oxField(60, oxField::T_RAW);
        $oPrice = $oPayment->getPaymentPrice( $oBasket );

        // testing
        $this->assertEquals( 50, $oPrice->getVat() );
        $this->assertEquals( 60, $oPrice->getBruttoPrice() );
        $this->assertEquals( 60/1.5, $oPrice->getNettoPrice() );
        $this->assertEquals( 60-60/1.5, $oPrice->getVatValue() );
    }

    /**
     * Test getting payment price
     */
    // positive price
    public function testGetPaymentPricePositivePrice()
    {
        $oProductsPrice = $this->getMock( "oxpricelist", array( "getBruttoSum" ) );
        $oProductsPrice->expects( $this->once() )->method( 'getBruttoSum' )->will( $this->returnValue( 30 ) );

        $oTotalDiscount = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oTotalDiscount->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        $oDeliveryCosts = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oDeliveryCosts->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        $oWrappingCosts = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oWrappingCosts->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        // preparing basket for input
        $oBasket = $this->getMock( 'oxbasket', array('getProductsPrice', 'getTotalDiscount', 'getVoucherDiscValue', 'getCosts', 'getMostUsedVatPercent') );
        $oBasket->expects( $this->at( 0 ) )->method( 'getProductsPrice' )->will( $this->returnValue( $oProductsPrice ) );
        $oBasket->expects( $this->at( 1 ) )->method( 'getTotalDiscount' )->will( $this->returnValue( $oTotalDiscount ) );
        $oBasket->expects( $this->at( 2 ) )->method( 'getVoucherDiscValue' )->will( $this->returnValue( 10 ) );
        $oBasket->expects( $this->at( 3 ) )->method( 'getCosts' )->with( 'oxdelivery' )->will( $this->returnValue( $oDeliveryCosts ) );
        $oBasket->expects( $this->at( 4 ) )->method( 'getCosts' )->with( 'oxwrapping' )->will( $this->returnValue( $oWrappingCosts ) );
        $oBasket->expects( $this->at( 5 ) )->method( 'getMostUsedVatPercent' )->will( $this->returnValue( 50 ) );

        // preparing payment for test
        $oPayment = $this->getMock( 'oxPayment', array('getPaymentValue') );
        $oPayment->expects( $this->once() )->method( 'getPaymentValue')->with( 30 )->will( $this->returnValue( 60 ) );
        $oPayment->oxpayments__oxaddsum = new oxField(60, oxField::T_RAW);
        $oPayment->oxpayments__oxaddsumrules = new oxField(31, oxField::T_RAW);
        $oPrice = $oPayment->getPaymentPrice( $oBasket );

        // testing
        $this->assertEquals( 50, $oPrice->getVat() );
        $this->assertEquals( 60, $oPrice->getBruttoPrice() );
        $this->assertEquals( 60/1.5, $oPrice->getNettoPrice() );
        $this->assertEquals( 60-60/1.5, $oPrice->getVatValue() );
    }

    public function testGetPaymentPriceVatOnTop()
    {
        $oProductsPrice = $this->getMock( "oxpricelist", array( "getBruttoSum" ) );
        $oProductsPrice->expects( $this->once() )->method( 'getBruttoSum' )->will( $this->returnValue( 30 ) );

        $oTotalDiscount = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oTotalDiscount->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        $oDeliveryCosts = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oDeliveryCosts->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        $oWrappingCosts = $this->getMock( "oxprice", array( "getBruttoPrice" ) );
        $oWrappingCosts->expects( $this->once() )->method( 'getBruttoPrice' )->will( $this->returnValue( 10 ) );

        // preparing basket for input
        $oBasket = $this->getMock( 'oxbasket', array('getProductsPrice', 'getTotalDiscount', 'getVoucherDiscValue', 'getCosts', 'getMostUsedVatPercent') );
        $oBasket->expects( $this->at( 0 ) )->method( 'getProductsPrice' )->will( $this->returnValue( $oProductsPrice ) );
        $oBasket->expects( $this->at( 1 ) )->method( 'getTotalDiscount' )->will( $this->returnValue( $oTotalDiscount ) );
        $oBasket->expects( $this->at( 2 ) )->method( 'getVoucherDiscValue' )->will( $this->returnValue( 10 ) );
        $oBasket->expects( $this->at( 3 ) )->method( 'getCosts' )->with( 'oxdelivery' )->will( $this->returnValue( $oDeliveryCosts ) );
        $oBasket->expects( $this->at( 4 ) )->method( 'getCosts' )->with( 'oxwrapping' )->will( $this->returnValue( $oWrappingCosts ) );
        $oBasket->expects( $this->at( 5 ) )->method( 'getMostUsedVatPercent' )->will( $this->returnValue( 50 ) );

        modConfig::getInstance()->setConfigParam( 'blPaymentVatOnTop', true );

        // preparing payment for test
        $oPayment = $this->getMock( 'oxPayment', array('getPaymentValue') );
        $oPayment->expects( $this->once() )->method( 'getPaymentValue')->with( 30 )->will( $this->returnValue( 60 ) );
        $oPayment->oxpayments__oxaddsum = new oxField(60, oxField::T_RAW);
        $oPayment->oxpayments__oxaddsumrules = new oxField(31, oxField::T_RAW);
        $oPrice = $oPayment->getPaymentPrice( $oBasket );

        // testing
        $this->assertEquals( 50, $oPrice->getVat() );
        $this->assertEquals( 60, $oPrice->getNettoPrice() );
        $this->assertEquals( 60*1.5, $oPrice->getBruttoPrice() );
        $this->assertEquals( 60*1.5-60, $oPrice->getVatValue() );
    }

/*
    // negative price
    public function testGetPaymentPriceNegativePrice()
    {
        // preparing data for basket
        $oPrice0 = new oxprice();
        $oPrice0->setPrice( 10 );

        $oPrice1 = new oxprice();
        $oPrice1->setPrice( 20 );

        $oDiscountProductsPrice = new oxprice();
        $oDiscountProductsPrice->setPrice( 30 );

        $oDeliveryCost = new oxprice();
        $oDeliveryCost->setPrice( 40 );

        $aActions = array( 'getCosts', 'getMostUsedVatPercent', 'getDiscountProductsPrice' );

        // preparing basket for input
        $oBasket = $this->getMock( 'oxbasket', $aActions );
        $oBasket->expects( $this->once() )->method( 'getDiscountProductsPrice')->will( $this->returnValue( $oDiscountProductsPrice ) );
        $oBasket->expects( $this->once() )->method( 'getCosts' )->will( $this->returnValue( $oDeliveryCost ) );
        $oBasket->expects( $this->never() )->method( 'getMostUsedVatPercent' );

        oxConfig::getInstance()->setConfigParam( 'blCalcSkontoForDelivery', true );

        // preparing payment for test
        $oPayment = new oxpayment();
        $oPayment->oxpayments__oxaddsumtype = new oxField('%', oxField::T_RAW);
        $oPayment->oxpayments__oxaddsum = new oxField(-50, oxField::T_RAW);
        $oPrice = $oPayment->getPaymentPrice( $oBasket );

        // testing
        $this->assertEquals(   0, $oPrice->getVat() );
        $this->assertEquals( -35, $oPrice->getBruttoPrice() );
        $this->assertEquals( -35, $oPrice->getNettoPrice() );
        $this->assertEquals(   0, $oPrice->getVatValue() );
    }
    // negative price
    public function testGetBasketPrice()
    {
        // preparing data for basket
        $oPrice0 = new oxprice();
        $oPrice0->setPrice( 10 );

        $oPrice1 = new oxprice();
        $oPrice1->setPrice( 20 );

        $oProductsPrice = new oxpricelist();
        $oProductsPrice->addToPriceList( $oPrice0 );
        $oProductsPrice->addToPriceList( $oPrice1 );

        $oDiscountProductsPrice = new oxprice();
        $oDiscountProductsPrice->setPrice( 30 );

        $aActions = array();
            $aActions[] = 'getProductsPrice';

        // preparing basket for input
        $oBasket = $this->getMock( 'oxbasket', $aActions );

            $oBasket->expects( $this->once() )->method( 'getProductsPrice')->will( $this->returnValue( $oProductsPrice ) );

        // preparing payment for test
        $oPayment = new oxpayment();
        $dPrice = $oPayment->getBasketPrice( $oBasket );

        // testing
        $this->assertEquals(   30, $dPrice );
    }
*/
/*
    public function testGetBasketPriceSpecCurrency()
    {
        // preparing data for basket
        oxConfig::getInstance()->setActShopCurrency(2);
        $oPrice0 = new oxprice();
        $oPrice0->setPrice( 10 );

        $oPrice1 = new oxprice();
        $oPrice1->setPrice( 20 );

        $oProductsPrice = new oxpricelist();
        $oProductsPrice->addToPriceList( $oPrice0 );
        $oProductsPrice->addToPriceList( $oPrice1 );

        $oDiscountProductsPrice = new oxprice();
        $oDiscountProductsPrice->setPrice( 30 );

        $aActions = array();
            $aActions[] = 'getProductsPrice';

        // preparing basket for input
        $oBasket = $this->getMock( 'oxbasket', $aActions );

            $oBasket->expects( $this->once() )->method( 'getProductsPrice')->will( $this->returnValue( $oProductsPrice ) );

        // preparing payment for test
        $oPayment = new oxpayment();
        $dPrice = $oPayment->getBasketPrice( $oBasket );

        // testing
        $this->assertEquals(   30, $dPrice );
    }
*/
    /**
     * Test get payment countries
     */
    public function testGetCountries()
    {
        $oPayment = new oxPayment();
        $oPayment->load( 'oxidcreditcard' );
        $this->assertEquals(3, count( $oPayment->getCountries() ), "Failed getting countries list" );
    }

    /**
     * Test payment delete from db
     */
    public function testDelete()
    {
        $oPayment = new oxPayment();

        $oDB = oxDb::getDb();
        $sQ = "insert into oxpayments (oxid, oxactive, oxaddsum, oxaddsumtype) values ('oxpaymenttest', 1, '5', 'abs')";
        $oDB->execute( $sQ );

        $sQ = "insert into oxobject2payment (oxid, oxpaymentid, oxobjectid, oxtype) values ('oxob2p_testid', 'oxpaymenttest', 'testid', 'oxdelset')";
        $oDB->execute( $sQ );

        $oPayment->load( 'oxpaymenttest' );
        $oPayment->delete( 'oxpaymenttest' );

        $sQ = "select count(oxid) from oxpayments where oxid = 'oxpaymenttest' ";
        $this->assertEquals(0, $oDB->getOne( $sQ ), "Failed deleting payment items from oxpayments table");

        $sQ = "select count(oxid) from oxobject2payment where oxid = 'oxob2p_testid' ";
        $this->assertEquals(0, $oDB->getOne( $sQ ), "Failed deleting items from oxobject2payment table");
    }
    public function testDeleteNotSetObject()
    {
        $oPayment = new oxPayment();

        $oDB = oxDb::getDb();
        $sQ = "insert into oxpayments (oxid, oxactive, oxaddsum, oxaddsumtype) values ('oxpaymenttest', 1, '5', 'abs')";
        $oDB->execute( $sQ );

        $sQ = "insert into oxobject2payment (oxid, oxpaymentid, oxobjectid, oxtype) values ('oxob2p_testid', 'oxpaymenttest', 'testid', 'oxdelset')";
        $oDB->execute( $sQ );

        $oPayment->delete();

        $sQ = "select count(oxid) from oxpayments where oxid = 'oxpaymenttest' ";
        $this->assertEquals(1, $oDB->getOne( $sQ ));

        $sQ = "select count(oxid) from oxobject2payment where oxid = 'oxob2p_testid' ";
        $this->assertEquals(1, $oDB->getOne( $sQ ));
    }

    /**
     * Test checking if it is ipayment or not
     */
    /*
    // has module and payment is ON
    public function testIsIPaymentHasModulePaymentIsOn()
    {
            return; // EE only

        modConfig::getInstance()->setConfigParam( 'iPayment_blActive', true );

        $oPayment = new oxpayment();
        $oPayment->load( 'oxiddebitnote' );

        //same shop id for both PE and EE, just for testing
        $this->assertTrue( $oPayment->isIPayment( oxConfig::getBaseShopId() ) );

        $oPayment = new oxpayment();
        $oPayment->load( 'oxiddebitnote' );
        $this->assertFalse( $oPayment->isIPayment( 'xxx' ) );
    }
    // has module, but payment is off
    public function testIsIPaymentHasModulePaymentIsOff()
    {
            return; // EE only

        modConfig::getInstance()->setConfigParam( 'iPayment_blActive', false );

        $oPayment = new oxpayment();
        $oPayment->load( 'oxiddebitnote' );

        //same shop id for both PE and EE, just for testing
        $this->assertFalse( $oPayment->isIPayment( oxConfig::getBaseShopId() ) );

        $oPayment = new oxpayment();
        $oPayment->load( 'oxiddebitnote' );
        $this->assertFalse( $oPayment->isIPayment( 'xxx' ) );
    }
    // has no module, but payment is off
    public function testIsIPaymentHasNotModulePaymentIsOff()
    {
            return; // EE only

        modConfig::getInstance()->setConfigParam( 'iPayment_blActive', false );

        $oPayment = new oxpayment();
        $oPayment->load( 'oxiddebitnote' );

        //same shop id for both PE and EE, just for testing
        $this->assertFalse( $oPayment->isIPayment( oxConfig::getBaseShopId() ) );

        $oPayment = new oxpayment();
        $oPayment->load( 'oxiddebitnote' );
        $this->assertFalse( $oPayment->isIPayment( 'xxx' ) );
    }
*/
    /**
     * Test payment validation for empty payment
     * other payments for user country exist
     */
    public function testIsValidPaymentEmptyPaymentButThereAreCountries()
    {
        $oPayment = new oxpayment();
        $oPayment->oxpayments__oxid = new oxField('oxempty', oxField::T_RAW);
        $oPayment->oxpayments__oxactive = new oxField( 1 );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $blRes = $oPayment->isValidPayment( array(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'oxidstandard' );
        $this->assertFalse( $blRes );
        $this->assertEquals( -3, $oPayment->getPaymentErrorNumber() );
    }
    /**
     * Test payment validation for empty payment
     * no other payments for user country exist
     */
    public function testIsValidPaymentEmptyPaymentAndThereAreNoCountries()
    {
        $oPayment = new oxpayment();
        $oPayment->oxpayments__oxid = new oxField('oxempty', oxField::T_RAW);
        $oPayment->oxpayments__oxactive = new oxField( 1 );

        $oUser = $this->getMock('oxuser', array('getActiveCountry'));
        $oUser->expects($this->once())->method('getActiveCountry')->will($this->returnValue('otherCountry'));
        $oUser->Load( 'oxdefaultadmin' );

        $blRes = $oPayment->isValidPayment( array(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'oxidstandard' );
        $this->assertTrue( $blRes );
    }

    public function testIsValidPaymentBlOtherCountryOrderIfFalse()
    {
        modConfig::getInstance()->setConfigParam( 'blOtherCountryOrder', false );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $oPayment = new oxpayment();
        $oPayment->oxpayments__oxid = new oxField( 'oxempty' );
        $oPayment->oxpayments__oxactive = new oxField( 1 );
        $this->assertFalse( $oPayment->isValidPayment( array(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'oxidstandard' ) );
        $this->assertEquals( -2, $oPayment->getPaymentErrorNumber() );
    }

    public function testIsValidPaymentOxemptyIsInActive()
    {
        $oPayment = new oxpayment();
        $oPayment->oxpayments__oxid = new oxField( 'oxempty' );
        $oPayment->oxpayments__oxactive = new oxField( 0 );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $this->assertFalse( $oPayment->isValidPayment( array(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'oxidstandard' ) );
        $this->assertEquals( -2, $oPayment->getPaymentErrorNumber() );
    }

    /**
     * Test payment credit/debit card validation
     */
    public function testIsValidPaymentCreditCardChecking()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $aDynvalue = $this->getDynValues();
        $aDynvalue['kknumber'] = ''; //wrong number
        $blRes = $oPayment->isValidPayment($aDynvalue, oxConfig::getInstance()->getBaseShopId(), null, 0.0, 'oxidstandard');
        $this->assertFalse($blRes);

    }

    /*
    public function testIsValidPayment_NoCreditCardCheckingIPayment()
    {
            return; // EE only

        $oPayment = $this->getMock( 'oxpayment', array( 'isIPayment' ) );
        $oPayment->expects( $this->once() )->method( 'isIPayment' )->will( $this->returnValue( true ) );
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $blRes = $oPayment->isValidPayment( array(), oxConfig::getBaseShopId(), $oUser, 0.0, 'oxidstandard' );
        $this->assertTrue( $blRes );
    }
    */

    /**
     * Test payment validation
     */
    public function testIsValidPaymentWithNotValidShippingSetId()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'nosuchvalue');
        $this->assertFalse($blRes);
    }

    /**
     * Test payment validation without passing shipping set id
     */
    public function testIsValidPaymentWithoutShippingSetId()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 5.0, null);
        $this->assertFalse($blRes);
    }


    /**
     * Test payment validation with boni
     */
    public function testIsValidPayment_FromBoni()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        $oUser->oxuser__oxboni = new oxField($oPayment->oxpayments__oxfromboni->value - 1, oxField::T_RAW);
        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'oxidstandard');
        $this->assertFalse($blRes);
    }

    /**
     * Test payment validation with amount
     */
    public function testIsValidPayment_FromAmount()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        //oxpayments__oxfromamount = 0, so passing lower value price
        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, -1, 'oxidstandard');
        $this->assertFalse($blRes);
    }

    /**
     * Test payment validation with amount
     */
    public function testIsValidPayment_ToAmount()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );

        //oxpayments__oxtoamount is 1000000, so passing price that is greater
        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 1000001, 'oxidstandard');
        $this->assertFalse($blRes);
    }

    /**
     * Test payment validation with groups
     */
    public function testIsValidPaymentInGroup()
    {
        $oPayment = new testPayment();
        $oPayment->Load('oxcreditcard');

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );
        $oUser->addToGroup( '_testGroupId' );

        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 0.0, 'oxidstandard');
        $this->assertFalse($blRes);
    }

    /**
     * Test payment validation - validation good
     */
    public function testIsValidPayment()
    {
        $oPayment = new oxPayment();
        $oPayment->Load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->Load( 'oxdefaultadmin' );
        $oUser->oxuser__oxboni = new oxField($oPayment->oxpayments__oxfromboni->value + 1, oxField::T_RAW);

        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 5.0, 'oxidstandard');
        $this->assertTrue($blRes);
    }

    /**
     * Test payment validation sets correct error codes
     */
    public function testIsValidPayment_settingErrorNumber()
    {
        $oPayment = $this->getProxyClass( "oxPayment" );
        $oPayment->load( 'oxidcreditcard' );

        $oUser = new oxuser();
        $oUser->load( 'oxdefaultadmin' );

        //oxpayments__oxtoamount is 1000000, so passing price that is greater
        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 1000001, 'oxidstandard');
        $this->assertEquals(-3, $oPayment->getNonPublicVar('_iPaymentError') );

        //no shipping set id
        $blRes = $oPayment->isValidPayment($this->getDynValues(), oxConfig::getInstance()->getBaseShopId(), $oUser, 1000001, null);
        $this->assertEquals(-2, $oPayment->getNonPublicVar('_iPaymentError') );

        //not valid input
        $blRes = $oPayment->isValidPayment(null, oxConfig::getInstance()->getBaseShopId(), $oUser, 1000001, null);
        $this->assertEquals(1, $oPayment->getNonPublicVar('_iPaymentError') );
    }

    /**
     * Test payment error number  getter
     */
    public function testGetPaymentErrorNumber()
    {
        $oPayment = $this->getProxyClass( "oxPayment" );
        $oPayment->setNonPublicVar( '_iPaymentError', 2 );
        $this->assertEquals(2, $oPayment->getPaymentErrorNumber() );
    }

    /**
     * Test payment config setter
     */
    public function testSetPaymentVatOnTop()
    {
        $oPayment = $this->getProxyClass( "oxPayment" );
        $oPayment->setPaymentVatOnTop( true );
        $this->assertTrue( $oPayment->getNonPublicVar( "_blPaymentVatOnTop" ));
    }
    /**
     * Test logging ipayment action
     */
    /*
    public function testLogIPayment()
    {
            return; // EE only

        $myConfig = oxConfig::getInstance();
        $mySession = oxSession::getInstance();

        $myConfig->setConfigParam( 'iPayment_blLogPaymentActions', true );
        modConfig::setParameter('paymentid', '_testPaymentId');
        $mySession->setVar('_ipaysessid', '_testIPaymentSessId');

        $oPayment = new oxPayment();
        $oPayment->logIPayment( '1', '2', '3', '4', '5', '6', '7' );

        $sLogMsg  = "ACTIVE CLASS: 6 (ipayment)\n";
        $sLogMsg .= "TRANSACTION TYPE: 7\n";
        $sLogMsg .= "ERROR TEXT: 5\n";

        $sSql = "select * from oxpaylogs where oxsessid = '_testIPaymentSessId' ";
        $aLogs = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getAll( $sSql );

        $this->assertEquals( '_testIPaymentSessId', $aLogs[0]['OXSESSID'] );
        $this->assertEquals( oxConfig::getInstance()->getBaseShopId(), $aLogs[0]['OXSHOPID'] );
        $this->assertEquals( '_testPaymentId', $aLogs[0]['OXPAYID'] );
        $this->assertEquals( '1', $aLogs[0]['OXAMOUNT'] );
        $this->assertEquals( '2', $aLogs[0]['OXORDERID'] );
        $this->assertEquals( '3', $aLogs[0]['OXTRANSID'] );
        $this->assertEquals( '4', $aLogs[0]['OXERRCODE'] );

        $sDbLogMsg = unserialize(stripslashes($aLogs[0]['OXERRTEXT']));

        $sLogMsg = preg_replace("/\r|\n/", "", $sLogMsg);
        $sDbLogMsg = preg_replace("/\r|\n/", "", $sDbLogMsg);

        $this->assertEquals( $sLogMsg, $sDbLogMsg );

    }
    */
    /**
     * Test logging ipayment action when logging is off
     */
    /*
    public function testLogIPaymentWhenLoggingIsOff()
    {
            return; // EE only

        $myConfig = oxConfig::getInstance();

        $myConfig->setConfigParam( 'iPayment_blLogPaymentActions', false );

        $oPayment = new oxPayment();
        $oPayment->logIPayment( '1', '2', '3', '4', '5', '6', '7' );

        $sSql = "select * from oxpaylogs where oxsessid = '_testIPaymentSessId' ";
        $aLogs = oxDb::getDb( oxDB::FETCH_MODE_ASSOC )->getAll( $sSql );

        $this->assertEquals( 0, count($aLogs) );
    }
    */
}
