<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

interface ThemeParentCompatibilityCheckerInterface
{
    /**
     * @throws ThemeParentCompatibilityException
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeConfigurationException
     * @throws InvalidThemeMetaDataException
     */
    public function validate(string $themeId, int $shopId): void;
}
