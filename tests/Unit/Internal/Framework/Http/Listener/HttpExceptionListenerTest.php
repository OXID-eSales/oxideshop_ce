<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\HttpExceptionListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final class HttpExceptionListenerTest extends TestCase
{
    private array $logged = [];

    public function testSubscribesBetweenTheTranslatingAndTheRenderingListeners(): void
    {
        $this->assertSame(
            [KernelEvents::EXCEPTION => ['onKernelException', -20]],
            HttpExceptionListener::getSubscribedEvents()
        );
    }

    public function testRespondsWithStatusTextAndHeadersForHtmlClients(): void
    {
        $event = $this->createExceptionEvent(
            new TooManyRequestsHttpException(45, 'Too many requests.', headers: ['X-RateLimit-Limit' => '100'])
        );

        $this->createListener()->onKernelException($event);

        $response = $event->getResponse();
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $response->getStatusCode());
        $this->assertSame('Too Many Requests', $response->getContent());
        $this->assertSame('45', $response->headers->get('Retry-After'));
        $this->assertSame('100', $response->headers->get('X-RateLimit-Limit'));
        $this->assertTrue($event->isPropagationStopped());
    }

    public function testRespondsWithJsonWhenTheClientAcceptsJson(): void
    {
        $event = $this->createExceptionEvent(
            new NotFoundHttpException('Sensitive detail'),
            Request::create('/api/x', 'GET', server: ['HTTP_ACCEPT' => 'application/json'])
        );

        $this->createListener()->onKernelException($event);

        $this->assertSame(Response::HTTP_NOT_FOUND, $event->getResponse()->getStatusCode());
        $this->assertSame('{"error":"Not Found"}', $event->getResponse()->getContent());
        $this->assertSame('application/json', $event->getResponse()->headers->get('Content-Type'));
    }

    public function testRespondsWithJsonWhenTheRouteDeclaresJson(): void
    {
        $request = Request::create('/api/x');
        $request->attributes->set('_format', 'json');
        $event = $this->createExceptionEvent(new NotFoundHttpException('Sensitive detail'), $request);

        $this->createListener()->onKernelException($event);

        $this->assertSame('{"error":"Not Found"}', $event->getResponse()->getContent());
    }

    public function testRespondsWithLoggedJsonServerErrorForOtherThrowablesWhenJsonIsPreferred(): void
    {
        $exception = new RuntimeException('boom');
        $event = $this->createExceptionEvent(
            $exception,
            Request::create('/api/x', 'GET', server: ['HTTP_ACCEPT' => 'application/json'])
        );

        $this->createListener()->onKernelException($event);

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $event->getResponse()->getStatusCode());
        $this->assertSame('{"error":"Internal Server Error"}', $event->getResponse()->getContent());
        $this->assertSame([['boom', $exception]], $this->logged);
    }

    public function testLeavesOtherThrowablesToTheGlobalHandlerForHtmlClients(): void
    {
        $event = $this->createExceptionEvent(new RuntimeException('boom'));

        $this->createListener()->onKernelException($event);

        $this->assertFalse($event->hasResponse());
        $this->assertFalse($event->isPropagationStopped());
        $this->assertSame([], $this->logged);
    }

    private function createListener(): HttpExceptionListener
    {
        $logger = $this->createStub(LoggerInterface::class);
        $logger->method('error')->willReturnCallback(function (string $message, array $context): void {
            $this->logged[] = [$message, $context['exception']];
        });

        return new HttpExceptionListener($logger);
    }

    private function createExceptionEvent(Throwable $throwable, ?Request $request = null): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            $request ?? Request::create('/index.php?cl=start'),
            HttpKernelInterface::MAIN_REQUEST,
            $throwable
        );
    }
}
