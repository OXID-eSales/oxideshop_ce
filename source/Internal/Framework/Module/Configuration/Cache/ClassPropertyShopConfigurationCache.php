<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

class ClassPropertyShopConfigurationCache implements ShopConfigurationCacheInterface
{
    /**
     * @var ShopConfiguration[]
     */
    private $cache;

    public function put(int $shopId, ShopConfiguration $configuration): void
    {
        $this->cache[$shopId] = $configuration;
    }

    public function get(int $shopId): ShopConfiguration
    {
        return $this->cache[$shopId];
    }

    public function exists(int $shopId): bool
    {
        return isset($this->cache[$shopId]);
    }

    public function evict(int $shopId): void
    {
        unset($this->cache[$shopId]);
    }
}
