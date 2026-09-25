<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

readonly class DefaultMigrationRunPolicy implements MigrationRunPolicyInterface
{
    public function shouldRun(string $migrationConfigPath): bool
    {
        return is_file($migrationConfigPath);
    }
}
