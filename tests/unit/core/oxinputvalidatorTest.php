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
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_oxInputValidatorTest_oxutils extends oxutils
{
    public function isValidEmail( $sEmail )
    {
        return false;
    }
}

/**
 * Test input validation class (oxInputValidator)
 */
class Unit_Core_oxInputValidatorTest extends OxidTestCase
{
    private $_oValidator = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oValidator = oxNew('oxInputValidator', 'core');
    }

    /**
     * Test case for oxinputvalidator::validateBasketAmount()
     * tests rounding of validator
     *
     * @return null
     */
    public function testValidateBasketAmountnoUneven()
    {
        try {
            $this->assertEquals($this->_oValidator->validateBasketAmount('1,6'), 2);
            $this->assertEquals($this->_oValidator->validateBasketAmount('1.6'), 2);
            $this->assertEquals($this->_oValidator->validateBasketAmount('1.1'), 1);
        } catch ( oxArticleInputException $e ) {
            $this->fail( 'Error while executing test: testValidateBasketAmountnoUneven' );
        }
    }

    /**
     * Test case for oxinputvalidator::validateBasketAmount()
     * tests uneven amount
     *
     * @return null
     */
    public function testValidateBasketAmountallowUneven()
    {
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', true );
        $this->assertEquals($this->_oValidator->validateBasketAmount('1.6'), 1.6);
    }

    /**
     * Test case for oxinputvalidator::validateBasketAmount()
     * tests unallowed input
     *
     * @return null
     */
    public function testValidateBasketAmountBadInput()
    {
        $iStat = 0;
        modConfig::getInstance()->setConfigParam( 'blAllowUnevenAmounts', false );
        try {
            $this->_oValidator->validateBasketAmount( -1 );
        } catch ( oxArticleInputException $e ) {
            $iStat++;
        }

        //FS#1758
        try {
            $this->_oValidator->validateBasketAmount( 'Alpha' );
        } catch ( oxArticleInputException $e ) {
            $iStat++;
        }

        try {
            $this->_oValidator->validateBasketAmount( '0.000,0' );
        } catch ( oxArticleInputException $e ) {
            $iStat++;
        }

        if ( $iStat != 3 )
            $this->fail( 'Bad input passed' );
    }

    /**
     * Test case for oxinputvalidator::validatePaymentInputData()
     * 1. unknown payment, which has no testing conditions
     *
     * @return null
     */
    public function testValidatePaymentInputDataUnknownPayment()
    {
        $aDynvalue = array();
        $oValidator = new oxinputvalidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( 'xxx', $aDynvalue ) );
    }

    /**
     * Test case for oxinputvalidator::validatePaymentInputData()
     * 2. CC: missing input fields
     *
     * @return null
     */
    public function testValidatePaymentInputDataCCMissingFields()
    {
        $aDynvalue = array();
        $oValidator = new oxinputvalidator();
        $this->assertFalse( $oValidator->validatePaymentInputData( 'oxidcreditcard', $aDynvalue ) );
    }

    /**
     * Test case for oxinputvalidator::validatePaymentInputData()
     * 3. CC: wrong card type
     *
     * @return null
     */
    public function testValidatePaymentInputDataCCWrongCardType()
    {
        $aDynvalue = array( 'kktype'   => 'xxx',
                            'kknumber' => 'xxx',
                            'kkmonth'  => 'xxx',
                            'kkyear'   => 'xxx',
                            'kkname'   => 'xxx',
                            'kkpruef'  => 'xxx'
                          );
        $oValidator = new oxinputvalidator();
        $this->assertFalse( $oValidator->validatePaymentInputData( 'oxidcreditcard', $aDynvalue ) );
    }

    /**
     * Test case for oxinputvalidator::validatePaymentInputData()
     * 4. CC: all input is fine
     *
     * @return null
     */
    public function testValidatePaymentInputDataCCAllInputIsFine()
    {
        $aDynvalue = array( 'kktype'   => 'vis',
                            'kknumber' => '4111111111111111',
                            'kkmonth'  => '01',
                            'kkyear'   => date( 'Y' ) + 1,
                            'kkname'   => 'Hans Mustermann',
                            'kkpruef'  => '333'
                          );

        $oValidator = new oxinputvalidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( 'oxidcreditcard', $aDynvalue ) );
    }

    /**
     * Test case for oxinputvalidator::validatePaymentInputData()
     * 5. DC: missing input fields
     *
     * @return null
     */
    public function testValidatePaymentInputDataDCMissingFields()
    {
        $aDynvalue = array();
        $oValidator = new oxinputvalidator();
        $this->assertFalse( $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }

    /**
     * Test case for oxinputvalidator::validatePaymentInputData()
     * 6. DC: all input is fine
     *
     * @return null
     */
    public function testValidatePaymentInputDataDCAllInputIsFine()
    {
        $aDynvalue = array( 'lsbankname'   => 'Bank name',
                            'lsblz'        => '12345678',
                            'lsktonr'      => '123456789',
                            'lsktoinhaber' => 'Hans Mustermann'
                          );

        $oValidator = new oxinputvalidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }


    /**
     * Test for bug #1150
     *
     * @return null
     */
    public function test4CharLsblz()
    {
        $iErr = -4;
        $aDynvalue = array( 'lsbankname'   => 'Bank name',
                            'lsblz'        => '1234',
                            'lsktonr'      => '123456789',
                            'lsktoinhaber' => 'Hans Mustermann'
                          );

        $oValidator = new oxInputValidator();
        $this->assertEquals( $iErr, $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }

    /**
     * Test for bug #1150
     *
     * @return null
     */
    public function test5CharLsblz()
    {
        $aDynvalue = array( 'lsbankname'   => 'Bank name',
                            'lsblz'        => '12345',
                            'lsktonr'      => '123456789',
                            'lsktoinhaber' => 'Hans Mustermann'
                          );


        $oValidator = new oxinputvalidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }

    /**
     * Test for bug #1150
     *
     * @return null
     */
    public function test6CharLsblz()
    {
        $aDynvalue = array( 'lsbankname'   => 'Bank name',
                            'lsblz'        => '123456',
                            'lsktonr'      => '123456789',
                            'lsktoinhaber' => 'Hans Mustermann'
                          );


        $oValidator = new oxinputvalidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }

    /**
     * Test for bug #1150
     *
     * @return null
     */
    public function test8CharLsblz()
    {
        $aDynvalue = array( 'lsbankname'   => 'Bank name',
                            'lsblz'        => '12345678',
                            'lsktonr'      => '123456789',
                            'lsktoinhaber' => 'Hans Mustermann'
                          );


        $oValidator = new oxinputvalidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }

    /**
     * Test for bug #1150
     *
     * @return null
     */
    public function test9CharLsblz()
    {
        $iErr = -4;
        $aDynvalue = array( 'lsbankname'   => 'Bank name',
                            'lsblz'        => '123456789',
                            'lsktonr'      => '123456789',
                            'lsktoinhaber' => 'Hans Mustermann'
                          );

        $oValidator = new oxInputValidator();
        $this->assertEquals( $iErr, $oValidator->validatePaymentInputData( 'oxiddebitnote', $aDynvalue ) );
    }

    /**
     * Test case for oxinputvalidator::_addValidationError()
     *               oxinputvalidator::getFieldValidationErrors()
     *               oxinputvalidator::getFirstValidationError()
     *
     * @return null
     */
    public function testAddValidationError()
    {
        $oValidator = new oxinputvalidator();
        $this->assertEquals( array(), $oValidator->getFieldValidationErrors() );
        $this->assertNull( $oValidator->getFirstValidationError() );

        $oValidator->UNITaddValidationError( "userid", "err" );
        $oValidator->UNITaddValidationError( "fieldname", "err" );
        $oValidator->UNITaddValidationError( "error", "err" );

        $this->assertEquals( array( "userid" => array( "err" ), "fieldname" => array( "err" ), "error" => array( "err" ) ), $oValidator->getFieldValidationErrors() );
        $this->assertEquals( "err", $oValidator->getFirstValidationError() );
    }

    /**
     * Testing VAT id checker - no check if no vat id or company name in params list
     * (Check performed when company name param is empty)
     * (Check performed when vat id param is empty)
     *
     * @return null
     */
    public function testCheckVatIdWithoutVatIdOrCompanyName()
    {
        $oUser = oxNew( "oxUser" );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 1 ) );

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 0) );
    }

    /**
     * Testing VAT id checker - with vat id, company name, but without or bad country id
     * (Vat Id should not be checked without country id)
     *
     * @return null
     */
    public function testCheckVatIdWithBadCountryId()
    {
        $oUser = oxNew( "oxUser" );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 1, 'oxuser__oxcountryid' => null) );
    }

    /**
     * Testing VAT id checker - with home country id
     * (while trying to check home country business user with vat id)
     *
     * @return null
     */
    public function testCheckVatIdWithHomeCountryId()
    {
        $oUser = oxNew( "oxUser" );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $aHome = oxConfig::getInstance()->getConfigParam( 'aHomeCountry' );

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 1, 'oxuser__oxcountryid' => $aHome[0]) );
    }

    /**
     * Testing VAT id checker - with foreign country id in which disabled vat checking
     * (while trying to check foreign country business user with vat id, but country does not allow checking)
     *
     * @return null
     */
    public function testCheckVatIdWithForeignCountryWithDisabledVatChecking()
    {
        $oUser = oxNew( "oxUser" );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $sForeignCountryId = "a7c40f6321c6f6109.43859248"; //Switzerland

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 1, 'oxuser__oxcountryid' => $sForeignCountryId ) );
    }

    /**
     * Testing VAT id checker - with foreign country id and bad vat id
     * (while trying to check foreign country business user with bad vat id)
     *
     * @return null
     */
    public function testCheckVatIdWithForeignCountryIdAndBadVatId()
    {

        $oUser = oxNew( "oxUser" );
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxustid'),
                        $this->attributeEqualTo('message','VAT_MESSAGE_ID_NOT_VALID')
                );

        $sForeignCountryId = "a7c40f6320aeb2ec2.72885259"; //Austria

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 1, 'oxuser__oxcountryid' => $sForeignCountryId ) );
    }

    /**
     * Testing VAT id checker - with foreign country id and good vat id
     * (while trying to check foreign country business user with good vat id)
     *
     * @return null
     */
    public function testCheckVatId()
    {

        $oUser = oxNew( "oxUser" );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $sForeignCountryId = "a7c40f6320aeb2ec2.72885259"; //Austria

        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 'AT123', 'oxuser__oxcountryid' => $sForeignCountryId ) );
    }


    /**
     * Test case for oxinputvalidator::checkCountries()
     * @return null
     */
    public function testCheckCountriesWrongCountries()
    {
        $oUser = oxNew( "oxUser" );
        $oUser->setId('testusr');

        $oValidator = oxNew( "oxinputvalidator" );
        $oValidator->checkCountries( $oUser, array( "oxuser__oxcountryid" => "xxx" ), array( "oxaddress__oxcountryid" => "yyy" ) );

        $this->assertTrue( $oValidator->getFirstValidationError() instanceof oxUserException, "error in oxinputvalidator::checkCountries()" );
    }

    /**
     * Test case for oxinputvalidator::checkCountries()
     *
     * @return null
     */
    public function testCheckCountriesGoodCountries()
    {
        $oUser = oxNew( "oxUser" );
        $oUser->setId('testx');
        $oValidator = oxNew( "oxinputvalidator" );
        $oValidator->checkCountries( $oUser, array( "oxuser__oxcountryid" => "a7c40f631fc920687.20179984" ), array( "oxaddress__oxcountryid" => "a7c40f6320aeb2ec2.72885259" ) );
        $this->assertNull( $oValidator->getFirstValidationError() );
    }

    /**
     * Test case for oxInputValidator::checkRequiredArrayFields()
     *
     * @return null
     */
    public function testCheckRequiredArrayFieldsEmptyField()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('xxx'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_NOTALLFIELDS')
                        )
                );

        $oValidator->checkRequiredArrayFields( $oUser, 'xxx', array( 'aaa' => ' ' ) );
    }

    /**
     * Test case for oxInputValidator::checkRequiredArrayFields()
     *
     * @return null
     */
    public function testCheckRequiredArrayFieldsFilledField()
    {
        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $oValidator->checkRequiredArrayFields( new oxuser(), 'xxx', array( 'aaa' => 'xxx' ) );
    }


    /**
     * Test case for oxInputValidator::checkPassword()
     * 1. defining required fields in aMustFillFields. While testing original
     * function must throw an exception that not all required fields are filled
     *
     * @return null
     */
    public function testCheckRequiredFieldsSomeMissingAccordingToaMustFillFields()
    {

        $aMustFillFields = array( 'oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxstreet',
                                  'oxuser__oxstreetnr', 'oxuser__oxzip', 'oxuser__oxcity',
                                  'oxuser__oxcountryid',
                                  'oxaddress__oxfname', 'oxaddress__oxlname', 'oxaddress__oxstreet',
                                  'oxaddress__oxstreetnr', 'oxaddress__oxzip', 'oxaddress__oxcity',
                                  'oxaddress__oxcountryid'
                                  );

        modConfig::getInstance()->setConfigParam( 'aMustFillFields', $aMustFillFields );

        $aInvAdress = array();
        $aDelAdress = array();

        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->at(0))->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxfname'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_NOTALLFIELDS')
                        )
                );
        $oValidator->expects($this->at(1))->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxlname'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_NOTALLFIELDS')
                        )
                );

        $oValidator->checkRequiredFields( $oUser, $aInvAdress, $aDelAdress);
    }

    /**
     * Test case for oxInputValidator::checkPassword()
     * 2. defining required fields in aMustFillFields. While testing original
     * function must not fail because all defined fields are filled with some values
     *
     * @return null
     */
    public function testCheckRequiredFieldsAllFieldsAreFine()
    {

        $aMustFillFields = array( 'oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxbirthdate' );

        modConfig::getInstance()->setConfigParam( 'aMustFillFields', $aMustFillFields );

        $aInvAdress = array( 'oxuser__oxfname' => 'xxx', 'oxuser__oxbirthdate' => array( 'year' => '123' ) );
        $aDelAdress = array( 'oxuser__oxlname' => 'yyy' );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $oValidator->checkRequiredFields( new oxUser(), $aInvAdress, $aDelAdress);
    }

    /**
     * Test case for oxInputValidator::checkPassword()
     * 1. for user without password - no checks
     *
     * @return null
     */
    public function testCheckPasswordUserWithoutPasswordNothingMustHappen()
    {
        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->never())->method('_addValidationError');

        $oValidator->checkPassword( new oxuser(), '', '' );
    }

    /**
     * Test case for oxInputValidator::checkPassword()
     * 2. for user without password - and check if it is empty on
     *
     * @return null
     */
    public function testCheckPasswordUserWithoutPassword()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxpassword'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_EMPTYPASS')
                        )
                );

        $oValidator->checkPassword( $oUser, '', '', true );
    }

    /**
     * Test case for oxInputValidator::checkPassword()
     * 3. for user without password - no checks
     *
     * @return null
     */
    public function testCheckPasswordPassTooShort()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxpassword'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_PASSTOOSHORT')
                        )
                );

        $oValidator->checkPassword( $oUser, 'xxx', '', true );
    }

    /**
     * Test case for oxInputValidator::checkPassword()
     * 4. for user without password - no checks
     *
     * @return null
     */
    public function testCheckPasswordPassDoNotMatch()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxpassword'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxUserException'),
                            $this->attributeEqualTo('message','EXCEPTION_USER_PWDDONTMATCH')
                        )
                );

        $oValidator->checkPassword( $oUser, 'xxxxxx', 'yyyyyy', $blCheckLenght = false  );
    }

    /**
     * Test case for oxInputValidator::checkEmail()
     * 1. user forgot to pass user login - must fail
     *
     * @return null
     */
    public function testCheckEmailNoEmail()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxusername'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_NOTALLFIELDS')
                        )
                );

        $oValidator->checkEmail( $oUser, '', 1 );
    }

    /**
     * Test case for oxInputValidator::checkEmail()
     * 2. checking is email validation is executed
     *
     * @return null
     */
    public function testCheckEmailEmailValidation()
    {
        oxAddClassModule( 'Unit_oxInputValidatorTest_oxutils', 'oxUtils' );

        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxusername'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','EXCEPTION_INPUT_NOVALIDEMAIL')
                        )
                );

        $oValidator->checkEmail( $oUser, 'a@a.a', 1 );
    }

    /**
     * Test case for oxInputValidator::checkLogin()
     * 1. testing if method detects dublicate records
     *
     * @return null
     */
    public function testCheckLoginUserWithPassDublicateLogin()
    {
        // loading some demo user to test if dublicates possible
        $oUser = $this->getMock( "oxuser", array( "checkIfEmailExists" ) );
        $oUser->setId("testlalaa_");

        $oUser->expects( $this->once() )->method( 'checkIfEmailExists' )->will( $this->returnValue( true ) );
        $oUser->oxuser__oxusername = new oxField( "testuser" );

        $aInvAdress['oxuser__oxusername'] = $oUser->oxuser__oxusername->value;

        $oLang = oxLang::getInstance();
        $sMsg = sprintf( $oLang->translateString( 'EXCEPTION_USER_USEREXISTS', $oLang->getTplLanguage() ), $aInvAdress['oxuser__oxusername'] );

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxusername'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxUserException'),
                            $this->attributeEqualTo('message', $sMsg)
                        )
                );

        $oValidator->checkLogin( $oUser, $oUser->oxuser__oxusername->value, $aInvAdress );
    }

    /**
     * Test case for oxInputValidator::checkLogin()
     * 2. if user tries to change login password must be entered ...
     *
     * @return null
     */
    public function testCheckLoginNewLoginNoPass()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oUser->oxuser__oxpassword = new oxField('b@b.b', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('b@b.b', oxField::T_RAW);

        $aInvAdress['oxuser__oxusername'] = 'a@a.a';
        $aInvAdress['oxuser__oxpassword'] = '';

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxpassword'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message', 'EXCEPTION_INPUT_NOTALLFIELDS')
                        )
                );

        $oValidator->checkLogin( $oUser, "test", $aInvAdress );
    }

    /**
     * Test case for oxInputValidator::checkLogin()
     * 3. if user tries to change login CORRECT password must be entered ...
     *
     * @return null
     */
    public function testCheckLoginNewLoginWrongPass()
    {
        $oUser = new oxuser();
        $oUser->setId("testlalaa_");

        $oUser->oxuser__oxpassword = new oxField('a@a.a', oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('b@b.b', oxField::T_RAW);

        $aInvAdress['oxuser__oxusername'] = 'a@a.a';
        $aInvAdress['oxuser__oxpassword'] = 'b@b.b';

        $oValidator = $this->getMock('oxinputvalidator', array('_addValidationError'));
        $oValidator->expects($this->once())->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxpassword'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxUserException'),
                            $this->attributeEqualTo('message', 'EXCEPTION_USER_PWDDONTMATCH')
                        )
                );

        $oValidator->checkLogin( $oUser, '', $aInvAdress );
    }

    /**
     * Test case for oxInputValidator::getInstance()
     *
     * @return null
     */
    public function testGetInstance()
    {
        $this->assertTrue( oxInputValidator::getInstance() instanceof oxInputValidator );
    }
}
