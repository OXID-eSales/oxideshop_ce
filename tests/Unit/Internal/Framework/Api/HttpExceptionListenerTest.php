<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Api;

use OxidEsales\EshopCommunity\Internal\Framework\Api\HttpExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpExceptionListenerTest extends TestCase
{
    public function testNotFoundHttpExceptionReturns404(): void
    {
        $listener = new HttpExceptionListener();
        $event = $this->createExceptionEvent(new NotFoundHttpException('Resource not found'));

        $listener->onKernelException($event);

        $this->assertSame(404, $event->getResponse()->getStatusCode());
    }

    public function testReturnsGenericMessageInProductionMode(): void
    {
        putenv('OXID_DEBUG_MODE=');

        $listener = new HttpExceptionListener();
        $event = $this->createExceptionEvent(new NotFoundHttpException('Sensitive internal path info'));

        $listener->onKernelException($event);

        $response = json_decode($event->getResponse()->getContent(), true);
        $this->assertSame('Not Found', $response['error']);
    }

    public function testReturnsActualMessageInDebugMode(): void
    {
        putenv('OXID_DEBUG_MODE=1');

        $listener = new HttpExceptionListener();
        $event = $this->createExceptionEvent(new NotFoundHttpException('Sensitive internal path info'));

        $listener->onKernelException($event);

        $response = json_decode($event->getResponse()->getContent(), true);
        $this->assertSame('Sensitive internal path info', $response['error']);
    }

    private function createExceptionEvent(\Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api/test');

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
    }
}
