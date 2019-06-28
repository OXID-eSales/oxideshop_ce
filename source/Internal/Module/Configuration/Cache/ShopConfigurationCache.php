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
interface ShopConfigurationCache
{
    public function put(string $environment, int $shopId, ShopConfiguration $configuration): void;

    public function get(string $environment, int $shopId): ShopConfiguration;

    public function exists(string $environment, int $shopId): bool;

    public function evict(string $environment, int $shopId): void;
}
