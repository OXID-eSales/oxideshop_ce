<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

interface ThemeMetaDataByIdProviderInterface
{
    /**
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeMetaDataException
     */
    public function get(string $themeId, int $shopId): ThemeMetaData;
}
