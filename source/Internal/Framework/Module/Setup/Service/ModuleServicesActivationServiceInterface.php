<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

interface ModuleServicesActivationServiceInterface
{
    public function activateModuleServices(string $moduleId, int $shopId): void;

    public function deactivateModuleServices(string $moduleId, int $shopId): void;
}
