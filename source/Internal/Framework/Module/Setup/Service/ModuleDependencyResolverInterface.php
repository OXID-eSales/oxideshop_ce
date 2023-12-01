<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\UnresolvedModuleDependencies;

interface ModuleDependencyResolverInterface
{
    public function getUnresolvedActivationDependencies(string $moduleId, int $shopId): UnresolvedModuleDependencies;

    public function getUnresolvedDeactivationDependencies(string $moduleId, int $shopId): UnresolvedModuleDependencies;
}
