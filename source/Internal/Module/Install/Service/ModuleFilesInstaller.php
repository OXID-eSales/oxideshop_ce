<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Common\FileSystem\FinderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\DataObject\OxidEshopPackage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PathUtil\Path;

/**
 * Class ModuleFilesInstaller
 *
 * @internal
 */
class ModuleFilesInstaller implements ModuleFilesInstallerInterface
{
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
     * @param BasicContextInterface  $context
     * @param Filesystem             $fileSystemService
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(
        BasicContextInterface $context,
        Filesystem $fileSystemService,
        FinderFactoryInterface $finderFactory
    ) {
        $this->context = $context;
        $this->fileSystemService = $fileSystemService;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function install(OxidEshopPackage $package)
    {
        $sourceDirectory = $this->getSourcePath($package);
        $finder = $this->getFinder($sourceDirectory, $package->getBlackListFilters());

        $this->fileSystemService->mirror(
            $sourceDirectory,
            $this->getTargetPath($package),
            $finder,
            ['override' => true]
        );
    }

    /**
     * @param OxidEshopPackage $package
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return file_exists($this->getTargetPath($package));
    }

    /**
     * @param string $sourceDirectory
     * @param array  $blackListFilters
     * @return Finder
     */
    private function getFinder(string $sourceDirectory, array $blackListFilters): Finder
    {
        $finder = $this->finderFactory->create();
        $finder->in($sourceDirectory);

        foreach ($blackListFilters as $filter) {
            $finder->notName($filter);
        }

        return $finder;
    }

    /**
     * If module source directory option provided add it's relative path.
     * Otherwise return plain package path.
     *
     * @param OxidEshopPackage $package
     *
     * @return string
     */
    private function getSourcePath(OxidEshopPackage $package) : string
    {
        return !empty($package->getSourceDirectory())
            ? Path::join($package->getPackagePath(), $package->getSourceDirectory())
            : $package->getPackagePath();
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
