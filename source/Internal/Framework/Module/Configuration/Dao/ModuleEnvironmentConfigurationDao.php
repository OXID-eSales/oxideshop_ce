<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Filesystem;

class ModuleEnvironmentConfigurationDao implements ModuleEnvironmentConfigurationDaoInterface
{
    public function __construct(
        private FileStorageFactoryInterface $fileStorageFactory,
        private Filesystem $fileSystem,
        private NodeInterface $node,
        private BasicContextInterface $context
    ) {
    }

    public function get(string $moduleId, int $shopId): array
    {
        $data = [];

        $configurationFilePath = $this->getEnvironmentConfigurationFilePath($moduleId, $shopId);

        if ($this->fileSystem->exists($configurationFilePath)) {
            $storage = $this->fileStorageFactory->create(
                $this->getEnvironmentConfigurationFilePath($moduleId, $shopId)
            );

            try {
                $data = $this->node->normalize($storage->get());
            } catch (InvalidConfigurationException $exception) {
                throw new InvalidConfigurationException(
                    'File ' .
                    $this->getEnvironmentConfigurationFilePath($moduleId, $shopId) .
                    ' is broken: ' . $exception->getMessage()
                );
            }
        }

        return $data;
    }

    /**
     * backup environment configuration file
     *
     * @param int $shopId
     */
    public function remove(string $moduleId, int $shopId): void
    {
        $path = $this->getEnvironmentConfigurationFilePath($moduleId, $shopId);

        if ($this->fileSystem->exists($path)) {
            $this->fileSystem->rename($path, $path . '.bak', true);
        }
    }

    /**
     * @param int $shopId
     *
     * @return string
     */
    private function getEnvironmentConfigurationFilePath(string $moduleId, int $shopId): string
    {
        return $this->context->getProjectConfigurationDirectory()
            . 'environment/shops/' . $shopId . '/modules/' . $moduleId . '.yaml';
    }
}
