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
    /**
     * @param int $shopId
     *
     * @return ShopConfiguration
     */
    public function get(int $shopId): ShopConfiguration;

    /**
     * @deprecated use ModuleConfigurationDaoInterface::save() and ClassExtensionsChainDaoInterface::saveChain() instead
     */
    public function save(ShopConfiguration $shopConfiguration, int $shopId): void;

    /**
     * @return ShopConfiguration[]
     */
    public function getAll(): array;

    /**
     * @deprecated will be completely removed
     */
    public function deleteAll(): void;
}
