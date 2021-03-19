<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxUserException;
use \oxCompanyVatInCountryChecker;
use \oxOnlineVatIdCheck;
use \oxutils;
use \oxCompanyVatInValidator;
use \oxuser;
use OxidEsales\Eshop\Core\Field;
use \oxRegistry;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\InputValidator;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\InputException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Application\Model\User;

class InputValidatorTest extends UnitTestCase
{
    private $_validator = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_validator = oxNew(InputValidator::class, 'core');
    }

    public function testValidateBasketAmountnoUneven()
    {
        try {
            $this->assertEquals($this->_validator->validateBasketAmount('1,6'), 2);
            $this->assertEquals($this->_validator->validateBasketAmount('1.6'), 2);
            $this->assertEquals($this->_validator->validateBasketAmount('1.1'), 1);
        } catch (ArticleInputException $e) {
            $this->fail('Error while executing test: testValidateBasketAmountnoUneven');
        }
    }

    public function testValidateBasketAmountallowUneven()
    {
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', true);
        $this->assertEquals($this->_validator->validateBasketAmount('1.6'), 1.6);
    }

    public function providerNotAllowedArticleAmounts()
    {
        return [
            [-1],
            ['Alpha'], //FS#1758
            ['0.000,0']
        ];
    }

/**
 * @dataProvider providerNotAllowedArticleAmounts
 */
    public function testValidateBasketAmountBadInput($notAllowedAmount)
    {
        $this->getConfig()->setConfigParam('blAllowUnevenAmounts', false);
        $this->expectException(ArticleInputException::class);
        $this->_validator->validateBasketAmount($notAllowedAmount);
    }

    public function testValidatePaymentInputDataUnknownPayment()
    {
        $dynvalue = [];
        $validator = oxNew('oxinputvalidator');
        $this->assertTrue($validator->validatePaymentInputData('xxx', $dynvalue));
    }

    public function testValidatePaymentInputDataDCMissingFields()
    {
        $dynvalue = [];
        $validator = oxNew('oxinputvalidator');
        $this->assertFalse($validator->validatePaymentInputData('oxiddebitnote', $dynvalue));
    }

    public function lsblz()
    {
        return [
            ['12345678'],
            ['12345'],
            ['123456'],
        ];
    }

    /**
     * @dataProvider lsblz
     */
    public function testValidatePaymentWithValidInputData($lsblz)
    {
        $value = [
            'lsbankname'   => 'Bank name',
            'lsblz'        => $lsblz,
            'lsktonr'      => '123456789',
            'lsktoinhaber' => 'Hans Mustermann'
        ];

        $validator = oxNew(InputValidator::class);
        $this->assertTrue($validator->validatePaymentInputData('oxiddebitnote', $value));
    }

    public function badblz()
    {
        return [
            ['1234'],
            ['123456789']
        ];
    }

    /**
     * @dataProvider lsblz
     */
    public function testValidatePaymentWithInvalidInputData($lsblz)
    {
        $value = [
            'lsbankname'   => 'Bank name',
            'lsblz'        => $lsblz,
            'lsktonr'      => '123456789',
            'lsktoinhaber' => 'Hans Mustermann'
        ];

        $validator = oxNew(InputValidator::class);
        $iErr = -4;
        $this->assertEquals($iErr, $validator->validatePaymentInputData('oxiddebitnote', $value));
    }

    public function testAddValidationError()
    {
        $validator = oxNew(InputValidator::class);
        $this->assertEquals([], $validator->getFieldValidationErrors());
        $this->assertNull($validator->getFirstValidationError());

        $validator->addValidationError("userid", "err");
        $validator->addValidationError("fieldname", "err");
        $validator->addValidationError("error", "err");

        $this->assertEquals(
            [
                "userid" => ["err"], 
                "fieldname" => ["err"], 
                "error" => ["err"]
            ],
            $validator->getFieldValidationErrors()
        );
        $this->assertEquals("err", $validator->getFirstValidationError());
    }

    /**
     * @dataProvider formValuesDataProviderWithMissingFields
     */
    public function testCheckVatIdWithMissingParametersForCheck($valuesFromForm)
    {
        $user = oxNew("oxUser");

        $validator = $this->getMock(InputValidator::class, ['getCompanyVatInValidator']);
        $validator->expects($this->never())->method('getCompanyVatInValidator');

        $validator->checkVatId($user, $valuesFromForm);
    }

    public function formValuesDataProviderWithMissingFields()
    {
        return [
            [[]],
            [['oxuser__oxustid' => 1]],
            [['oxuser__oxustid' => 1, 'oxuser__oxcountryid' => 1]],
            [['oxuser__oxcountryid' => 1]],
            [['oxuser__oxcountryid' => 1, 'oxuser__oxcompany' => 1]],
            [['oxuser__oxcompany' => 1, 'oxuser__oxustid' => 1]]
        ];
    }

    public function testCheckVatIdWithMissingParametersForCheckCountryMissingError()
    {
        $user = oxNew("oxUser");
        $validator = oxNew(InputValidator::class);
        $validator->checkVatId(
            $user, 
            [
                'oxuser__oxustid' => 'AT123',
                'oxuser__oxcountryid' => 'a7c40f6320aeb2ec2.72885259'
            ]
        );

        $this->assertNotNull($validator->getFirstValidationError());
    }

    public function testCheckVatIdWithAllFieldSet()
    {
        $user = oxNew("oxUser");

        $validator = $this->getMock(InputValidator::class, ['getCompanyVatInValidator']);
        $validator->expects($this->any())->method('getCompanyVatInValidator')->will($this->returnValue(new oxCompanyVatInValidator(oxNew('oxCountry'))));

        $validator->checkVatId(
            $user,
            [
                'oxuser__oxustid' => 'AT123',
                'oxuser__oxcountryid' => 'a7c40f6320aeb2ec2.72885259',
                'oxuser__oxcompany' => 'Company'
            ]
        );
    }

    public function testCheckVatIdWithEuCountry()
    {
        $user = oxNew("oxUser");

        $validator = $this->getMock(InputValidator::class, ['getCompanyVatInValidator']);
        $validator->expects($this->any())->method('getCompanyVatInValidator')->will($this->returnValue(new oxCompanyVatInValidator(oxNew('oxCountry'))));

        $validator->checkVatId(
            $user, 
            [
                'oxuser__oxustid' => 'AT123',
                'oxuser__oxcountryid' => 'a7c40f6320aeb2ec2.72885259',
                'oxuser__oxcompany' => 'Company'
            ]
        );
    }

    public function testCheckVatIdWithNotEuCountry()
    {
        $user = oxNew("oxUser");

        $validator = $this->getMock(InputValidator::class, ['getCompanyVatInValidator']);
        $validator->expects($this->never())->method('getCompanyVatInValidator');

        $validator->checkVatId(
            $user, 
            [
                'oxuser__oxustid' => 'AT123',
                'oxuser__oxcountryid' => 'a7c40f6321c6f6109.43859248',
                'oxuser__oxcompany' => 'Company'
            ]
        );
    }

    public function testCheckCountriesWrongCountries()
    {
        $user = oxNew("oxUser");
        $user->setId('testusr');

        $validator = oxNew("oxinputvalidator");
        $validator->checkCountries(
            $user, 
            ["oxuser__oxcountryid" => "xxx"],
            ["oxaddress__oxcountryid" => "yyy"]
        );

        $this->assertTrue($validator->getFirstValidationError() instanceof UserException, "error in oxinputvalidator::checkCountries()");
    }

    public function testCheckCountriesAddsCorrectKeyForValidationError()
    {
        $user = oxNew('oxUser');
        $user->setId('testusr');

        $validator = oxNew(InputValidator::class);
        $validator->checkCountries(
            $user,
            ['oxuser__oxcountryid' => 'xxx'],
            ['oxaddress__oxcountryid' => 'yyy']
        );

        $fieldValidationErrors = $validator->getFieldValidationErrors();

        $this->assertTrue(
            array_key_exists('oxuser__oxcountryid', $fieldValidationErrors),
            'Correct key must be set for the country validation error'
        );
    }

    public function testCheckCountriesGoodCountries()
    {
        $user = oxNew("oxUser");
        $user->setId('testx');
        $validator = oxNew("oxinputvalidator");
        $validator->checkCountries(
            $user, 
            ["oxuser__oxcountryid" => "a7c40f631fc920687.20179984"],
            ["oxaddress__oxcountryid" => "a7c40f6320aeb2ec2.72885259"]
        );
        $this->assertNull($validator->getFirstValidationError());
    }

    public function testCheckRequiredFieldsSomeMissingAccordingToaMustFillFields()
    {
        $mustFillFields = [
            'oxuser__oxfname', 'oxuser__oxlname', 'oxuser__oxstreet',
            'oxuser__oxstreetnr', 'oxuser__oxzip', 'oxuser__oxcity',
            'oxuser__oxcountryid',
            'oxaddress__oxfname', 'oxaddress__oxlname', 'oxaddress__oxstreet',
            'oxaddress__oxstreetnr', 'oxaddress__oxzip', 'oxaddress__oxcity',
            'oxaddress__oxcountryid'
        ];

        $this->getConfig()->setConfigParam('aMustFillFields', $mustFillFields);

        $user = oxNew(User::class);
        $user->setId("testlalaa_");

        $validator = oxNew(InputValidator::class);

        $this->assertSame(
            [],
            \array_keys($validator->getFieldValidationErrors())
        );

        $validator->checkRequiredFields($user, [], ['foo' => 'bar']);

        $this->assertInstanceOf(
            InputException::class,
            $validator->getFirstValidationError()
        );

        $this->assertSame(
            $mustFillFields,
            \array_keys($validator->getFieldValidationErrors())
        );
    }

    public function testGetPasswordLengthDefaultValue()
    {
        $oViewConf = oxNew(InputValidator::class);
        $this->assertEquals(6, $oViewConf->getPasswordLength());
    }

    public function testGetPasswordLengthFromConfig()
    {
        $oViewConf = oxNew(InputValidator::class);

        $this->getConfig()->setConfigParam("iPasswordLength", 66);
        $this->assertEquals(66, $oViewConf->getPasswordLength());
    }

    public function testCheckRequiredFieldsAllFieldsAreFine()
    {
        $aMustFillFields = [
            'oxuser__oxfname',
            'oxuser__oxbirthdate',
            'oxaddress__oxlname'
        ];

        $this->getConfig()->setConfigParam('aMustFillFields', $aMustFillFields);

        $invAdress = [
            'oxuser__oxfname' => 'xxx',
            'oxuser__oxbirthdate' => [
                'year' => '123'
            ]
        ];
        $aDelAdress = ['oxaddress__oxlname' => 'yyy'];

        $validator = $this->getMock(InputValidator::class, ['addValidationError']);
        $validator->expects($this->never())->method('addValidationError');

        $validator->checkRequiredFields(new oxUser(), $invAdress, $aDelAdress);
    }

    public function testCheckPassword_NoError_WhenPasswordCorrect()
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $validator = $this->getMock(InputValidator::class, ['addValidationError']);
        $validator->expects($this->never())->method('addValidationError');

        $validator->checkPassword($user, '1234567', '1234567', true);
    }

    public function testCheckPassword_NoError_WhenPasswordLengthIsSameAsCustomDefined()
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $this->setConfigParam('iPasswordLength', 7);

        $validator = $this->getMock(InputValidator::class, ['addValidationError']);
        $validator->expects($this->never())->method('addValidationError');

        $validator->checkPassword($user, '1234567', '1234567', true);
    }

    public function testCheckPassword_ThrowError_WhenWhenPasswordIsShortenThenCustomDefined()
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $this->setConfigParam('iPasswordLength', 8);

        $validator = $this->getMock(InputValidator::class, ['addValidationError']);
        $validator->expects($this->atLeastOnce())->method('addValidationError');

        $validator->checkPassword($user, '1234567', '1234567', true);
    }

    public function testCheckPasswordUserWithoutPasswordNothingMustHappen()
    {
        $validator = $this->getMock(InputValidator::class, ['addValidationError']);
        $validator->expects($this->never())->method('addValidationError');

        $validator->checkPassword(new oxuser(), '', '');
    }

    public function passwordInputChecksProvider()
    {
        return [
            [
                '',
                '',
                true,
                InputException::class,
                'ERROR_MESSAGE_INPUT_EMPTYPASS'
            ],
            [
                'xxx',
                '',
                true,
                InputException::class,
                'ERROR_MESSAGE_PASSWORD_TOO_SHORT'
            ],
            [
                'xxxxxx',
                'yyyyyy',
                false,
                UserException::class,
                'ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'
            ],
        ];
    }
    
    /**
     * @dataProvider passwordInputChecksProvider
     */
    public function testCheckPasswordUserPasswordInputErrors($password, $passwordCheck, $blCheckLength, $exception, $errorMsg)
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $validator = oxNew(InputValidator::class);
        $validator->checkPassword($user, $password, $passwordCheck, $blCheckLength);
        $fieldError = $validator->getFirstValidationError();

        $expected = oxNew($exception);
        $expected->setMessage(oxRegistry::getLang()->translateString($errorMsg));
        $this->assertEquals(
            $expected,
            $fieldError
        );
    }

    public function emailInputChecksProvider()
    {
        return [
            [
                '',
                'ERROR_MESSAGE_INPUT_NOTALLFIELDS'
            ],
            [
                'a@aa',
                'ERROR_MESSAGE_INPUT_NOVALIDEMAIL'
            ]
        ];
    }

    /**
     * @dataProvider emailInputChecksProvider
     */
    public function testCheckEmailValidation($email, $errorMsg)
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");
        $validator = oxNew(InputValidator::class);
        $validator->checkEmail($user, $email);

        $fieldError = $validator->getFirstValidationError();

        $expected = oxNew(InputException::class);
        $expected->setMessage(oxRegistry::getLang()->translateString($errorMsg));
        $this->assertEquals(
            $expected,
            $fieldError
        );
    }

    public function testCheckLoginUserWithPassDuplicateLogin()
    {
        // loading some demo user to test if duplicates possible
        $user = $this->getMock(User::class, ["checkIfEmailExists"]);
        $user->setId("testlalaa_");

        $user->expects($this->once())->method('checkIfEmailExists')->will($this->returnValue(true));
        $user->oxuser__oxusername = new Field("testuser");

        $invAdress['oxuser__oxusername'] = $user->oxuser__oxusername->value;

        $lang = oxRegistry::getLang();
        $msg = sprintf($lang->translateString('ERROR_MESSAGE_USER_USEREXISTS', $lang->getTplLanguage()), $invAdress['oxuser__oxusername']);

        $validator = oxNew(InputValidator::class);

        $validator->checkLogin($user, $user->oxuser__oxusername->value, $invAdress);
        
        $fieldError = $validator->getFirstValidationError();
        $expected = oxNew(UserException::class);
        $expected->setMessage($msg);
        $this->assertEquals(
            $expected,
            $fieldError
        );
    }

    public function testCheckLoginNewLoginNoPass()
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $user->oxuser__oxpassword = new Field('b@b.b', Field::T_RAW);
        $user->oxuser__oxusername = new Field('b@b.b', Field::T_RAW);

        $invAdress['oxuser__oxusername'] = 'a@a.a';
        $invAdress['oxuser__oxpassword'] = '';

        $validator = oxNew(InputValidator::class);
        $validator->checkLogin($user, "test", $invAdress);

        $fieldError = $validator->getFirstValidationError();
        
        $expected = oxNew(InputException::class);
        $expected->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS'));
        $this->assertEquals(
            $expected,
            $fieldError
        );
    }

    public function testCheckLoginNewLoginWrongPass()
    {
        $user = oxNew(User::class);
        $user->setId("testlalaa_");

        $user->oxuser__oxpassword = new Field('a@a.a', Field::T_RAW);
        $user->oxuser__oxpasssalt = new Field(md5('salt'), Field::T_RAW);
        $user->oxuser__oxusername = new Field('b@b.b', Field::T_RAW);

        $invoiceAdress['oxuser__oxusername'] = 'a@a.a';
        $invoiceAdress['oxuser__oxpassword'] = 'b@b.b';

        $validator = oxNew(InputValidator::class);
        $validator->checkLogin($user, '', $invoiceAdress);

        $fieldErrors = $validator->getFieldValidationErrors();
        $firstError = array_pop($fieldErrors);
        
        $expected = oxNew(UserException::class);
        $expected->setMessage(oxRegistry::getLang()->translateString('ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH'));
        $this->assertEquals(
            $expected,
            $firstError[0]
        );
    }

    public function testCheckLoginWithUserNameTakenFromParameters()
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $validator = oxNew(InputValidator::class);

        $this->assertEquals('a@a.a', $validator->checkLogin($user, 'a@a.a', []));
    }

    public function testCheckLoginWithUserNameTakenFromAddress()
    {
        $user = oxNew('oxuser');
        $user->setId("testlalaa_");

        $invAdress['oxuser__oxusername'] = 'a@a.a';

        $validator = oxNew(InputValidator::class);

        $this->assertEquals('a@a.a', $validator->checkLogin($user, null, $invAdress));
    }

    public function testValidatePaymentInputData_SepaBankCodeCorrectSepaAccountNumberCorrect_NoError()
    {
        $sBankCode = $this->_getSepaBankCode();
        $sAccountNumber = $this->_getSepaAccountNumber();

        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $this->assertTrue($validator->validatePaymentInputData("oxiddebitnote", $dynValue), 'Error should not appear.');
    }

    public function providerValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError()
    {
        $sOldAccountNumberTooShort = "12345678";
        $sOldAccountNumber = $this->_getOldAccountNumber();
        $sOldBankCode = $this->_getOldBankCode();

        return [
            [$sOldBankCode, $sOldAccountNumber],
            [$sOldBankCode, $sOldAccountNumberTooShort]
        ];
    }

    /**
     * @dataProvider providerValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError
     */
    public function testValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError($sBankCode, $sAccountNumber)
    {
        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $this->assertTrue($validator->validatePaymentInputData("oxiddebitnote", $dynValue), 'Error should not appear.');
    }

    /**
     * @dataProvider providerValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrect_NoError
     */
    public function testValidatePaymentInputData_OldBankCodeCorrectOldAccountNumberCorrectOldBankInfoNotAllowed_Error($sBankCode, $sAccountNumber)
    {
        $this->setConfigParam('blSkipDebitOldBankInfo', true);

        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $this->assertSame($this->_getBankCodeErrorNo(), $validator->validatePaymentInputData("oxiddebitnote", $dynValue), 'Error should appear as old bank information not allowed.');
    }

    public function providerValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber()
    {
        $sOldAccountNumberTooLong = "1234567890123";
        $sOldAccountIncorrectFormat = "ABC1234567";

        return [
            [$sOldAccountNumberTooLong],
            [$sOldAccountIncorrectFormat],
        ];
    }

    /**
     * @dataProvider providerValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber
     */
    public function testValidatePaymentInputData_BankCodeOldCorrectAccountNumberIncorrect_ErrorAccountNumber($sAccountNumber)
    {
        $sBankCode = $this->_getOldBankCode();

        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $oValidationResult = $validator->validatePaymentInputData("oxiddebitnote", $dynValue);

        $sErrorAccountNumberNo = $this->_getAccountNumberErrorNo();
        $this->assertSame($sErrorAccountNumberNo, $oValidationResult, 'Should validate as account number error.');
    }

    public function testValidatePaymentInputData_BankCodeOldCorrectAccountNumberSepaCorrect_ErrorAccountNumber()
    {
        $sBankCode = $this->_getOldBankCode();
        $sAccountNumber = $this->_getSepaAccountNumber();

        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $oValidationResult = $validator->validatePaymentInputData("oxiddebitnote", $dynValue);

        $iErrorNumber = $this->_getAccountNumberErrorNo();
        $this->assertSame($iErrorNumber, $oValidationResult, 'Should validate as bank code error.');
    }

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

        return [
            [$sOldBankCodeTooShort, $sOldAccountNumber],
            [$sOldBankCodeTooShort, $sOldAccountNumberTooLong],
            [$sOldBankCodeTooShort, $sOldAccountIncorrectFormat],
            [$sOldBankCodeTooShort, $sSepaAccountNumber],
            [$sOldBankCodeTooShort, $sSepaAccountNumberWrong],

            [$sOldBankCodeTooLong, $sOldAccountNumber],
            [$sOldBankCodeTooLong, $sOldAccountNumberTooLong],
            [$sOldBankCodeTooLong, $sOldAccountIncorrectFormat],
            [$sOldBankCodeTooLong, $sSepaAccountNumber],
            [$sOldBankCodeTooLong, $sSepaAccountNumberWrong],

            [$sOldBankCodeWrongFormat, $sOldAccountNumber],
            [$sOldBankCodeWrongFormat, $sOldAccountNumberTooLong],
            [$sOldBankCodeWrongFormat, $sOldAccountIncorrectFormat],
            [$sOldBankCodeWrongFormat, $sSepaAccountNumber],
            [$sOldBankCodeWrongFormat, $sSepaAccountNumberWrong],

            [$sSepaBankCodeWrong, $sOldAccountNumber],
            [$sSepaBankCodeWrong, $sOldAccountNumberTooLong],
            [$sSepaBankCodeWrong, $sOldAccountIncorrectFormat],
            [$sSepaBankCodeWrong, $sSepaAccountNumber],
            [$sSepaBankCodeWrong, $sSepaAccountNumberWrong],
        ];
    }

    /**
     * @dataProvider providerValidatePaymentInputData_BankCodeIncorrect_ErrorBankCode
     */
    public function testValidatePaymentInputData_BankCodeIncorrect_ErrorBankCode($sBankCode, $sAccountNumber)
    {
        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $oValidationResult = $validator->validatePaymentInputData("oxiddebitnote", $dynValue);

        $sErrorBankCodeNo = $this->_getBankCodeErrorNo();
        $this->assertSame($sErrorBankCodeNo, $oValidationResult, 'Should validate as bank code error.');
    }

    public function providerValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber()
    {
        $sOldBankCodeTooShort = '1234';
        $sOldAccountNumberTooLong = "123456789123456789";
        $sOldAccountIncorrectFormat = "ABC1234567";
        $sSepaAccountNumberIncorrect = 'NX9386011117947';

        return [
            [$sOldBankCodeTooShort],
            [$sOldAccountNumberTooLong],
            [$sOldAccountIncorrectFormat],
            [$sSepaAccountNumberIncorrect]
        ];
    }

    /**
     * @dataProvider providerValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber
     */
    public function testValidatePaymentInputData_SepaBankCodeCorrectAccountNumberIncorrect_ErrorAccountNumber($sAccountNumber)
    {
        $sBankCode = $this->_getSepaBankCode();
        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $oValidationResult = $validator->validatePaymentInputData("oxiddebitnote", $dynValue);

        $sErrorNumber = $this->_getAccountNumberErrorNo();
        $this->assertSame($sErrorNumber, $oValidationResult, 'Should validate as account number error.');
    }

    public function testValidatePaymentInputData_SepaBankCodeCorrectOldAccountNumberCorrect_ErrorAccountNumber()
    {
        $sBankCode = $this->_getSepaBankCode();
        $sAccountNumber = $this->_getOldAccountNumber();
        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $oValidationResult = $validator->validatePaymentInputData("oxiddebitnote", $dynValue);

        $sErrorNumber = $this->_getAccountNumberErrorNo();
        $this->assertSame($sErrorNumber, $oValidationResult, 'Should validate as bank code error.');
    }

    public function testValidatePaymentInputData_SepaBankCodeCorrectOldAccountNumberCorrectOldBankInfoNotAllowed_ErrorAccountNumber()
    {
        $this->setConfigParam('blSkipDebitOldBankInfo', true);

        $sBankCode = $this->_getSepaBankCode();
        $sAccountNumber = $this->_getOldAccountNumber();
        $dynValue = $this->_getBankData($sBankCode, $sAccountNumber);

        $validator = oxNew(InputValidator::class);
        $oValidationResult = $validator->validatePaymentInputData("oxiddebitnote", $dynValue);

        $this->assertSame($this->_getAccountNumberErrorNo(), $oValidationResult, 'Error should appear as old bank information not allowed.');
    }

    private function _getSepaBankCode(): string
    {
        return "ASPKAT2L";
    }

    private function _getSepaAccountNumber(): string
    {
        return "MT84MALT011000012345MTLCAST001S";
    }

    private function _getOldBankCode(): string
    {
        return "12345678";
    }

    private function _getOldAccountNumber(): string
    {
        return "123456789012";
    }

    private function _getBankData($sBankCode, $sAccountNumber): array
    {
        $dynvalue = [
            'lsbankname'   => 'Bank name',
            'lsblz'        => $sBankCode,
            'lsktonr'      => $sAccountNumber,
            'lsktoinhaber' => 'Hans Mustermann'
        ];

        return $dynvalue;
    }

    private function _getAccountNumberErrorNo(): int
    {
        return -5;
    }

    private function _getBankCodeErrorNo(): int
    {
        return -4;
    }

    public function testGetCompanyVatInValidator_Set()
    {
        $oCountry = oxNew('oxCountry');
        $oInputValidator = oxNew(InputValidator::class);
        $oVatInValidator = new oxCompanyVatInValidator($oCountry);

        $oInputValidator->setCompanyVatInValidator($oVatInValidator);

        $this->assertSame($oVatInValidator, $oInputValidator->getCompanyVatInValidator($oCountry));
    }

    public function testGetCompanyVatInValidator_Default()
    {
        $oInputValidator = oxNew(InputValidator::class);

        $oVatInValidator = $oInputValidator->getCompanyVatInValidator(oxNew('oxCountry'));

        $this->assertTrue($oVatInValidator instanceof \OxidEsales\EshopCommunity\Core\CompanyVatInValidator);
        $aCheckers = $oVatInValidator->getCheckers();

        $this->assertSame(2, count($aCheckers));

        $this->assertTrue($aCheckers[0] instanceof \OxidEsales\EshopCommunity\Core\CompanyVatInCountryChecker);
        $this->assertTrue($aCheckers[1] instanceof \OxidEsales\EshopCommunity\Core\OnlineVatIdCheck);
    }

    public function testGetCompanyVatInValidator_DefaultTurnedOffOnline()
    {
        $this->getConfig()->setConfigParam('blVatIdCheckDisabled', true);

        $oInputValidator = oxNew(InputValidator::class);
        $oVatInValidator = $oInputValidator->getCompanyVatInValidator(oxNew('oxCountry'));

        $this->assertTrue($oVatInValidator instanceof \OxidEsales\EshopCommunity\Core\CompanyVatInValidator);

        $aCheckers = $oVatInValidator->getCheckers();
        $this->assertSame(1, count($aCheckers));
        $this->assertFalse($aCheckers[0] instanceof \OxidEsales\EshopCommunity\Core\OnlineVatIdCheck);
    }
}
