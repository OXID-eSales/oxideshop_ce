<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FinderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Exception\TwoStarsWithinBlacklistFilterException;
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
     *
     * @return Finder
     * @throws TwoStarsWithinBlacklistFilterException
     */
    private function getFinder(string $sourceDirectory, array $blackListFilters): Finder
    {
        $finder = $this->finderFactory->create();
        $finder->in($sourceDirectory);

        foreach ($blackListFilters as $filter) {
            $this->checkTwoStars($filter);

            if ($this->isAFilenameInTheRootOfModule($filter)) {
                $finder->notName($filter);
            } else {
                $finder->notPath($filter);
            }
        }

        return $finder;
    }

    /**
     * @param string $filter
     *
     * @throws TwoStarsWithinBlacklistFilterException
     */
    private function checkTwoStars(string $filter): void
    {
        if (\strpos($filter, '**') !== false) {
            throw new TwoStarsWithinBlacklistFilterException(
                "Invalid 'blacklist-filter' value in composer.json. "
                . "Glob patterns (**) are not allowed here: $filter"
            );
        }
    }

    /**
     * @param string $filter
     *
     * @return bool
     */
    private function isAFilenameInTheRootOfModule(string $filter): bool
    {
        return Path::hasExtension($filter) && !Path::getDirectory($filter);
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
