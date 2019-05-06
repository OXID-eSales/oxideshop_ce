<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Path;

/**
 * @internal
 */
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
