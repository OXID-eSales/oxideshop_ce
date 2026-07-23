<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\NotFoundListener;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class NotFoundListenerTest extends TestCase
{
    public function testIgnoresOtherThrowables(): void
    {
        $event = $this->createExceptionEvent(new \RuntimeException(), HttpKernelInterface::MAIN_REQUEST);

        $this->createListener()->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    public function testIgnoresSubRequests(): void
    {
        $event = $this->createExceptionEvent(new RoutingException(), HttpKernelInterface::SUB_REQUEST);

        $this->createListener()->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    private function createListener(): NotFoundListener
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->never())->method('renderTemplate');

        return new NotFoundListener($renderer);
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
