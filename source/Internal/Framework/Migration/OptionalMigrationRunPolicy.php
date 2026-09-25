<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

readonly class OptionalMigrationRunPolicy implements MigrationRunPolicyInterface
{
    public function __construct(private MigrationAvailabilityCheckerInterface $availabilityChecker)
    {
    }

    public function shouldRun(string $migrationConfigPath): bool
    {
        return $this->availabilityChecker->hasMigrations($migrationConfigPath);
    }
}
