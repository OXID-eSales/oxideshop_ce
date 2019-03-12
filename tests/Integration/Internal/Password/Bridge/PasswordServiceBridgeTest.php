<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Bridge;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Password\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class PasswordServiceBridgeTest extends TestCase
{
    use ContainerTrait;

    /**
     * End-to-end test for the password hashing service.
     */
    public function testGetPasswordHashServiceReturnsWorkingPasswordHashServiceBcrypt()
    {
        if (!defined('PASSWORD_BCRYPT') ||
            Registry::getConfig()->getConfigParam('passwordHashingAlgorithm', PASSWORD_DEFAULT) !== PASSWORD_BCRYPT
        ) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordHashService = $passwordServiceBridge->getPasswordHashService(PASSWORD_BCRYPT);
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     * End-to-end test for the password hashing service.
     */
    public function testGetPasswordHashServiceReturnsWorkingPasswordHashServiceArgon2i()
    {
        if (!defined('PASSWORD_ARGON2I') ||
            Registry::getConfig()->getConfigParam('passwordHashingAlgorithm', PASSWORD_DEFAULT) !== PASSWORD_ARGON2I
        ) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordHashService = $passwordServiceBridge->getPasswordHashService(PASSWORD_ARGON2I);
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }


    /**
     * End-to-end test for the password hashing service.
     */
    public function testGetPasswordHashServiceReturnsWorkingPasswordHashServiceArgon2id()
    {
        if (!defined('PASSWORD_ARGON2ID') ||
            Registry::getConfig()->getConfigParam('passwordHashingAlgorithm', PASSWORD_DEFAULT) !== PASSWORD_ARGON2ID
        ) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordHashService = $passwordServiceBridge->getPasswordHashService(PASSWORD_ARGON2ID);
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2ID, $info['algo']);
    }

    /**
     * End-to-end test for the password verification service.
     */
    public function testGetPasswordVerificationServiceReturnsWorkingService()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordVerificationService = $passwordServiceBridge->getPasswordVerificationService();

        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->assertTrue(
            $passwordVerificationService->verifyPassword($password, $passwordHash)
        );
    }
}
