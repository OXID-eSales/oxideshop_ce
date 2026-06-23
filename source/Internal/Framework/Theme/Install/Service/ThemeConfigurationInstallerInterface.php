<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

interface ThemeConfigurationInstallerInterface
{
    public function install(string $themeSourcePath): void;

    public function uninstall(string $themeSourcePath): void;

    public function uninstallById(string $themeId): void;

    public function isInstalled(string $themeSourcePath): bool;
}
