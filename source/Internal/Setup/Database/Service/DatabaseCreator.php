<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseConnectionException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsException;
use PDO;

/**
 * Class DatabaseCreator.
 */
class DatabaseCreator implements DatabaseCreatorInterface
{
    /**
     * @var PDO
     */
    private $dbConnection;

    /**
     * @throws DatabaseExistsException
     * @throws DatabaseConnectionException
     */
    public function createDatabase(string $host, int $port, string $username, string $password, string $name): void
    {
        $this->getDatabaseConnection($host, $port, $username, $password);

        if ($this->isDatabaseExist($name)) {
            throw new DatabaseExistsException();
        }

        $this->dbConnection->exec('CREATE DATABASE ' . $name . ' CHARACTER SET utf8 COLLATE utf8_general_ci;');
    }

    /**
     * @throws DatabaseConnectionException
     */
    private function getDatabaseConnection(string $host, int $port, string $username, string $password): void
    {
        try {
            $this->dbConnection = new PDO(
                sprintf('mysql:host=%s;port=%s', $host, $port),
                $username,
                $password,
                [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                ]
            );
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\Throwable $exception) {
            throw new DatabaseConnectionException('Failed: Unable to connect to database', $exception->getCode(), $exception);
        }
    }

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
