<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Configuration as DbalConfiguration;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\DriverManager;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\DatabaseLoggerFactoryInterface;

class ConnectionFactory implements ConnectionFactoryInterface
{
    private readonly Connection $connection;

    public function __construct(
        private readonly ConnectionParameterProviderInterface $connectionParameterProvider,
        private readonly DatabaseLoggerFactoryInterface $databaseLoggerFactory,
    ) {
    }

    public function create(): Connection
    {
        if (!isset($this->connection)) {
            $dbalConfiguration = new DbalConfiguration();
            $dbalConfiguration->setSQLLogger(
                $this->databaseLoggerFactory->getDatabaseLogger()
            );
            $this->connection = DriverManager::getConnection(
                $this->connectionParameterProvider->getParameters(),
                $dbalConfiguration,
            );
        }
        return $this->connection;
    }
}
