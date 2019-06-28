<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ClassPropertyShopConfigurationCache implements ShopConfigurationCache
{
    /**
     * @var string[]
     */
    private $cache;

    public function put(string $environment, int $shopId, ShopConfiguration $configuration): void
    {
        $this->cache[$environment][$shopId] = $configuration;
    }

    public function get(string $environment, int $shopId): ShopConfiguration
    {
        return $this->cache[$environment][$shopId];
    }

    public function exists(string $environment, int $shopId): bool
    {
        return isset($this->cache[$environment][$shopId]);
    }

    public function evict(string $environment, int $shopId): void
    {
        unset($this->cache[$environment][$shopId]);
    }
}
