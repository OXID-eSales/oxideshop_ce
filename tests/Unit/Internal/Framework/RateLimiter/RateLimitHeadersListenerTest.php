<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\RateLimitHeadersListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RateLimitHeadersListenerTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        $events = RateLimitHeadersListener::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::RESPONSE, $events);
        $this->assertSame(['onKernelResponse', -10], $events[KernelEvents::RESPONSE]);
    }

    public function testIgnoresSubRequests(): void
    {
        $listener = new RateLimitHeadersListener();

        $request = Request::create('/api/test');
        $request->attributes->set('_rate_limit_info', [
            'limit' => 100,
            'remaining' => 99,
            'reset' => time() + 60,
        ]);

        $response = new Response();
        $event = $this->createResponseEvent($request, $response, HttpKernelInterface::SUB_REQUEST);

        $listener->onKernelResponse($event);

        $this->assertFalse($response->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($response->headers->has('X-RateLimit-Remaining'));
        $this->assertFalse($response->headers->has('X-RateLimit-Reset'));
    }

    public function testIgnoresRequestsWithoutRateLimitInfo(): void
    {
        $listener = new RateLimitHeadersListener();

        $request = Request::create('/api/test');
        $response = new Response();
        $event = $this->createResponseEvent($request, $response);

        $listener->onKernelResponse($event);

        $this->assertFalse($response->headers->has('X-RateLimit-Limit'));
        $this->assertFalse($response->headers->has('X-RateLimit-Remaining'));
        $this->assertFalse($response->headers->has('X-RateLimit-Reset'));
    }

    public function testAddsRateLimitHeadersToResponse(): void
    {
        $listener = new RateLimitHeadersListener();

        $resetTime = time() + 60;
        $request = Request::create('/api/test');
        $request->attributes->set('_rate_limit_info', [
            'limit' => 100,
            'remaining' => 95,
            'reset' => $resetTime,
        ]);

        $response = new Response();
        $event = $this->createResponseEvent($request, $response);

        $listener->onKernelResponse($event);

        $this->assertTrue($response->headers->has('X-RateLimit-Limit'));
        $this->assertTrue($response->headers->has('X-RateLimit-Remaining'));
        $this->assertTrue($response->headers->has('X-RateLimit-Reset'));

        $this->assertSame('100', $response->headers->get('X-RateLimit-Limit'));
        $this->assertSame('95', $response->headers->get('X-RateLimit-Remaining'));
        $this->assertSame((string) $resetTime, $response->headers->get('X-RateLimit-Reset'));
    }

    private function createResponseEvent(
        Request $request,
        Response $response,
        int $requestType = HttpKernelInterface::MAIN_REQUEST
    ): ResponseEvent {
        $kernel = $this->createMock(HttpKernelInterface::class);
        return new ResponseEvent($kernel, $request, $requestType, $response);
    }
}
