<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontClientIdentifierProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class StorefrontClientIdentifierProviderTest extends TestCase
{
    public function testUsesLoggedInUserIdAsIdentifier(): void
    {
        $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => '203.0.113.5']);

        $identifier = (new StorefrontClientIdentifierProvider($this->session('user-x')))->getClientIdentifier($request);

        $this->assertSame('user-x', $identifier);
    }

    public function testFallsBackToClientIpForAnonymousUser(): void
    {
        $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => '203.0.113.5']);

        $identifier = (new StorefrontClientIdentifierProvider($this->session('')))->getClientIdentifier($request);

        $this->assertSame('203.0.113.5', $identifier);
    }

    public function testFallsBackToUnknownWhenNeitherUserNorIpIsAvailable(): void
    {
        $identifier = (new StorefrontClientIdentifierProvider($this->session('')))->getClientIdentifier(new Request());

        $this->assertSame('unknown', $identifier);
    }

    private function session(string $userId): SessionInterface
    {
        return new class ($userId) implements SessionInterface {
            public function __construct(private string $userId)
            {
            }

            public function get(string $name, mixed $default = null): mixed
            {
                return $name === 'usr' && $this->userId !== '' ? $this->userId : $default;
            }

            public function has(string $name): bool
            {
                return $name === 'usr' && $this->userId !== '';
            }

            public function set(string $name, mixed $value): void
            {
            }

            public function remove(string $name): mixed
            {
                return null;
            }
        };
    }
}
