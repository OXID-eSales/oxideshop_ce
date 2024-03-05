<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Service;

use PHPUnit\Framework\MockObject\MockObject;
use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Exception\PasswordPolicyException;
use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\Argon2IPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\BcryptPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class PasswordHashServiceTest extends TestCase
{
    use ContainerTrait;

    /**
     */
    public function testPasswordNeedsRehashReturnsTrueOnChangedAlgorithm(): void
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped(
                'The password hashing algorithms PASSWORD_ARGON2I and/or PASSWORD_ARGON2I are not available'
            );
        }

        $argon2iPasswordHashService = $this->getArgon2IPasswordHashService();
        $bcryptPasswordHashService = $this->getBcryptPasswordHashService();

        $bcryptHash = $bcryptPasswordHashService->hash('secret');
        $argon2iHash = $argon2iPasswordHashService->hash('secret');

        $this->assertFalse($bcryptPasswordHashService->passwordNeedsRehash($bcryptHash));
        $this->assertFalse($argon2iPasswordHashService->passwordNeedsRehash($argon2iHash));
        $this->assertTrue($bcryptPasswordHashService->passwordNeedsRehash($argon2iHash));
        $this->assertTrue($argon2iPasswordHashService->passwordNeedsRehash($bcryptHash));
    }

    /**
     * End-to-end test to ensure, that the password policy checking is called during password hashing
     */
    public function testPasswordHashServiceEnforcesPasswordPolicy(): void
    {
        $this->expectException(PasswordPolicyException::class);

        $passwordUtf8 = 'äääääää';
        $passwordIso = mb_convert_encoding($passwordUtf8, 'ISO-8859-15');

        $passwordHashService = $this->get(PasswordHashServiceInterface::class);
        $passwordHashService->hash($passwordIso);
    }

    private function getBcryptPasswordHashService(): PasswordHashServiceInterface
    {
        $passwordPolicy = $this->getPasswordPolicyMock();

        return new BcryptPasswordHashService(
            $passwordPolicy,
            4
        );
    }

    private function getArgon2IPasswordHashService(): PasswordHashServiceInterface
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
