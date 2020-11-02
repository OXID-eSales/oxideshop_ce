<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\State;

interface ModuleStateServiceInterface
{
    public function isActive(string $moduleId, int $shopId): bool;

    /**
     * @throws ModuleStateIsAlreadySetException
     */
    public function setActive(string $moduleId, int $shopId);

    /**
     * @throws ModuleStateIsAlreadySetException
     */
    public function setDeactivated(string $moduleId, int $shopId);
}
