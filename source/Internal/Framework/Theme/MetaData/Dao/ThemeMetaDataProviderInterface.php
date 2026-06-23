<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

interface ThemeMetaDataProviderInterface
{
    /**
     * @throws InvalidThemeMetaDataException
     */
    public function getData(string $themePath): array;

    public function getMetaDataFilePath(string $themePath): string;

    public function isThemePackage(string $themePath): bool;
}
