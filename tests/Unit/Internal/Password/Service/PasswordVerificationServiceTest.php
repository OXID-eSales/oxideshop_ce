<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordVerificationService;
use PHPUnit\Framework\TestCase;

/**
 * Class PasswordVerificationServiceTest
 */
class PasswordVerificationServiceTest extends TestCase
{
    /**
     *
     */
    public function testPasswordVerificationVerifiesCorrectPassword()
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
    public function testPasswordVerificationDoesNotVerifyWrongPassword()
    {
        $passwordUtf8 = 'äääää';
        $passwordHash = password_hash($passwordUtf8, PASSWORD_DEFAULT);

        $passwordVerificationService = $this->getPasswordVerificationService();

        $this->assertFalse(
            $passwordVerificationService->verifyPassword('WRONG_PASSWORD', $passwordHash)
        );
    }

    /**
     * @return PasswordVerificationService
     */
    private function getPasswordVerificationService(): PasswordVerificationService
    {
        $passwordPolicyServiceMock = $this
            ->getMockBuilder(PasswordPolicyServiceInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();
        $passwordVerificationService = new PasswordVerificationService($passwordPolicyServiceMock);

        return $passwordVerificationService;
    }
}
