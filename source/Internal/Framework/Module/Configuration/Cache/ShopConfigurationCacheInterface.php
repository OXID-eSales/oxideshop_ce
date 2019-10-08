<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

interface ShopConfigurationCacheInterface
{
    public function put(int $shopId, ShopConfiguration $configuration): void;

    public function get(int $shopId): ShopConfiguration;

    public function exists(int $shopId): bool;

    public function evict(int $shopId): void;
}
