<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Configuration as DbalConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

readonly class ConnectionFactory implements ConnectionFactoryInterface
{
    private Connection $connection;

    public function __construct(
        private ConnectionParameterProviderInterface $connectionParameterProvider,
        private iterable $middlewares,
    ) {
    }

    public function create(): Connection
    {
        if (!isset($this->connection)) {
            $dbalConfiguration = new DbalConfiguration();
            $dbalConfiguration->setMiddlewares(
                iterator_to_array($this->middlewares)
            );
            $this->connection = DriverManager::getConnection(
                $this->connectionParameterProvider->getParameters(),
                $dbalConfiguration,
            );
        }

        return $this->connection;
    }
}
