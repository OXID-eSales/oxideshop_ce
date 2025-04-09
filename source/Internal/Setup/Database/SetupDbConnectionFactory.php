<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Connection;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;

class SetupDbConnectionFactory implements SetupDbConnectionFactoryInterface
{
    public function getServerConnection(DatabaseConfiguration $databaseConfiguration): Connection
    {
        $connectionParameters = $databaseConfiguration->getConnectionParameters();
        unset($connectionParameters['dbname']);
        return $this->getConnection($connectionParameters);
    }

    public function getDatabaseConnection(DatabaseConfiguration $databaseConfiguration): Connection
    {
        return $this->getConnection($databaseConfiguration->getConnectionParameters());
    }

    private function getConnection(array $parameters): Connection
    {
        $connection =  DriverManager::getConnection($parameters);
        $connection->getServerVersion();

        return $connection;
    }
}
