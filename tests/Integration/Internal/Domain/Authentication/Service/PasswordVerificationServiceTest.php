<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Exception\PasswordPolicyException;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\PasswordVerificationServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordVerificationServiceTest
 */
final class PasswordVerificationServiceTest extends TestCase
{
    use ContainerTrait;

    /**
     * End-to-end test to ensure, that the password policy checking is called during password verification
     */
    public function testverifyPasswordHashEnforcesPasswordPolicy(): void
    {
        $this->expectException(PasswordPolicyException::class);

        $passwordUtf8 = 'äääääää';
        $passwordIso = mb_convert_encoding($passwordUtf8, 'ISO-8859-15');

        $passwordHash = password_hash($passwordIso, PASSWORD_DEFAULT);

        $passwordVerificationService = $this->get(PasswordVerificationServiceInterface::class);
        $passwordVerificationService->verifyPassword($passwordIso, $passwordHash);
    }
}
