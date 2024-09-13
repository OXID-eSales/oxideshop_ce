<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database;

use Doctrine\DBAL\DriverManager;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use Doctrine\DBAL\Connection;

class SetupDbConnectionFactory implements SetupDbConnectionFactoryInterface
{
    public function getServerConnection(DatabaseConfiguration $databaseConfiguration): Connection
    {
        $connection = DriverManager::getConnection(
            [
                'url' => sprintf(
                    '%s://%s:%s@%s:%d',
                    $databaseConfiguration->getScheme(),
                    $databaseConfiguration->getUser(),
                    $databaseConfiguration->getPass(),
                    $databaseConfiguration->getHost(),
                    $databaseConfiguration->getPort(),
                ),
            ]
        );
        $connection->connect();

        return $connection;
    }

    public function getDatabaseConnection(DatabaseConfiguration $databaseConfiguration): Connection
    {
        $connection = DriverManager::getConnection(
            ['url' => $databaseConfiguration->getDatabaseUrl(),]
        );
        $connection->connect();

        return $connection;
    }
}
