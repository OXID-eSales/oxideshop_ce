<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);
namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\KeyProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
        $request = $this->createRequest(['lgn_usr' => ' Shopper@Example.com ']);

        $key = $this->createProvider()->get(['key' => 'email'], $request);

        $this->assertSame(hash('sha256', 'shopper@example.com-203.0.113.10'), $key);
    }

    public function testEmailKeyIsEmptyWithoutLoginUser(): void
    {
        $key = $this->createProvider()->get(['key' => 'email'], $this->createRequest());

        $this->assertSame('', $key);
    }

    public function testEmailKeyRejectsNonStringLoginName(): void
    {
        $request = $this->createRequest(['lgn_usr' => ['shopper@example.com']]);

        $this->expectException(BadRequestException::class);

        $this->createProvider()->get(['key' => 'email'], $request);
    }

    public function testEmailKeyIgnoresLoginNameInQuery(): void
    {
        $request = Request::create('/?lgn_usr=shopper@example.com', 'POST', [], [], [], ['REMOTE_ADDR' => '203.0.113.10']);

        $key = $this->createProvider()->get(['key' => 'email'], $request);

        $this->assertSame('', $key);
    }

    public function testEmailKeyDiffersByClientIpForTheSameLoginName(): void
    {
        $provider = $this->createProvider();
        $post = ['lgn_usr' => 'victim@example.com'];

        $keyA = $provider->get(['key' => 'email'], $this->createRequest($post, '203.0.113.10'));
        $keyB = $provider->get(['key' => 'email'], $this->createRequest($post, '198.51.100.7'));

        $this->assertNotSame($keyA, $keyB);
    }

    private function createProvider(string $userId = ''): KeyProvider
    {
        $session = $this->createStub(SessionInterface::class);
        $session->method('get')->willReturnMap([['usr', '', $userId]]);

        return new KeyProvider($session);
    }

    /**
     * @param array<string, mixed> $post
     */
    private function createRequest(array $post = [], string $clientIp = '203.0.113.10'): Request
    {
        return Request::create('/', 'POST', $post, [], [], ['REMOTE_ADDR' => $clientIp]);
    }
}
