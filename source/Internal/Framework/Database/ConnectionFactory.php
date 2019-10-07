<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;

/**
 * Factory for the Symfony DI container to fetch the database
 * connection.
 */
class ConnectionFactory
{
    /**
     * Uses reflection to fetch the database connection from
     * the DatabaseProvider.
     *
     * @return Connection
     * @throws DatabaseConnectionException
     * @throws \ReflectionException
     */
    public static function get()
    {
        $database = DatabaseProvider::getDb();
        $r = new \ReflectionMethod(Database::class, 'getConnection');
        $r->setAccessible(true);

        return $r->invoke($database);
    }
}
