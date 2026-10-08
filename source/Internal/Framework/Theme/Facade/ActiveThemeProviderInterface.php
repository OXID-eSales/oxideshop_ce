<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\DataObject\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeNotLoadableException;

interface ActiveThemeProviderInterface
{
    /**
     * @throws ActiveThemeNotFoundException
     * @throws ThemeNotLoadableException
     */
    public function getActiveTheme(): ActiveTheme;
}
