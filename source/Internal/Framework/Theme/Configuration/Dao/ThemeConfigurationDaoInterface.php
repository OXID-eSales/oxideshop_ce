<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;

interface ThemeConfigurationDaoInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     */
    public function get(string $themeId, int $shopId): ThemeConfiguration;

    public function save(ThemeConfiguration $themeConfiguration, int $shopId): void;

    public function delete(string $themeId, int $shopId): void;

    /**
     * @return ThemeConfiguration[]
     */
    public function getAll(int $shopId): array;

    public function exists(string $themeId, int $shopId): bool;
}
