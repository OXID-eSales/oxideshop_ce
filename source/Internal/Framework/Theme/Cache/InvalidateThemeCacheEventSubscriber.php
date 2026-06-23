<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Event\FinalizingThemeActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Event\FinalizingThemeDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Event\ThemeSetupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidateThemeCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ShopCacheCleanerInterface $shopCacheCleaner)
    {
    }

    public function invalidateThemeCache(ThemeSetupEvent|ThemeConfigurationChangedEvent $event): void
    {
        $this->shopCacheCleaner->clear($event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingThemeActivationEvent::class => 'invalidateThemeCache',
            FinalizingThemeDeactivationEvent::class => 'invalidateThemeCache',
            ThemeConfigurationChangedEvent::class => 'invalidateThemeCache',
        ];
    }
}
