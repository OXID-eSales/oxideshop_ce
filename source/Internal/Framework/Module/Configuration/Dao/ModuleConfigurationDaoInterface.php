<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface ModuleConfigurationDaoInterface
{
    public function get(string $moduleId, int $shopId): ModuleConfiguration;

    public function save(ModuleConfiguration $moduleConfiguration, int $shopId);

    public function delete(string $moduleId, int $shopId): void;

    /**
     * @param int $shopId
     * @return ModuleConfiguration[]
     */
    public function getAll(int $shopId): array;

    /**
     * @deprecated will be completely removed
     */
    public function deleteAll(int $shopId): void;

    public function exists(string $moduleId, int $shopId): bool;
}
