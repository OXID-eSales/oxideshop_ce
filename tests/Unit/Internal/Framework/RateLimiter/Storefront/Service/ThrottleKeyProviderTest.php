<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\ThrottleKeyProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ThrottleKeyProviderTest extends TestCase
{
    public function testUserKeyHashesTheLoggedInUserId(): void
    {
        $key = $this->provider('user-x')->keyFor(['key' => 'user'], $this->request());

        $this->assertSame(hash('sha256', 'user-x'), $key);
    }

    public function testUserKeyFallsBackToHashedClientIpForAnonymousUser(): void
    {
        $key = $this->provider()->keyFor(['key' => 'user'], $this->request());

        $this->assertSame(hash('sha256', '203.0.113.10'), $key);
    }

    public function testUserKeyFallsBackToUnknownWithoutUserAndClientIp(): void
    {
        $key = $this->provider()->keyFor(['key' => 'user'], new Request());

        $this->assertSame(hash('sha256', 'unknown'), $key);
    }

    public function testIpKeyHashesTheClientIp(): void
    {
        $key = $this->provider()->keyFor(['key' => 'ip'], $this->request());

        $this->assertSame(hash('sha256', '203.0.113.10'), $key);
    }

    public function testEmailKeyCombinesNormalizedEmailAndClientIp(): void
    {
        $key = $this->provider()->keyFor(
            ['key' => 'email'],
            $this->request(['lgn_usr' => ' Shopper@Example.com '])
        );

        $this->assertSame(hash('sha256', 'shopper@example.com-203.0.113.10'), $key);
    }

    public function testEmailKeyUsesPostValueOverQueryDecoy(): void
    {
        $request = Request::create(
            '/?lgn_usr=decoy@example.com',
            'POST',
            ['lgn_usr' => 'victim@example.com'],
            [],
            [],
            ['REMOTE_ADDR' => '203.0.113.10']
        );

        $key = $this->provider()->keyFor(['key' => 'email'], $request);

        $this->assertSame(hash('sha256', 'victim@example.com-203.0.113.10'), $key);
    }

    public function testEmailKeyUsesPostValueDespiteEmptyQueryDecoy(): void
    {
        $request = Request::create(
            '/?lgn_usr=',
            'POST',
            ['lgn_usr' => 'person@example.com'],
            [],
            [],
            ['REMOTE_ADDR' => '203.0.113.10']
        );

        $key = $this->provider()->keyFor(['key' => 'email'], $request);

        $this->assertSame(hash('sha256', 'person@example.com-203.0.113.10'), $key);
    }

    public function testEmailKeyIsEmptyForArrayParameter(): void
    {
        $request = Request::create('/', 'GET', ['lgn_usr' => ['x@example.com']]);

        $this->assertSame('', $this->provider()->keyFor(['key' => 'email'], $request));
    }

    public function testEmailKeyIsEmptyWithoutLoginUser(): void
    {
        $key = $this->provider()->keyFor(['key' => 'email'], $this->request());

        $this->assertSame('', $key);
    }

    private function provider(string $userId = ''): ThrottleKeyProvider
    {
        $session = $this->createStub(SessionInterface::class);
        $session->method('get')->willReturnMap([['usr', '', $userId]]);

        return new ThrottleKeyProvider($session);
    }

    /**
     * @param array<string, string> $query
     */
    private function request(array $query = []): Request
    {
        return Request::create('/', 'GET', $query, [], [], ['REMOTE_ADDR' => '203.0.113.10']);
    }
}
