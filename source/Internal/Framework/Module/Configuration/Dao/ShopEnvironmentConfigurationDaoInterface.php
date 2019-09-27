<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

interface ShopEnvironmentConfigurationDaoInterface
{
    /**
     * @param int $shopId
     *
     * @return array
     */
    public function get(int $shopId): array;

    /**
     * @param int $shopId
     *
     * @return void
     */
    public function remove(int $shopId): void;
}
