<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Core;

use Exception;
use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;

/**
 * @deprecated since v6.4.0 (2019-09-24) use QueryBuilderFactoryInterface
 * @see \OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface
 */
class DatabaseProvider
{
    /**
     * @var int Fetch mode - numeric
     */
    const FETCH_MODE_NUM = DatabaseInterface::FETCH_MODE_NUM;

    /**
     * @var int Fetch mode - associative
     */
    const FETCH_MODE_ASSOC = DatabaseInterface::FETCH_MODE_ASSOC;

    /**
     * @var ?DatabaseProvider
     */
    protected static $instance = null;

    /**
     * @var null|DatabaseInterface Database connection object
     */
    protected static $db = null;

    /**
     * @var array Database tables descriptions cache array
     */
    protected static $tblDescCache = [];

    /**
     * This class is a singleton and should be instantiated with getInstance().
     */
    private function __construct()
    {
    }

    /**
     * @throws Exception
     */
    public function __clone()
    {
        throw new Exception("This object is a singleton, thou shalt not clone.");
    }

    /**
     * Returns the singleton instance of this class or of a subclass of this class.
     *
     * @return DatabaseProvider The singleton instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Return the database connection instance as a singleton.
     *
     * @param int $fetchMode The fetch mode. Default is numeric (0).
     *
     * @return DatabaseInterface
     * @throws DatabaseConnectionException Error while initiating connection to DB.
     *
     */
    public static function getDb($fetchMode = \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_NUM)
    {
        if (null === static::$db) {
            $databaseFactory = static::getInstance();
            static::$db = $databaseFactory->createDatabase();

            /** Post connect actions will be taken only once per connection */
            $databaseFactory->onPostConnect();
        }

        /** The following actions be taken on each call to getDb */
        static::$db->setFetchMode($fetchMode);

        return static::$db;
    }

    /**
     * Return the database master connection instance as a singleton.
     * In case the shop is not allowed a master/slave setup, this function
     * is simply a wrapper for DatabaseProvider::getDb.
     *
     * @param int $fetchMode The fetch mode. Default is numeric (0).
     *
     * @return DatabaseInterface
     * @throws DatabaseConnectionException Error while initiating connection to DB
     *
     */
    public static function getMaster($fetchMode = \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_NUM)
    {
        static::getDb($fetchMode)->forceMasterConnection();

        return static::getDb($fetchMode);
    }

    /**
     * @param string $tableName Name of table to invest.
     *
     * @return array
     */
    public function getTableDescription($tableName)
    {
        if (!isset(self::$tblDescCache[$tableName])) {
            self::$tblDescCache[$tableName] = $this->fetchTableDescription($tableName);
        }

        return self::$tblDescCache[$tableName];
    }

    /**
     * Extracts and returns table metadata from DB.
     * This method is extended in the Enterprise Edition.
     *
     * @param string $tableName
     *
     * @return array
     */
    protected function fetchTableDescription($tableName)
    {
        return static::getDb()->metaColumns($tableName);
    }

    /**
     * @return DatabaseInterface
     * @throws DatabaseConnectionException
     *
     */
    protected function createDatabase()
    {
        $databaseAdapter = new Database();
        $databaseAdapter->connect();

        return $databaseAdapter;
    }

    /**
     * Post connect hook. This method is called only once per connection right after the connection to the database has
     * been established.
     */
    protected function onPostConnect()
    {
    }
}
