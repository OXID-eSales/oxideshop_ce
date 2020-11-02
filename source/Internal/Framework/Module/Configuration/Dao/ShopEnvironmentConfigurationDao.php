<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Filesystem;

class ShopEnvironmentConfigurationDao implements ShopEnvironmentConfigurationDaoInterface
{
    /**
     * @var FileStorageFactoryInterface
     */
    private $fileStorageFactory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * ShopConfigurationDao constructor.
     */
    public function __construct(
        FileStorageFactoryInterface $fileStorageFactory,
        Filesystem $fileSystem,
        NodeInterface $node,
        BasicContextInterface $context
    ) {
        $this->fileStorageFactory = $fileStorageFactory;
        $this->fileSystem = $fileSystem;
        $this->node = $node;
        $this->context = $context;
    }

    public function get(int $shopId): array
    {
        $data = [];

        $configurationFilePath = $this->getEnvironmentConfigurationFilePath($shopId);

        if ($this->fileSystem->exists($configurationFilePath)) {
            $storage = $this->fileStorageFactory->create(
                $this->getEnvironmentConfigurationFilePath($shopId)
            );

            try {
                $data = $this->node->normalize($storage->get());
            } catch (InvalidConfigurationException $exception) {
                throw new InvalidConfigurationException('File ' . $this->getEnvironmentConfigurationFilePath($shopId) . ' is broken: ' . $exception->getMessage());
            }
        }

        return $data;
    }

    /**
     * backup environment configuration file.
     */
    public function remove(int $shopId): void
    {
        $path = $this->getEnvironmentConfigurationFilePath($shopId);

        if ($this->fileSystem->exists($path)) {
            $this->fileSystem->rename($path, $path . '.bak', true);
        }
    }

    private function getEnvironmentConfigurationFilePath(int $shopId): string
    {
        return $this->context->getProjectConfigurationDirectory() . 'environment/' . $shopId . '.yaml';
    }
}
