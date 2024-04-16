<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\DriverManager;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\BootstrapConfigurationFactory;

class BootstrapConnectionFactory
{
    private static Connection $connection;

    public static function create(): Connection
    {
        if (!isset(self::$connection)) {
            $bootstrapConfiguration = (new BootstrapConfigurationFactory())->create();
            self::$connection = DriverManager::getConnection(
                ['url' => $bootstrapConfiguration->getDatabaseUrl()]
            );
        }

        return self::$connection;
    }
}
