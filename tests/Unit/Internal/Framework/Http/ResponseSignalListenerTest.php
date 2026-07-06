<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseSignalListener;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use PHPUnit\Framework\TestCase;

final class ResponseSignalListenerTest extends TestCase
{
    public function testDeliversTheSignalResponseAllowingItsStatusCode(): void
    {
        $expected = new Response('stopped', Response::HTTP_OK);
        $event = $this->createExceptionEvent(new ResponseReady($expected));

        (new ResponseSignalListener())->onKernelException($event);

        $this->assertSame($expected, $event->getResponse());
        $this->assertTrue($event->isAllowingCustomResponseCode());
    }

    public function testDeliversRedirectSignals(): void
    {
        $event = $this->createExceptionEvent(
            new ResponseReady(new RedirectResponse('https://example.com/target', 302))
        );

        (new ResponseSignalListener())->onKernelException($event);

        $this->assertSame('https://example.com/target', $event->getResponse()->headers->get('Location'));
        $this->assertSame(302, $event->getResponse()->getStatusCode());
    }

    public function testIgnoresOtherThrowables(): void
    {
        $event = $this->createExceptionEvent(new RuntimeException());

        (new ResponseSignalListener())->onKernelException($event);

        $this->assertNull($event->getResponse());
        $this->assertFalse($event->isAllowingCustomResponseCode());
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
