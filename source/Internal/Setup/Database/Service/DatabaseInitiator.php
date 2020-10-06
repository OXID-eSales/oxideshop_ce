<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\InitiateDatabaseException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PDO;

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

    /** @var PDO */
    private $dbConnection;

    /**
     * DatabaseInitiator constructor.
     *
     * @param BasicContextInterface       $context
     * @param MigrationExecutorInterface  $migrationExecutor
     */
    public function __construct(
        BasicContextInterface $context,
        MigrationExecutorInterface $migrationExecutor
    ) {
        $this->context = $context;
        $this->migrationExecutor = $migrationExecutor;
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $name
     * @throws InitiateDatabaseException
     */
    public function initiateDatabase(string $host, int $port, string $username, string $password, string $name): void
    {
        $this->dbConnection = $this->getDatabaseConnection($host, $port, $username, $password, $name);

        $this->initiateSqlFiles();

        $this->executeMigrations();
    }

    /**
     * @throws InitiateDatabaseException
     */
    private function initiateSqlFiles(): void
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
        $queries = \file_get_contents($sqlFilePath);
        if (!$queries) {
            throw new InitiateDatabaseException(InitiateDatabaseException::READ_SQL_FILE_PROBLEM);
        }

        $this->dbConnection->exec($queries);
    }

    private function getDatabaseConnection(
        string $host,
        int $port,
        string $username,
        string $password,
        string $name
    ): PDO {
        $dbConnection = new PDO(
            \sprintf('mysql:host=%s;port=%s;dbname=%s', $host, $port, $name),
            $username,
            $password,
            [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
        );
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $dbConnection;
    }
}
