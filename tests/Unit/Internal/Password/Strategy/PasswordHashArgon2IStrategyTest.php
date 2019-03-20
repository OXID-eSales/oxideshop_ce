<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordHashException;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashArgon2IStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyOptionsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;

/**
 * Class PasswordHashArgon2IStrategyTest
 */
class PasswordHashArgon2IStrategyTest extends AbstractPasswordHashStrategyTest
{
    /**
     *
     */
    protected function setUp()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }
        $this->hashingAlgorithm = PASSWORD_ARGON2I;
    }

    /**
     */
    public function testImplementationThrowsExceptionOnPasswordHashWarnings()
    {
        $this->expectException(PasswordHashException::class);

        $options = [
            'memory_cost' => 1 << 32, // The value 2^32 is out of range and will produce a PHP Warning.
            'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
        ];

        $passwordHashServiceOptionProviderMock = $this->getPasswordHashStrategyOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            $options
        );
        $passwordPolicyServiceMock = $this->getPasswordPolicyServiceMock();

        $passwordHashService = new PasswordHashArgon2IStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );
        $passwordHashService->initialize();

        $passwordHashService->hash('secret');
    }

    /**
     * @return PasswordHashStrategyInterface
     */
    protected function getPasswordHashStrategyImplementation(): PasswordHashStrategyInterface
    {
        $passwordHashServiceOptionProviderMock = $this->getPasswordHashStrategyOptionProviderMock();
        $passwordHashServiceOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyServiceMock = $this->getPasswordPolicyServiceMock();

        $passwordHashService = new PasswordHashArgon2IStrategy(
            $passwordHashServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );
        $passwordHashService->initialize();

        return $passwordHashService;
    }

    /**
     * @return PasswordHashStrategyOptionsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getPasswordHashStrategyOptionProviderMock()
    {
        $passwordHashStrategyOptionProviderMock = $this
            ->getMockBuilder(PasswordHashStrategyOptionsProviderInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOptions'])
            ->getMock();

        return $passwordHashStrategyOptionProviderMock;
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
