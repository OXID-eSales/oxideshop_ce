<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\DataObject\OxidThemePackage;

interface ThemeInstallerInterface
{
    public function install(OxidThemePackage $package): void;

    public function uninstall(OxidThemePackage $package): void;

    public function isInstalled(OxidThemePackage $package): bool;
}
