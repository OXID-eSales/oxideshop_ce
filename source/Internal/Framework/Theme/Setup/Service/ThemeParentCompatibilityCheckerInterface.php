<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentCompatibilityException;

interface ThemeParentCompatibilityCheckerInterface
{
    /**
     * @throws ThemeParentCompatibilityException
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeMetaDataException
     */
    public function validate(string $themeId, string $parentThemeId, int $shopId): void;
}
