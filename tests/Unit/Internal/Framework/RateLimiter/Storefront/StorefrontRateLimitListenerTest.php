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
    private int $enforcements = 0;

    public function testSubscribesToKernelRequest(): void
    {
        $this->assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 10]],
            StorefrontRateLimitListener::getSubscribedEvents()
        );
    }

    public function testEnforcesForMainRequest(): void
    {
        $this->createListener(enabled: true)->onKernelRequest($this->createRequestEvent());

        $this->assertSame(1, $this->enforcements);
    }

    public function testDoesNotEnforceWhenDisabled(): void
    {
        $this->createListener(enabled: false)->onKernelRequest($this->createRequestEvent());

        $this->assertSame(0, $this->enforcements);
    }

    public function testDoesNotEnforceForSubRequest(): void
    {
        $this->createListener(enabled: true)->onKernelRequest(
            $this->createRequestEvent(HttpKernelInterface::SUB_REQUEST)
        );

        $this->assertSame(0, $this->enforcements);
    }

    private function createListener(bool $enabled): StorefrontRateLimitListener
    {
        $rateLimiter = $this->createStub(StorefrontRateLimiterInterface::class);
        $rateLimiter->method('enforce')->willReturnCallback(function (): void {
            $this->enforcements++;
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
