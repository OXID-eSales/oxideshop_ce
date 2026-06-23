<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge;

interface ThemeViewItemFactoryInterface
{
    /**
     * @return ThemeViewItem[]
     */
    public function getAll(int $shopId): array;

    public function get(string $themeId, int $shopId): ?ThemeViewItem;
}
