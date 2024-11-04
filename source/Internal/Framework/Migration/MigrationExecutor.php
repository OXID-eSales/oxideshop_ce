<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;

class MigrationExecutor implements MigrationExecutorInterface
{
    public function execute(): void
    {
        (new MigrationsBuilder())
            ->build()
            ->execute(Migrations::MIGRATE_COMMAND);
    }
}
