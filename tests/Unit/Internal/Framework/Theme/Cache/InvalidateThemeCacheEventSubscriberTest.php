<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\InvalidateThemeCacheEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ThemeConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use PHPUnit\Framework\TestCase;

final class InvalidateThemeCacheEventSubscriberTest extends TestCase
{
    public function testSubscriberEvictsConfigurationAndClearsShopCache(): void
    {
        $shopCacheCleaner = $this->createMock(ShopCacheCleanerInterface::class);
        $shopCacheCleaner
            ->expects($this->once())
            ->method('clear')
            ->with(1);

        $configurationCache = $this->createMock(ThemeConfigurationCacheInterface::class);
        $configurationCache
            ->expects($this->once())
            ->method('evict')
            ->with('apex', 1);

        $subscriber = new InvalidateThemeCacheEventSubscriber($shopCacheCleaner, $configurationCache);
        $subscriber->invalidateThemeCache(new ThemeConfigurationChangedEvent('apex', 1));
    }

    public function testSubscriberEvictsConfigurationAndClearsShopCacheOnThemeActivation(): void
    {
        $shopCacheCleaner = $this->createMock(ShopCacheCleanerInterface::class);
        $shopCacheCleaner
            ->expects($this->once())
            ->method('clear')
            ->with(1);

        $configurationCache = $this->createMock(ThemeConfigurationCacheInterface::class);
        $configurationCache
            ->expects($this->once())
            ->method('evict')
            ->with('apex', 1);

        $subscriber = new InvalidateThemeCacheEventSubscriber($shopCacheCleaner, $configurationCache);
        $subscriber->invalidateThemeCache(new ThemeActivatedEvent(1, 'apex'));
    }

    public function testSubscribesToThemeConfigurationChangedAndActivatedEvents(): void
    {
        $events = InvalidateThemeCacheEventSubscriber::getSubscribedEvents();

        $this->assertSame('invalidateThemeCache', $events[ThemeConfigurationChangedEvent::class]);
        $this->assertSame('invalidateThemeCache', $events[ThemeActivatedEvent::class]);
    }
}
