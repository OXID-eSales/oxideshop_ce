<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

interface ThemeConfigurationExtenderInterface
{
    public function extend(ThemeConfiguration $themeConfiguration, int $shopId): ThemeConfiguration;
}
