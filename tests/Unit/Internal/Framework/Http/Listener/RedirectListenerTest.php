<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\RedirectListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class RedirectListenerTest extends TestCase
{
    public function testSubscribesBeforeTheErrorListenerLogsExceptions(): void
    {
        $this->assertSame(
            [KernelEvents::EXCEPTION => ['onKernelException', 16]],
            RedirectListener::getSubscribedEvents()
        );
    }

    public function testDeliversRedirectResponseForRedirectException(): void
    {
        $event = $this->createExceptionEvent(new RedirectException('https://shop.example/target', 301));

        new RedirectListener()->onKernelException($event);

        $response = $event->getResponse();
        $this->assertSame('https://shop.example/target', $response->headers->get('Location'));
        $this->assertSame(301, $response->getStatusCode());
        $this->assertTrue($event->isPropagationStopped());
    }

    #[DataProvider('nonRedirectStatusCodes')]
    public function testNormalisesNonRedirectStatusCodeToFound(int $statusCode): void
    {
        $event = $this->createExceptionEvent(new RedirectException('https://shop.example/target', $statusCode));

        new RedirectListener()->onKernelException($event);

        $this->assertSame('https://shop.example/target', $event->getResponse()->headers->get('Location'));
        $this->assertSame(302, $event->getResponse()->getStatusCode());
    }

    public static function nonRedirectStatusCodes(): array
    {
        return [[200], [300], [304], [305], [306], [500]];
    }

    public function testIgnoresOtherThrowables(): void
    {
        $event = $this->createExceptionEvent(new \RuntimeException());

        new RedirectListener()->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    private function createExceptionEvent(\Throwable $throwable): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $throwable
        );
    }
}
