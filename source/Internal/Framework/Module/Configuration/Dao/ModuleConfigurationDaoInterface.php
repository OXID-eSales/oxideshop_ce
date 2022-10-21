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
    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return ModuleConfiguration
     */
    public function get(string $moduleId, int $shopId): ModuleConfiguration;

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function save(ModuleConfiguration $moduleConfiguration, int $shopId);

    /**
     * @param int $shopId
     * @return ModuleConfiguration[]
     */
    public function getAll(int $shopId): array;

    public function deleteAll(int $shopId): void;

    public function exists(string $moduleId, int $shopId): bool;
}
