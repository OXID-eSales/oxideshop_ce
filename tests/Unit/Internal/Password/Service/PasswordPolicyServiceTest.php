<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordPolicyException;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyService;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordVerificationServiceTest
 */
class PasswordPolicyServiceTest extends TestCase
{
    /**
     *
     */
    public function testPasswordPolicyAcceptsUtf8EncodedStrings()
    {
        $passwordUtf8 = 'äääää';

        $passwordPolicyService = new PasswordPolicyService();
        $passwordPolicyService->enforcePasswordPolicy($passwordUtf8);
    }

    /**
     * @dataProvider unsupportedEncodingDataProvider
     *
     * @param string $unsupportedEncoding
     *
     * @throws PasswordPolicyException
     */
    public function testPasswordPolicyRejectsStringNonUtf8Encoding(string $unsupportedEncoding)
    {
        $this->expectException(PasswordPolicyException::class);
        $this->expectExceptionMessage('The password policy requires UTF-8 encoded strings');

        $passwordUtf8 = 'äääää';
        $passwordIso = mb_convert_encoding($passwordUtf8, $unsupportedEncoding);

        $passwordPolicyService = new PasswordPolicyService();
        $passwordPolicyService->enforcePasswordPolicy($passwordIso);
    }

    /**
     * @return array
     */
    public function unsupportedEncodingDataProvider(): array
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
