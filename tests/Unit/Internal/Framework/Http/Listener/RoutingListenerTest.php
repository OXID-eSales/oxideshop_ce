<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\RoutingListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RoutingListenerTest extends TestCase
{
    public function testOnKernelRequestSetsRouteOfMatchingPath(): void
    {
        $request = Request::create('/routed/some-id');

        $this->dispatch($request);

        $this->assertSame('routed', $request->attributes->get('_route'));
        $this->assertSame('some-id', $request->attributes->get('id'));
    }

    public function testOnKernelRequestSetsNoFormatOutsideApi(): void
    {
        $request = Request::create('/routed/some-id');

        $this->dispatch($request);

        $this->assertFalse($request->attributes->has('_format'));
    }

    public function testOnKernelRequestDefaultsApiRouteFormatToJson(): void
    {
        $request = Request::create('/api/routed/some-id');

        $this->dispatch($request);

        $this->assertSame('json', $request->attributes->get('_format'));
    }

    public function testOnKernelRequestKeepsFormatDeclaredByApiRoute(): void
    {
        $request = Request::create('/api/xml/some-id');

        $this->dispatch($request);

        $this->assertSame('xml', $request->attributes->get('_format'));
    }

    public function testOnKernelRequestSetsFallbackControllerForUnroutedPath(): void
    {
        $fallbackController = static fn(): Response => new Response();
        $request = Request::create('/unrouted/path');
        $request->attributes->set('_fallback_controller', $fallbackController);

        $this->dispatch($request);

        $this->assertSame($fallbackController, $request->attributes->get('_controller'));
    }

    public function testOnKernelRequestRejectsUnroutedPathWithoutFallbackController(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->dispatch(Request::create('/unrouted/path'));
    }

    public function testOnKernelRequestRejectsUnknownApiPathDespiteFallbackController(): void
    {
        $request = Request::create('/api/nothing');
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response());

        $this->expectException(NotFoundHttpException::class);

        $this->dispatch($request);
    }

    public function testOnKernelRequestRejectsWrongMethodDespiteFallbackController(): void
    {
        $request = Request::create('/routed/some-id', 'POST');
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response());

        $this->expectException(MethodNotAllowedHttpException::class);

        $this->dispatch($request);
    }

    public function testOnKernelRequestKeepsControllerAlreadySet(): void
    {
        $request = Request::create('/routed/some-id');
        $request->attributes->set('_controller', 'preset-controller');

        $this->dispatch($request);

        $this->assertFalse($request->attributes->has('_route'));
    }

    private function dispatch(Request $request): void
    {
        $event = new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST
        );

        (new RoutingListener($this->compiledRoutes()))->onKernelRequest($event);
    }

    private function compiledRoutes(): array
    {
        $routes = new RouteCollection();
        $routes->add('routed', new Route('/routed/{id}', ['_controller' => 'routed-controller'], methods: ['GET']));
        $routes->add('api', new Route('/api/routed/{id}', ['_controller' => 'routed-controller'], methods: ['GET']));
        $routes->add('api_xml', new Route('/api/xml/{id}', ['_controller' => 'routed-controller', '_format' => 'xml']));

        return (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
    }
}
