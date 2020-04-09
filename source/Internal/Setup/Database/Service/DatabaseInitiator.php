<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\InitiateDatabaseException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

/**
 * Class DatabaseInitiator
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Database
 */
class DatabaseInitiator implements DatabaseInitiatorInterface
{

    /** @var BasicContextInterface */
    private $context;

    /** @var MigrationExecutorInterface */
    private $migrationExecutor;

    /** @var ConnectionProviderInterface */
    private $connectionProvider;

    /** @var Connection */
    private $dbConnection;

    /**
     * DatabaseInitiator constructor.
     *
     * @param BasicContextInterface       $context
     * @param MigrationExecutorInterface  $migrationExecutor
     * @param ConnectionProviderInterface $connectionProvider
     */
    public function __construct(
        BasicContextInterface $context,
        MigrationExecutorInterface $migrationExecutor,
        ConnectionProviderInterface $connectionProvider
    ) {
        $this->context = $context;
        $this->migrationExecutor = $migrationExecutor;
        $this->connectionProvider = $connectionProvider;
    }

    /**
     * @throws InitiateDatabaseException
     */
    public function initiateDatabase(): void
    {
        $this->dbConnection = $this->connectionProvider->get();

        $this->initiateSqlFiles();

        $this->executeMigrations();
    }

    /**
     * @throws InitiateDatabaseException
     */
    public function initiateSqlFiles(): void
    {
        $sqlFilePath = $this->context->getCommunityEditionSourcePath() . '/Internal/Setup/Database/Sql';
        $this->executeSqlQueryFromFile("$sqlFilePath/database_schema.sql");
        $this->executeSqlQueryFromFile("$sqlFilePath/initial_data.sql");
    }

    /**
     * @throws InitiateDatabaseException
     */
    private function executeMigrations(): void
    {
        try {
            $this->migrationExecutor->execute();
        } catch (\Throwable $exception) {
            throw new InitiateDatabaseException(
                InitiateDatabaseException::EXECUTE_MIGRATIONS_PROBLEM,
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param string $sqlFilePath
     *
     * @throws InitiateDatabaseException
     */
    private function executeSqlQueryFromFile(string $sqlFilePath): void
    {
        $queries = file_get_contents($sqlFilePath);
        if (!$queries) {
            throw new InitiateDatabaseException(InitiateDatabaseException::READ_SQL_FILE_PROBLEM);
        }

        try {
            $this->dbConnection->exec($queries);
        } catch (\Throwable $exception) {
            throw new InitiateDatabaseException(
                InitiateDatabaseException::RUN_SQL_FILE_PROBLEM,
                $exception->getCode(),
                $exception
            );
        }
    }
}
