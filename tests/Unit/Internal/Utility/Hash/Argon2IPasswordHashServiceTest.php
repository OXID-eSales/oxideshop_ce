<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\Argon2IPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy\PasswordPolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class Argon2IPasswordHashServiceTest
 */
class Argon2IPasswordHashServiceTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped("Argon2I is not available on this system.");
        }
    }

    public function testHashForGivenPasswordIsEncryptedWithProperAlgorithm()
    {
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }

    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm()
    {
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }

    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes()
    {
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashService();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }

    /**
     * Invalid values as a memory cost value of 2^32 + 1 can cause the method hash to fail.
     */
    public function testHashThrowsExceptionOnInvalidSettings()
    {
        $this->expectWarning(\PHPUnit\Framework\Error\Warning::class);
        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new Argon2IPasswordHashService(
            $passwordPolicyMock,
            1 << 32, // The value 2^32 is out of range and will produce a PHP Warning.
            PASSWORD_ARGON2_DEFAULT_TIME_COST,
            PASSWORD_ARGON2_DEFAULT_THREADS
        );

        $passwordHashService->hash('secret');
    }

    public function testPasswordNeedsRehashReturnsTrueOnChangedAlgorithm()
    {
        $passwordHashedWithOriginalAlgorithm = password_hash('secret', PASSWORD_BCRYPT);
        $passwordHashService = $this->getPasswordHashService();

        $this->assertTrue(
            $passwordHashService->passwordNeedsRehash($passwordHashedWithOriginalAlgorithm)
        );
    }

    public function testHashWithValidCostOption()
    {
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('secret');

        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
        $this->assertSame(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads' => PASSWORD_ARGON2_DEFAULT_THREADS
            ],
            $info['options']
        );
    }

    /**
     * @return PasswordHashServiceInterface
     */
    private function getPasswordHashService(): PasswordHashServiceInterface
    {
        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new Argon2IPasswordHashService(
            $passwordPolicyMock,
            PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            PASSWORD_ARGON2_DEFAULT_TIME_COST,
            PASSWORD_ARGON2_DEFAULT_THREADS
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordPolicyInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordPolicyMock(): PasswordPolicyInterface
    {
        $passwordPolicyMock = $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();

        return $passwordPolicyMock;
    }
}
