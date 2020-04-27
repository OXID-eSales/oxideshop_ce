<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Email;

use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorService;
use PHPUnit\Framework\TestCase;

/**
 * Class EmailValidatorServiceTest
 *
 * @covers \OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorService
 */
class EmailValidatorServiceTest extends TestCase
{
    public function providerEmailsToValidate()
    {
        return [
            ['mathias.krieck@oxid-esales.com', true],
            ['mytest@com.org', true],
            ['my+test@com.org', true],
            ['mytest@oxid-esales.museum', true],
            ['?mathias.krieck@oxid-esales.com', true],
            ['my/test@com.org', true],
            ['mytest@-com.org', false],
            ['@com.org', false],
            ['mytestcom.org', false],
            ['foo.bar@-.-,-,-.oxid-esales.com', false],
            ['mytest@com', false],
            ['info@�vyturys.lt', false],
        ];
    }

    /**
     * @dataProvider providerEmailsToValidate
     */
    public function testValidateEmailWithValidEmail(string $email, bool $validMail): void
    {
        $mailValidator = new EmailValidatorService();
        $result = $mailValidator->isEmailValid($email);
        if ($validMail) {
            $this->assertTrue(
                $result,
                'Mail ' . $email . ' validation failed. This mail is valid so should validate.'
            );
        } else {
            $this->assertFalse(
                $result,
                'Mail ' . $email . ' was valid. Should not be valid.'
            );
        }
    }
}
