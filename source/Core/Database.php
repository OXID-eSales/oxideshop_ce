<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace OxidEsales\Eshop\Core;

use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Doctrine as DatabaseAdapter;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException;

/**
 * Database connection class
 */
class Database
{

    /**
     * Fetch mode - numeric
     *
     * @var int
     */
    const FETCH_MODE_NUM = DatabaseInterface::FETCH_MODE_NUM;

    /**
     * Fetch mode - associative
     *
     * @var int
     */
    const FETCH_MODE_ASSOC = DatabaseInterface::FETCH_MODE_ASSOC;

    /**
     * A singleton instance of this class or a sub class of this class
     *
     * @var null|Database
     */
    protected static $instance = null;

    /**
     * Database connection object
     *
     * @var null|DatabaseAdapter
     */
    protected static $db = null;

    /**
     * Database tables descriptions cache array
     *
     * @var array
     */
    protected static $tblDescCache = array();

    /**
     * Database type
     *
     * @var null|ConfigFile
     */
    protected $configFile;

    /**
     * This class is a singleton and should be instantiated with getInstance().
     *
     * @deprecated in v5.3.0 (2016-06-08) The constructor will be protected in the future. Use getInstance() instead.
     *
     * Database constructor.
     */
    public function __construct()
    {
    }

    /**
     * As this class is a singleton, an instance of this class must not be cloned.
     *
     * @throws \Exception
     */
    public function __clone()
    {
        throw new \Exception("This object is a singleton, thou shalt not clone.");
    }

    /**
     * Returns the singleton instance of this class or of a sub class of this class
     *
     * @return Database The singleton instance.
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
     * @param int $fetchMode - fetch mode default numeric - 0
     *
     * @throws DatabaseConnectionException error while initiating connection to DB
     *
     * @return DatabaseInterface
     */
    public static function getDb($fetchMode = Database::FETCH_MODE_NUM)
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
     * Sets class properties needed for a successful database connection
     *
     * @param ConfigFile $configFile The file config.inc.php wrapped in an object
     */
    public function setConfigFile(ConfigFile $configFile)
    {
        $this->configFile = $configFile;
    }

    /**
     * Extracts and returns table metadata from DB.
     *
     * @param string $tableName Name of table to invest.
     *
     * @return array
     */
    public function getTableDescription($tableName)
    {
        // simple cache
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
     * Creates database connection and returns it.
     *
     * @throws DatabaseConnectionException
     * @throws DatabaseNotConfiguredException
     *
     * @return DatabaseInterface
     */
    protected function createDatabase()
    {
        /** Call to fetchConfigFile redirects to setup wizard, if shop has not been configured. */
        $configFile = $this->fetchConfigFile();

        /** Validate the configuration file */
        $this->validateConfigFile($configFile);

        /** Set config file to be able to read shop configuration within the class */
        $this->setConfigFile($configFile);

        /** @var array $connectionParameters Parameters needed for the database connection */
        $connectionParameters = $this->getConnectionParameters();

        $databaseAdapter = new DatabaseAdapter();
        $databaseAdapter->setConnectionParameters($connectionParameters);
        $databaseAdapter->connect();

        return $databaseAdapter;
    }

    /**
     * Post connect hook. This method is called right after the connection to the database has been established.
     */
    protected function onPostConnect()
    {
        $this->setSqlMode();

        // @todo Set database logging from iDebug
        // @todo Set user auditing from blLogChangesInAdmin
    }

    /**
     * Set the sql_mode of the MySQL server for the session.
     */
    protected function setSqlMode()
    {
        static::getDb()->execute('SET @@session.sql_mode = ""');
    }

    /**
     * Get an instance of the config file.
     *
     * @throws DatabaseNotConfiguredException
     *
     * @return ConfigFile
     */
    protected function fetchConfigFile()
    {
        /**
         * Do the configuration of the database connection parameters
         */
        /** @var ConfigFile $configFile */
        $configFile = Registry::get('oxConfigFile');

        return $configFile;
    }

    /**
     * Validate configuration file.
     * The parameters are validated and on failure the method behaves like this:
     * - if the shop is has not been configured yet, throws a DatabaseNotConfiguredException
     *
     * @param ConfigFile $configFile
     *
     * @throws DatabaseNotConfiguredException
     */
    protected function validateConfigFile(ConfigFile $configFile)
    {
        $isDatabaseConfigured = $this->isDatabaseConfigured($configFile);
        if (!$isDatabaseConfigured) {
            throw new DatabaseNotConfiguredException('The database connection has not been configured in config.inc.php', 0);
        }
    }

    /**
     * Get all parameters needed to connect to the database.
     *
     * @return array
     */
    protected function getConnectionParameters()
    {
        /** Collect the parameters, that are necessary to initialize the database connection */
        /**
         * @var string $databaseDriver
         * At the moment the database adapter uses always 'pdo_mysql'
         */
        $databaseDriver = $this->getConfigParam('dbType');
        /**
         * @var string $databaseHost
         * The database host to connect to.
         * Be aware, that the connection between the MySQL client and the server is unencrypted.
         */
        $databaseHost = $this->getConfigParam('dbHost');
        /**
         * @var integer $databasePort
         * TCP port to connect to
         */
        $databasePort = (int) $this->getConfigParam('dbPort');
        if (!$databasePort) {
            $databasePort = 3306;
        }
        /**
         * @var string $databaseName
         * The name of the database or scheme to connect to
         */
        $databaseName = $this->getConfigParam('dbName');
        /**
         * @var string $databaseUser
         * The user id of the database user
         */
        $databaseUser = $this->getConfigParam('dbUser');
        /**
         * @var string $databasePassword
         * The password of the database user
         */
        $databasePassword = $this->getConfigParam('dbPwd');

        $connectionParameters = array(
            'default' => array(
                'databaseDriver'    => $databaseDriver,
                'databaseHost'      => $databaseHost,
                'databasePort'      => $databasePort,
                'databaseName'      => $databaseName,
                'databaseUser'      => $databaseUser,
                'databasePassword'  => $databasePassword,
            )
        );

        /** The charset has to be set during the connection to the database */
        if ($this->getConfigParam('iUtfMode')) {
            $charset = 'utf8';
        } else {
            $charset = $this->getConfigParam('sDefaultDatabaseConnection');
        }
        if ($charset) {
            $connectionParameters['default'] = array_merge($connectionParameters['default'], array('connectionCharset' => $charset));
        }

        return $connectionParameters;
    }

    /**
     * Return local config value by given name.
     *
     * @param string $configVar returning config name.
     *
     * @return mixed
     */
    protected function getConfigParam($configVar)
    {
        return $this->configFile->getVar($configVar);
    }

    /**
     * Return false if the database connection has not been configured in the eShop configuration file.
     *
     * @param ConfigFile $config
     *
     * @return bool
     */
    protected function isDatabaseConfigured(ConfigFile $config)
    {
        $isValid = true;
        // If the shop has not been configured yet the hostname has the format '<dbHost>'
        if (false  !== strpos($config->getVar('dbHost'), '<')) {
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Call function is admin from oxFunction. Need to mock in tests.
     *
     * @return bool
     */
    protected function isAdmin()
    {
        return isAdmin();
    }
}
