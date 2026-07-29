<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Exception\ThemeConfigurationInstallException;

interface ThemeConfigurationInstallerInterface
{
    /** @throws ThemeConfigurationInstallException */
    public function install(string $themePath): void;

    /** @throws ThemeConfigurationInstallException */
    public function uninstall(string $themePath): void;

    public function isInstalled(string $themePath): bool;
}
