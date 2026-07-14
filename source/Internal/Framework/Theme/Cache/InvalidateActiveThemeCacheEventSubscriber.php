<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache\ActiveThemeCacheInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class InvalidateActiveThemeCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ActiveThemeCacheInterface $activeThemeCache,
    ) {
    }

    public function invalidateActiveThemeCache(ThemeActivatedEvent|ThemeConfigurationChangedEvent $event): void
    {
        $this->activeThemeCache->evict($event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ThemeActivatedEvent::class => 'invalidateActiveThemeCache',
            ThemeConfigurationChangedEvent::class => 'invalidateActiveThemeCache',
        ];
    }
}
