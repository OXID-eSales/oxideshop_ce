<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InMemoryActiveThemeCache implements ActiveThemeCacheInterface, EventSubscriberInterface
{
    private array $activeThemes = [];

    public function __construct(private readonly ActiveThemeCacheInterface $activeThemeCache)
    {
    }

    public function put(int $shopId, ActiveTheme $activeTheme): void
    {
        $this->activeThemeCache->put($shopId, $activeTheme);
        $this->activeThemes[$shopId] = $activeTheme;
    }

    public function get(int $shopId): ActiveTheme
    {
        return $this->activeThemes[$shopId] ??= $this->activeThemeCache->get($shopId);
    }

    public function forget(ClearShopCacheEvent $event): void
    {
        unset($this->activeThemes[$event->getShopId()]);
    }

    public static function getSubscribedEvents(): array
    {
        return [ClearShopCacheEvent::class => 'forget'];
    }
}
