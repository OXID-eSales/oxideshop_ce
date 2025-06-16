<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Core\Exception\InputException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\InputValidator;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class InputValidatorTest extends IntegrationTestCase
{
    private string $countryId = 'test_country_id';
    private string $validUstId = 'DD123456789';
    private string $invalidUstId = 'DE123456789';
    private string $oxidDebitNote = 'oxiddebitnote';
    private bool $configVatIdCheckDisabled;
    private InputValidator $inputValidator;

    public function setUp(): void
    {
        parent::setUp();

        $this->inputValidator = new InputValidator();

        $this->configVatIdCheckDisabled = (bool) Registry::getConfig()->getConfigParam('blVatIdCheckDisabled');
        Registry::getConfig()->setConfigParam('blVatIdCheckDisabled', true);
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('blVatIdCheckDisabled', $this->configVatIdCheckDisabled);

        parent::tearDown();
    }

    public function testCheckVatIdValidationPassesForValidEU(): void
    {
        $user = $this->createMock(User::class);

        $this->createCountry();

        $invAddress = [
            'oxuser__oxustid' => $this->validUstId,
            'oxuser__oxcountryid' => $this->countryId,
            'oxuser__oxcompany' => 'Test Company',
        ];

        $this->inputValidator->checkVatId($user, $invAddress);

        $this->assertEmpty($this->inputValidator->getFieldValidationErrors());
    }

    public function testValidatePaymentInputDataWithCorrectBankCode(): void
    {
        $testValues = [
            'lsbankname' => 'Bank name',
            'lsblz' => 'DEDEDEFF',
            'lsktonr' => 'DE55200800000770876200',
            'lsktoinhaber' => 'Hans Mustermann',
        ];

        $result = $this->inputValidator->validatePaymentInputData($this->oxidDebitNote, $testValues);

        $this->assertTrue($result);
    }

    public function testCheckVatIdThrowsExceptionForMissingCompany(): void
    {
        $user = $this->createMock(User::class);
        $invAddress = [
            'oxuser__oxustid' => $this->invalidUstId,
            'oxuser__oxcountryid' => $this->countryId,
            'oxuser__oxcompany' => null
        ];

        $this->inputValidator->checkVatId($user, $invAddress);

        $this->assertInstanceOf(
            InputException::class,
            $this->inputValidator->getFieldValidationErrors()['oxuser__oxcompany'][0]
        );
    }

    public function testCheckVatIdThrowsExceptionForInvalidVatId(): void
    {
        $user = $this->createMock(User::class);
        $invAddress = [
            'oxuser__oxustid' => $this->invalidUstId,
            'oxuser__oxcountryid' => $this->countryId,
            'oxuser__oxcompany' => 'Test Company',
        ];
        $this->createCountry();

        $this->inputValidator->checkVatId($user, $invAddress);

        $this->assertInstanceOf(
            InputException::class,
            $this->inputValidator->getFieldValidationErrors()['oxuser__oxustid'][0]
        );
    }

    public function testValidatePaymentInputDataWithSpaceCharacterForBankCode(): void
    {
        $testValues = [
            'lsbankname' => 'Bank name',
            'lsblz' => ' ',
            'lsktonr' => '123456',
            'lsktoinhaber' => 'Hans Mustermann',
        ];

        $result = $this->inputValidator->validatePaymentInputData($this->oxidDebitNote, $testValues);

        $this->assertEquals(
            InputValidator::INVALID_BANK_CODE,
            $result
        );
    }

    public function testValidatePaymentInputDataWithBlankBankCode(): void
    {
        $testValues = [
            'lsbankname' => 'Bank name',
            'lsblz' => '',
            'lsktonr' => '123456',
            'lsktoinhaber' => 'Hans Mustermann',
        ];

        $validationResult = $this->inputValidator->validatePaymentInputData($this->oxidDebitNote, $testValues);

        $this->assertEquals(
            InputValidator::INVALID_BANK_CODE,
            $validationResult
        );
    }

    private function createCountry(): void
    {
        $country = new Country();
        $country->setId($this->countryId);
        $country->oxcountry__oxactive = new Field(1);
        $country->oxcountry__oxvatstatus = new Field(1);
        $country->oxcountry__oxvatinprefix = new Field('DD');
        $country->save();
    }
}
