<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeEnvironmentConfiguration;

interface ThemeEnvironmentConfigurationDaoInterface
{
    public function get(string $themeId, int $shopId): ThemeEnvironmentConfiguration;
}
