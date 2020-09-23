<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

interface ModuleCacheServiceInterface
{
    /**
     * Invalidate all module related cache items for a given module and a given shop
     *
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidateModuleCache(string $moduleId, int $shopId): void;

    /**
     * @param string $key
     * @param int    $shopId
     * @param array  $data
     */
    public function put(string $key, int $shopId, array $data): void;

    /**
     * @param string $key
     * @param int    $shopId
     *
     * @return array
     */
    public function get(string $key, int $shopId): array;

    /**
     * @param string $key
     * @param int    $shopId
     *
     * @return bool
     */
    public function exists(string $key, int $shopId): bool;

    /**
     * @param string $key
     * @param int    $shopId
     */
    public function evict(string $key, int $shopId): void;
}
