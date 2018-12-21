<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

/**
 * @internal
 */
interface ModuleServicesActivationServiceInterface
{

    /**
     * @param string $moduleDir
     * @param array  $shopIds
     *
     * @return void
     */
    public function activateServicesForShops(string $moduleDir, array $shopIds);

    /**
     * @param string $moduleDir
     * @param array  $shopIds
     *
     * @return void
     */
    public function deactivateServicesForShops(string $moduleDir, array $shopIds);
}
