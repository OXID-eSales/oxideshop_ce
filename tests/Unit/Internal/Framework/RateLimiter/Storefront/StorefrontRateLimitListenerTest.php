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

final class StorefrontRateLimitListenerTest extends TestCase
{
    public function testEnforcesForMainRequest(): void
    {
        $event = $this->createRequestEvent();
        $rateLimiter = $this->createMock(StorefrontRateLimiterInterface::class);
        $rateLimiter->expects($this->once())->method('enforce')->with($event->getRequest());

        $listener = new StorefrontRateLimitListener($rateLimiter, enabled: true);

        $listener->onKernelRequest($event);
    }

    public function testDoesNotEnforceWhenDisabled(): void
    {
        $rateLimiter = $this->createMock(StorefrontRateLimiterInterface::class);
        $rateLimiter->expects($this->never())->method('enforce');

        $listener = new StorefrontRateLimitListener($rateLimiter, enabled: false);

        $listener->onKernelRequest($this->createRequestEvent());
    }

    public function testDoesNotEnforceForSubRequest(): void
    {
        $rateLimiter = $this->createMock(StorefrontRateLimiterInterface::class);
        $rateLimiter->expects($this->never())->method('enforce');

        $listener = new StorefrontRateLimitListener($rateLimiter, enabled: true);

        $listener->onKernelRequest($this->createRequestEvent(HttpKernelInterface::SUB_REQUEST));
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
