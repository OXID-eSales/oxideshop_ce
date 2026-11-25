<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class StorefrontRateLimitListenerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->container->register('oxid_esales.rate_limiter.storefront.storage', InMemoryStorage::class);
        $this->container->setParameter('oxid_esales.debug_mode', false);
        $this->container->setParameter(
            'oxid_esales.rate_limiter.storefront.rules',
            [['id' => 'api', 'format' => 'json', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute']]
        );
        $this->compileContainer();
        $this->replaceContainerInstance();
    }

    public function testRejectsSecondJsonRequestWithinTheLimitWindowWithJson(): void
    {
        $this->handle($this->createJsonRequest());

        $response = $this->handle($this->createJsonRequest());

        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testRejectedResponseCarriesRateLimitHeaders(): void
    {
        $this->handle($this->createJsonRequest());

        $response = $this->handle($this->createJsonRequest());

        $this->assertSame('1', $response->headers->get('X-RateLimit-Limit'));
        $this->assertSame('0', $response->headers->get('X-RateLimit-Remaining'));
        $this->assertTrue($response->headers->has('X-RateLimit-Reset'));
        $this->assertTrue($response->headers->has('Retry-After'));
    }

    public function testLimitsRoutedApiRequestWithoutDeclaredFormat(): void
    {
        $this->handle($this->createApiRequest());

        $response = $this->handle($this->createApiRequest());

        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
    }

    private function handle(Request $request): Response
    {
        return $this->get(HttpKernelInterface::class)->handle($request);
    }

    private function createJsonRequest(): Request
    {
        $request = $this->createApiRequest();
        $request->attributes->set('_controller', static fn(): Response => new Response('ok'));
        $request->attributes->set('_format', 'json');

        return $request;
    }

    private function createApiRequest(): Request
    {
        return Request::create('/api/products', server: ['REMOTE_ADDR' => '203.0.113.10']);
    }
}
