<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\DataObject\OxidThemePackage;

readonly class BootstrapThemeInstaller implements ThemeInstallerInterface
{
    public function __construct(
        private ThemeFilesInstallerInterface $themeFilesInstaller,
        private ThemeConfigurationInstallerInterface $themeConfigurationInstaller
    ) {
    }

    public function install(OxidThemePackage $package): void
    {
        $this->themeConfigurationInstaller->install($package->getPackagePath());
        $this->themeFilesInstaller->install($package);
    }

    public function uninstall(OxidThemePackage $package): void
    {
        $this->themeConfigurationInstaller->uninstall($package->getPackagePath());
        $this->themeFilesInstaller->uninstall($package);
    }

    public function isInstalled(OxidThemePackage $package): bool
    {
        return $this->themeFilesInstaller->isInstalled($package)
            && $this->themeConfigurationInstaller->isInstalled($package->getPackagePath());
    }
}
