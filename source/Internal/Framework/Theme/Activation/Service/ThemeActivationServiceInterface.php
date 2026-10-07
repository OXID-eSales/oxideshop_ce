<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ThemeParentCompatibilityException;

interface ThemeActivationServiceInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeConfigurationException
     * @throws InvalidThemeMetaDataException
     * @throws ThemeParentCompatibilityException
     */
    public function activate(string $themeId, int $shopId): void;
}
