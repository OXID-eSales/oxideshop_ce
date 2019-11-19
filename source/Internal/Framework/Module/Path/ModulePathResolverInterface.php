<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Path;

interface ModulePathResolverInterface
{
    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return string
     */
    public function getFullModulePathFromConfiguration(string $moduleId, int $shopId): string;
}
