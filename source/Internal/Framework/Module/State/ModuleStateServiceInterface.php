<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\State;

interface ModuleStateServiceInterface
{
    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return bool
     */
    public function isActive(string $moduleId, int $shopId): bool;

    /**
     * @deprecated will be completely removed in v7.0
     *
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleStateIsAlreadySetException
     */
    public function setActive(string $moduleId, int $shopId);

    /**
     * @deprecated will be completely removed in v7.0
     *
     * @param string $moduleId
     * @param int    $shopId
     *
     * @throws ModuleStateIsAlreadySetException
     */
    public function setDeactivated(string $moduleId, int $shopId);
}
