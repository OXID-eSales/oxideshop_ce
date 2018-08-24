<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfigurationIdentifier;

/**
 * @internal
 */
interface ModuleActivationServiceInterface
{
    /**
     * @param string $moduleName
     * @param int    $shopId
     */
    public function activate(string $moduleName, int $shopId);

    /**
     * @param string $moduleName
     * @param int    $shopId
     */
    public function deactivate(string $moduleName, int $shopId);
}
