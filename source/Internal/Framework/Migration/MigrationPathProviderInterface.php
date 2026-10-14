<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationsNotFoundException;

interface MigrationPathProviderInterface
{
    /**
     * @throws MigrationsNotFoundException
     */
    public function getMigrationConfigPath(): string;
}
