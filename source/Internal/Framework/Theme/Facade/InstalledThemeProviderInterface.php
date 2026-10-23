<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

interface InstalledThemeProviderInterface
{
    /** @return string[] */
    public function getThemeIds(): array;
}
