<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\RoutingListener;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ShopRunnerTest extends TestCase
{
    private EventDispatcher $dispatcher;
    private ?string $terminatedWith = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new RoutingListener($this->compiledRoutes()));
        $this->dispatcher->addListener(
            KernelEvents::TERMINATE,
            function (TerminateEvent $event): void {
                $this->terminatedWith = (string) $event->getResponse()->getContent();
            }
        );
    }

    public function testRunSendsFallbackResponseAndTerminatesKernel(): void
    {
        $this->expectOutputString('fallback-body');

        $this->createShopRunner(Request::create('/'))->run(static fn(): Response => new Response('fallback-body'));

        $this->assertSame('fallback-body', $this->terminatedWith);
    }

    public function testRunSendsResponseOfMatchingRouteInsteadOfFallback(): void
    {
        $this->expectOutputString('routed-body id=some-id');

        $this->createShopRunner(Request::create('/routed/some-id'))
            ->run(static fn(): Response => new Response('fallback-body'));
    }

    public static function routedController(string $id): Response
    {
        return new Response('routed-body id=' . $id);
    }

    private function createShopRunner(Request $request): ShopRunner
    {
        return new ShopRunner(
            new HttpKernel(
                $this->dispatcher,
                new ContainerControllerResolver(new Container()),
                new RequestStack(),
                new ArgumentResolver()
            ),
            $request
        );
    }

    private function compiledRoutes(): array
    {
        $routes = new RouteCollection();
        $routes->add('routed', new Route('/routed/{id}', ['_controller' => self::class . '::routedController']));

        return new CompiledUrlMatcherDumper($routes)->getCompiledRoutes();
    }
}
