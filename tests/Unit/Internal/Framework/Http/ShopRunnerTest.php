<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ShopRunner;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use PHPUnit\Framework\TestCase;

final class ShopRunnerTest extends TestCase
{
    public function testRunsTheFallbackControllerWhenNoRouteMatches(): void
    {
        $request = Request::create('/unrouted/path');

        $sentContent = $this->runAndCaptureOutput(
            $request,
            static fn(): Response => new Response('fallback-body')
        );

        $this->assertSame('fallback-body', $sentContent);
    }

    public function testRunsTheRoutedControllerWhenARouteMatches(): void
    {
        $request = Request::create('/routed/some-id');

        $sentContent = $this->runAndCaptureOutput(
            $request,
            static fn(): Response => new Response('fallback-body')
        );

        $this->assertSame('routed-body id=some-id', $sentContent);
    }

    public static function routedController(string $id): Response
    {
        return new Response('routed-body id=' . $id);
    }

    private function runAndCaptureOutput(Request $request, callable $fallbackController): string
    {
        $runner = new ShopRunner(
            new HttpKernel(
                new EventDispatcher(),
                new ContainerControllerResolver(new Container()),
                new RequestStack(),
                new ArgumentResolver(),
                handleAllThrowables: true
            ),
            $request,
            $this->compiledRoutes()
        );

        ob_start();
        $runner->run($fallbackController);

        return (string) ob_get_clean();
    }

    private function compiledRoutes(): array
    {
        $routes = new RouteCollection();
        $routes->add('routed', new Route('/routed/{id}', ['_controller' => self::class . '::routedController']));

        return (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
    }
}
