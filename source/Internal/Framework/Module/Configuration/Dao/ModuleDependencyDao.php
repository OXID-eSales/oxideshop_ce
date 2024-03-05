<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleDependencies;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;

class ModuleDependencyDao implements ModuleDependencyDaoInterface
{
    public function __construct(
        private readonly FileStorageFactoryInterface $fileStorageFactory,
        private readonly ModulePathResolverInterface $modulePathResolver,
        private readonly ContextInterface $context
    ) {
    }

    public function get(string $moduleId): ModuleDependencies
    {
        return new ModuleDependencies(
            $this->storageExists($moduleId) ? $this->getStorage($moduleId)->get() : []
        );
    }

    private function storageExists(string $moduleId): bool
    {
        return file_exists($this->getStorageFilePath($moduleId));
    }

    private function getStorage(string $moduleId): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create($this->getStorageFilePath($moduleId));
    }

    private function getStorageFilePath(string $moduleId): string
    {
        $modulePath = $this->modulePathResolver->getFullModulePathFromConfiguration(
            $moduleId,
            $this->context->getCurrentShopId()
        );

        return Path::join($modulePath, 'dependencies.yaml');
    }
}
