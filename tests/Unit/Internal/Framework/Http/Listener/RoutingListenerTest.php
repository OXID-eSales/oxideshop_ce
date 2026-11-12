<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ProblemJsonErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\RoutingListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\Controller\ErrorController;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RoutingListenerTest extends TestCase
{
    public function testRunsTheFallbackControllerWhenNoRouteMatches(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/unrouted/path')));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testRunsTheFallbackControllerWhenTheMethodDoesNotMatchOutsideTheApi(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/routed/some-id', 'POST')));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testRunsTheRoutedControllerWhenARouteMatches(): void
    {
        $request = $this->withFallbackController(Request::create('/routed/some-id'));

        $response = $this->handle($request);

        $this->assertSame('routed-body id=some-id', $response->getContent());
        $this->assertSame('routed', $request->attributes->get('_route'));
        $this->assertFalse($request->attributes->has('_format'));
    }

    public function testDefaultsApiRoutesWithoutDeclaredFormatToJson(): void
    {
        $request = Request::create('/api/routed/some-id');

        $response = $this->handle($request);

        $this->assertSame('routed-body id=some-id', $response->getContent());
        $this->assertSame('json', $request->attributes->get('_format'));
    }

    public function testKeepsTheFormatDeclaredByAnApiRoute(): void
    {
        $request = Request::create('/api/xml/some-id');

        $this->handle($request);

        $this->assertSame('xml', $request->attributes->get('_format'));
    }

    public function testAnswersUnknownApiPathsWithJsonNotFoundDespiteTheFallbackController(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/api/nothing')));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame($this->problem(404, 'Not Found'), json_decode($response->getContent(), true));
    }

    public function testAnswersWrongMethodOnApiRoutesWithJsonMethodNotAllowedDespiteTheFallbackController(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/api/routed/some-id', 'POST')));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
        $this->assertSame($this->problem(405, 'Method Not Allowed'), json_decode($response->getContent(), true));
    }

    public function testAnswersUnroutedPathWithoutFallbackControllerWithNotFound(): void
    {
        $response = $this->handle(Request::create('/unrouted/path'));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAnswersWrongMethodWithoutFallbackControllerWithMethodNotAllowed(): void
    {
        $response = $this->handle(Request::create('/routed/some-id', 'POST'));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
    }

    public function testKeepsTheControllerAlreadySetOnTheRequest(): void
    {
        $request = Request::create('/routed/some-id');
        $request->attributes->set('_controller', static fn(): Response => new Response('preset-body'));

        $response = $this->handle($request);

        $this->assertSame('preset-body', $response->getContent());
        $this->assertFalse($request->attributes->has('_route'));
    }

    public static function routedController(string $id): Response
    {
        return new Response('routed-body id=' . $id);
    }

    private function handle(Request $request): Response
    {
        $dispatcher = new EventDispatcher();
        $requestStack = new RequestStack();
        $kernel = new HttpKernel(
            $dispatcher,
            new ContainerControllerResolver(new Container()),
            $requestStack,
            new ArgumentResolver(),
            handleAllThrowables: true
        );
        $dispatcher->addSubscriber(new RoutingListener($this->compiledRoutes()));
        $dispatcher->addSubscriber(new ErrorListener(new ErrorController(
            $kernel,
            null,
            new ProblemJsonErrorRenderer($requestStack, new HtmlErrorRenderer(), false)
        )));

        return $kernel->handle($request);
    }

    private function withFallbackController(Request $request): Request
    {
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response('fallback-body'));

        return $request;
    }

    private function problem(int $status, string $detail): array
    {
        return [
            'type' => 'https://tools.ietf.org/html/rfc2616#section-10',
            'title' => 'An error occurred',
            'status' => $status,
            'detail' => $detail,
        ];
    }

    private function compiledRoutes(): array
    {
        $controller = ['_controller' => self::class . '::routedController'];
        $routes = new RouteCollection();
        $routes->add('routed', new Route('/routed/{id}', $controller, methods: ['GET']));
        $routes->add('api', new Route('/api/routed/{id}', $controller, methods: ['GET']));
        $routes->add('api_xml', new Route('/api/xml/{id}', $controller + ['_format' => 'xml']));

        return new CompiledUrlMatcherDumper($routes)->getCompiledRoutes();
    }
}
