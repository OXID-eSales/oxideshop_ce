<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18.04.18
 * Time: 13:38
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Factory;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Class ConnectionFactory
 *
 * Factory for the Symfony DI container to fetch the database
 * connection.
 *
 * @package OxidEsales\EshopCommunity\Internal\Common\Factory
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
