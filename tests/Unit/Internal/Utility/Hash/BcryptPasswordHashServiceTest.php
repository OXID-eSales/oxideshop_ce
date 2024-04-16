<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Hash\Service;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\BcryptPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Authentication\Policy\PasswordPolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 */
final class BcryptPasswordHashServiceTest extends TestCase
{
    public function testHashForGivenPasswordIsEncryptedWithProperAlgorithm(): void
    {
        $password = 'secret';
        $passwordHashService = $this->getPasswordHashServiceMock();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm(): void
    {
        $password = '';

        $passwordHashService = $this->getPasswordHashServiceMock();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes(): void
    {
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashServiceMock();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }

    public function testPasswordNeedsRehashReturnsTrueOnChangedParameters(): void
    {
        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => 4 + 1]);

        $passwordHashService = $this->getPasswordHashServiceMock();

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash));
    }

    public function testPasswordNeedsRehashReturnsTrueOnUnknownHash(): void
    {
        $passwordHash = 'some_unrecognizable_custom_hash';

        $passwordHashService = $this->getPasswordHashServiceMock();

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash));
    }

    public function testPasswordNeedsRehashReturnsFalseOnSameAlgorithmAndOptions(): void
    {
        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => 4]);

        $passwordHashService = $this->getPasswordHashServiceMock();

        $this->assertFalse($passwordHashService->passwordNeedsRehash($passwordHash));
    }

    public function testHashWithWithValidCostOptionValue(): void
    {
        $passwordHashService = $this->getPasswordHashServiceMock(4);

        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(4, $info['options']['cost']);
    }

    /**
     * @param array $invalidCostOption
     */
    #[DataProvider('invalidCostOptionDataProvider')]
    public function testHashWithInvalidCostOptionValueThrowsPasswordHashException(int $invalidCostOption): void
    {
        $this->expectException(PasswordHashException::class);

        $this->getPasswordHashServiceMock($invalidCostOption);
        $bcryptPasswordHashService->hash('secret');
    }

    public static function invalidCostOptionDataProvider(): array
    {
        return [
            [-5],
            [0],
            [3], // Cost must not be smaller than 4
            [32] // Cost must not be bigger than 31
        ];
    }


    private function getPasswordHashServiceMock(int $cost = 4): PasswordHashServiceInterface
    {
        $passwordPolicy = $this->getPasswordPolicyMock();

        return new BcryptPasswordHashService(
            $passwordPolicy,
            $cost
        );
    }

    private function getPasswordPolicyMock(): PasswordPolicyInterface
    {
        return $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->onlyMethods(['enforcePasswordPolicy'])
            ->getMock();
    }
}
