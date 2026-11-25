<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Cookie\ResponseCookieQueue;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\ResponseCookieListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ResponseCookieListenerTest extends TestCase
{
    public function testOnKernelResponseAddsQueuedCookiesToMainResponse(): void
    {
        $queue = new ResponseCookieQueue();
        $cookie = Cookie::create('some-cookie', 'some-value');
        $queue->add($cookie);
        $event = $this->createResponseEvent(HttpKernelInterface::MAIN_REQUEST);

        (new ResponseCookieListener($queue))->onKernelResponse($event);

        $this->assertSame([$cookie], $event->getResponse()->headers->getCookies());
    }

    public function testOnKernelResponseKeepsCookiesForMainResponseOnSubRequest(): void
    {
        $queue = new ResponseCookieQueue();
        $cookie = Cookie::create('some-cookie', 'some-value');
        $queue->add($cookie);
        $listener = new ResponseCookieListener($queue);
        $subRequestEvent = $this->createResponseEvent(HttpKernelInterface::SUB_REQUEST);
        $mainRequestEvent = $this->createResponseEvent(HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelResponse($subRequestEvent);
        $listener->onKernelResponse($mainRequestEvent);

        $this->assertSame([], $subRequestEvent->getResponse()->headers->getCookies());
        $this->assertSame([$cookie], $mainRequestEvent->getResponse()->headers->getCookies());
    }

    public function testOnKernelRequestDiscardsCookiesOfPreviousRequest(): void
    {
        $queue = new ResponseCookieQueue();
        $queue->add(Cookie::create('some-cookie', 'some-value'));
        $event = $this->createRequestEvent(HttpKernelInterface::MAIN_REQUEST);

        (new ResponseCookieListener($queue))->onKernelRequest($event);

        $this->assertSame([], $queue->getAll());
    }

    public function testOnKernelRequestKeepsCookiesOfMainRequestOnSubRequest(): void
    {
        $queue = new ResponseCookieQueue();
        $cookie = Cookie::create('some-cookie', 'some-value');
        $queue->add($cookie);
        $event = $this->createRequestEvent(HttpKernelInterface::SUB_REQUEST);

        (new ResponseCookieListener($queue))->onKernelRequest($event);

        $this->assertSame([$cookie], $queue->getAll());
    }

    private function createRequestEvent(int $requestType): RequestEvent
    {
        return new RequestEvent($this->createStub(HttpKernelInterface::class), new Request(), $requestType);
    }

    private function createResponseEvent(int $requestType): ResponseEvent
    {
        return new ResponseEvent(
            $this->createStub(HttpKernelInterface::class),
            new Request(),
            $requestType,
            new Response()
        );
    }
}
