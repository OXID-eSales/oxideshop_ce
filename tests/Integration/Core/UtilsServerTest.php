<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\UtilsServer;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class UtilsServerTest extends TestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    public function testSetUserCookieWillCallPasswordHashing(): void
    {
        $passwordService = $this->prophesize(PasswordServiceBridgeInterface::class);
        $password = 'some-password';
        $passwordService
            ->hash(Argument::containingString($password))
            ->willReturn('some-hash');
        $this->createContainer();
        $this->replaceService(PasswordServiceBridgeInterface::class, $passwordService->reveal());
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $utilsServer = oxNew(UtilsServer::class);
        $utilsServer->setUserCookie('some-user-name', $password);

        $passwordService->hash($password . User::USER_COOKIE_SALT)->shouldHaveBeenCalledOnce();
    }

    public function testIsTrustedServerWithDefaultConfig(): void
    {
        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertFalse($isTrusted);
    }

    public function testIsTrustedServerWithConfiguredIp(): void
    {
        $someIp = '255.255.255.255';
        $_SERVER['HTTP_CLIENT_IP'] = $someIp;
        $this->createContainer();
        $this->container->setParameter('oxid_trusted_ips', [$someIp]);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertTrue($isTrusted);
    }

    public function testIsTrustedServerWithNonTrustedIp(): void
    {
        $someIp = '255.255.255.255';
        $_SERVER['HTTP_CLIENT_IP'] = $someIp;
        $this->createContainer();
        $this->container->setParameter('oxid_trusted_ips', ['1.2.3.4', '5.6.7.8']);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $isTrusted = oxNew(UtilsServer::class)->isTrustedClientIp();

        $this->assertFalse($isTrusted);
    }
}
