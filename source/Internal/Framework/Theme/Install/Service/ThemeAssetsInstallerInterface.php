<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;

interface ThemeAssetsInstallerInterface
{
    /** @throws InvalidThemeMetaDataException */
    public function install(string $themePath): void;

    /** @throws InvalidThemeMetaDataException */
    public function uninstall(string $themePath): void;

    /** @throws InvalidThemeMetaDataException */
    public function isInstalled(string $themePath): bool;
}
