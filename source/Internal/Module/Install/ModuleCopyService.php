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

    /** @var CopyGlobServiceInterface $copyGlobService */
    private $copyGlobService;

    /**
     * ModuleCopyService constructor.
     *
     * @param OxidEshopPackageFactoryInterface $packageService
     * @param BasicContextInterface            $basicContext
     * @param CopyGlobServiceInterface         $copyGlobService
     */
    public function __construct(
        OxidEshopPackageFactoryInterface $packageService,
        BasicContextInterface $basicContext,
        CopyGlobServiceInterface $copyGlobService
    ) {
        $this->packageService = $packageService;
        $this->context = $basicContext;
        $this->copyGlobService = $copyGlobService;
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

            $exception = new DirectoryExistentException($this->formTargetPath($package));
            throw $exception;
        }

        $this->triggerCopyGlobService($packagePath);
    }

    /**
     * @param string $packagePath
     */
    public function forceCopy(string $packagePath)
    {
        $this->triggerCopyGlobService($packagePath);
    }

    /**
     * @param string $packagePath
     *
     * @return bool
     */
    private function isInstalled(string $packagePath): bool
    {
        $package = $this->packageService->getPackage($packagePath);
        return file_exists($this->formTargetPath($package));
    }

    /**
     * @param string $packagePath
     */
    private function triggerCopyGlobService(string $packagePath)
    {
        $package = $this->packageService->getPackage($packagePath);

        $filtersToApply = [
            $this->getBlacklistFilterValue($package),
            $this->getVCSFilter(),
        ];

        $this->copyGlobService->copy(
            $this->formSourcePath($packagePath, $package),
            $this->formTargetPath($package),
            $this->getCombinedFilters($filtersToApply)
        );
    }

    /**
     * Return the value defined in composer extra parameters for blacklist filtering.
     *
     * @param OxidEshopPackage $package
     *
     * @return array
     */
    private function getBlacklistFilterValue(OxidEshopPackage $package) : array
    {
        $extra = $package->getExtraParameters();
        return $extra[OxidEshopPackage::EXTRA_PARAMETER_KEY_ROOT][OxidEshopPackage::EXTRA_PARAMETER_FILTER_BLACKLIST] ?? [];
    }

    /**
     * Get VCS glob filter expression
     *
     * @return array
     */
    private function getVCSFilter() : array
    {
        return [OxidEshopPackage::BLACKLIST_VCS_DIRECTORY_FILTER, OxidEshopPackage::BLACKLIST_VCS_IGNORE_FILE];
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
    private function formSourcePath(string $packagePath, OxidEshopPackage $package) : string
    {
        $extra = $package->getExtraParameters();
        $sourceDirectory = $extra[OxidEshopPackage::EXTRA_PARAMETER_KEY_ROOT][OxidEshopPackage::EXTRA_PARAMETER_KEY_SOURCE] ?? '';

        return !empty($sourceDirectory)?
            Path::join($packagePath, $sourceDirectory):
            $packagePath;
    }

    /**
     * @param OxidEshopPackage $package
     *
     * @return string
     */
    private function formTargetPath(OxidEshopPackage $package) : string
    {
        $extra = $package->getExtraParameters();
        $targetDirectory = $extra[OxidEshopPackage::EXTRA_PARAMETER_KEY_ROOT][OxidEshopPackage::EXTRA_PARAMETER_KEY_TARGET] ?? $package->getName();

        return Path::join($this->context->getModulesPath(), $targetDirectory);
    }

    /**
     * Combine multiple glob expression lists into one list
     *
     * @param array $listOfGlobExpressionLists E.g. [["*.txt", "*.pdf"], ["*.md"]]
     *
     * @return array
     */
    private function getCombinedFilters(array $listOfGlobExpressionLists) : array
    {
        $filters = [];
        foreach ($listOfGlobExpressionLists as $filter) {
            $filters = array_merge($filters, $filter);
        }

        return $filters;
    }
}
