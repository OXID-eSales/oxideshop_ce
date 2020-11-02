<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\MissingServiceException;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\SystemServiceOverwriteException;
use Psr\Container\ContainerInterface;

class DIConfigWrapper
{
    private const SERVICE_SECTION = 'services';
    private const RESOURCE_KEY = 'resource';
    private const IMPORTS_SECTION = 'imports';

    private $sectionDefaults = [
        self::SERVICE_SECTION => [
            '_defaults' => [
                'public' => false,
                'autowire' => true,
            ],
        ],
    ];

    /**
     * @var array
     */
    private $configArray;

    /**
     * DIConfigWrapper constructor.
     */
    public function __construct(array $configArray)
    {
        $this->configArray = $configArray;
    }

    public function addImport(string $importFilePath): void
    {
        $this->addSectionIfMissing(static::IMPORTS_SECTION);
        foreach ($this->getImports() as $import) {
            if ($import[static::RESOURCE_KEY] === $importFilePath) {
                return;
            }
        }
        $this->configArray[static::IMPORTS_SECTION][] = [
            static::RESOURCE_KEY => $importFilePath,
        ];
    }

    public function getImportFileNames(): array
    {
        $importFileNames = [];
        foreach ($this->getImports() as $import) {
            $importFileNames[] = $import[static::RESOURCE_KEY];
        }

        return $importFileNames;
    }

    public function removeImport(string $importFilePath): void
    {
        $imports = [];
        foreach ($this->getImports() as $import) {
            if ($import[static::RESOURCE_KEY] !== $importFilePath) {
                $imports[] = $import;
            }
        }
        $this->configArray[static::IMPORTS_SECTION] = $imports;
    }

    public function hasService(string $serviceKey): bool
    {
        try {
            $this->getService($serviceKey);
        } catch (MissingServiceException $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws MissingServiceException
     */
    public function getService(string $serviceKey): DIServiceWrapper
    {
        /** @var DIServiceWrapper $service */
        foreach ($this->getServices() as $service) {
            if ($service->getKey() === $serviceKey) {
                return $service;
            }
        }
        throw new MissingServiceException("Service $serviceKey not found");
    }

    public function addOrUpdateService(DIServiceWrapper $service): void
    {
        $this->addSectionIfMissing(static::SERVICE_SECTION);
        $this->configArray[static::SERVICE_SECTION][$service->getKey()] = $service->getServiceAsArray();
    }

    public function getConfigAsArray(): array
    {
        $this->cleanUpConfig();

        return $this->configArray;
    }

    /**
     * @throws SystemServiceOverwriteException
     */
    public function checkServices(ContainerInterface $container): void
    {
        /** @var DIServiceWrapper $service */
        foreach ($this->getServices() as $service) {
            if ($container->has($service->getKey())) {
                throw new SystemServiceOverwriteException($service->getKey() . ' is already defined');
            }
        }
    }

    /**
     * Checks that the service classes configured for a
     * module / package are really usable. It may happen
     * that module code is still in the modules directory
     * but is removed from autoloading. In this case, the
     * services.yaml file should not be evaluated in any
     * way.
     */
    public function checkServiceClassesCanBeLoaded(): bool
    {
        foreach ($this->getServices() as $service) {
            if (!$service->checkClassExists()) {
                return false;
            }
        }

        return true;
    }

    private function getImports(): array
    {
        if (!\array_key_exists(static::IMPORTS_SECTION, $this->configArray)) {
            return [];
        }

        return $this->configArray[static::IMPORTS_SECTION];
    }

    /**
     * @return DIServiceWrapper[]
     */
    public function getServices(): array
    {
        if (!\array_key_exists(static::SERVICE_SECTION, $this->configArray)) {
            return [];
        }
        $services = [];
        foreach ($this->configArray[$this::SERVICE_SECTION] as $serviceId => $serviceArguments) {
            $services[] = new DIServiceWrapper($serviceId, $serviceArguments ?? []);
        }

        return $services;
    }

    /**
     * Removes not activated services and
     * empty import or service sections from the array.
     */
    private function cleanUpConfig(): void
    {
        $this->removeInactiveServices();
        $this->removeEmptySections();
    }

    /**
     * Removes section entries when they are empty.
     */
    private function removeEmptySections(): void
    {
        $sections = [static::IMPORTS_SECTION, static::SERVICE_SECTION];
        foreach ($sections as $section) {
            if (
                \array_key_exists($section, $this->configArray) &&
                (!$this->configArray[$section] || !\count($this->configArray[$section]))
            ) {
                unset($this->configArray[$section]);
            }
        }
    }

    private function removeInactiveServices(): void
    {
        /** @var DIServiceWrapper $service */
        foreach ($this->getServices() as $service) {
            if ($service->isShopAware() && !$service->hasActiveShops()) {
                $this->removeService($service);
            }
        }
    }

    private function removeService(DIServiceWrapper $service): void
    {
        unset($this->configArray['services'][$service->getKey()]);
    }

    /**
     * @param string $section
     */
    private function addSectionIfMissing($section): void
    {
        if (!\array_key_exists($section, $this->configArray)) {
            if (\array_key_exists($section, $this->sectionDefaults)) {
                $this->configArray[$section] = $this->sectionDefaults[$section];
            } else {
                $this->configArray[$section] = [];
            }
        }
    }
}
