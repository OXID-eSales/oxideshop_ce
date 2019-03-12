<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Password\Service;

use OxidEsales\EshopCommunity\Internal\Password\Exception\PasswordPolicyException;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptServiceOptionsProvider;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordPolicyServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class PasswordHashBcryptServiceTest extends TestCase
{
    /**
     *
     */
    public function testHashForGivenPasswordIsEncryptedWithBcrypt()
    {
        $password = 'secret';
        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     *
     */
    public function testHashForEmptyPasswordIsEncryptedWithBcrypt()
    {
        $password = '';

        $passwordHashService = $this->getPasswordHashService();
        $hash = $passwordHashService->hash($password);
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     *
     */
    public function testConsecutiveHashingTheSamePasswordProducesDifferentHashes()
    {
        $password = 'secret';

        $passwordHashService = $this->getPasswordHashService();
        $hash_1 = $passwordHashService->hash($password);
        $hash_2 = $passwordHashService->hash($password);

        $this->assertNotSame($hash_1, $hash_2);
    }

    /**
     * @return PasswordHashBcryptService
     */
    private function getPasswordHashService(): PasswordHashBcryptService
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

        return $passwordHashService;
    }
}
