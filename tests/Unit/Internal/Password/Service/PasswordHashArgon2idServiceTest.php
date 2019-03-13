<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashArgon2idService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceOptionsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;

/**
 * Class PasswordHashArgon2idServiceTest
 */
class PasswordHashArgon2idServiceTest extends AbstractPasswordHashServiceTest
{
    /**
     *
     */
    protected function setUp()
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }
        $this->hashingAlgorithm = PASSWORD_ARGON2ID;
    }

    /**
     * @return PasswordHashServiceInterface
     */
    protected function getPasswordHashServiceImplementation(): PasswordHashServiceInterface
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

        $passwordHashService = new PasswordHashArgon2idService(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );
        $passwordHashService->initialize();

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
