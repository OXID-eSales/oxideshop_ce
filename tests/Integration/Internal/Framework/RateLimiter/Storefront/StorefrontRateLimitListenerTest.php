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

final class StorefrontRateLimitListenerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $_SERVER['REMOTE_ADDR'] = '203.0.113.' . random_int(1, 254);
        $_SERVER['REQUEST_URI'] = '/api/products';
    }

    public function testSecondJsonRequestWithinTheLimitWindowIsRejectedWithJson(): void
    {
        $this->assertSame(Response::HTTP_OK, $this->handleAsNewRequest()->getStatusCode());

        $response = $this->handleAsNewRequest();

        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertSame($this->tooManyRequestsProblem(), json_decode($response->getContent(), true));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertTrue($response->headers->has('Retry-After'));
    }

    public function testRoutedApiRequestWithoutDeclaredFormatIsLimited(): void
    {
        $this->assertSame('api-body', $this->runRoutedApiRequest());

        $this->assertSame($this->tooManyRequestsProblem(), json_decode($this->runRoutedApiRequest(), true));
    }

    private function handleAsNewRequest(): Response
    {
        $this->configureLimiter();
        $request = $this->get(Request::class);
        $request->attributes->set('_controller', static fn (): Response => new Response('ok'));
        $request->attributes->set('_format', 'json');

        return $this->get(HttpKernelInterface::class)->handle($request);
    }

    private function runRoutedApiRequest(): string
    {
        $this->configureLimiter();

        return $this->get(HttpKernelInterface::class)->handle($this->get(Request::class))->getContent();
    }

    private function tooManyRequestsProblem(): array
    {
        return [
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => 'An error occurred',
            'status' => 429,
            'detail' => 'Too Many Requests',
        ];
    }

    private function configureLimiter(): void
    {
        $this->createContainer();
        $this->loadYamlFixture(__DIR__ . '/Fixtures');
        $this->container->setParameter(
            'oxid_esales.rate_limiter.storefront.rules',
            [['id' => 'api', 'format' => 'json', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute']]
        );
        $this->compileContainer();
        $this->replaceContainerInstance();
    }
}
