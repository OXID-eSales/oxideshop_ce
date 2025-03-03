<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

/**
 * @deprecated v7.2 and will be removed as of v8.0. Instead, new ModuleCache interface will be introduced.
 */
interface ModuleCacheServiceInterface
{
    public function invalidate(string $moduleId, int $shopId): void;

    /**
     * @deprecated and will be removed as of v8.0.
     */
    public function invalidateAll(): void;

    public function put(string $key, int $shopId, array $data): void;

    /**
     * @throws CacheNotFoundException
     * @throws \JsonException
     */
    public function get(string $key, int $shopId): array;

    /**
     * @deprecated use direct get() method instead
     */
    public function exists(string $key, int $shopId): bool;
}
