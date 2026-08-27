<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;

interface ThemeStateServiceInterface
{
    public function isActive(string $themeId, int $shopId): bool;

    /** @throws ActiveThemeNotFoundException */
    public function getActiveThemeId(int $shopId): string;

    /**
     * Parent of the active (child) theme, or the active theme itself when standalone.
     *
     * @throws ActiveThemeNotFoundException
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeMetaDataException
     */
    public function getBaseThemeId(int $shopId): string;
}
