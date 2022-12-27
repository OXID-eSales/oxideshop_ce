<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModuleAssetsPathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ModuleFilesInstaller implements ModuleFilesInstallerInterface
{
    public function __construct(
        private Filesystem $fileSystemService,
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ModuleAssetsPathResolverInterface $moduleAssetsPathResolver
    ) {
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package): void
    {
        $symlinkFile = $this->getModuleAssetsPath($package);

        if (PHP_OS_FAMILY === 'Windows') {
            // symlink() fails with relative path on windows (even with copyOnWindows-flag set)
            // mirror with absolute path instead:
            $this->fileSystemService->mirror(
                Path::join($package->getPackagePath(), '/assets'),
                $symlinkFile,
                null, [
                    'copy_on_windows' => true,
                    'override' => true,
                    'delete' => true
                ]
            );
        } else {
            $modulesAssetsDirectory = Path::getDirectory($symlinkFile);
            $relativePathToPackageAssets = Path::makeRelative(
                Path::join($package->getPackagePath(), '/assets'),
                $modulesAssetsDirectory
            );

            $this->fileSystemService->symlink($relativePathToPackageAssets, $symlinkFile);
        }
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $this->fileSystemService->remove($this->getModuleAssetsPath($package));
    }

    /**
     * @param OxidEshopPackage $package
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return is_link($this->getModuleAssetsPath($package));
    }

    private function getModuleAssetsPath(OxidEshopPackage $package): string
    {
        return $this->moduleAssetsPathResolver->getAssetsPath($this->getModuleId($package));
    }

    private function getModuleId(OxidEshopPackage $package): string
    {
        return $this
            ->moduleConfigurationDao
            ->get($package->getPackagePath())
            ->getId();
    }
}
