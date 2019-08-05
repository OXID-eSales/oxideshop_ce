<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Common\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Common\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Cache\ShopConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ShopConfigurationNotFoundException;
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
     * @var ShopConfigurationCacheInterface
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
     * @param ShopConfigurationCacheInterface $cache
     * @param Filesystem $fileSystem
     * @param NodeInterface $node
     */
    public function __construct(
        ShopConfigurationDataMapperInterface $shopConfigurationMapper,
        FileStorageFactoryInterface $fileStorageFactory,
        BasicContextInterface $context,
        ShopConfigurationCacheInterface $cache,
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
     *
     * @return ShopConfiguration
     * @throws ShopConfigurationNotFoundException
     */
    public function get(int $shopId): ShopConfiguration
    {
        if (!$this->isShopIdExists($shopId)) {
            throw new ShopConfigurationNotFoundException(
                'ShopId ' . $shopId . ' does not exist'
            );
        }

        if ($this->cache->exists($shopId)) {
            $shopConfiguration = $this->cache->get($shopId);
        } else {
            $shopConfiguration = $this->getConfigurationFromStorage($shopId);
            $this->cache->put($shopId, $shopConfiguration);
        }

        return $shopConfiguration;
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param int               $shopId
     */
    public function save(ShopConfiguration $shopConfiguration, int $shopId): void
    {
        $this->cache->evict($shopId);

        $storage = $this->getDataFromStorage($shopId);

        $storage->save(
            $this->shopConfigurationMapper->toData($shopConfiguration)
        );
    }

    /**
     * @return ShopConfiguration[]
     * @throws ShopConfigurationNotFoundException
     */
    public function getAll(): array
    {
        $configurations = [];

        foreach ($this->getShopIds() as $shopId) {
            $configurations[$shopId] = $this->get($shopId);
        }

        return $configurations;
    }

    /**
     * @return int[]
     */
    private function getShopIds(): array
    {
        $shopIds = [];

        $dir = new \DirectoryIterator($this->getShopsConfigurationDirectory());

        foreach ($dir as $fileInfo) {
            if ($fileInfo->isFile()) {
                $shopIds[] = (int)$fileInfo->getFilename();
            }
        }

        return $shopIds;
    }

    /**
     * @param int $shopId
     *
     * @return ArrayStorageInterface
     */
    private function getDataFromStorage(int $shopId): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create(
            $this->getShopConfigurationFilePath($shopId)
        );
    }

    /**
     * @param int $shopId
     *
     * @return string
     */
    private function getShopConfigurationFilePath(int $shopId): string
    {
        return $this->getShopsConfigurationDirectory() . $shopId . '.yaml';
    }

    /**
     * @return string
     */
    private function getShopsConfigurationDirectory(): string
    {
        return $this->context->getProjectConfigurationDirectory() . '/shops/';
    }

    /**
     * @return bool
     */
    private function hasShops(): bool
    {
        return $this->fileSystem->exists(
            $this->getShopsConfigurationDirectory()
        );
    }

    /**
     * @param int $shopId
     *
     * @return ShopConfiguration
     */
    private function getConfigurationFromStorage(int $shopId): ShopConfiguration
    {
        $storage = $this->getDataFromStorage($shopId);

        $shopConfiguration = $this->shopConfigurationMapper->fromData(
            $this->node->normalize($storage->get())
        );
        return $shopConfiguration;
    }

    /**
     * @param int    $shopId
     * @param string $environment
     *
     * @return bool
     */
    private function isShopIdExists(int $shopId, string $environment): bool
    {
        $shopIds = $this->getShopIds($environment);

        return in_array($shopId, $shopIds);
    }
}
