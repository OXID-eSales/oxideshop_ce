<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ParentThemeNotFoundException;

interface ThemeParentProviderInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     * @throws \InvalidArgumentException
     * @throws InvalidThemeMetaDataException
     */
    public function hasParentTheme(string $themeId, int $shopId): bool;

    /**
     * @throws ParentThemeNotFoundException
     * @throws ThemeConfigurationNotFoundException
     * @throws \InvalidArgumentException
     * @throws InvalidThemeMetaDataException
     */
    public function getParentThemeId(string $themeId, int $shopId): string;
}
