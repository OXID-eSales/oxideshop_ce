<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Provider\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ThemeAssetsInstaller implements ThemeAssetsInstallerInterface
{
    private const ASSETS_DIRECTORY = 'assets';

    public function __construct(
        private Filesystem $filesystem,
        private ThemeMetaDataProviderInterface $metaDataProvider,
        private BasicContextInterface $context,
    ) {
    }

    public function install(string $themePath): void
    {
        if (!$this->hasAssets($themePath)) {
            return;
        }

        $assetsLinkPath = $this->getAssetsLinkPath($themePath);

        $this->removeCopiedAssets($assetsLinkPath);
        $this->filesystem->symlink(
            Path::makeRelative($this->getAssetsPath($themePath), Path::getDirectory($assetsLinkPath)),
            $assetsLinkPath,
            true
        );
    }

    public function uninstall(string $themePath): void
    {
        $this->filesystem->remove($this->getAssetsLinkPath($themePath));
    }

    public function isInstalled(string $themePath): bool
    {
        $assetsLinkPath = $this->getAssetsLinkPath($themePath);

        return is_link($assetsLinkPath) && is_dir($assetsLinkPath);
    }

    private function hasAssets(string $themePath): bool
    {
        return is_dir($this->getAssetsPath($themePath));
    }

    private function getAssetsPath(string $themePath): string
    {
        return Path::join($themePath, self::ASSETS_DIRECTORY);
    }

    private function removeCopiedAssets(string $assetsLinkPath): void
    {
        if (!is_link($assetsLinkPath) && $this->filesystem->exists($assetsLinkPath)) {
            $this->filesystem->remove($assetsLinkPath);
        }
    }

    private function getAssetsLinkPath(string $themePath): string
    {
        return Path::join($this->context->getOutPath(), $this->metaDataProvider->get($themePath)->getId());
    }
}
