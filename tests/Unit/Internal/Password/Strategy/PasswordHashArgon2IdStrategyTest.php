<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashArgon2IdStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyOptionsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Password\Policy\PasswordPolicyInterface;

/**
 * Class PasswordHashArgon2IdStrategyTest
 */
class PasswordHashArgon2IdStrategyTest extends AbstractPasswordHashStrategyTest
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
     * @return PasswordHashStrategyInterface
     */
    protected function getPasswordHashStrategyImplementation(): PasswordHashStrategyInterface
    {
        $passwordHashStrategyOptionProviderMock = $this->getPasswordHashStrategyOptionProviderMock();
        $passwordHashStrategyOptionProviderMock->method('getOptions')->willReturn(
            [
                'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
                'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
                'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS
            ]
        );

        $passwordPolicyMock = $this->getPasswordPolicyMock();

        $passwordHashService = new PasswordHashArgon2IdStrategy(
            $passwordHashStrategyOptionProviderMock,
            $passwordPolicyMock
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
