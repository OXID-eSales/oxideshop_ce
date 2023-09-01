<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

interface ModuleDependencyResolverInterface
{
    public function canActivateModule(string $moduleId, int $shopId): bool;

    public function canDeactivateModule(string $moduleId, int $shopId): bool;
}
