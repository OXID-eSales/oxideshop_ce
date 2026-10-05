<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MemoizedThemeSettingCache implements ThemeSettingCacheInterface, EventSubscriberInterface
{
    private array $settings = [];

    public function __construct(private readonly ThemeSettingCacheInterface $themeSettingCache)
    {
    }

    public function put(string $key, array $data): void
    {
        $this->themeSettingCache->put($key, $data);
        $this->settings[$key] = $data;
    }

    public function get(string $key): array
    {
        return $this->settings[$key] ??= $this->themeSettingCache->get($key);
    }

    public function forget(ClearShopCacheEvent $event): void
    {
        $this->settings = [];
    }

    public static function getSubscribedEvents(): array
    {
        return [ClearShopCacheEvent::class => 'forget'];
    }
}
