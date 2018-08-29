<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Factory;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Factory for the Symfony DI container to fetch the database
 * connection.
 *
 * @internal
 */
class ConnectionFactory
{

    /**
     * Uses reflection to fetch the database connection from
     * the DatabaseProvider.
     *
     * @return Connection
     */
    public static function get()
    {

        $database = DatabaseProvider::getDb();
        $r = new \ReflectionMethod(Database::class, 'getConnection');
        $r->setAccessible(true);

        return $r->invoke($database);
    }
}
