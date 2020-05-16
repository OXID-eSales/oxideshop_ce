<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FinderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PathUtil\Path;

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
    public function install(OxidEshopPackage $package): void
    {
        $finder = $this->getFinder($package->getPackageSourcePath(), $package->getBlackListFilters());

        $this->fileSystemService->mirror(
            $package->getPackageSourcePath(),
            $this->getTargetPath($package),
            $finder,
            ['override' => true]
        );
    }

    /**
     * @param OxidEshopPackage $package
     */
    public function uninstall(OxidEshopPackage $package): void
    {
        $this->fileSystemService->remove($this->getTargetPath($package));
    }

    /**
     * @param OxidEshopPackage $package
     * @return bool
     */
    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->fileSystemService->exists($this->getTargetPath($package));
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
            if ($this->isDirectoryFilter($filter)) {
                $finder->notPath($this->getDirectoryForFilter($filter));
            } else {
                $finder->notName($this->normalizeFileFilter($filter));
            }
        }

        return $finder;
    }

    /**
     * @param string $filter
     * @return bool
     */
    private function isDirectoryFilter(string $filter): bool
    {
        return substr($filter, -5) === '/**/*';
    }

    /**
     * @param string $filter
     * @return string
     */
    private function getDirectoryForFilter(string $filter): string
    {
        return substr($filter, 0, -5);
    }

    /**
     * @param string $filter
     * @return string
     */
    private function normalizeFileFilter(string $filter): string
    {
        if (substr($filter, 0, 3) === '**/') {
            $filter = substr($filter, 3);
        }

        return $filter;
    }

    /**
     * @param OxidEshopPackage $package
     *
     * @return string
     */
    private function getTargetPath(OxidEshopPackage $package): string
    {
        $targetDirectory = $package->getTargetDirectory();
        return Path::join($this->context->getModulesPath(), $targetDirectory);
    }
}
