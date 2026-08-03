<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Request;

use OxidEsales\EshopCommunity\Internal\Framework\Request\HttpsRequestResolverInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

final class HttpsRequestResolverTest extends TestCase
{
    use ContainerTrait;

    private const TRUSTED_PROXY = '10.0.0.1';

    #[RunInSeparateProcess]
    public function testResolverReadsForwardedProtoFromConfiguredTrustedProxy(): void
    {
        $_SERVER['REMOTE_ADDR'] = self::TRUSTED_PROXY;
        $_SERVER['HTTP_FORWARDED'] = 'for=203.0.113.9;proto=https';

        $this->assertTrue($this->getResolver()->isHttps());
    }

    #[RunInSeparateProcess]
    public function testXForwardedProtoTakesPrecedenceOverForwardedHeader(): void
    {
        $_SERVER['REMOTE_ADDR'] = self::TRUSTED_PROXY;
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_FORWARDED'] = 'for=203.0.113.9;proto=http';

        $this->assertTrue($this->getResolver()->isHttps());
    }

    private function getResolver(): HttpsRequestResolverInterface
    {
        $this->createContainer();
        $this->setParameter('oxid_esales.request.trusted_proxies', [self::TRUSTED_PROXY]);
        $this->compileContainer();

        return $this->get(HttpsRequestResolverInterface::class);
    }
}
