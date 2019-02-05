<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryExistentException;
use OxidEsales\EshopCommunity\Internal\Common\FileSystem\FinderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\Dao\OxidEshopPackageDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

/**
 * Class ModuleFilesInstaller
 *
 * @internal
 */
class ModuleFilesInstaller implements ModuleFilesInstallerInterface
{
    /**
     * @var OxidEshopPackageDaoInterface
     */
    private $packageDao;

    /** @var BasicContextInterface $context */
    private $context;

    /** @var Filesystem $fileSystemService */
    private $fileSystemService;

    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * ModuleFilesInstaller constructor.
     * @param OxidEshopPackageDaoInterface $packageDao
     * @param BasicContextInterface        $context
     * @param Filesystem                   $fileSystemService
     * @param FinderFactoryInterface       $finderFactory
     */
    public function __construct(
        OxidEshopPackageDaoInterface $packageDao,
        BasicContextInterface $context,
        Filesystem $fileSystemService,
        FinderFactoryInterface $finderFactory
    ) {
        $this->packageDao = $packageDao;
        $this->context = $context;
        $this->fileSystemService = $fileSystemService;
        $this->finderFactory = $finderFactory;
    }


    /**
     * Copies from vendor directory to source/modules directory respecting the blacklist filters given by the module.
     *
     * @param string $packagePath Path to the package like /var/www/vendor/oxid-esales/paypal
     *
     * @throws DirectoryExistentException
     */
    public function copy(string $packagePath)
    {
        if ($this->isInstalled($packagePath)) {
            $package = $this->packageDao->getPackage($packagePath);
            throw new DirectoryExistentException($this->getTargetPath($package));
        }

        $this->copyFiles($packagePath);
    }

    /**
     * @param string $packagePath
     */
    public function forceCopy(string $packagePath)
    {
        $this->copyFiles($packagePath);
    }

    /**
     * @param string $packagePath
     *
     * @return bool
     */
    public function isInstalled(string $packagePath): bool
    {
        $package = $this->packageDao->getPackage($packagePath);
        return file_exists($this->getTargetPath($package));
    }

    /**
     * @param string $packagePath
     */
    private function copyFiles(string $packagePath)
    {
        $package = $this->packageDao->getPackage($packagePath);

        $sourceDirectory = $this->getSourcePath($packagePath, $package);

        $finder = $this->finderFactory->create();
        $finder->in($sourceDirectory);

        foreach ($package->getBlackListFilters() as $filter) {
            $finder->notName($filter);
        }

        $this->fileSystemService->mirror(
            $sourceDirectory,
            $this->getTargetPath($package),
            $finder,
            ['override' => true]
        );
    }

    /**
     * If module source directory option provided add it's relative path.
     * Otherwise return plain package path.
     *
     * @param string           $packagePath
     * @param OxidEshopPackage $package
     *
     * @return string
     */
    private function getSourcePath(string $packagePath, OxidEshopPackage $package) : string
    {
        return !empty($package->getSourceDirectory())
            ? Path::join($packagePath, $package->getSourceDirectory())
            : $packagePath;
    }

    /**
     * @param OxidEshopPackage $package
     *
     * @return string
     */
    private function getTargetPath(OxidEshopPackage $package) : string
    {
        $targetDirectory = $package->getTargetDirectory();
        return Path::join($this->context->getModulesPath(), $targetDirectory);
    }
}
