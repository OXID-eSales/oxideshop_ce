<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

interface ModuleEnvironmentConfigurationDaoInterface
{
    public function get(string $moduleId, int $shopId): array;
    public function remove(string $moduleId, int $shopId): void;
}
