<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\InvalidateModuleCacheEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ModuleSetupEvent;
use PHPUnit\Framework\TestCase;

final class InvalidateModuleCacheEventSubscriberTest extends TestCase
{
    public function testSubscriberCallsModuleCacheService(): void
    {
        $shopCacheCleaner = $this->getMockBuilder(ShopCacheCleanerInterface::class)->getMock();
        $shopCacheCleaner
            ->expects($this->once())
            ->method('clear');

        $event = new class (1, 'testModuleId') extends ModuleSetupEvent {
        };

        $subscriber = new InvalidateModuleCacheEventSubscriber($shopCacheCleaner);
        $subscriber->invalidateModuleCache($event);
    }
}
