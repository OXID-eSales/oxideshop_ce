<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

use function json_decode;
use function json_encode;

class FilesystemModuleCache implements ModuleCacheServiceInterface
{
    public function __construct(
        private ShopAdapterInterface $shopAdapter,
        private Filesystem $fileSystem,
        private BasicContextInterface $basicContext,
        private TemplateCacheServiceInterface $templateCacheService
    ) {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidate(string $moduleId, int $shopId): void
    {
        $this->templateCacheService->invalidateTemplateCache();
        $this->shopAdapter->invalidateModuleCache($moduleId);
        $this->fileSystem->remove($this->getModulePathCacheDirectory($shopId));
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
     * @param int $shopId
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
            $this->getModulePathCacheDirectory($shopId),
            $key . '.txt'
        );
    }

    private function getModulePathCacheDirectory(int $shopId): string
    {
        return Path::join(
            $this->basicContext->getCacheDirectory(),
            'modules',
            (string) $shopId
        );
    }

    /**
     * @param array $data
     * @return string
     * @throws \JsonException
     */
    private function encode(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @param string $data
     * @return mixed
     * @throws \JsonException
     */
    private function decode(string $data)
    {
        return json_decode(
            $data,
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
