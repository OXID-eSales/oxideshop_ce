<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashArgon2IStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashBcryptStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyOptionsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceFactory;
use OxidEsales\EshopCommunity\Internal\Password\Policy\PasswordPolicyInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class PasswordHashServiceFactoryTest extends TestCase
{
    /**
     */
    public function testGetPasswordHashServiceThrowsExceptionOnNonSupportedAlgorithm()
    {
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashStrategy::class);

        $algorithm = '1234';
        $factory = new PasswordHashServiceFactory();

        $factory->getPasswordHashService($algorithm);
    }

    /**
     */
    public function testGetPasswordHashServiceReturnsInstanceOfBcryptService()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }
        $algorithm = 'PASSWORD_BCRYPT';
        $factory = new PasswordHashServiceFactory();
        $factory->addPasswordHashStrategy('PASSWORD_BCRYPT', $this->getPasswordHashServiceBcrypt());

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashBcryptStrategy::class, $service);
    }

    /**
     */
    public function testGetPasswordHashServiceReturnsInstanceOfArgon2iService()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }
        $algorithm = 'PASSWORD_ARGON2I';
        $factory = new PasswordHashServiceFactory();
        $factory->addPasswordHashStrategy('PASSWORD_ARGON2I', $this->getPasswordHashServiceArgon2i());

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashArgon2IStrategy::class, $service);
    }

    /**
     */
    public function testGetPasswordHashServiceReturnsInstanceOfArgon2idService()
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }
        $algorithm = 'PASSWORD_ARGON2ID';
        $factory = new PasswordHashServiceFactory();
        $factory->addPasswordHashStrategy('PASSWORD_ARGON2ID', $this->getPasswordHashServiceArgon2id());

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashArgon2IStrategy::class, $service);
    }

    /**
     * @return PasswordHashBcryptStrategy
     */
    private function getPasswordHashServiceBcrypt(): PasswordHashBcryptStrategy
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashServiceOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(['cost' => 4]);

        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new PasswordHashBcryptStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashArgon2IStrategy
     */
    private function getPasswordHashServiceArgon2i(): PasswordHashArgon2IStrategy
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashServiceOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new PasswordHashArgon2IStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashArgon2IStrategy
     */
    private function getPasswordHashServiceArgon2id(): PasswordHashArgon2IStrategy
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashServiceOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyMock = $this->getPasswordPolicyMock();
        $passwordHashService = new PasswordHashArgon2IStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashStrategyOptionsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordHashServiceOptionProviderMock()
    {
        $passwordHashServiceOptionProviderMock = $this
            ->getMockBuilder(PasswordHashStrategyOptionsProviderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOptions'])
            ->getMock();

        return $passwordHashServiceOptionProviderMock;
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
