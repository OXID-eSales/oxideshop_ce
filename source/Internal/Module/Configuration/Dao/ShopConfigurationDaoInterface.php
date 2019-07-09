<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;

/**
 * @internal
 */
interface ShopConfigurationDaoInterface
{
    /**
     * @param int $shopId
     * @param string $environment
     * @return ShopConfiguration
     */
    public function get(int $shopId, string $environment): ShopConfiguration;

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param int $shopId
     * @param string $environment
     */
    public function save(ShopConfiguration $shopConfiguration, int $shopId, string $environment): void;

    /**
     * @param string $environment
     * @return ShopConfiguration[]
     */
    public function getAll(string $environment): array;
}
