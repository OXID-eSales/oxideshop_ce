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
        $assetsLinkPath = $this->getAssetsLinkPath($themePath);

        $this->removeCopiedAssets($assetsLinkPath);
        $this->filesystem->symlink(
            Path::makeRelative(Path::join($themePath, self::ASSETS_DIRECTORY), Path::getDirectory($assetsLinkPath)),
            $assetsLinkPath
        );
    }

    public function uninstall(string $themePath): void
    {
        $this->filesystem->remove($this->getAssetsLinkPath($themePath));
    }

    public function isInstalled(string $themePath): bool
    {
        return is_link($this->getAssetsLinkPath($themePath));
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
