<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ResponseException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\LegacyExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class LegacyExceptionListenerTest extends TestCase
{
    private LoggerInterface $logger;
    private LegacyExceptionListener $listener;

    protected function setUp(): void
    {
        $this->logger = $this->createStub(LoggerInterface::class);
        $this->listener = new LegacyExceptionListener($this->logger);
    }

    public function testRedirectExceptionReturnsRedirectResponse(): void
    {
        $event = $this->createEvent(new RedirectException('http://example.com/target', 301));

        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('http://example.com/target', $response->headers->get('Location'));
    }

    public function testRedirectExceptionDefaultsTo302(): void
    {
        $event = $this->createEvent(new RedirectException('http://example.com/'));

        $this->listener->onKernelException($event);

        $this->assertSame(302, $event->getResponse()->getStatusCode());
    }

    public function testResponseExceptionReturnsCarriedResponse(): void
    {
        $carried = new Response('Setup required', 503);
        $event = $this->createEvent(new ResponseException($carried));

        $this->listener->onKernelException($event);

        $this->assertSame($carried, $event->getResponse());
        $this->assertSame(503, $event->getResponse()->getStatusCode());
    }

    public function testUnhandledExceptionReturns404(): void
    {
        $event = $this->createEvent(new \RuntimeException('Something broke'));

        $this->listener->onKernelException($event);

        $this->assertSame(404, $event->getResponse()->getStatusCode());
    }

    public function testUnhandledExceptionIsLogged(): void
    {
        $exception = new \RuntimeException('Something broke');
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with('Something broke', [$exception]);

        $listener = new LegacyExceptionListener($logger);
        $event = $this->createEvent($exception);
        $listener->onKernelException($event);
    }

    public function testRedirectExceptionIsNotLogged(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $listener = new LegacyExceptionListener($logger);
        $event = $this->createEvent(new RedirectException('http://example.com/'));
        $listener->onKernelException($event);
    }

    public function testResponseExceptionIsNotLogged(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('error');

        $listener = new LegacyExceptionListener($logger);
        $event = $this->createEvent(new ResponseException(new Response('', 503)));
        $listener->onKernelException($event);
    }

    public function testDebugModeDoesNotSetResponse(): void
    {
        $listener = new LegacyExceptionListener($this->logger, debugMode: true);
        $event = $this->createEvent(new \RuntimeException('Debug error'));

        $listener->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    public function testDebugModeStillHandlesRedirectException(): void
    {
        $listener = new LegacyExceptionListener($this->logger, debugMode: true);
        $event = $this->createEvent(new RedirectException('http://example.com/'));

        $listener->onKernelException($event);

        $this->assertInstanceOf(RedirectResponse::class, $event->getResponse());
    }

    private function createEvent(\Throwable $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }
}
