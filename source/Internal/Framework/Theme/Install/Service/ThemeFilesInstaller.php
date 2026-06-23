<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\DataObject\OxidThemePackage;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Dao\ThemeMetaDataConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ThemeFilesInstaller implements ThemeFilesInstallerInterface
{
    private const ASSETS_DIRECTORY = 'out';

    public function __construct(
        private Filesystem $fileSystemService,
        private ThemeMetaDataConfigurationDaoInterface $metadataThemeConfigurationDao,
        private BasicContextInterface $context
    ) {
    }

    public function install(OxidThemePackage $package): void
    {
        $this->link($this->getPackageAssetsPath($package), $this->getThemeAssetsPath($package));
    }

    public function uninstall(OxidThemePackage $package): void
    {
        $this->fileSystemService->remove($this->getThemeAssetsPath($package));
    }

    public function isInstalled(OxidThemePackage $package): bool
    {
        return is_link($this->getThemeAssetsPath($package));
    }

    private function link(string $source, string $target): void
    {
        if ($this->fileSystemService->exists($target) && !is_link($target)) {
            $this->fileSystemService->remove($target);
        }

        $this->fileSystemService->symlink(
            Path::makeRelative($source, Path::getDirectory($target)),
            $target,
            true
        );
    }

    private function getThemeId(OxidThemePackage $package): string
    {
        return $this->metadataThemeConfigurationDao->get($package->getPackagePath())->getId();
    }

    private function getThemeAssetsPath(OxidThemePackage $package): string
    {
        return Path::join($this->context->getOutPath(), $this->getThemeId($package));
    }

    private function getPackageAssetsPath(OxidThemePackage $package): string
    {
        return Path::join($package->getPackagePath(), self::ASSETS_DIRECTORY, $this->getThemeId($package));
    }
}
