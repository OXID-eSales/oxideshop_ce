<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Utility\Hash\Service;

use OxidEsales\EshopCommunity\Internal\Utility\Hash\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\BcryptPasswordHashService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Policy\PasswordPolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class BcryptPasswordHashServiceTest extends TestCase
{
    public function testHashForGivenPasswordIsEncryptedWithProperAlgorithm()
    {
        $password = 'secret';
        $passwordHashService = $this->getPasswordHashServiceMock();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    public function testHashForEmptyPasswordIsEncryptedWithProperAlgorithm()
    {
        $password = '';

        $passwordHashService = $this->getPasswordHashServiceMock();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes()
    {
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashServiceMock();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }

    public function testPasswordNeedsRehashReturnsTrueOnChangedParameters()
    {
        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => 4 + 1]);

        $passwordHashService = $this->getPasswordHashServiceMock();

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash));
    }

    public function testPasswordNeedsRehashReturnsTrueOnUnknownHash()
    {
        $passwordHash = 'some_unrecognizable_custom_hash';

        $passwordHashService = $this->getPasswordHashServiceMock();

        $this->assertTrue($passwordHashService->passwordNeedsRehash($passwordHash));
    }

    public function testPasswordNeedsRehashReturnsFalseOnSameAlgorithmAndOptions()
    {
        $passwordHash = password_hash('secret', PASSWORD_BCRYPT, ['cost' => 4]);

        $passwordHashService = $this->getPasswordHashServiceMock();

        $this->assertFalse($passwordHashService->passwordNeedsRehash($passwordHash));
    }

    public function testHashWithWithValidCostOptionValue()
    {
        $passwordHashService = $this->getPasswordHashServiceMock(4);

        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(4, $info['options']['cost']);
    }

    /**
     * @dataProvider invalidCostOptionDataProvider
     *
     * @param array $invalidCostOption
     */
    public function testHashWithInvalidCostOptionValueThrowsPasswordHashException($invalidCostOption)
    {
        $this->expectException(PasswordHashException::class);

        $this->getPasswordHashServiceMock($invalidCostOption);
        $bcryptPasswordHashService->hash('secret');
    }

    /**
     * @return array
     */
    public function invalidCostOptionDataProvider(): array
    {
        return [
            [-5],
            [0],
            [3], // Cost must not be smaller than 4
            [32] // Cost must not be bigger than 31
        ];
    }

    /**
     * @param int $cost
     *
     * @return PasswordHashServiceInterface
     */
    private function getPasswordHashServiceMock(int $cost = 4): PasswordHashServiceInterface
    {
        $passwordPolicy = $this->getPasswordPolicyMock();

        $passwordHashService = new BcryptPasswordHashService(
            $passwordPolicy,
            $cost
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordPolicyInterface
     */
    private function getPasswordPolicyMock(): PasswordPolicyInterface
    {
        return $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();
    }
}
