<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

interface ThemeOverrideDirectoryResolverInterface
{
    /** @return string[] */
    public function getOverrideDirectories(string $themeId, int $shopId): array;
}
