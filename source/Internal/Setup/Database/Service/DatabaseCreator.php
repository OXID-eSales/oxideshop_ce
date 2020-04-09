<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\CreateDatabaseException;
use PDO;

/**
 * Class DatabaseCreator
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Database
 */
class DatabaseCreator implements DatabaseCreatorInterface
{

    /** @var PDO */
    private $dbConnection;

    /**
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     * @param string $name
     *
     * @throws CreateDatabaseException
     */
    public function createDatabase(string $host, int $port, string $username, string $password, string $name): void
    {
        $this->getDatabaseConnection($host, $port, $username, $password);

        if ($this->isDatabaseExist($name)) {
            throw new CreateDatabaseException(CreateDatabaseException::DATABASE_IS_EXIST);
        }

        $this->dbConnection->exec('CREATE DATABASE ' . $name . ' CHARACTER SET utf8 COLLATE utf8_general_ci;');
    }

    /**
     * @param string $host
     * @param int    $port
     * @param string $username
     * @param string $password
     *
     * @throws CreateDatabaseException
     */
    private function getDatabaseConnection(string $host, int $port, string $username, string $password): void
    {
        try {
            $this->dbConnection = new PDO(
                sprintf('mysql:host=%s;port=%s', $host, $port),
                $username,
                $password,
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8']
            );
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\Throwable $exception) {
            throw new CreateDatabaseException(
                CreateDatabaseException::CONNECTION_PROBLEM,
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isDatabaseExist(string $name): bool
    {
        try {
            $this->dbConnection->exec('USE ' . $name);
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }
}
