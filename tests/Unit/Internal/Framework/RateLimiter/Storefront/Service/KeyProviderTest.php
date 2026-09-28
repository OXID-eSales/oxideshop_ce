<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);
namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\KeyProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Request\RequestInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class KeyProviderTest extends TestCase
{
    public function testUserKeyHashesTheLoggedInUserId(): void
    {
        $key = $this->createProvider('user-x')->get(['key' => 'user'], $this->createRequest());

        $this->assertSame(hash('sha256', 'user-x'), $key);
    }

    public function testUserKeyFallsBackToHashedClientIpForAnonymousUser(): void
    {
        $key = $this->createProvider()->get(['key' => 'user'], $this->createRequest());

        $this->assertSame(hash('sha256', '203.0.113.10'), $key);
    }

    public function testUserKeyFallsBackToUnknownWithoutUserAndClientIp(): void
    {
        $key = $this->createProvider()->get(['key' => 'user'], new Request());

        $this->assertSame(hash('sha256', 'unknown'), $key);
    }

    public function testIpKeyHashesTheClientIp(): void
    {
        $key = $this->createProvider()->get(['key' => 'ip'], $this->createRequest());

        $this->assertSame(hash('sha256', '203.0.113.10'), $key);
    }

    public function testEmailKeyCombinesNormalizedEmailAndClientIp(): void
    {
        $provider = $this->createProvider('', ['lgn_usr' => ' Shopper@Example.com ']);

        $key = $provider->get(['key' => 'email'], $this->createRequest());

        $this->assertSame(hash('sha256', 'shopper@example.com-203.0.113.10'), $key);
    }

    public function testEmailKeyIsEmptyWithoutLoginUser(): void
    {
        $key = $this->createProvider()->get(['key' => 'email'], $this->createRequest());

        $this->assertSame('', $key);
    }

    public function testEmailKeyIsEmptyForNonScalarLoginUser(): void
    {
        $provider = $this->createProvider('', ['lgn_usr' => ['shopper@example.com']]);

        $key = $provider->get(['key' => 'email'], $this->createRequest());

        $this->assertSame('', $key);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function createProvider(string $userId = '', array $parameters = []): KeyProvider
    {
        $session = $this->createStub(SessionInterface::class);
        $session->method('get')->willReturnMap([['usr', '', $userId]]);

        $request = $this->createStub(RequestInterface::class);
        $request->method('get')->willReturnCallback(
            fn (string $name, mixed $default = null): mixed => $parameters[$name] ?? $default,
        );

        return new KeyProvider($session, $request);
    }

    private function createRequest(): Request
    {
        return Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '203.0.113.10']);
    }
}
