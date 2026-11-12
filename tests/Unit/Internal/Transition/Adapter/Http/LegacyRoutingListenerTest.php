<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ErrorRenderer\ProblemJsonErrorRenderer;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\RoutingListener;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimitListener;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http\LegacyRoutingListener;
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
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class LegacyRoutingListenerTest extends TestCase
{
    public function testSubscribesAfterRoutingAndBeforeTheRateLimiter(): void
    {
        $priority = $this->requestPriority(LegacyRoutingListener::getSubscribedEvents());

        $this->assertLessThan($this->requestPriority(RoutingListener::getSubscribedEvents()), $priority);
        $this->assertGreaterThan($this->requestPriority(StorefrontRateLimitListener::getSubscribedEvents()), $priority);
    }

    public function testKeepsTheLegacyControllerForTheRootPath(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/')));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testKeepsTheLegacyControllerForAPathNamingAController(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/unrouted/path?cl=start')));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testKeepsTheLegacyControllerForAPostedControllerKey(): void
    {
        $request = Request::create('/unrouted/path', 'POST', ['cl' => 'start']);

        $response = $this->handle($this->withFallbackController($request));

        $this->assertSame('fallback-body', $response->getContent());
    }

    public function testAnswersUnknownPathWithNotFound(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/unrouted/path')));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAnswersEmptyControllerKeyWithNotFound(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/unrouted/path?cl=')));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testIgnoresAControllerKeyInABodyTheShopDoesNotRead(): void
    {
        $request = Request::create('/unrouted/path', 'PUT', ['cl' => 'start']);

        $response = $this->handle($this->withFallbackController($request));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testIgnoresAControllerKeyThatIsEmptyOnceEscaped(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/unrouted/path?cl=%00')));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAnswersWrongMethodOnARoutePathWithoutControllerKeyWithNotFound(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/routed/some-id', 'POST')));

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testLeavesRoutedRequestsToTheirController(): void
    {
        $response = $this->handle($this->withFallbackController(Request::create('/routed/some-id')));

        $this->assertSame('routed-body id=some-id', $response->getContent());
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
        $dispatcher->addSubscriber(new LegacyRoutingListener());
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

    private function requestPriority(array $subscribedEvents): int
    {
        return $subscribedEvents[KernelEvents::REQUEST][1];
    }

    private function compiledRoutes(): array
    {
        $routes = new RouteCollection();
        $routes->add(
            'routed',
            new Route('/routed/{id}', ['_controller' => self::class . '::routedController'], methods: ['GET'])
        );

        return new CompiledUrlMatcherDumper($routes)->getCompiledRoutes();
    }
}
