<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\CustomThemeNotFoundException;

interface CustomThemeProviderInterface
{
    public function hasCustomTheme(int $shopId): bool;

    /** @throws CustomThemeNotFoundException */
    public function getCustomThemeId(int $shopId): string;
}
