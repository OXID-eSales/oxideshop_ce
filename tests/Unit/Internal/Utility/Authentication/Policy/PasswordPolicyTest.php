<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Authentication\Policy;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Exception\PasswordPolicyException;
use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicy;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordVerificationServiceTest
 */
class PasswordPolicyTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testPasswordPolicyAcceptsUtf8EncodedStrings()
    {
        $passwordUtf8 = 'äääää';

        $passwordPolicy = new PasswordPolicy();
        $passwordPolicy->enforcePasswordPolicy($passwordUtf8);
    }

    /**
     * @dataProvider unsupportedEncodingDataProvider
     * @throws PasswordPolicyException
     */
    public function testPasswordPolicyRejectsStringNonUtf8Encoding(string $unsupportedEncoding)
    {
        $this->expectException(PasswordPolicyException::class);
        $this->expectExceptionMessage('The password policy requires UTF-8 encoded strings');

        $passwordUtf8 = 'äääää';
        $passwordIso = mb_convert_encoding($passwordUtf8, $unsupportedEncoding);

        $passwordPolicy = new PasswordPolicy();
        $passwordPolicy->enforcePasswordPolicy($passwordIso);
    }

    public static function unsupportedEncodingDataProvider(): array
    {
        return
            [
                ['UTF-32'],
                ['UTF-32BE'],
                ['UTF-32LE'],
                ['UTF-16'],
                ['UTF-16BE'],
                ['UTF-16LE'],
                ['ISO-8859-1'],
                ['ISO-8859-15'],
                ['Windows-1252']
            ];
    }
}
