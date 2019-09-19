<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database as DatabaseAdapter;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException;

/**
 * Database connection class
 *
 * @deprecated since v6.5.0 (2019-09-24); Use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface
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
     * @var null|DatabaseProvider A singleton instance of this class or a sub class of this class
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
     * @var null|ConfigFile Database type
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
     * Returns the singleton instance of this class or of a sub class of this class.
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
     * @throws DatabaseConnectionException Error while initiating connection to DB.
     *
     * @return DatabaseInterface
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
     * @throws DatabaseConnectionException Error while initiating connection to DB
     *
     * @return DatabaseInterface
     */
    public static function getMaster($fetchMode = \OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_NUM)
    {
        static::getDb($fetchMode)->forceMasterConnection();

        return static::getDb($fetchMode);
    }

    /**
     * Sets class properties needed for a successful database connection
     *
     * @param ConfigFile $configFile The file config.inc.php wrapped in an object
     */
    public function setConfigFile(\OxidEsales\Eshop\Core\ConfigFile $configFile)
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
     * Flush the table description cache of this class.
     */
    public function flushTableDescriptionCache()
    {
        self::$tblDescCache = [];
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
     * Post connect hook. This method is called only once per connection right after the connection to the database has
     * been established.
     */
    protected function onPostConnect()
    {
        // @todo Set database logging from iDebug
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
        $configFile = Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class);

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
    protected function validateConfigFile(\OxidEsales\Eshop\Core\ConfigFile $configFile)
    {
        $isDatabaseConfigured = $this->isDatabaseConfigured($configFile);

        if (!$isDatabaseConfigured) {
            throw new \OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException(
                'The database connection has not been configured in config.inc.php',
                0
            );
        }
    }

    /**
     * Get all parameters needed to connect to the database.
     *
     * @return array
     */
    protected function getConnectionParameters()
    {
        /** Collect the parameters, that are necessary to initialize the database connection: */

        /**
         * @var string $databaseDriver At the moment the database adapter uses always 'pdo_mysql'.
         */
        $databaseDriver = $this->getConfigParam('dbType');
        /**
         * @var string $databaseHost The database host to connect to.
         * Be aware, that the connection between the MySQL client and the server is unencrypted.
         */
        $databaseHost = $this->getConfigParam('dbHost');
        /**
         * @var integer $databasePort TCP port to connect to.
         */
        $databasePort = (int) $this->getConfigParam('dbPort');
        if (!$databasePort) {
            $databasePort = 3306;
        }
        /**
         * @var string $databaseName The name of the database or scheme to connect to.
         */
        $databaseName = $this->getConfigParam('dbName');
        /**
         * @var string $databaseUser The user id of the database user.
         */
        $databaseUser = $this->getConfigParam('dbUser');
        /**
         * @var string $databasePassword The password of the database user.
         */
        $databasePassword = $this->getConfigParam('dbPwd');

        $connectionParameters = [
            'default' => [
                'databaseDriver'    => $databaseDriver,
                'databaseHost'      => $databaseHost,
                'databasePort'      => $databasePort,
                'databaseName'      => $databaseName,
                'databaseUser'      => $databaseUser,
                'databasePassword'  => $databasePassword,
            ]
        ];

        /**
         * The charset has to be set during the connection to the database.
         */
        $charset = (string) $this->getConfigParam('dbCharset');
        //backwards compatibility with old config files.
        if (null == $charset) {
            $charset = 'utf8';
        }
        
        $connectionParameters['default'] = array_merge($connectionParameters['default'], ['connectionCharset' => $charset]);

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
    protected function isDatabaseConfigured(\OxidEsales\Eshop\Core\ConfigFile $config)
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
