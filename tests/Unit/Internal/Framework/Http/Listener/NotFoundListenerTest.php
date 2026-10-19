<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\NotFoundListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class NotFoundListenerTest extends TestCase
{
    public function testSubscribesToKernelExceptionAfterTheHttpExceptionListener(): void
    {
        $this->assertSame(
            [KernelEvents::EXCEPTION => ['onKernelException', -30]],
            NotFoundListener::getSubscribedEvents()
        );
    }

    public function testIgnoresOtherThrowables(): void
    {
        $event = $this->createExceptionEvent(new \RuntimeException(), HttpKernelInterface::MAIN_REQUEST);

        new NotFoundListener()->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    public function testIgnoresSubRequests(): void
    {
        $event = $this->createExceptionEvent(new RoutingException(), HttpKernelInterface::SUB_REQUEST);

        new NotFoundListener()->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    private function createExceptionEvent(\Throwable $throwable, int $requestType): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/'),
            $requestType,
            $throwable
        );
    }
}
