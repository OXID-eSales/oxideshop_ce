<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\InvalidateActiveThemeCacheEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache\ActiveThemeCacheInterface;
use PHPUnit\Framework\TestCase;

final class InvalidateActiveThemeCacheEventSubscriberTest extends TestCase
{
    public function testInvalidateActiveThemeCacheEvictsCacheForThemeActivatedEvent(): void
    {
        $activeThemeCache = $this->createMock(ActiveThemeCacheInterface::class);
        $activeThemeCache->expects($this->once())->method('evict')->with(1);

        $subscriber = new InvalidateActiveThemeCacheEventSubscriber($activeThemeCache);
        $subscriber->invalidateActiveThemeCache(new ThemeActivatedEvent(1, 'apex'));
    }

    public function testInvalidateActiveThemeCacheEvictsCacheForThemeConfigurationChangedEvent(): void
    {
        $activeThemeCache = $this->createMock(ActiveThemeCacheInterface::class);
        $activeThemeCache->expects($this->once())->method('evict')->with(1);

        $subscriber = new InvalidateActiveThemeCacheEventSubscriber($activeThemeCache);
        $subscriber->invalidateActiveThemeCache(
            new ThemeConfigurationChangedEvent((new ThemeConfiguration())->setId('apex'), 1)
        );
    }

    public function testSubscribesToThemeActivatedAndThemeConfigurationChangedEvents(): void
    {
        $this->assertSame(
            [
                ThemeActivatedEvent::class => 'invalidateActiveThemeCache',
                ThemeConfigurationChangedEvent::class => 'invalidateActiveThemeCache',
            ],
            InvalidateActiveThemeCacheEventSubscriber::getSubscribedEvents()
        );
    }
}
