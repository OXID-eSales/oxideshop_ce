<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutorInterface;
use Symfony\Component\Filesystem\Path;

class ShopDbManager implements ShopDbManagerInterface
{
    private DatabaseConfiguration $databaseConfiguration;

    public function __construct(
        private readonly SetupDbConnectionFactoryInterface $databaseConnectionFactory,
        private readonly MigrationExecutorInterface $migrationExecutor,
        private readonly ViewsGeneratorFactoryInterface $databaseViewsGeneratorFactory,
    ) {
    }

    public function create(DatabaseConfiguration $databaseConfiguration): void
    {
        $this->databaseConfiguration = $databaseConfiguration;

        $this->createDatabase();
        $this->loadSqlDumps();
        $this->migrationExecutor->execute();
        $this->databaseViewsGeneratorFactory
            ->create()
            ->generate();
    }

    private function createDatabase(): void
    {
        $connection = $this->databaseConnectionFactory->getServerConnection($this->databaseConfiguration);
        $connection->executeStatement(
            sprintf(
                'CREATE DATABASE `%s` CHARACTER SET utf8 COLLATE utf8_general_ci;',
                $this->databaseConfiguration->getName()
            )
        );
        $connection->close();
    }

    private function loadSqlDumps(): void
    {
        $connection = $this->databaseConnectionFactory->getDatabaseConnection($this->databaseConfiguration);
        $connection->executeStatement($this->readDumpFromFile('database_schema'));
        $connection->executeStatement($this->readDumpFromFile('initial_data'));
        $connection->close();
    }

    private function readDumpFromFile(string $file): string
    {
        return file_get_contents(
            Path::join(
                __DIR__,
                'sql',
                "$file.sql"
            )
        );
    }
}
