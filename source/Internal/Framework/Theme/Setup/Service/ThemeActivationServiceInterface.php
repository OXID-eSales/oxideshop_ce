<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;

interface ThemeActivationServiceInterface
{
    /** @throws ThemeConfigurationNotFoundException */
    public function activate(string $themeId, int $shopId): void;
}
