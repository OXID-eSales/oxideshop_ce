<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Service;

/**
 * @internal
 */
interface ShopActivationServiceInterface
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
