<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Core\Registry;
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
     * End-to-end test for the PasswordService bridge
     */
    public function testHashWithBcrypt()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret', 'PASSWORD_BCRYPT');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     * End-to-end test for the PasswordService bridge
     */
    public function testHashWithArgon2i()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret', 'PASSWORD_ARGON2I');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2I, $info['algo']);
    }


    /**
     * End-to-end test for the PasswordService bridge
     */
    public function testHashWithArgon2id()
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret', 'PASSWORD_ARGON2ID');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_ARGON2ID, $info['algo']);
    }

    /**
     * End-to-end test for bcrypt settings in Config
     */
    public function testConfigSettingsForBcrypt()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }

        $expectedCost = 7;
        Registry::getConfig()->setConfigParam('passwordHashingBcryptCost', $expectedCost);

        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret', 'PASSWORD_BCRYPT');
        $info = password_get_info($hash);

        $this->assertSame($expectedCost, $info['options']['cost']);
    }

    /**
     * End-to-end test for argon2 settings in Config.
     */
    public function testConfigSettingsForArgon2i()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }

        $config = Registry::getConfig();
        $expectedMemoryCost = 512;
        $expectedTimeCost = 1;
        $expectedThreads = 1;

        $config->setConfigParam('passwordHashingArgon2MemoryCost', $expectedMemoryCost);
        $config->setConfigParam('passwordHashingArgon2TimeCost', $expectedTimeCost);
        $config->setConfigParam('passwordHashingArgon2Threads', $expectedThreads);

        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret', 'PASSWORD_ARGON2I');
        $info = password_get_info($hash);

        $this->assertSame($expectedMemoryCost, $info['options']['memory_cost']);
        $this->assertSame($expectedTimeCost, $info['options']['time_cost']);
        $this->assertSame($expectedThreads, $info['options']['threads']);
    }

    /**
     * End-to-end test for argon2 settings in Config.
     */
    public function testConfigSettingsForArgon2id()
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }

        $config = Registry::getConfig();
        $expectedMemoryCost = 512;
        $expectedTimeCost = 1;
        $expectedThreads = 1;

        $config->setConfigParam('passwordHashingArgon2MemoryCost', $expectedMemoryCost);
        $config->setConfigParam('passwordHashingArgon2TimeCost', $expectedTimeCost);
        $config->setConfigParam('passwordHashingArgon2Threads', $expectedThreads);

        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret', 'PASSWORD_ARGON2ID');
        $info = password_get_info($hash);

        $this->assertSame($expectedMemoryCost, $info['options']['memory_cost']);
        $this->assertSame($expectedTimeCost, $info['options']['time_cost']);
        $this->assertSame($expectedThreads, $info['options']['threads']);
    }

    /**
     * End-to-end test for the password verification service.
     */
    public function testVerifyPasswordWithBcrypt()
    {
        if (!defined('PASSWORD_BCRYPT')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_BCRYPT" is not available');
        }

        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);

        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->assertTrue(
            $passwordServiceBridge->verifyPassword($password, $passwordHash)
        );
    }

    /**
     * End-to-end test for the password verification service.
     */
    public function testVerifyPasswordWithArgon2i()
    {
        if (!defined('PASSWORD_ARGON2I')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2I" is not available');
        }

        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);

        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_ARGON2I);

        $this->assertTrue(
            $passwordServiceBridge->verifyPassword($password, $passwordHash)
        );
    }

    /**
     * End-to-end test for the password verification service.
     */
    public function testVerifyPasswordWithWithArgon2id()
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            $this->markTestSkipped('The password hashing algorithm "PASSWORD_ARGON2ID" is not available');
        }

        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);

        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        $this->assertTrue(
            $passwordServiceBridge->verifyPassword($password, $passwordHash)
        );
    }
}
