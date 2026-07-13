<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\InitiateDatabaseException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PDO;

/**
 * Class DatabaseInitiator
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Database
 */
readonly class DatabaseInitiator implements DatabaseInitiatorInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private MigrationExecutorInterface $migrationExecutor,
        private ConfigurableMigrationExecutorInterface $taggedMigrationExecutor,
    ) {
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
        $connection = $this->getDatabaseConnection($host, $port, $username, $password, $name);

        $this->initiateSqlFiles($connection);

        $this->executeMigrations();
    }

    /**
     * @throws InitiateDatabaseException
     */
    private function initiateSqlFiles(PDO $connection): void
    {
        $sqlFilePath = $this->context->getCommunityEditionSourcePath() . '/Internal/Setup/Database/Sql';
        $this->executeSqlQueryFromFile($connection, "$sqlFilePath/database_schema.sql");
        $this->executeSqlQueryFromFile($connection, "$sqlFilePath/initial_data.sql");
    }

    /**
     * @throws InitiateDatabaseException
     */
    private function executeMigrations(): void
    {
        try {
            $this->migrationExecutor->execute();
            $status = $this->taggedMigrationExecutor->executeWithOptions();
        } catch (\Throwable $exception) {
            throw new InitiateDatabaseException(
                InitiateDatabaseException::EXECUTE_MIGRATIONS_PROBLEM,
                $exception->getCode(),
                $exception
            );
        }

        if ($status !== 0) {
            throw new InitiateDatabaseException(InitiateDatabaseException::EXECUTE_MIGRATIONS_PROBLEM, $status);
        }
    }

    /**
     * @param string $sqlFilePath
     *
     * @throws InitiateDatabaseException
     */
    private function executeSqlQueryFromFile(PDO $connection, string $sqlFilePath): void
    {
        $queries = file_get_contents($sqlFilePath);
        if (!$queries) {
            throw new InitiateDatabaseException(InitiateDatabaseException::READ_SQL_FILE_PROBLEM);
        }

        $connection->exec($queries);
    }

    private function getDatabaseConnection(
        string $host,
        int $port,
        string $username,
        string $password,
        string $name
    ): PDO {
        $dbConnection = new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s', $host, $port, $name),
            $username,
            $password,
            [Database::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
        );
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $dbConnection;
    }
}
