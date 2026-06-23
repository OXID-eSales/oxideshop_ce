<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

interface ThemeEnvironmentConfigurationDaoInterface
{
    public function get(string $themeId, int $shopId): array;

    public function remove(string $themeId, int $shopId): void;
}
