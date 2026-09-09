<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Migration;

interface ModuleMigrationConfigLocatorInterface
{
    /**
     * @return array<string, string>
     */
    public function getMigrationConfigPaths(): array;
}
