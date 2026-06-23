<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

class ClassPropertyThemeConfigurationCache implements ThemeConfigurationCacheInterface
{
    /**
     * @var ThemeConfiguration[][]
     */
    private array $cache = [];

    public function put(int $shopId, ThemeConfiguration $configuration): void
    {
        $this->cache[$shopId][$configuration->getId()] = $configuration;
    }

    public function get(string $themeId, int $shopId): ThemeConfiguration
    {
        return $this->cache[$shopId][$themeId];
    }

    public function exists(string $themeId, int $shopId): bool
    {
        return isset($this->cache[$shopId][$themeId]);
    }

    public function evict(string $themeId, int $shopId): void
    {
        if ($this->exists($themeId, $shopId)) {
            unset($this->cache[$shopId][$themeId]);
        }
    }
}
