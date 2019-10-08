<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;

interface ModuleActivationServiceInterface
{
    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleSetupException
     */
    public function activate(string $moduleId, int $shopId);

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleSetupException
     */
    public function deactivate(string $moduleId, int $shopId);
}
