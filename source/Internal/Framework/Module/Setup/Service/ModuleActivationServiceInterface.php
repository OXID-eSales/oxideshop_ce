<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;

interface ModuleActivationServiceInterface
{
    /**
     * @throws ModuleConfigurationNotFoundException
     */
    public function activate(string $moduleId, int $shopId): void;

    /**
     * @throws ModuleConfigurationNotFoundException
     */
    public function deactivate(string $moduleId, int $shopId): void;
}
