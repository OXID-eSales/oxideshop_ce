<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

/**
 * @internal
 */
interface ModuleActivationServiceInterface
{
    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId);

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId);
}
