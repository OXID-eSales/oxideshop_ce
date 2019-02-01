<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install;

use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryExistentException;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Common\CopyGlob\CopyGlobServiceInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\PathUtil\Path;

/**
 * Class ModuleCopyService
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\Setup\Install
 */
class ModuleCopyService implements ModuleCopyServiceInterface
{
    /** @var $packageService */
    private $packageService;

    /** @var BasicContextInterface $context */
    private $context;

    /** @var CopyGlobServiceInterface $fileSystemService */
    private $fileSystemService;

    /**
     * ModuleCopyService constructor.
     *
     * @param OxidEshopPackageFactoryInterface $packageService
     * @param BasicContextInterface            $basicContext
     * @param Filesystem                       $fileSystemService
     */
    public function __construct(
        OxidEshopPackageFactoryInterface $packageService,
        BasicContextInterface $basicContext,
        Filesystem $fileSystemService
    ) {
        $this->packageService = $packageService;
        $this->context = $basicContext;
        $this->fileSystemService = $fileSystemService;
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
            $package = $this->packageService->getPackage($packagePath);
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
    private function isInstalled(string $packagePath): bool
    {
        $package = $this->packageService->getPackage($packagePath);
        return file_exists($this->getTargetPath($package));
    }

    /**
     * @param string $packagePath
     */
    private function copyFiles(string $packagePath)
    {
        $package = $this->packageService->getPackage($packagePath);

        $sourceDirectory = $this->getSourcePath($packagePath, $package);

        $finder = new Finder();
        $finder
            ->in($sourceDirectory)
            ->notName($package->getBlackListFilters());

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
