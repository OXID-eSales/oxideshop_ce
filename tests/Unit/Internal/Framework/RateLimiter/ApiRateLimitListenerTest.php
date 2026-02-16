<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\ApiRateLimiterFactory;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\ApiRateLimitListener;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\ClientIdentifierProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiRateLimitListenerTest extends TestCase
{
    private string $testId;

    protected function setUp(): void
    {
        $this->testId = uniqid();
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertArrayHasKey(KernelEvents::REQUEST, ApiRateLimitListener::getSubscribedEvents());
    }

    public function testIgnoresNonApiRequests(): void
    {
        $listener = $this->createListener(limit: 1);

        $request = Request::create('/shop/home');
        $event = $this->createRequestEvent($request);

        $listener->onKernelRequest($event);

        $this->assertFalse($event->hasResponse());
    }

    public function testAllowsApiRequestsWithinLimit(): void
    {
        $listener = $this->createListener(limit: 10);

        $request = Request::create('/api/test');
        $event = $this->createRequestEvent($request);

        $listener->onKernelRequest($event);

        $this->assertFalse($event->hasResponse());
        $this->assertTrue($request->attributes->has('_rate_limit_info'));
    }

    public function testBlocksApiRequestsExceedingLimit(): void
    {
        $listener = $this->createListener(limit: 2);
        $ip = $this->uniqueIp();

        for ($i = 0; $i < 2; $i++) {
            $request = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
            $event = $this->createRequestEvent($request);
            $listener->onKernelRequest($event);
            $this->assertFalse($event->hasResponse());
        }

        $request = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event = $this->createRequestEvent($request);
        $listener->onKernelRequest($event);

        $this->assertTrue($event->hasResponse());
        $this->assertSame(Response::HTTP_TOO_MANY_REQUESTS, $event->getResponse()->getStatusCode());
    }

    public function testDisabledRateLimiterAllowsAllRequests(): void
    {
        $listener = $this->createListener(enabled: false, limit: 1);
        $ip = $this->uniqueIp();

        for ($i = 0; $i < 5; $i++) {
            $request = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
            $event = $this->createRequestEvent($request);
            $listener->onKernelRequest($event);
            $this->assertFalse($event->hasResponse());
        }
    }

    public function testExcludedRoutesAreNotRateLimited(): void
    {
        $listener = $this->createListener(limit: 1, excludedRoutes: ['/api/health']);
        $ip = $this->uniqueIp();

        for ($i = 0; $i < 5; $i++) {
            $request = Request::create('/api/health', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
            $event = $this->createRequestEvent($request);
            $listener->onKernelRequest($event);
            $this->assertFalse($event->hasResponse());
        }
    }

    public function testExcludedRoutesWithWildcardPattern(): void
    {
        $listener = $this->createListener(limit: 1, excludedRoutes: ['/api/public/*']);
        $ip = $this->uniqueIp();

        for ($i = 0; $i < 5; $i++) {
            $request = Request::create('/api/public/anything', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
            $event = $this->createRequestEvent($request);
            $listener->onKernelRequest($event);
            $this->assertFalse($event->hasResponse());
        }
    }

    public function testWildcardPatternDoesNotMatchExactPath(): void
    {
        $listener = $this->createListener(limit: 1, excludedRoutes: ['/api/public/*']);
        $ip = $this->uniqueIp();

        $request1 = Request::create('/api/public', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event1 = $this->createRequestEvent($request1);
        $listener->onKernelRequest($event1);
        $this->assertFalse($event1->hasResponse());

        $request2 = Request::create('/api/public', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event2 = $this->createRequestEvent($request2);
        $listener->onKernelRequest($event2);
        $this->assertTrue($event2->hasResponse());
    }

    public function testNonExcludedRoutesAreRateLimited(): void
    {
        $listener = $this->createListener(limit: 1, excludedRoutes: ['/api/public/*']);
        $ip = $this->uniqueIp();

        $request1 = Request::create('/api/private/data', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event1 = $this->createRequestEvent($request1);
        $listener->onKernelRequest($event1);
        $this->assertFalse($event1->hasResponse());

        $request2 = Request::create('/api/private/data', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event2 = $this->createRequestEvent($request2);
        $listener->onKernelRequest($event2);
        $this->assertTrue($event2->hasResponse());
    }

    public function testRateLimitResponseContainsRetryAfterHeaders(): void
    {
        $listener = $this->createListener(limit: 1);
        $ip = $this->uniqueIp();

        $request1 = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event1 = $this->createRequestEvent($request1);
        $listener->onKernelRequest($event1);

        $request2 = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $ip]);
        $event2 = $this->createRequestEvent($request2);
        $listener->onKernelRequest($event2);

        $this->assertTrue($event2->hasResponse());
        $response = $event2->getResponse();
        $this->assertTrue($response->headers->has('Retry-After'));
        $this->assertTrue($response->headers->has('X-RateLimit-Reset'));
    }

    public function testDifferentClientsHaveSeparateLimits(): void
    {
        $listener = $this->createListener(limit: 1);

        $request1 = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $this->uniqueIp()]);
        $event1 = $this->createRequestEvent($request1);
        $listener->onKernelRequest($event1);
        $this->assertFalse($event1->hasResponse());

        $request2 = Request::create('/api/test', 'GET', [], [], [], ['REMOTE_ADDR' => $this->uniqueIp()]);
        $event2 = $this->createRequestEvent($request2);
        $listener->onKernelRequest($event2);
        $this->assertFalse($event2->hasResponse());
    }

    public function testIgnoresSubRequests(): void
    {
        $listener = $this->createListener(limit: 1);

        $request = Request::create('/api/test');
        $event = $this->createRequestEvent($request, HttpKernelInterface::SUB_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertFalse($event->hasResponse());
        $this->assertFalse($request->attributes->has('_rate_limit_info'));
    }

    public function testHandlesNullClientIp(): void
    {
        $listener = $this->createListener(limit: 100);

        $request = Request::create('/api/test');
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);

        $event = $this->createRequestEvent($request);
        $listener->onKernelRequest($event);

        $this->assertFalse($event->hasResponse());
        $this->assertTrue($request->attributes->has('_rate_limit_info'));
    }

    private function createListener(
        bool $enabled = true,
        int $limit = 100,
        array $excludedRoutes = []
    ): ApiRateLimitListener {
        $factory = new ApiRateLimiterFactory($limit, 60, 'token_bucket', new BasicContext());

        return new ApiRateLimitListener($enabled, $excludedRoutes, $factory, new ClientIdentifierProvider());
    }

    private function createRequestEvent(
        Request $request,
        int $requestType = HttpKernelInterface::MAIN_REQUEST
    ): RequestEvent {
        $kernel = $this->createStub(HttpKernelInterface::class);
        return new RequestEvent($kernel, $request, $requestType);
    }

    private function uniqueIp(): string
    {
        return $this->testId . '_' . uniqid();
    }
}
