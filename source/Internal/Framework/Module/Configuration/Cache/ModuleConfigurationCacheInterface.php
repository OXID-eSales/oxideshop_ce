<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface ModuleConfigurationCacheInterface
{
    public function put(int $shopId, ModuleConfiguration $configuration): void;

    public function get(string $moduleId, int $shopId): ModuleConfiguration;

    public function exists(string $moduleId, int $shopId): bool;

    public function evict(string $moduleId, int $shopId): void;
}
