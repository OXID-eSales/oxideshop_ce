<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptServiceOptionsProvider;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashServiceInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;

/**
 *
 */
class PasswordHashBcryptServiceTest extends AbstractPasswordHashServiceTest
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
     * @return PasswordHashBcryptService
     */
    protected function getPasswordHashServiceImplementation(): PasswordHashServiceInterface
    {
        $passwordHashBcryptServiceOptionProviderMock = $this
            ->getMockBuilder(PasswordHashBcryptServiceOptionsProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['getOptions'])
            ->getMock();
        $passwordHashBcryptServiceOptionProviderMock->method('getOptions')->willReturn(['cost' => 4]);

        $passwordPolicyServiceMock = $this
            ->getMockBuilder(PasswordPolicyServiceInterface::class)
            ->setMethods(['enforcePasswordPolicy'])
            ->getMock();

        $passwordHashService = new PasswordHashBcryptService(
            $passwordHashBcryptServiceOptionProviderMock,
            $passwordPolicyServiceMock
        );
        $passwordHashService->initialize();

        return $passwordHashService;
    }
}
