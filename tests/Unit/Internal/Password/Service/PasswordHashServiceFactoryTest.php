<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashArgon2iService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceFactory;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceOptionsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;
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
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Password\Exception\UnavailablePasswordHashAlgorithm::class);

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
        $factory->addPasswordHashService('PASSWORD_BCRYPT', $this->getPasswordHashServiceBcrypt());

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashBcryptService::class, $service);
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
        $factory->addPasswordHashService('PASSWORD_ARGON2I', $this->getPasswordHashServiceArgon2i());

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashArgon2iService::class, $service);
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
        $factory->addPasswordHashService('PASSWORD_ARGON2ID', $this->getPasswordHashServiceArgon2id());

        $service = $factory->getPasswordHashService($algorithm);

        $this->assertInstanceOf(PasswordHashArgon2iService::class, $service);
    }

    /**
     * @return PasswordHashBcryptService
     */
    private function getPasswordHashServiceBcrypt(): PasswordHashBcryptService
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashServiceOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(['cost' => 4]);

        $passwordPolicyServiceMock = $this->getPasswordPolicyServiceMock();

        $passwordHashService = new PasswordHashBcryptService(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashArgon2iService
     */
    private function getPasswordHashServiceArgon2i(): PasswordHashArgon2iService
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashServiceOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyServiceMock = $this->getPasswordPolicyServiceMock();

        $passwordHashService = new PasswordHashArgon2iService(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashArgon2iService
     */
    private function getPasswordHashServiceArgon2id(): PasswordHashArgon2iService
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashServiceOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyServiceMock = $this->getPasswordPolicyServiceMock();
        $passwordHashService = new PasswordHashArgon2iService(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );

        return $passwordHashService;
    }

    /**
     * @return PasswordHashServiceOptionsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordHashServiceOptionProviderMock()
    {
        $passwordHashServiceOptionProviderMock = $this
            ->getMockBuilder(PasswordHashServiceOptionsProviderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOptions'])
            ->getMock();

        return $passwordHashServiceOptionProviderMock;
    }

    /**
     * @return PasswordPolicyServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordPolicyServiceMock()
    {
        $passwordPolicyServiceMock = $this
            ->getMockBuilder(PasswordPolicyServiceInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();

        return $passwordPolicyServiceMock;
    }
}
