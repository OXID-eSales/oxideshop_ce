<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class InvalidateActiveThemeCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ActiveThemeCacheInterface $activeThemeCache,
    ) {
    }

    public function invalidateActiveThemeCache(ThemeActivatedEvent $event): void
    {
        $this->activeThemeCache->evict($event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ThemeActivatedEvent::class => 'invalidateActiveThemeCache',
        ];
    }
}
