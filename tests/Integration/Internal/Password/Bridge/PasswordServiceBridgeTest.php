<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Password\Bridge;

use OxidEsales\EshopCommunity\Core\Hasher;
use OxidEsales\EshopCommunity\Internal\Password\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashBcryptService;
use OxidEsales\EshopCommunity\Internal\Password\Service\PasswordHashSha512Service;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class PasswordServiceBridgeTest extends TestCase
{
    use ContainerTrait;

    public function testGetPasswordHashServiceReturnsWorkingPasswordHashServiceSha512()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordHashService = $passwordServiceBridge->getPasswordHashService('sha512', []);
        $hash = $passwordHashService->hash('secret');

        $this->assertInstanceOf(Hasher::class, $passwordHashService);
        $this->assertNotEmpty($hash);
    }

    public function testGetPasswordHashServiceReturnsWorkingPasswordHashServiceBcrypt()
    {
        /** @var PasswordServiceBridgeInterface $passwordServiceBridge */
        $passwordServiceBridge = $this->get(PasswordServiceBridgeInterface::class);
        $passwordHashService = $passwordServiceBridge->getPasswordHashService('bcrypt', ['foo' => 'bar']);
        $hash = $passwordHashService->hash('secret');
        $info = password_get_info($hash);

        $this->assertInstanceOf(PasswordHashBcryptService::class, $passwordHashService);
        $this->assertSame('bcrypt', $info['algoName']);
    }
}
