<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeInheritanceException;

interface ThemeParentCompatibilityCheckerInterface
{
    /**
     * @throws ThemeInheritanceException
     * @throws ThemeConfigurationNotFoundException
     */
    public function validateCompatibility(string $themeId, string $parentThemeId, int $shopId): void;
}
