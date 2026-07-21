<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ThemeConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class InvalidateThemeCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ShopCacheCleanerInterface $shopCacheCleaner,
        private ThemeConfigurationCacheInterface $resolvedConfigurationCache,
    ) {
    }

    public function invalidateThemeCache(ThemeConfigurationChangedEvent $event): void
    {
        $this->resolvedConfigurationCache->evict($event->getThemeId(), $event->getShopId());
        $this->shopCacheCleaner->clear($event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ThemeConfigurationChangedEvent::class => 'invalidateThemeCache',
        ];
    }
}
