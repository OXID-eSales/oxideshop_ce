<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationExecutor implements MigrationExecutorInterface, ConfigurableMigrationExecutorInterface
{
    public function execute(): void
    {
        $this->executeWithOptions();
    }

    /**
     * @param array<string, mixed> $options
     */
    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        $migrations = $this->createMigrations();

        if ($output instanceof Output) {
            $migrations->setOutput($output);
        }

        return $migrations->execute(Migrations::MIGRATE_COMMAND, null, $options);
    }

    private function createMigrations(): Migrations
    {
        return (new MigrationsBuilder())->build();
    }
}
