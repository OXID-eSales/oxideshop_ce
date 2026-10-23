<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

interface ThemeAssetsInstallerInterface
{
    public function install(string $themePath): void;

    public function uninstall(string $themePath): void;

    public function isInstalled(string $themePath): bool;
}
