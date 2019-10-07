<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

interface ModuleServicesActivationServiceInterface
{
    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return void
     */
    public function activateModuleServices(string $moduleId, int $shopId);

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return void
     */
    public function deactivateModuleServices(string $moduleId, int $shopId);
}
