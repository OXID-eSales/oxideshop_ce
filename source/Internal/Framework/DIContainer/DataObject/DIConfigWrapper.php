<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject;

use function array_key_exists;

class DIConfigWrapper
{
    private const SERVICE_SECTION = 'services';
    private const RESOURCE_KEY = 'resource';
    private const IMPORTS_SECTION = 'imports';

    private array $sectionDefaults = [self::SERVICE_SECTION => ['_defaults' => ['autowire' => true]]];

    public function __construct(private array $configArray)
    {
    }

    /**
     * @param string $importFilePath
     * @return void
     */
    public function addImport(string $importFilePath): void
    {
        $this->addSectionIfMissing(static::IMPORTS_SECTION);
        foreach ($this->getImports() as $import) {
            if ($import[static::RESOURCE_KEY] === $importFilePath) {
                return;
            }
        }
        $this->configArray[static::IMPORTS_SECTION][] = [static::RESOURCE_KEY => $importFilePath];
    }

    /**
     * @return array
     */
    public function getImportFileNames(): array
    {
        $importFileNames = [];
        foreach ($this->getImports() as $import) {
            $importFileNames[] = $import[static::RESOURCE_KEY];
        }
        return $importFileNames;
    }

    /**
     * @param string $importFilePath
     */
    public function removeImport(string $importFilePath)
    {
        $imports = [];
        foreach ($this->getImports() as $import) {
            if ($import[static::RESOURCE_KEY] !== $importFilePath) {
                $imports[] = $import;
            }
        }
        $this->configArray[static::IMPORTS_SECTION] = $imports;
    }

    /**
     * @return array
     */
    public function getConfigAsArray(): array
    {
        $this->cleanUpConfig();

        return $this->configArray;
    }

    /**
     * @return array
     */
    private function getImports(): array
    {
        if (!array_key_exists(static::IMPORTS_SECTION, $this->configArray)) {
            return [];
        }

        return $this->configArray[static::IMPORTS_SECTION];
    }

    /**
     * Removes not activated services and
     * empty import or service sections from the array
     */
    private function cleanUpConfig()
    {
        $this->removeEmptySections();
    }

    /**
     * Removes section entries when they are empty
     */
    private function removeEmptySections()
    {
        $sections = [static::IMPORTS_SECTION];
        foreach ($sections as $section) {
            if (
                array_key_exists($section, $this->configArray) &&
                (!$this->configArray[$section] || !count($this->configArray[$section]))
            ) {
                unset($this->configArray[$section]);
            }
        }
    }

    /**
     * @param string $section
     */
    private function addSectionIfMissing($section)
    {
        if (!array_key_exists($section, $this->configArray)) {
            if (array_key_exists($section, $this->sectionDefaults)) {
                $this->configArray[$section] = $this->sectionDefaults[$section];
            } else {
                $this->configArray[$section] = [];
            }
        }
    }
}
