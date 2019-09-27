<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Authentication\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy\PasswordPolicyInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Service\PasswordVerificationService;
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
        $passwordPolicyMock = $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();
        $passwordVerificationService = new PasswordVerificationService($passwordPolicyMock);

        return $passwordVerificationService;
    }
}
