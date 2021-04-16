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
use Webmozart\PathUtil\Path;

class ModuleFilesInstaller implements ModuleFilesInstallerInterface
{
    /** @var Filesystem $fileSystemService */
    private $fileSystemService;

    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ModuleAssetsPathResolverInterface
     */
    private $moduleAssetsPathResolver;

    public function __construct(
        Filesystem $fileSystemService,
        ModuleConfigurationDaoInterface $moduleConfigurationDao,
        ModuleAssetsPathResolverInterface $moduleAssetsPathResolver
    ) {
        $this->fileSystemService = $fileSystemService;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->moduleAssetsPathResolver = $moduleAssetsPathResolver;
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package): void
    {
        $symlinkFile = $this->getModuleAssetsPath($package);
        $modulesAssetsDirectory = Path::getDirectory($symlinkFile);
        $relativePathToPackageAssets = Path::makeRelative(
            Path::join($package->getPackagePath(), '/assets'),
            $modulesAssetsDirectory
        );
        $this->fileSystemService->symlink(
            $relativePathToPackageAssets,
            $symlinkFile,
            true
        );
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
