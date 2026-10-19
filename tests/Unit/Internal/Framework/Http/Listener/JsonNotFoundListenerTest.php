<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\JsonNotFoundListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class JsonNotFoundListenerTest extends TestCase
{
    public function testSubscribesToKernelExceptionBeforeTheHttpExceptionListener(): void
    {
        $this->assertSame(
            [KernelEvents::EXCEPTION => ['onKernelException', 0]],
            JsonNotFoundListener::getSubscribedEvents()
        );
    }

    public function testTurnsRoutingExceptionIntoNotFoundForClientsAcceptingJson(): void
    {
        $routingException = new RoutingException('unknown controller');
        $event = $this->createExceptionEvent(
            $routingException,
            Request::create('/nothing', 'GET', server: ['HTTP_ACCEPT' => 'application/json'])
        );

        new JsonNotFoundListener()->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getThrowable()->getStatusCode());
        $this->assertSame($routingException, $event->getThrowable()->getPrevious());
        $this->assertFalse($event->hasResponse());
    }

    public function testTurnsRoutingExceptionIntoNotFoundForRoutesDeclaringJson(): void
    {
        $request = Request::create('/api/nothing');
        $request->attributes->set('_format', 'json');
        $event = $this->createExceptionEvent(new RoutingException(), $request);

        new JsonNotFoundListener()->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getThrowable()->getStatusCode());
    }

    public function testLeavesRoutingExceptionOfHtmlClientsUntouched(): void
    {
        $routingException = new RoutingException();
        $event = $this->createExceptionEvent($routingException, Request::create('/nothing'));

        new JsonNotFoundListener()->onKernelException($event);

        $this->assertSame($routingException, $event->getThrowable());
    }

    public function testIgnoresOtherThrowables(): void
    {
        $throwable = new \RuntimeException();
        $event = $this->createExceptionEvent(
            $throwable,
            Request::create('/nothing', 'GET', server: ['HTTP_ACCEPT' => 'application/json'])
        );

        new JsonNotFoundListener()->onKernelException($event);

        $this->assertSame($throwable, $event->getThrowable());
    }

    public function testIgnoresSubRequests(): void
    {
        $routingException = new RoutingException();
        $event = $this->createExceptionEvent(
            $routingException,
            Request::create('/nothing', 'GET', server: ['HTTP_ACCEPT' => 'application/json']),
            HttpKernelInterface::SUB_REQUEST
        );

        new JsonNotFoundListener()->onKernelException($event);

        $this->assertSame($routingException, $event->getThrowable());
    }

    private function createExceptionEvent(
        \Throwable $throwable,
        Request $request,
        int $requestType = HttpKernelInterface::MAIN_REQUEST
    ): ExceptionEvent {
        return new ExceptionEvent($this->createStub(HttpKernelInterface::class), $request, $requestType, $throwable);
    }
}
