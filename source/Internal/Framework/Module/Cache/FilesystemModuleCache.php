<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use FilesystemIterator;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

use function json_decode;
use function json_encode;

/**
 * @deprecated and it will be moved to ModuleCache as of v8.0
 */
class FilesystemModuleCache implements ModuleCacheServiceInterface
{
    public function __construct(
        private ShopAdapterInterface $shopAdapter,
        private Filesystem $fileSystem,
        private BasicContextInterface $basicContext,
        private ShopTemplateCacheServiceInterface $shopTemplateCacheService
    ) {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidate(string $moduleId, int $shopId): void
    {
        $this->shopTemplateCacheService->invalidateCache($shopId);
        $this->shopAdapter->invalidateModuleCache($moduleId);
        $this->fileSystem->remove($this->getShopModulePathCacheDirectory($shopId));
    }

    public function invalidateAll(): void
    {
        $this->shopTemplateCacheService->invalidateAllShopsCache();
        $this->shopAdapter->invalidateModulesCache();

        $templateCacheDirectory = $this->basicContext->getModuleCacheDirectory();
        if ($this->fileSystem->exists($templateCacheDirectory)) {
            $recursiveIterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($templateCacheDirectory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            $this->fileSystem->remove($recursiveIterator);
        }
    }

    /**
     * @param string $key
     * @param int    $shopId
     * @param array  $data
     */
    public function put(string $key, int $shopId, array $data): void
    {
        $this->fileSystem->dumpFile(
            $this->getModulePathCacheFilePath($key, $shopId),
            $this->encode($data)
        );
    }

    /**
     * @param string $key
     * @param int    $shopId
     *
     * @return array
     * @throws CacheNotFoundException
     */
    public function get(string $key, int $shopId): array
    {
        if (!$this->exists($key, $shopId)) {
            throw new CacheNotFoundException("Cache with key '$key' for the shop with id $shopId not found.");
        }

        return $this->getCacheFileContent($this->getModulePathCacheFilePath($key, $shopId));
    }

    /**
     * @param string $key
     * @param int    $shopId
     *
     * @return bool
     */
    public function exists(string $key, int $shopId): bool
    {
        return $this->fileSystem->exists($this->getModulePathCacheFilePath($key, $shopId));
    }

    private function getCacheFileContent(string $modulePathCacheFilePath): array
    {
        return $this->decode(
            file_get_contents($modulePathCacheFilePath)
        );
    }

    private function getModulePathCacheFilePath(string $key, int $shopId): string
    {
        return Path::join(
            $this->getShopModulePathCacheDirectory($shopId),
            $key . '.txt'
        );
    }

    private function getShopModulePathCacheDirectory(int $shopId): string
    {
        return Path::join(
            $this->basicContext->getModuleCacheDirectory(),
            (string)$shopId
        );
    }

    /**
     * @param array $data
     *
     * @return string
     * @throws \JsonException
     */
    private function encode(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $data
     *
     * @return mixed
     * @throws \JsonException
     */
    private function decode(string $data): mixed
    {
        return json_decode(
            $data,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
