<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;

interface ThemePathProviderInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeConfigurationException
     */
    public function getForTheme(string $themeId): string;

    /**
     * @return string[]
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeConfigurationException
     */
    public function getForInstalledThemes(): array;
}
