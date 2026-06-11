<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData;

interface ThemeMetaDataProviderInterface
{
    /** @throws \InvalidArgumentException */
    public function get(string $themePath): ThemeMetaData;
}
