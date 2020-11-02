<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject;

class OxidEshopPackage
{
    /**
     * If blacklist-filter is given, it will be used to filter out unwanted files and directories while the copy from
     * source-directory to target-directory takes place.
     *
     * @var array
     */
    private $blackListFilters = [];

    /**
     * If source-directory is given, the value defines which directory will be used to define where the files
     * and directories will be picked from. When the parameter is not given, the root directory of the module is used
     * instead.
     *
     * @var string
     */
    private $sourceDirectory = '';

    /**
     * target-directory value will be used to create a folder inside the Shop modules directory.
     * This folder will be used to place all files of the module.
     *
     * @var string
     */
    private $targetDirectory;

    /**
     * Package path is the absolute path to the root directory, e.g. /var/www/oxideshop/vendor/oxid-esales/paypal-module.
     *
     * @var string
     */
    private $packagePath;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $name, string $packagePath)
    {
        $this->name = $name;
        $this->packagePath = $packagePath;
    }

    public function getPackageSourcePath(): string
    {
        return !empty($this->sourceDirectory)
            ? $this->packagePath . \DIRECTORY_SEPARATOR . $this->sourceDirectory
            : $this->packagePath;
    }

    public function getBlackListFilters(): array
    {
        return $this->blackListFilters;
    }

    public function setBlackListFilters(array $filters): void
    {
        $this->blackListFilters = $filters;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory ?? $this->name;
    }

    public function setTargetDirectory(string $path): void
    {
        $this->targetDirectory = $path;
    }

    public function getSourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    public function setSourceDirectory(string $sourceDirectory): void
    {
        $this->sourceDirectory = $sourceDirectory;
    }

    public function getPackagePath(): string
    {
        return $this->packagePath;
    }
}
