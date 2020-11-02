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

/**
 * Class MigrationExecutor.
 */
class MigrationExecutor implements MigrationExecutorInterface
{
    /**
     * @var ContextInterface
     */
    protected $context;

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    public function execute(): void
    {
        $migrations = $this->createMigrations();
        $migrations->execute(Migrations::MIGRATE_COMMAND);
    }

    private function createMigrations(): Migrations
    {
        $migrationsBuilder = new MigrationsBuilder();

        return $migrationsBuilder->build($this->context->getFacts());
    }
}
