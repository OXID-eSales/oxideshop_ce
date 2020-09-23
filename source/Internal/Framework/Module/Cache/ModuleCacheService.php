<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Exception\ModulePathCacheException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;

class ModuleCacheService implements ModuleCacheServiceInterface
{
    /** @var ShopAdapterInterface */
    private $shopAdapter;

    /** @var Filesystem */
    private $fileSystem;

    /** @var BasicContextInterface */
    private $basicContext;

    /**
     * ModuleCacheService constructor.
     *
     * @param ShopAdapterInterface        $shopAdapter
     * @param Filesystem                  $fileSystem
     * @param BasicContextInterface       $basicContext
     */
    public function __construct(
        ShopAdapterInterface $shopAdapter,
        Filesystem $fileSystem,
        BasicContextInterface $basicContext
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->fileSystem = $fileSystem;
        $this->basicContext = $basicContext;
    }

    /**
     * Invalidate all module related cache items for a given module and a given shop
     *
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidateModuleCache(string $moduleId, int $shopId): void
    {
        $this->shopAdapter->invalidateModuleCache($moduleId);
    }

    /**
     * @param string $key
     * @param int    $shopId
     * @param array  $data
     */
    public function put(string $key, int $shopId, array $data): void
    {
        $modulePathCacheFilePath = $this->basicContext->getModulePathCacheFilePath($shopId);

        $modulePathCacheFileContent = $this->getCacheFileContent($modulePathCacheFilePath);
        $modulePathCacheFileContent[$key] = $data;

        $this->fileSystem->dumpFile($modulePathCacheFilePath, serialize($modulePathCacheFileContent));
    }

    /**
     * @param string $key
     * @param int    $shopId
     *
     * @return array
     */
    public function get(string $key, int $shopId): array
    {
        $modulePathCacheFilePath = $this->basicContext->getModulePathCacheFilePath($shopId);

        $modulePathCacheFileContent = $this->getCacheFileContent($modulePathCacheFilePath);

        $data = [];
        if ($this->exists($key, $shopId)) {
            $data = $modulePathCacheFileContent[$key];
        }

        return $data;
    }

    /**
     * @param string $key
     * @param int    $shopId
     *
     * @return bool
     */
    public function exists(string $key, int $shopId): bool
    {
        $modulePathCacheFilePath = $this->basicContext->getModulePathCacheFilePath($shopId);

        $modulePathCacheFileContent = $this->getCacheFileContent($modulePathCacheFilePath);

        if (array_key_exists($key, $modulePathCacheFileContent)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     * @param int    $shopId
     */
    public function evict(string $key, int $shopId): void
    {
        $modulePathCacheFilePath = $this->basicContext->getModulePathCacheFilePath($shopId);

        $modulePathCacheFileContent = $this->getCacheFileContent($modulePathCacheFilePath);

        if ($this->exists($key, $shopId)) {
            unset($modulePathCacheFileContent[$key]);
            $this->fileSystem->dumpFile($modulePathCacheFilePath, serialize($modulePathCacheFileContent));
        }
    }

    private function getCacheFileContent(string $modulePathCacheFilePath): array
    {
        $modulePathCacheFileContent = [];

        if ($this->fileSystem->exists($modulePathCacheFilePath)) {
            if ((!is_readable($modulePathCacheFilePath) || !is_writable($modulePathCacheFilePath))) {
                throw new ModulePathCacheException("Module path cache file has no permission.");
            }

            $fileContent = file_get_contents($modulePathCacheFilePath);
            $modulePathCacheFileContent = unserialize($fileContent, ['allowed_classes' => false]);
        }

        return $modulePathCacheFileContent;
    }
}
