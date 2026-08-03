<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class UtilsServerTest extends TestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        $utils = oxNew('oxutilsserver');
        if ($utils->getUserCookie()) {
            $utils->deleteUserCookie();
        }
        parent::tearDown();
    }

    public function testGetSetAndDeleteUserCookie(): void
    {
        $utils = oxNew('oxutilsserver');

        $this->assertNull($utils->getUserCookie());

        $utils->setUserCookie('admin', 'admin', null, 31536000, User::USER_COOKIE_SALT);

        $aData = explode('@@@', (string) $utils->getUserCookie());

        $this->assertTrue(
            $this->get(PasswordServiceBridgeInterface::class)->verifyPassword(
                'admin' . User::USER_COOKIE_SALT,
                $aData[1]
            )
        );

        $utils->deleteUserCookie();
        $this->assertNull($utils->getUserCookie());
    }

    #[RunInSeparateProcess]
    public function testGetRemoteAddressRespectsXForwardedForWithTrustedProxy(): void
    {
        $proxyIp = '1.2.3.4';
        $clientIp = '9.9.9.9';

        $_SERVER['REMOTE_ADDR'] = $proxyIp;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $clientIp;

        $this->createContainer();
        $this->setParameter('oxid_esales.request.trusted_proxies', [$proxyIp]);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $this->assertSame($clientIp, oxNew('oxutilsserver')->getRemoteAddress());
    }

    #[RunInSeparateProcess]
    public function testGetRemoteAddressIgnoresRfc7239ForwardedHeader(): void
    {
        $proxyIp = '1.2.3.4';
        $clientIp = '9.9.9.9';

        $_SERVER['REMOTE_ADDR'] = $proxyIp;
        $_SERVER['HTTP_X_FORWARDED_FOR'] = $clientIp;
        $_SERVER['HTTP_FORWARDED'] = 'for=203.0.113.9';

        $this->createContainer();
        $this->setParameter('oxid_esales.request.trusted_proxies', [$proxyIp]);
        $this->compileContainer();
        $this->attachContainerToContainerFactory();

        $this->assertSame($clientIp, oxNew('oxutilsserver')->getRemoteAddress());
    }
}
