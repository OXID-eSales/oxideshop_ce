<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

interface ShopConfigurationDaoInterface
{

    /**
     * @param int $shopId
     *
     * @return ShopConfiguration
     */
    public function get(int $shopId): ShopConfiguration;

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param int               $shopId
     */
    public function save(ShopConfiguration $shopConfiguration, int $shopId): void;

    /**
     * @return ShopConfiguration[]
     */
    public function getAll(): array;

    /**
     * delete all shop configurations
     */
    public function deleteAll(): void;
}
