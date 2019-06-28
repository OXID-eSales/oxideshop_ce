<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Common\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Common\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Cache\ShopConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class ShopConfigurationDao implements ShopConfigurationDaoInterface
{
    /**
     * @var ShopConfigurationDataMapperInterface
     */
    private $shopConfigurationMapper;

    /**
     * @var FileStorageFactoryInterface
     */
    private $fileStorageFactory;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var ShopConfigurationCache
     */
    private $cache;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var NodeInterface
     */
    private $node;

    /**
     * ShopConfigurationDao constructor.
     * @param ShopConfigurationDataMapperInterface $shopConfigurationMapper
     * @param FileStorageFactoryInterface $fileStorageFactory
     * @param BasicContextInterface $context
     * @param ShopConfigurationCache $cache
     * @param Filesystem $fileSystem
     * @param NodeInterface $node
     */
    public function __construct(
        ShopConfigurationDataMapperInterface $shopConfigurationMapper,
        FileStorageFactoryInterface $fileStorageFactory,
        BasicContextInterface $context,
        ShopConfigurationCache $cache,
        Filesystem $fileSystem,
        NodeInterface $node
    ) {
        $this->shopConfigurationMapper = $shopConfigurationMapper;
        $this->fileStorageFactory = $fileStorageFactory;
        $this->context = $context;
        $this->cache = $cache;
        $this->fileSystem = $fileSystem;
        $this->node = $node;
    }

    /**
     * @param int $shopId
     * @param string $environment
     * @return ShopConfiguration
     */
    public function get(int $shopId, string $environment): ShopConfiguration
    {
        if ($this->cache->exists($environment, $shopId)) {
            $shopConfiguration = $this->cache->get($environment, $shopId);
        } else {
            $shopConfiguration = $this->getConfigurationFromStorage($shopId, $environment);
            $this->cache->put($environment, $shopId, $shopConfiguration);
        }

        return $shopConfiguration;
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param int $shopId
     * @param string $environment
     */
    public function save(ShopConfiguration $shopConfiguration, int $shopId, string $environment): void
    {
        $this->cache->evict($environment, $shopId);

        $storage = $this->getDataFromStorage($shopId, $environment);

        $storage->save(
            $this->shopConfigurationMapper->toData($shopConfiguration)
        );
    }

    /**
     * @param string $environment
     * @return ShopConfiguration[]
     */
    public function getAll(string $environment): array
    {
        $configurations = [];

        foreach ($this->getShopIds($environment) as $shopId) {
            $configurations[$shopId] = $this->get($shopId, $environment);
        }

        return $configurations;
    }

    /**
     * @param string $environment
     * @return int[]
     */
    private function getShopIds(string $environment): array
    {
        $shopIds = [];

        if ($this->hasShops($environment)) {
            $dir = new \DirectoryIterator($this->getShopsConfigurationDirectory($environment));

            foreach ($dir as $fileinfo) {
                if ($fileinfo->isFile()) {
                    $shopIds[] = (int)$fileinfo->getFilename();
                }
            }
        }

        return $shopIds;
    }

    /**
     * @param int $shopId
     * @param string $environment
     * @return ArrayStorageInterface
     */
    private function getDataFromStorage(int $shopId, string $environment): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create(
            $this->getShopConfigurationFilePath($shopId, $environment)
        );
    }

    /**
     * @param int $shopId
     * @param string $environment
     * @return string
     */
    private function getShopConfigurationFilePath(int $shopId, string $environment): string
    {
        return $this->getShopsConfigurationDirectory($environment) . $shopId . '.yaml';
    }

    /**
     * @param string $environment
     * @return string
     */
    private function getShopsConfigurationDirectory(string $environment): string
    {
        return $this->context->getProjectConfigurationDirectory() . $environment . '/shops/';
    }

    /**
     * @param string $environment
     * @return bool
     */
    private function hasShops(string $environment): bool
    {
        return $this->fileSystem->exists(
            $this->getShopsConfigurationDirectory($environment)
        );
    }

    /**
     * @param int $shopId
     * @param string $environment
     * @return ShopConfiguration
     */
    private function getConfigurationFromStorage(int $shopId, string $environment): ShopConfiguration
    {
        $storage = $this->getDataFromStorage($shopId, $environment);

        $shopConfiguration = $this->shopConfigurationMapper->fromData(
            $this->node->normalize($storage->get())
        );
        return $shopConfiguration;
    }
}
