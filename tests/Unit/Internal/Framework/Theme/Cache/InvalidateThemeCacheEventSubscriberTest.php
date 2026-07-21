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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
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

        $configuration = (new ThemeConfiguration())->setId('apex');
        $event = new ThemeConfigurationChangedEvent($configuration, 1);

        $subscriber = new InvalidateThemeCacheEventSubscriber($shopCacheCleaner, $configurationCache);
        $subscriber->invalidateThemeCache($event);
    }

    public function testSubscribesToThemeConfigurationChangedEvent(): void
    {
        $events = InvalidateThemeCacheEventSubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(ThemeConfigurationChangedEvent::class, $events);
        $this->assertSame('invalidateThemeCache', $events[ThemeConfigurationChangedEvent::class]);
    }
}
