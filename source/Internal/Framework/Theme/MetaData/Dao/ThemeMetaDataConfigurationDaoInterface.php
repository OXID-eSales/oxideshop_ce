<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

interface ThemeMetaDataConfigurationDaoInterface
{
    /**
     * @throws InvalidThemeMetaDataException
     */
    public function get(string $themePath): ThemeConfiguration;
}
