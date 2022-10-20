<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class ModuleServicesImporter implements ModuleServicesImporterInterface
{
    public function __construct(
        private BasicContextInterface $context
    ) {
    }

    public function addImport(string $serviceDir, int $shopId): void
    {
        if (!realpath($serviceDir)) {
            throw new NoServiceYamlException();
        }
        $services = $this->loadDIConfigFile($this->context->getActiveModuleServicesFilePath($shopId));
        $services->addImport($this->getServiceRelativeFilePath($serviceDir, $shopId));

        $this->saveServicesFile($services, $shopId);
    }

    public function removeImport(string $serviceDir, int $shopId): void
    {
        $services = $this->loadDIConfigFile($this->context->getActiveModuleServicesFilePath($shopId));
        $services->removeImport($this->getServiceRelativeFilePath($serviceDir, $shopId));

        $this->saveServicesFile($services, $shopId);
    }

    public function removeNonExistingImports(int $shopId): void
    {
        $services = $this->loadDIConfigFile($this->context->getActiveModuleServicesFilePath($shopId));

        foreach ($services->getImportFileNames() as $fileName) {
            if (!file_exists($this->getAbsolutePath($fileName, $shopId))) {
                $services->removeImport($fileName);
                $this->saveServicesFile($services, $shopId);
            }
        }
    }

    private function getServiceRelativeFilePath(string $serviceDir, int $shopId): string
    {
        return Path::makeRelative(
            $serviceDir . DIRECTORY_SEPARATOR . 'services.yaml',
            Path::getDirectory($this->context->getActiveModuleServicesFilePath($shopId))
        );
    }

    private function loadDIConfigFile(string $path): DIConfigWrapper
    {
        $yamlArray = [];

        if (file_exists($path)) {
            $yamlArray = Yaml::parse(file_get_contents($path), Yaml::PARSE_CUSTOM_TAGS) ?? [];
        }

        return new DIConfigWrapper($yamlArray);
    }

    private function saveServicesFile(DIConfigWrapper $config, int $shopId): void
    {
        file_put_contents(
            $this->context->getActiveModuleServicesFilePath($shopId),
            Yaml::dump($config->getConfigAsArray(), 3, 2)
        );
    }

    private function getAbsolutePath(string $fileName, int $shopId): string
    {
        return Path::makeAbsolute(
            $fileName,
            Path::getDirectory($this->context->getActiveModuleServicesFilePath($shopId))
        );
    }
}
