<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;

interface ThemeConfigurationDaoInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeConfigurationException
     */
    public function get(string $themeId, int $shopId): ThemeConfiguration;

    public function save(ThemeConfiguration $configuration, int $shopId): void;

    /** @return array<string, ThemeConfiguration> */
    public function getAll(int $shopId): array;

    public function delete(string $themeId, int $shopId): void;

    public function deleteAll(int $shopId): void;

    public function exists(string $themeId, int $shopId): bool;
}
