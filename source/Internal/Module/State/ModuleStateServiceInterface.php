<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\State;

/**
 * @internal
 */
interface ModuleStateServiceInterface
{
    /**
     * @param string $moduleName
     * @param int    $shopId
     * @return bool
     */
    public function isActive(string $moduleName, int $shopId): bool;

    /**
     * @param string $moduleName
     */
    public function setDeleted(string $moduleName);
}
