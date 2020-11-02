<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class FilesystemModuleCache implements ModuleCacheServiceInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    public function __construct(
        ShopAdapterInterface $shopAdapter,
        Filesystem $fileSystem,
        BasicContextInterface $basicContext
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->fileSystem = $fileSystem;
        $this->basicContext = $basicContext;
    }

    public function invalidate(string $moduleId, int $shopId): void
    {
        $this->shopAdapter->invalidateModuleCache($moduleId);
        $this->fileSystem->remove($this->getModulePathCacheDirectory($shopId));
    }

    public function put(string $key, int $shopId, array $data): void
    {
        $this->fileSystem->dumpFile($this->getModulePathCacheFilePath($key, $shopId), serialize($data));
    }

    /**
     * @throws CacheNotFoundException
     */
    public function get(string $key, int $shopId): array
    {
        if (!$this->exists($key, $shopId)) {
            throw new CacheNotFoundException("Cache with key '$key' for the shop with id $shopId not found.");
        }

        return $this->getCacheFileContent($this->getModulePathCacheFilePath($key, $shopId));
    }

    public function exists(string $key, int $shopId): bool
    {
        return $this->fileSystem->exists($this->getModulePathCacheFilePath($key, $shopId));
    }

    private function getCacheFileContent(string $modulePathCacheFilePath): array
    {
        return unserialize(file_get_contents($modulePathCacheFilePath), [
            'allowed_classes' => false,
        ]);
    }

    private function getModulePathCacheFilePath(string $key, int $shopId): string
    {
        return Path::join(
            $this->getModulePathCacheDirectory($shopId),
            $key . '.txt'
        );
    }

    private function getModulePathCacheDirectory(int $shopId): string
    {
        return Path::join(
            $this->basicContext->getCacheDirectory(),
            'modules',
            (string)$shopId
        );
    }
}
