<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimiterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\StorefrontRateLimitListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class StorefrontRateLimitListenerTest extends TestCase
{
    /** @var Request[] */
    private array $enforcedRequests = [];

    public function testSubscribesToKernelRequest(): void
    {
        $this->assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 10]],
            StorefrontRateLimitListener::getSubscribedEvents()
        );
    }

    public function testEnforcesForMainRequest(): void
    {
        $event = $this->createRequestEvent();

        $this->createListener(enabled: true)->onKernelRequest($event);

        $this->assertSame([$event->getRequest()], $this->enforcedRequests);
    }

    public function testDoesNotEnforceWhenDisabled(): void
    {
        $this->createListener(enabled: false)->onKernelRequest($this->createRequestEvent());

        $this->assertSame([], $this->enforcedRequests);
    }

    public function testDoesNotEnforceForSubRequest(): void
    {
        $this->createListener(enabled: true)->onKernelRequest(
            $this->createRequestEvent(HttpKernelInterface::SUB_REQUEST)
        );

        $this->assertSame([], $this->enforcedRequests);
    }

    private function createListener(bool $enabled): StorefrontRateLimitListener
    {
        $rateLimiter = $this->createStub(StorefrontRateLimiterInterface::class);
        $rateLimiter->method('enforce')->willReturnCallback(function (Request $request): void {
            $this->enforcedRequests[] = $request;
        });

        return new StorefrontRateLimitListener($rateLimiter, $enabled);
    }

    private function createRequestEvent(int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        return new RequestEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/index.php?cl=start'),
            $requestType
        );
    }
}
