<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Exception\ThemeInheritanceException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

interface ThemeInheritanceResolverInterface
{
    /**
     * @throws ThemeInheritanceException
     * @throws ThemeConfigurationNotFoundException
     * @throws InvalidThemeMetaDataException
     */
    public function resolve(string $themeId, int $shopId): ThemeInheritance;
}
