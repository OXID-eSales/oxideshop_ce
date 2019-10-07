<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache\ShopConfigurationCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ShopConfigurationNotFoundException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Filesystem\Filesystem;

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
     * @var ShopEnvironmentConfigurationDaoInterface
     */
    private $shopEnvironmentConfigurationDao;

    /**
     * @param ShopConfigurationDataMapperInterface     $shopConfigurationMapper
     * @param FileStorageFactoryInterface              $fileStorageFactory
     * @param BasicContextInterface                    $context
     * @param ShopConfigurationCacheInterface          $cache
     * @param Filesystem                               $fileSystem
     * @param NodeInterface                            $node
     * @param ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao
     */
    public function __construct(
        ShopConfigurationDataMapperInterface $shopConfigurationMapper,
        FileStorageFactoryInterface $fileStorageFactory,
        BasicContextInterface $context,
        ShopConfigurationCacheInterface $cache,
        Filesystem $fileSystem,
        NodeInterface $node,
        ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao
    ) {
        $this->shopConfigurationMapper = $shopConfigurationMapper;
        $this->fileStorageFactory = $fileStorageFactory;
        $this->context = $context;
        $this->cache = $cache;
        $this->fileSystem = $fileSystem;
        $this->node = $node;
        $this->shopEnvironmentConfigurationDao = $shopEnvironmentConfigurationDao;
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

        $this->getStorage($shopId)->save(
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
     * delete all shops configuration
     */
    public function deleteAll(): void
    {
        if ($this->fileSystem->exists($this->getShopsConfigurationDirectory())) {
            $this->fileSystem->remove(
                $this->getShopsConfigurationDirectory()
            );
        }
    }

    /**
     * @return int[]
     */
    private function getShopIds(): array
    {
        $shopIds = [];

        if (file_exists($this->getShopsConfigurationDirectory())) {
            $dir = new \DirectoryIterator($this->getShopsConfigurationDirectory());

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $shopIds[] = (int)$fileInfo->getFilename();
                }
            }
        }

        return $shopIds;
    }

    /**
     * @param int $shopId
     *
     * @return ArrayStorageInterface
     */
    private function getStorage(int $shopId): ArrayStorageInterface
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
        return $this->context->getProjectConfigurationDirectory() . 'shops/';
    }

    /**
     * @param int $shopId
     *
     * @return ShopConfiguration
     * @throws \Exception
     */
    private function getConfigurationFromStorage(int $shopId): ShopConfiguration
    {
        $data = $this->mergeShopConfigurationDataWithEnvironmentData(
            $this->getShopConfigurationData($shopId),
            $this->shopEnvironmentConfigurationDao->get($shopId)
        );

        return $this->shopConfigurationMapper->fromData($data);
    }

    private function mergeShopConfigurationDataWithEnvironmentData(
        array $shopConfigurationData,
        array $environmentShopConfigurationData
    ): array {
        return array_replace_recursive($shopConfigurationData, $environmentShopConfigurationData);
    }

    /**
     * @param int $shopId
     *
     * @return bool
     */
    private function isShopIdExists(int $shopId): bool
    {
        return in_array($shopId, $this->getShopIds(), true);
    }

    /**
     * @param int $shopId
     *
     * @return array
     * @throws \Exception
     */
    private function getShopConfigurationData(int $shopId): array
    {
        try {
            $data = $this->node->normalize($this->getStorage($shopId)->get());
        } catch (InvalidConfigurationException $exception) {
            throw new InvalidConfigurationException(
                'File ' . $this->getShopConfigurationFilePath($shopId) . ' is broken: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
        return $data;
    }
}
