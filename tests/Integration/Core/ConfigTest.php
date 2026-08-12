<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class ConfigTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const TRUSTED_PROXY = '10.0.0.1';

    public function testIsSslIsTrueBehindTrustedSslOffloader(): void
    {
        $_SERVER['REMOTE_ADDR'] = self::TRUSTED_PROXY;
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $this->bootContainerWithTrustedProxies([self::TRUSTED_PROXY]);

        $config = oxNew(Config::class);
        $config->setConfigParam('sSSLShopURL', 'https://shop.example.com/');

        $this->assertTrue($config->isSsl());
    }

    public function testIsSslIsFalseWhenForwardedProtoComesFromUntrustedSource(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $this->bootContainerWithTrustedProxies([self::TRUSTED_PROXY]);

        $config = oxNew(Config::class);
        $config->setConfigParam('sSSLShopURL', 'https://shop.example.com/');

        $this->assertFalse($config->isSsl());
    }

    private function bootContainerWithTrustedProxies(array $trustedProxies): void
    {
        $this->setParameter('oxid_esales.request.trusted_proxies', $trustedProxies);
    }
}
