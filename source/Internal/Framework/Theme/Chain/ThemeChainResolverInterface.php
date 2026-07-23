<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\Exception\ThemeInheritanceCycleException;

interface ThemeChainResolverInterface
{
    /** @throws ThemeInheritanceCycleException */
    public function getThemeChain(string $themeId, int $shopId): ThemeChain;
}