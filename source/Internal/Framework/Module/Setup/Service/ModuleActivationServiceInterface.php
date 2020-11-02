<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;

interface ModuleActivationServiceInterface
{
    /**
     * @throws ModuleSetupException
     */
    public function activate(string $moduleId, int $shopId);

    /**
     * @throws ModuleSetupException
     */
    public function deactivate(string $moduleId, int $shopId);
}
