<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\KernelFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseSignalListener;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use PHPUnit\Framework\TestCase;

final class KernelFactoryTest extends TestCase
{
    public function testShopKernelRunsTheAttachedController(): void
    {
        $expected = new Response('hello');
        $request = $this->requestWithController(fn(): Response => $expected);

        $response = $this->createKernelFactory()->createShopKernel()->handle($request);

        $this->assertSame($expected, $response);
    }

    public function testAttachedControllerReceivesTheHandledRequest(): void
    {
        $request = $this->requestWithController(
            fn(Request $handledRequest): Response => new Response($handledRequest->query->get('param'))
        );
        $request->query->set('param', 'from-query');

        $response = $this->createKernelFactory()->createShopKernel()->handle($request);

        $this->assertSame('from-query', $response->getContent());
    }

    public function testHandledRequestIsCurrentOnTheRequestStack(): void
    {
        $requestStack = new RequestStack();
        $seenRequest = null;
        $request = $this->requestWithController(
            function () use ($requestStack, &$seenRequest): Response {
                $seenRequest = $requestStack->getCurrentRequest();

                return new Response();
            }
        );

        $this->createKernelFactory($requestStack)->createShopKernel()->handle($request);

        $this->assertSame($request, $seenRequest);
    }

    public function testResponseSignalFromControllerIsDeliveredWithStatusPreserved(): void
    {
        $expected = new Response('stopped', Response::HTTP_OK);
        $request = $this->requestWithController(
            static function () use ($expected): Response {
                throw new ResponseReady($expected);
            }
        );

        $response = $this->createKernelFactory()->createShopKernel()->handle($request);

        $this->assertSame($expected, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testResponseSignalFromResponseListenerIsDelivered(): void
    {
        $expected = new Response('from-listener', Response::HTTP_OK);
        $dispatcher = $this->createDispatcherWithSignalListener();
        $alreadyThrown = false;
        $dispatcher->addListener(
            KernelEvents::RESPONSE,
            static function (ResponseEvent $event) use ($expected, &$alreadyThrown): void {
                if (!$alreadyThrown) {
                    $alreadyThrown = true;
                    throw new ResponseReady($expected);
                }
            }
        );
        $request = $this->requestWithController(fn(): Response => new Response('original'));

        $kernel = (new KernelFactory($dispatcher, new RequestStack(), new Container()))->createShopKernel();
        $response = $kernel->handle($request);

        $this->assertSame($expected, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    private function createKernelFactory(?RequestStack $requestStack = null): KernelFactory
    {
        return new KernelFactory(
            $this->createDispatcherWithSignalListener(),
            $requestStack ?? new RequestStack(),
            new Container()
        );
    }

    private function createDispatcherWithSignalListener(): EventDispatcher
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new ResponseSignalListener());

        return $dispatcher;
    }

    private function requestWithController(callable $controller): Request
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);

        return $request;
    }
}
