<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Core\InputValidator;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class InputValidatorTest extends IntegrationTestCase
{
    private string $oxidDebitNote = 'oxiddebitnote';

    public function testValidatePaymentInputDataWithSpaceCharacterForBankCode(): void
    {
        $testValues = [
            'lsbankname' => 'Bank name',
            'lsblz' => ' ',
            'lsktonr' => '123456',
            'lsktoinhaber' => 'Hans Mustermann',
        ];

        $validator = oxNew(InputValidator::class);
        $result = $validator->validatePaymentInputData($this->oxidDebitNote, $testValues);
        $this->assertEquals(InputValidator::INVALID_BANK_CODE, $result, 'Should validate as invalid bank code error.');
    }

    public function testValidatePaymentInputDataWithCorrectBankCode(): void
    {
        $testValues = [
            'lsbankname' => 'Bank name',
            'lsblz' => '12345678',
            'lsktonr' => '123456',
            'lsktoinhaber' => 'Hans Mustermann',
        ];

        $validator = oxNew(InputValidator::class);
        $result = $validator->validatePaymentInputData($this->oxidDebitNote, $testValues);
        $this->assertTrue($result, 'Should validate as True');
    }

    public function testValidatePaymentInputDataWithBlankBankCode(): void
    {
        $testValues = [
            'lsbankname' => 'Bank name',
            'lsblz' => '',
            'lsktonr' => '123456',
            'lsktoinhaber' => 'Hans Mustermann',
        ];

        $validator = oxNew(InputValidator::class);
        $validationResult = $validator->validatePaymentInputData($this->oxidDebitNote, $testValues);

        $this->assertEquals(
            InputValidator::INVALID_BANK_CODE,
            $validationResult,
            'Should validate as bank code error.'
        );
    }
}
