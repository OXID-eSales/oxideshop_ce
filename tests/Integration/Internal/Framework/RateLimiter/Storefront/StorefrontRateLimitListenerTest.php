<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunner;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

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
        $this->assertSame('{"error":"Too Many Requests"}', $response->getContent());
    }

    public function testRoutedApiRequestWithoutDeclaredFormatIsLimited(): void
    {
        $this->assertSame('api-body', $this->runRoutedApiRequest());

        $this->assertSame('{"error":"Too Many Requests"}', $this->runRoutedApiRequest());
    }

    public static function apiController(): Response
    {
        return new Response('api-body');
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
        $routes = new RouteCollection();
        $routes->add('api', new Route('/api/products', ['_controller' => self::class . '::apiController']));
        $runner = new ShopRunner(
            $this->get(HttpKernelInterface::class),
            $this->get(Request::class),
            new CompiledUrlMatcherDumper($routes)->getCompiledRoutes()
        );

        ob_start();
        $runner->run(static fn(): Response => new Response('fallback-body'));

        return (string) ob_get_clean();
    }

    private function configureLimiter(): void
    {
        $this->createContainer();
        $this->container->setParameter(
            'oxid_esales.rate_limiter.storefront.rules',
            [['id' => 'api', 'format' => 'json', 'key' => 'ip', 'limit' => 1, 'interval' => '1 minute']]
        );
        $this->compileContainer();
        $this->replaceContainerInstance();
    }
}
