<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

interface ShopConfigurationDaoInterface
{
    public function get(int $shopId): ShopConfiguration;

    public function save(ShopConfiguration $shopConfiguration, int $shopId): void;

    /**
     * @return ShopConfiguration[]
     */
    public function getAll(): array;

    /**
     * delete all shop configurations.
     */
    public function deleteAll(): void;
}
