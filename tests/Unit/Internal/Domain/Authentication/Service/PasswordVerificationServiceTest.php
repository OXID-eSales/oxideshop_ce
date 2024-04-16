<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Authentication\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\PasswordVerificationService;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordVerificationServiceTest
 */
final class PasswordVerificationServiceTest extends TestCase
{
    /**
     *
     */
    public function testPasswordVerificationVerifiesCorrectPassword(): void
    {
        $passwordUtf8 = 'äääää';
        $passwordHash = password_hash($passwordUtf8, PASSWORD_DEFAULT);

        $passwordVerificationService = $this->getPasswordVerificationService();

        $this->assertTrue(
            $passwordVerificationService->verifyPassword($passwordUtf8, $passwordHash)
        );
    }

    /**
     *
     */
    public function testPasswordVerificationDoesNotVerifyWrongPassword(): void
    {
        $passwordUtf8 = 'äääää';
        $passwordHash = password_hash($passwordUtf8, PASSWORD_DEFAULT);

        $passwordVerificationService = $this->getPasswordVerificationService();

        $this->assertFalse(
            $passwordVerificationService->verifyPassword('WRONG_PASSWORD', $passwordHash)
        );
    }

    private function getPasswordVerificationService(): PasswordVerificationService
    {
        $passwordPolicyMock = $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->onlyMethods(['enforcePasswordPolicy'])
            ->getMock();

        return new PasswordVerificationService($passwordPolicyMock);
    }
}
