<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Install\DataObject;

/**
 * @internal
 */
class OxidEshopPackage
{
    /**
     * @var array
     */
    private $blackListFilters = [];

    /**
     * @var string
     */
    private $sourceDirectory = '';

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var string
     */
    private $packagePath;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param string $packagePath
     */
    public function __construct(string $name, string $packagePath)
    {
        $this->name = $name;
        $this->packagePath = $packagePath;
    }

    /**
     * @return string
     */
    public function getPackageSourcePath() : string
    {
        return !empty($this->sourceDirectory)
            ? $this->packagePath . DIRECTORY_SEPARATOR . $this->sourceDirectory
            : $this->packagePath;
    }

    /**
     * @return array
     */
    public function getBlackListFilters(): array
    {
        return $this->blackListFilters;
    }

    /**
     * @param array $filters
     */
    public function setBlackListFilters(array $filters)
    {
        $this->blackListFilters = $filters;
    }

    /**
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory ?? $this->name;
    }

    /**
     * @param string $path
     */
    public function setTargetDirectory(string $path)
    {
        $this->targetDirectory = $path;
    }

    /**
     * @return string
     */
    public function getSourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    /**
     * @param string $sourceDirectory
     */
    public function setSourceDirectory(string $sourceDirectory)
    {
        $this->sourceDirectory = $sourceDirectory;
    }

    /**
     * @return string
     */
    public function getPackagePath(): string
    {
        return $this->packagePath;
    }

    /**
     * @param string $packagePath
     */
    public function setPackagePath(string $packagePath)
    {
        $this->packagePath = $packagePath;
    }
}
