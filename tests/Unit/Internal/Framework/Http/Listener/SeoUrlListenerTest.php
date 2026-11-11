<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\SeoUrlListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;

final class SeoUrlListenerTest extends TestCase
{
    public function testSubscribesBeforeTheRateLimiter(): void
    {
        $this->assertSame(
            [KernelEvents::REQUEST => ['onKernelRequest', 32]],
            SeoUrlListener::getSubscribedEvents()
        );
    }
}
