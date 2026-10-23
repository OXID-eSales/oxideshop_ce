<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ProblemJsonErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunner;
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
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ShopRunnerTest extends TestCase
{
    private ?Response $response = null;

    public function testRunsTheFallbackControllerWhenNoRouteMatches(): void
    {
        $response = $this->handleRequest(Request::create('/unrouted/path'));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testRunsTheFallbackControllerWhenTheMethodDoesNotMatchOutsideTheApi(): void
    {
        $response = $this->handleRequest(Request::create('/routed/some-id', 'POST'));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testRunsTheRoutedControllerWhenARouteMatches(): void
    {
        $request = Request::create('/routed/some-id');

        $response = $this->handleRequest($request);

        $this->assertSame('routed-body id=some-id', $response->getContent());
        $this->assertFalse($request->attributes->has('_format'));
    }

    public function testDefaultsApiRoutesWithoutDeclaredFormatToJson(): void
    {
        $request = Request::create('/api/routed/some-id');

        $response = $this->handleRequest($request);

        $this->assertSame('routed-body id=some-id', $response->getContent());
        $this->assertSame('json', $request->attributes->get('_format'));
    }

    public function testKeepsTheFormatDeclaredByAnApiRoute(): void
    {
        $request = Request::create('/api/xml/some-id');

        $this->handleRequest($request);

        $this->assertSame('xml', $request->attributes->get('_format'));
    }

    public function testAnswersUnknownApiPathsWithJsonNotFound(): void
    {
        $response = $this->handleRequest(Request::create('/api/nothing'));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertSame($this->problem(404, 'Not Found'), json_decode($response->getContent(), true));
    }

    public function testAnswersWrongMethodOnApiRoutesWithJsonMethodNotAllowed(): void
    {
        $response = $this->handleRequest(Request::create('/api/routed/some-id', 'POST'));

        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
        $this->assertSame('GET', $response->headers->get('Allow'));
        $this->assertSame($this->problem(405, 'Method Not Allowed'), json_decode($response->getContent(), true));
    }

    public static function routedController(string $id): Response
    {
        return new Response('routed-body id=' . $id);
    }

    private function handleRequest(Request $request): Response
    {
        ob_start();
        try {
            $this->createRunner($request)->run(static fn(): Response => new Response('fallback-body'));
        } finally {
            ob_end_clean();
        }

        return $this->response;
    }

    private function createRunner(Request $request): ShopRunner
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
        $dispatcher->addSubscriber(new ErrorListener(new ErrorController(
            $kernel,
            null,
            new ProblemJsonErrorRenderer($requestStack, new HtmlErrorRenderer(), false)
        )));
        $dispatcher->addListener(KernelEvents::RESPONSE, function (ResponseEvent $event): void {
            $this->response = $event->getResponse();
        });

        return new ShopRunner($kernel, $request, $this->compiledRoutes());
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
