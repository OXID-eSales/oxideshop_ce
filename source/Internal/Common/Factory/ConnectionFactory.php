<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18.04.18
 * Time: 13:38
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Factory;

use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;
use Ray\Di\ProviderInterface;

class ConnectionFactory
{
    public static function get()
    {

        $database = DatabaseProvider::getDb();
        $r = new \ReflectionMethod(Database::class, 'getConnection');
        $r->setAccessible(true);

        return $r->invoke($database);
    }
}