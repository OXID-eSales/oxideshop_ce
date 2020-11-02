<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

interface ShopEnvironmentConfigurationDaoInterface
{
    public function get(int $shopId): array;

    public function remove(int $shopId): void;
}
