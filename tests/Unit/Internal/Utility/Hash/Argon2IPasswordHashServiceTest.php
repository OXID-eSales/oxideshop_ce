<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Hash\Service;

use PHPUnit\Framework\MockObject\MockObject;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\Argon2IPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class Argon2IPasswordHashServiceTest
 */
final class Argon2IPasswordHashServiceTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped("Argon2I is not available on this system.");
        }
    }

    public function testHashForGivenPasswordIsEncryptedWithProperAlgorithm(): void
    {
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }

    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm(): void
    {
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash('');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }

    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes(): void
    {
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashService();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }

    public function testPasswordNeedsRehashReturnsTrueOnChangedAlgorithm(): void
    {
        $passwordHashedWithOriginalAlgorithm = password_hash('secret', PASSWORD_BCRYPT);
        $passwordHashService = $this->getPasswordHashService();

        $this->assertTrue(
            $passwordHashService->passwordNeedsRehash($passwordHashedWithOriginalAlgorithm)
        );
    }

    public function testHashWithValidCostOption(): void
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

    private function getPasswordHashService(): PasswordHashServiceInterface
    {
        $passwordPolicyMock = $this->getPasswordPolicyMock();

        return new Argon2IPasswordHashService(
            $passwordPolicyMock,
            PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            PASSWORD_ARGON2_DEFAULT_TIME_COST,
            PASSWORD_ARGON2_DEFAULT_THREADS
        );
    }

    /**
     * @return PasswordPolicyInterface|MockObject
     */
    private function getPasswordPolicyMock(): PasswordPolicyInterface
    {
        return $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->onlyMethods(['enforcePasswordPolicy'])
            ->getMock();
    }
}
