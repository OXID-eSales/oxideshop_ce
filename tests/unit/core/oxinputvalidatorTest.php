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
    public function testValidatePaymentInputData_BankCodeCorrect8LengthAccountNumberCorrect_valid()
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
    public function testValidatePaymentInputData_BankCodeCorrect5LengthAccountNumberCorrect_valid()
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
    public function testValidatePaymentInputData_BankCodeTooShortAccountNumberCorrect_bankCodeError()
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
    public function testValidatePaymentInputData_6CharBankCode_true()
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
    public function testValidatePaymentInputData_8CharBankCode_true()
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
    public function testValidatePaymentInputData_9CharBankCode_error()
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

        $oValidator = $this->getMock('oxInputValidator', array('_addValidationError', '_getVatIdValidator'));
        $oValidator->expects($this->never())->method('_addValidationError');


        $aHome = $this->getConfig()->getConfigParam( 'aHomeCountry' );
        $oValidator->checkVatId( $oUser, array('oxuser__oxustid' => 'DE123', 'oxuser__oxcountryid' => $aHome[0]) );
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
     * Greece has different VAT identification number, test checks this exception.
     * Test for bug #4212
     */
    public function testCheckVatId_ExceptionForGreece()
    {
        $oUser = oxNew( 'oxUser' );

        $oValidator = $this->getMock( 'oxinputvalidator', array( '_addValidationError' ) );
        $oValidator->expects( $this->never() )->method( '_addValidationError' );

        $sCountryId = 'a7c40f633114e8fc6.25257477'; //Greece

        $oValidator->checkVatId( $oUser, array( 'oxuser__oxustid' => 'EL123', 'oxuser__oxcountryid' => $sCountryId ) );
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_INPUT_NOTALLFIELDS')
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_INPUT_NOTALLFIELDS')
                        )
                );
        $oValidator->expects($this->at(1))->method('_addValidationError')
                ->with(
                        $this->equalTo('oxuser__oxlname'),
                        $this->logicalAnd(
                            $this->isInstanceOf('oxInputException'),
                            $this->attributeEqualTo('message','ERROR_MESSAGE_INPUT_NOTALLFIELDS')
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_INPUT_EMPTYPASS')
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_PASSWORD_TOO_SHORT')
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_USER_PWDDONTMATCH')
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_INPUT_NOTALLFIELDS')
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
                            $this->attributeEqualTo('message','ERROR_MESSAGE_INPUT_NOVALIDEMAIL')
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
        $sMsg = sprintf( $oLang->translateString( 'ERROR_MESSAGE_USER_USEREXISTS', $oLang->getTplLanguage() ), $aInvAdress['oxuser__oxusername'] );

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
                            $this->attributeEqualTo('message', 'ERROR_MESSAGE_INPUT_NOTALLFIELDS')
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
                            $this->attributeEqualTo('message', 'ERROR_MESSAGE_USER_PWDDONTMATCH')
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

    /**
     * Testing validatePaymentInputData with SepaBankCodeCorrect and SepaAccountNumberCorrect
     * expecting NoError
     */
    public function testValidatePaymentInputData_SepaBankCodeCorrectSepaAccountNumberCorrect_NoError()
    {
        $sBankCode = $this->_getSepaBankCode();
        $sAccountNumber = $this->_getSepaAccountNumber();

        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue ), 'Error should not appear.' );
    }

    /**
     * Data provider for testValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError
     *
     * @return array
     */
    public function providerValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError()
    {
        $sOldAccountNumberTooShort = "12345678";
        $sOldAccountNumber = $this->_getOldAccountNumber();
        $sOldBankCode = $this->_getOldBankCode();
        return array(
            array( $sOldBankCode, $sOldAccountNumber ),
            array( $sOldBankCode, $sOldAccountNumberTooShort ),
        );
    }

    /**
     * Testing validatePaymentInputData with OldBankCodeCorrect and OldAccountNumberCorrect
     * expecting NoError
     *
     * @dataProvider providerValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError
     *
     * @param $sBankCode
     * @param $sAccountNumber
     */
    public function testValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError( $sBankCode, $sAccountNumber )
    {
        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $this->assertTrue( $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue ), 'Error should not appear.' );
    }

    /**
     * Testing validatePaymentInputData with OldBankCodeCorrect and OldAccountNumberCorrect
     * expecting NoError
     *
     * @dataProvider providerValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError
     * expecting ErrorBankAccount
     *
     * @param $sBankCode
     * @param $sAccountNumber
     */
    public function testValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrectOldBankInfoNotAllowed_Error( $sBankCode, $sAccountNumber )
    {
        $this->setConfigParam( 'blSkipDebitOldBankInfo', true );

        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $this->assertSame( $this->_getBankCodeErrorNo(), $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue ), 'Error should appear as old bank information not allowed.' );
    }

    /**
     * Data provider for testValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber
     *
     * @return array
     */
    public function providerValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber()
    {
        $sOldAccountNumberTooLong = "1234567890123";
        $sOldAccountIncorrectFormat = "ABC1234567";
        return array(
            array( $sOldAccountNumberTooLong ),
            array( $sOldAccountIncorrectFormat ),
        );
    }

    /**
     * Testing validatePaymentInputData with BankCodeOldCorrect and AccountNumberIncorrect
     * expecting ErrorAccountNumber
     *
     * @dataProvider providerValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber
     */
    public function testValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber( $sAccountNumber )
    {
        $sBankCode = $this->_getOldBankCode();

        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $sErrorAccountNumberNo = $this->_getAccountNumberErrorNo();
        $this->assertSame( $sErrorAccountNumberNo, $oValidationResult, 'Should validate as account number error.' );
    }

    /**
     * Testing validatePaymentInputData with BankCodeOldCorrect and AccountNumberSepaCorrect
     * expecting ErrorBankCode
     */
    public function testValidatePaymentInputData_BankCodeOldCorrectAccountNumberSepaCorrect_ErrorAccountNumber()
    {
        $sBankCode = $this->_getOldBankCode();
        $sAccountNumber = $this->_getSepaAccountNumber();

        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $iErrorNumber = $this->_getAccountNumberErrorNo();
        $this->assertSame( $iErrorNumber, $oValidationResult, 'Should validate as bank code error.' );
    }

    /**
     * Testing validatePaymentInputData with BankCodeEmpty and AccountNumberSepaCorrect
     * expecting True
     */
    public function testValidatePaymentInputData_BankCodeEmptyAccountNumberSepaCorrect_True()
    {
        $sAccountNumber = $this->_getSepaAccountNumber();

        $aDynValue = $this->_getBankData( '', $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $this->assertTrue( $oValidationResult, 'Should validate as true.' );
    }

    /**
     * Data provider for testValidatePaymentInputData_BankCodeIncorrect_ErrorBankCode
     * @return array
     */
    public function providerValidatePaymentInputData_BankCodeIncorrect_ErrorBankCode()
    {
        $sOldBankCodeTooShort = '1234';
        $sOldBankCodeTooLong = '123456789';
        $sOldBankCodeWrongFormat = '123A5678';
        $sSepaBankCodeWrong = '123ABCDE';

        $sOldAccountNumber = $this->_getOldAccountNumber();
        $sOldAccountNumberTooLong = "12345678901";
        $sOldAccountIncorrectFormat = "ABC1234567";

        $sSepaAccountNumber = $this->_getSepaAccountNumber();
        $sSepaAccountNumberWrong = 'NX9386011117947';

        return array(
            array( $sOldBankCodeTooShort, $sOldAccountNumber ),
            array( $sOldBankCodeTooShort, $sOldAccountNumberTooLong ),
            array( $sOldBankCodeTooShort, $sOldAccountIncorrectFormat ),
            array( $sOldBankCodeTooShort, $sSepaAccountNumber ),
            array( $sOldBankCodeTooShort, $sSepaAccountNumberWrong ),

            array( $sOldBankCodeTooLong, $sOldAccountNumber ),
            array( $sOldBankCodeTooLong, $sOldAccountNumberTooLong ),
            array( $sOldBankCodeTooLong, $sOldAccountIncorrectFormat ),
            array( $sOldBankCodeTooLong, $sSepaAccountNumber ),
            array( $sOldBankCodeTooLong, $sSepaAccountNumberWrong ),

            array( $sOldBankCodeWrongFormat, $sOldAccountNumber ),
            array( $sOldBankCodeWrongFormat, $sOldAccountNumberTooLong ),
            array( $sOldBankCodeWrongFormat, $sOldAccountIncorrectFormat ),
            array( $sOldBankCodeWrongFormat, $sSepaAccountNumber ),
            array( $sOldBankCodeWrongFormat, $sSepaAccountNumberWrong ),

            array( $sSepaBankCodeWrong, $sOldAccountNumber ),
            array( $sSepaBankCodeWrong, $sOldAccountNumberTooLong ),
            array( $sSepaBankCodeWrong, $sOldAccountIncorrectFormat ),
            array( $sSepaBankCodeWrong, $sSepaAccountNumber ),
            array( $sSepaBankCodeWrong, $sSepaAccountNumberWrong ),
        );
    }

    /**
     * Testing ValidatePaymentInputData with BankCodeIncorrect
     * expecting ErrorBankCode
     *
     * @dataProvider providerValidatePaymentInputData_BankCodeIncorrect_ErrorBankCode
     *
     * @param $sBankCode
     * @param $sAccountNumber
     */
    public function testValidatePaymentInputData_BankCodeIncorrect_ErrorBankCode( $sBankCode, $sAccountNumber )
    {
        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $sErrorBankCodeNo = $this->_getBankCodeErrorNo();
        $this->assertSame( $sErrorBankCodeNo, $oValidationResult, 'Should validate as bank code error.' );
    }

    /**
     * Data provider for testValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber
     * @return array
     */
    public function providerValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber()
    {
        $sOldBankCodeTooShort = '1234';
        $sOldAccountNumberTooLong = "123456789123456789";
        $sOldAccountIncorrectFormat = "ABC1234567";
        $sSepaAccountNumberIncorrect = 'NX9386011117947';

        return array (
            array($sOldBankCodeTooShort),
            array($sOldAccountNumberTooLong),
            array($sOldAccountIncorrectFormat),
            array($sSepaAccountNumberIncorrect),
        );
    }

    /**
     * Fixed for bug entry 0005543: BIC is shown as incorrect if IBAN is incorrect, although BIC is correct
     *
     * Testing validatePaymentInputData with SepaBankCodeCorrect and AccountNumberIncorrect
     * expecting ErrorAccountNumber
     *
     * @dataProvider providerValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber
     * @param $sAccountNumber
     */
    public function testValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber( $sAccountNumber )
    {
        $sBankCode = $this->_getSepaBankCode();
        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $sErrorNumber = $this->_getAccountNumberErrorNo();
        $this->assertSame($sErrorNumber, $oValidationResult, 'Should validate as account number error.' );
    }

    /**
     * Testing validatePaymentInputData with SepaBankCodeCorrect and OldAccountNumberCorrect
     * expecting ErrorBankCode
     */
    public function testValidatePaymentInputData_SepaBankCodeCorrectOldAccountNumberCorrect_ErrorAccountNumber()
    {
        $sBankCode = $this->_getSepaBankCode();
        $sAccountNumber = $this->_getOldAccountNumber();
        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $sErrorNumber = $this->_getAccountNumberErrorNo();
        $this->assertSame( $sErrorNumber, $oValidationResult, 'Should validate as bank code error.' );
    }

    /**
     * Testing validatePaymentInputData with SepaBankCodeCorrect and OldAccountNumberCorrect when old bank info not allowed.
     * expecting ErrorBankAccount
     */
    public function testValidatePaymentInputData_SepaBankCodeCorrectOldAccountNumberCorrectOldBankInfoNotAllowed_ErrorAccountNumber()
    {
        $this->setConfigParam( 'blSkipDebitOldBankInfo', true );

        $sBankCode = $this->_getSepaBankCode();
        $sAccountNumber = $this->_getOldAccountNumber();
        $aDynValue = $this->_getBankData( $sBankCode, $sAccountNumber );

        $oValidator = new oxInputValidator();
        $oValidationResult = $oValidator->validatePaymentInputData( "oxiddebitnote", $aDynValue );

        $this->assertSame( $this->_getAccountNumberErrorNo(), $oValidationResult, 'Error should appear as old bank information not allowed.' );
    }

    /**
     * Returns valid SEPA bank code.
     * @return string
     */
    private function _getSepaBankCode()
    {
        return "ASPKAT2L";
    }

    /**
     * Returns valid SEPA account number.
     * @return string
     */
    private function _getSepaAccountNumber()
    {
        return "MT84MALT011000012345MTLCAST001S";
    }
    /**
     * Returns valid old bank code.
     * @return string
     */
    private function _getOldBankCode()
    {
        return "12345678";
    }

    /**
     * Returns valid old account number.
     * @return string
     */
    private function _getOldAccountNumber()
    {
        return "123456789012";
    }

    /**
     * @param $sBankCode
     * @param $sAccountNumber
     *
     * @return array
     */
    private function _getBankData( $sBankCode, $sAccountNumber )
    {
        $aDynvalue = array('lsbankname'   => 'Bank name',
                           'lsblz'        => $sBankCode,
                           'lsktonr'      => $sAccountNumber,
                           'lsktoinhaber' => 'Hans Mustermann'
        );

        return $aDynvalue;
    }

    /**
     * @return int
     */
    private function _getAccountNumberErrorNo()
    {
        return -5;
    }

    /**
     * @return int
     */
    private function _getBankCodeErrorNo()
    {
        return -4;
    }
}
