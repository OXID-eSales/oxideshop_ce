<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

interface ThemeConfigurationCacheInterface
{
    public function put(int $shopId, ThemeConfiguration $configuration): void;

    public function get(string $themeId, int $shopId): ThemeConfiguration;

    public function exists(string $themeId, int $shopId): bool;

    public function evict(string $themeId, int $shopId): void;
}
