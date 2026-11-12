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
use Symfony\Component\Routing\RouteCollection;

final class ShopRunnerTest extends TestCase
{
    public function testSendsTheResponseOfTheFallbackControllerAndTerminatesTheKernelWithIt(): void
    {
        $dispatcher = new EventDispatcher();
        $kernel = new HttpKernel(
            $dispatcher,
            new ContainerControllerResolver(new Container()),
            new RequestStack(),
            new ArgumentResolver()
        );
        $dispatcher->addSubscriber(
            new RoutingListener(new CompiledUrlMatcherDumper(new RouteCollection())->getCompiledRoutes())
        );
        $terminatedWith = null;
        $dispatcher->addListener(
            KernelEvents::TERMINATE,
            static function (TerminateEvent $event) use (&$terminatedWith): void {
                $terminatedWith = $event->getResponse()->getContent();
            }
        );

        ob_start();
        try {
            new ShopRunner($kernel, Request::create('/'))
                ->run(static fn(): Response => new Response('fallback-body'));
        } finally {
            $output = ob_get_clean();
        }

        $this->assertSame('fallback-body', $output);
        $this->assertSame('fallback-body', $terminatedWith);
    }
}
