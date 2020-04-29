<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

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
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $hash = $passwordServiceBridge->hash('secret');
        $info = password_get_info($hash);

        $this->assertSame(PASSWORD_BCRYPT, $info['algo']);
    }

    /**
     * End-to-end test for the password verification service.
     */
    public function testVerifyPassword()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);

        $password = 'secret';
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->assertTrue(
            $passwordServiceBridge->verifyPassword($password, $passwordHash)
        );
    }

    public function testPasswordNeedsRehash()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);

        $container = $this->getContainer();
        $cost = $container->getParameter('oxid_esales.utility.hash.service.password_hash.bcrypt.cost');

        $passwordHashWithCostFromConfiguration = password_hash('secret', PASSWORD_BCRYPT, ['cost' => $cost]);
        $passwordHashWithCostChangedCost = password_hash('secret', PASSWORD_BCRYPT, ['cost' => $cost + 1]);

        $this->assertFalse(
            $passwordServiceBridge->passwordNeedsRehash($passwordHashWithCostFromConfiguration)
        );
        $this->assertTrue(
            $passwordServiceBridge->passwordNeedsRehash($passwordHashWithCostChangedCost)
        );
    }

    /**
     * @return ContainerInterface
     */
    private function getContainer(): ContainerInterface
    {
        $factory = ContainerFactory::getInstance();

        return $factory->getContainer();
    }
}
