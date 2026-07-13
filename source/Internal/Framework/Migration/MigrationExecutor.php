<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated since v7.6.0, will be removed in v8.0, use
 *             ConfigurableMigrationExecutorInterface::executeWithOptions() instead
 */
class MigrationExecutor implements MigrationExecutorInterface, ConfigurableMigrationExecutorInterface
{
    public function __construct(protected ContextInterface $context)
    {
    }

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
        return (new MigrationsBuilder())->build($this->context->getFacts());
    }
}
