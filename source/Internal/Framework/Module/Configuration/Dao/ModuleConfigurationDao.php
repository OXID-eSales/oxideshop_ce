<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache\ModuleConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Webmozart\PathUtil\Path;

class ModuleConfigurationDao implements ModuleConfigurationDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private ModuleConfigurationDataMapperInterface $moduleConfigurationDataMapper,
        private FileStorageFactoryInterface $fileStorageFactory,
        private ModuleConfigurationCacheInterface $cache,
        private ModuleConfigurationExtenderInterface $moduleConfigurationExtender,
        private NodeInterface $node,
    )
    {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return ModuleConfiguration
     * @throws ModuleConfigurationNotFoundException
     */
    public function get(string $moduleId, int $shopId): ModuleConfiguration
    {
        if (!$this->cache->exists($moduleId, $shopId)) {
            if (!file_exists($this->getModuleConfigurationFilePath($shopId, $moduleId))) {
                throw new ModuleConfigurationNotFoundException('There is no module configuration with id ' . $moduleId);
            }

            $moduleConfiguration = $this->moduleConfigurationDataMapper->fromData(new ModuleConfiguration(), $this->getNormalizedData($shopId, $moduleId));
            $moduleConfiguration = $this->moduleConfigurationExtender->extend($moduleConfiguration, $shopId);

            $this->cache->put($shopId, $moduleConfiguration);
        }

        return $this->cache->get($moduleId, $shopId);
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function save(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        $this->cache->evict($moduleConfiguration->getId(), $shopId);

        $this->getStorage($shopId, $moduleConfiguration->getId())->save(
            $this->moduleConfigurationDataMapper->toData($moduleConfiguration)
        );
    }

    /**
     * @inheritDoc
     */
    public function getAll(int $shopId): array
    {
        $moduleConfigurations = [];

        foreach ($this->getModuleIds($shopId) as $id) {
            $moduleConfigurations[$id] = $this->get($id, $shopId);
        }

        return $moduleConfigurations;
    }

    public function exists(string $moduleId, int $shopId): bool
    {
        return in_array($moduleId, $this->getModuleIds($shopId), true);
    }

    private function getStorage(int $shopId, string $moduleId): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create(
            $this->getModuleConfigurationFilePath($shopId, $moduleId)
        );
    }

    private function getModulesConfigurationDirectory(int $shopId): string
    {
        return Path::join($this->context->getShopConfigurationDirectory($shopId), 'modules');
    }

    private function getModuleIds(int $shopId): array
    {
        $moduleIds = [];

        if (file_exists($this->getModulesConfigurationDirectory($shopId))) {
            $dir = new \DirectoryIterator($this->getModulesConfigurationDirectory($shopId));

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $moduleIds[] = $fileInfo->getBasename('.' .$fileInfo->getExtension());
                }
            }
        }

        sort($moduleIds);

        return $moduleIds;
    }

    private function getModuleConfigurationFilePath(int $shopId, string $moduleId): string
    {
        return Path::join($this->getModulesConfigurationDirectory($shopId), $moduleId . '.yaml');
    }

    private function getNormalizedData(int $shopId, string $moduleId): mixed
    {
        try {
            $data = $this->node->normalize($this->getStorage($shopId, $moduleId)->get());
        } catch (InvalidConfigurationException $exception) {
            throw new InvalidConfigurationException(
                'File ' . $this->getModuleConfigurationFilePath($shopId, $moduleId) . ' is broken: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
        return $data;
    }
}
