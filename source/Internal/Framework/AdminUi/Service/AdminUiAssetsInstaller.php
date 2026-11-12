<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Provider\AdminUiDirectoryProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class AdminUiAssetsInstaller implements AdminUiAssetsInstallerInterface
{
    private const ASSETS_DIRECTORY = 'assets';

    public function __construct(
        private Filesystem $filesystem,
        private AdminUiDirectoryProviderInterface $directoryProvider,
        private BasicContextInterface $context,
        private string $adminUiName,
    ) {
    }

    public function install(): void
    {
        if (!$this->hasAssets()) {
            return;
        }

        $assetsLinkPath = $this->getAssetsLinkPath();

        $this->removeCopiedAssets($assetsLinkPath);
        $this->filesystem->symlink(
            Path::makeRelative($this->getAssetsPath(), Path::getDirectory($assetsLinkPath)),
            $assetsLinkPath,
            true
        );
    }

    public function uninstall(): void
    {
        $this->filesystem->remove($this->getAssetsLinkPath());
    }

    public function isInstalled(): bool
    {
        $assetsLinkPath = $this->getAssetsLinkPath();

        return is_link($assetsLinkPath) && is_dir($assetsLinkPath);
    }

    private function hasAssets(): bool
    {
        return is_dir($this->getAssetsPath());
    }

    private function getAssetsPath(): string
    {
        return Path::join($this->directoryProvider->getDirectory(), self::ASSETS_DIRECTORY);
    }

    private function removeCopiedAssets(string $assetsLinkPath): void
    {
        if (!is_link($assetsLinkPath) && $this->filesystem->exists($assetsLinkPath)) {
            $this->filesystem->remove($assetsLinkPath);
        }
    }

    private function getAssetsLinkPath(): string
    {
        return Path::join($this->context->getOutPath(), $this->adminUiName);
    }
}
