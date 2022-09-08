<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class ClassPropertyModuleConfigurationCache implements ModuleConfigurationCacheInterface
{
    /**
     * @var ModuleConfiguration[][]
     */
    private array $cache;

    public function put(int $shopId, ModuleConfiguration $configuration): void
    {
        $this->cache[$shopId][$configuration->getId()] = $configuration;
    }

    public function get(string $moduleId, int $shopId): ModuleConfiguration
    {
        return $this->cache[$shopId][$moduleId];
    }

    public function exists(string $moduleId, int $shopId): bool
    {
        return isset($this->cache[$shopId][$moduleId]);
    }

    public function evict(string $moduleId, int $shopId): void
    {
        unset($this->cache[$shopId][$moduleId]);
    }
}
