<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PDO;

class DatabaseChecker implements DatabaseCheckerInterface
{
    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    public function __construct(BasicContextInterface $basicContext)
    {
        $this->basicContext = $basicContext;
    }

    /**
     * {@inheritdoc}
     */
    public function canCreateDatabase(
        string $host,
        int $port,
        string $user,
        string $password,
        string $name
    ): void {
        try {
            $connection = $this->getDatabaseConnection($host, $port, $user, $password);
            $connection->exec("USE `{$name}`");
            $connection->exec('SELECT 1 FROM ' . $this->basicContext->getConfigTableName() . ' LIMIT 1');
        } catch (\PDOException $exception) {
            return;
        }
        throw new DatabaseExistsAndNotEmptyException("Database `$name` already exists and is not empty");
    }

    private function getDatabaseConnection(string $host, int $port, string $user, string $password): PDO
    {
        return new \PDO(
            sprintf('mysql:host=%s;port=%s', $host, $port),
            $user,
            $password,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}
