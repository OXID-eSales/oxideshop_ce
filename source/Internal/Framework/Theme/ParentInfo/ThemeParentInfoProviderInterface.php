<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\ParentInfo;

interface ThemeParentInfoProviderInterface
{
    public function getByTheme(string $themeId, int $shopId): ThemeParentInfo;
}
