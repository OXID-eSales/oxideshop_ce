<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Strategy;

use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashBcryptStrategy;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashBcryptStrategyOptionsProvider;
use OxidEsales\EshopCommunity\Internal\Password\Strategy\PasswordHashStrategyInterface;
use OxidEsales\EshopCommunity\Internal\Password\Policy\PasswordPolicyInterface;

/**
 *
 */
class PasswordHashBcryptStrategyTest extends AbstractPasswordHashStrategyTest
{
    /**
     *
     */
    protected function setUp()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }
        $this->hashingAlgorithm = PASSWORD_BCRYPT;
    }

    /**
     * @return PasswordHashBcryptStrategy
     */
    protected function getPasswordHashStrategyImplementation(): PasswordHashStrategyInterface
    {
        $passwordHashBcryptStrategyOptionProviderMock = $this
            ->getMockBuilder(PasswordHashBcryptStrategyOptionsProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOptions'])
            ->getMock();
        $passwordHashBcryptStrategyOptionProviderMock->method('getOptions')->willReturn(['cost' => 4]);

        $passwordPolicyMock = $this
            ->getMockBuilder(PasswordPolicyInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();

        $passwordHashService = new PasswordHashBcryptStrategy(
            $passwordHashBcryptStrategyOptionProviderMock,
            $passwordPolicyMock
        );
        $passwordHashService->initialize();

        return $passwordHashService;
    }
}
