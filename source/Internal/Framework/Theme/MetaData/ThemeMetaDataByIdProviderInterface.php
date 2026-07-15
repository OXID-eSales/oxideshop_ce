<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

interface ThemeMetaDataByIdProviderInterface
{
    public function get(string $themeId, int $shopId): ThemeMetaData;
}
