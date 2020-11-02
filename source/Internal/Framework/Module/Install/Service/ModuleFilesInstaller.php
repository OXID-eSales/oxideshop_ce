<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FinderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Exception\TwoStarsWithinBlacklistFilterException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PathUtil\Path;

class ModuleFilesInstaller implements ModuleFilesInstallerInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var Filesystem
     */
    private $fileSystemService;

    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * ModuleFilesInstaller constructor.
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

    public function install(OxidEshopPackage $package): void
    {
        $finder = $this->getFinder($package->getPackageSourcePath(), $package->getBlackListFilters());

        $this->fileSystemService->mirror(
            $package->getPackageSourcePath(),
            $this->getTargetPath($package),
            $finder,
            [
                'override' => true,
            ]
        );
    }

    public function uninstall(OxidEshopPackage $package): void
    {
        $this->fileSystemService->remove($this->getTargetPath($package));
    }

    public function isInstalled(OxidEshopPackage $package): bool
    {
        return $this->fileSystemService->exists($this->getTargetPath($package));
    }

    /**
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
     * @throws TwoStarsWithinBlacklistFilterException
     */
    private function checkTwoStars(string $filter): void
    {
        if (false !== strpos($filter, '**')) {
            throw new TwoStarsWithinBlacklistFilterException("Invalid 'blacklist-filter' value in composer.json. " . "Glob patterns (**) are not allowed here: $filter");
        }
    }

    private function isAFilenameInTheRootOfModule(string $filter): bool
    {
        return Path::hasExtension($filter) && !Path::getDirectory($filter);
    }

    private function getTargetPath(OxidEshopPackage $package): string
    {
        $targetDirectory = $package->getTargetDirectory();

        return Path::join($this->context->getModulesPath(), $targetDirectory);
    }
}
