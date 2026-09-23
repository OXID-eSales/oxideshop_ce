<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

interface MigrationAvailabilityCheckerInterface
{
    public function hasMigrations(string $migrationConfigPath): bool;
}
