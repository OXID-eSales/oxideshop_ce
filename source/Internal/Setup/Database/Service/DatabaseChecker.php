<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PDO;

/**
 * Class DatabaseCreator
 *
 * @package OxidEsales\EshopCommunity\Internal\Setup\Database
 */
class DatabaseChecker implements DatabaseCheckerInterface
{

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    /**
     * @param BasicContextInterface $basicContext
     */
    public function __construct(BasicContextInterface $basicContext)
    {
        $this->basicContext = $basicContext;
    }

    /**
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     * @param string $name
     *
     * @return bool
     */
    public function checkIfDatabaseExistsAndNotEmpty(
        string $host,
        int $port,
        string $user,
        string $password,
        string $name
    ): bool {
        try {
            $connection = $this->getDatabaseConnection($host, $port, $user, $password);

            $connection->exec("USE `{$name}`");

            $connection->exec('SELECT 1 FROM ' . $this->basicContext->getConfigTableName() . ' LIMIT 1');
        } catch (\PDOException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @param string $host
     * @param int    $port
     * @param string $user
     * @param string $password
     *
     * @return PDO
     */
    private function getDatabaseConnection(string $host, int $port, string $user, string $password): PDO
    {
        return new \PDO(
            sprintf('mysql:host=%s;port=%s', $host, $port),
            $user,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }
}
