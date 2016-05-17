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

use ADOConnection;
use ADODB_Exception;
use mysql_driver_ADOConnection;
use mysqli_driver_ADOConnection;
use oxAdoDbException;
use oxConnectionException;
use OxidEsales\Eshop\Core\Database\DatabaseInterface;
use OxidEsales\Eshop\Core\Database\Doctrine as DatabaseAdapter;
use OxidEsales\Eshop\Core\exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\exception\DatabaseException;
use PHPMailer;

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
    protected static $_aTblDescCache = array();

    /**
     * Database type
     *
     * @var null|ConfigFile
     */
    protected $configFile;

    /**
     * This class is a singleton and should be instantiated with getInstance().
     *
     * @Deprecated in v6.0. The constructor will be protected in the future. Use getInstance() instead.
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
        throw new \Exception("You must not clone this object as it is a singleton.");
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
     * Returns database object
     *
     * @param int $fetchMode - fetch mode default numeric - 0
     *
     * @throws oxConnectionException error while initiating connection to DB
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
     * Creates database instance and returns it.
     *
     * @return DatabaseInterface
     */
    protected function createDatabase()
    {
        /**
         * We use an instance here in order to be able to call non static methods.
         * It will be nicer to test, if we do not have a lot of static methods.
         */
        $databaseFactory = static::getInstance();

        /** @var array $connectionParameters Parameters for the database connection */
        $connectionParameters = $databaseFactory->getConnectionParameters($databaseFactory);

        $databaseAdapter = new DatabaseAdapter();
        $databaseAdapter->setConnectionParameters($connectionParameters);
        // TODO Set debug mode
        try {
            $databaseAdapter->connect();
        } catch (DatabaseConnectionException $exception) {
            $databaseFactory->onConnectionError($exception);
        }

        return $databaseAdapter;
    }

    /**
     *
     */
    protected function onPostConnect()
    {
        // Todo implement functionality of prepareDatabaseConnection here and than refactor it to a own method
        // $databaseFactory->prepareDatabaseConnection(static::$_oDB);
        $this->setSqlMode();
    }

    /**
     * Get all parameters needed to connect to the database.
     * The parameters are validated and on failure the method behaves like this:
     * - if the shop is has not been configured yet, redirect to setup page
     * - Todo Add more validations
     *
     * @param Database $databaseFactory A singleton instance of this class
     *
     * @return array
     */
    protected function getConnectionParameters($databaseFactory)
    {
        /**
         * Do the configuration of the database connection parameters
         */
        /** @var ConfigFile $configFile */
        // TODO This has to use namespaces
        $configFile = Registry::get('oxConfigFile');

        /*
         * Validate configuration.
         */

        /** If the shop has not already been configured, the user is redirected to the OXID eShop setup page. */
        $isDatabaseConfigured = $databaseFactory->isDatabaseConfigured($configFile);
        if (!$isDatabaseConfigured) {
            self::redirectToSetupWizard();
        }

        /** Set local configuration parameters */
        $databaseFactory->setConfigFile($configFile);

        /** ------- TODO split this method here ------ */

        /** Collect the parameters, that are necessary to initialize the database connection */
        $connectionParameters = $databaseFactory->buildConnectionParameters();

        return $connectionParameters;
    }

    /**
     * Retrieve the connection related configuration parameters from the class configuration and return them in an array.
     *
     * @return array
     */
    protected function buildConnectionParameters()
    {
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
            'databaseDriver'    => $databaseDriver,
            'databaseHost'      => $databaseHost,
            'databasePort'      => $databasePort,
            'databaseName'      => $databaseName,
            'databaseUser'      => $databaseUser,
            'databasePassword'  => $databasePassword,
        );

        /** The charset has to be set during the connection to the database */
        if ($this->getConfigParam('iUtfMode')) {
            $charset = 'utf8';
        } else {
            $charset = $this->getConfigParam('sDefaultDatabaseConnection');
        }
        if ($charset) {
            $connectionParameters = array_merge($connectionParameters, array('connectionCharset' => $charset));
        }

        return $connectionParameters;
    }

    /**
     * Return false if the database connection has not been configured in the eShop configuration file.
     *
     * @param ConfigFile $config
     *
     * @return bool
     */
    protected static function isDatabaseConfigured(ConfigFile $config)
    {
        $isValid = true;

        // If the shop has not been configured yet the hostname has the format '<dbHost>'
        if (false  !== strpos($config->getVar('dbHost'), '<')) {
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * Sets class properties needed for a successful database connection
     *
     * @param ConfigFile $configFile The file config.inc.php wrapped in an object
     */
    public function setConfigFile(ConfigFile $configFile)
    {

        $this->configFile = $configFile;

        // Connection data
        $dbType = $this->configFile->getVar('dbType');
        $dbUser = $this->configFile->getVar('dbUser');
        $dbPwd = $this->configFile->getVar('dbPwd');
        $dbName = $this->configFile->getVar('dbName');
        $dbHost = $this->configFile->getVar('dbHost');

        // Debugging / performance / logging
        $debug = $this->configFile->getVar('iDebug');

        // Auditing
        $logChangesInAdmin = $this->configFile->getVar('blLogChangesInAdmin');

        // Database connection charsets
        $utfMode = $this->configFile->getVar('iUtfMode'); // utf8
        $defaultDatabaseConnection = $this->configFile->getVar('sDefaultDatabaseConnection'); // charset that differs from utf8 and latin1

        // Email address to send warnings to
        $adminEmail = $this->configFile->getVar('sAdminEmail');

        // time formats
        $localTimeFormat = $this->configFile->getVar('sLocalTimeFormat');
        $localDateFormat = $this->configFile->getVar('sLocalDateFormat');

        // Master-slave move
        $slaveHosts = $this->configFile->getVar('aSlaveHosts');

        // Database load balancing
        $masterSlaveBalance = $this->configFile->getVar('iMasterSlaveBalance');

    }

    /**
     * Setter for database connection object
     * Todo Ask shiftas of the use of this method and if it can be removed
     *
     * @param null|Database $newDbObject
     */
    public static function setDbObject($newDbObject)
    {
        self::$db = $newDbObject;
    }

    /**
     * Database connection object getter
     * Todo Ask shiftas of the use of this method and if it can be removed
     *
     * @return Database
     */
    public static function getDbObject()
    {
        return self::$db;
    }

    /**
     * Call to reset table description cache
     * Todo Check, if this could be private, it is used only in tests
     */
    public function resetTblDescCache()
    {
        self::$_aTblDescCache = array();
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
        if (!isset(self::$_aTblDescCache[$tableName])) {
            self::$_aTblDescCache[$tableName] = $this->formTableDescription($tableName);
        }

        return self::$_aTblDescCache[$tableName];
    }

    /**
     * Checks if given string is valid database field name.
     * It must contain from alphanumeric plus dot and underscore symbols
     * Todo refactor and move to Doctrine class
     * @See http://stackoverflow.com/questions/4977898/check-for-valid-sql-column-name, especially the notes on portability
     *
     * @param string $field field name
     *
     * @return bool
     */
    public function isValidFieldName($field)
    {
        return (boolean) getStr()->preg_match("#^[\w\d\._]*$#", $field);
    }

    /**
     * Escape string for using in mysql statements
     * Todo deprecate in 5.3 and move to doctrine class
     *
     * @param string $string string which will be escaped
     *
     * @return string
     */
    public function escapeString($string)
    {
        return trim(self::getDb()->quote($string), "'");
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

    /**
     * Return local config value by given name.
     *
     * @param string $configVar returning config name.
     *
     * @return mixed
     */
    protected function getConfigParam($configVar)
    {
        if (isset(self::$$configName)) {
            return self::$$configName;
        }
    }

    /**
     * Redirect to the OXID eShop setup wizard
     */
    protected static function redirectToSetupWizard()
    {
        $headerCode = "HTTP/1.1 302 Found";
        header($headerCode);
        header("Location: Setup/index.php");
        header("Connection: close");
        exit();
    }

    /**
     * Redirect to the OXID eShop maintenance wizard
     */
    protected function redirectToMaintenancePage()
    {
        $headerCode = "HTTP/1.1 302 Found";
        header($headerCode);
        header("Location: offline.html");
        header("Connection: close");
        exit();
    }

    /**
     * Returns database instance object for given type
     * Todo Remove this method
     *
     * @param bool $instanceType instance type
     *
     * @return mysql_driver_ADOConnection|mysqli_driver_ADOConnection
     */
    protected function createDatabaseConnection($instanceType)
    {
        $databaseType = $this->getConfigParam("dbType");

        /** @var mysql_driver_ADOConnection|mysqli_driver_ADOConnection $connection */
        $connection = ADONewConnection($databaseType, $this->_getModules());

        try {
            $this->connectToDatabase($connection, $instanceType);
        } catch (oxAdoDbException $e) {
            $this->onConnectionError($e);
        }
        self::_setUp($connection);

        return $connection;
    }

    /**
     * Returns which AdoDbLite modules should be loaded when creating database connection.
     * Todo Implement admin auditing/logging with doctrine
     * Todo Implement perfmon with doctrine (?)
     * Todo remove this method
     *
     * @return string
     */
    protected function _getModules()
    {
        $debugLevel = $this->getConfigParam('iDebug');

        $this->_registerAdoDbExceptionHandler();

        $modules = '';
        if ($debugLevel == 2 || $debugLevel == 3 || $debugLevel == 4 || $debugLevel == 7) {
            $modules = 'perfmon';
        }

        if ($this->isAdmin() && $this->getConfigParam('blLogChangesInAdmin')) {
            $modules .= ($modules ? ':' : '') . 'oxadminlog';
        }

        return $modules;
    }

    /**
     * Initiates actual database connection.
     * Todo remove this method
     *
     * @param mysql_driver_ADOConnection|mysqli_driver_ADOConnection $connection
     * @param bool                                                   $instanceType
     */
    protected function connectToDatabase($connection, $instanceType)
    {
        $host = $this->getConfigParam("dbHost");
        $user = $this->getConfigParam("dbUser");
        $password = $this->getConfigParam("dbPwd");
        $databaseName = $this->getConfigParam("dbName");

        $connection->connect($host, $user, $password, $databaseName);
    }

    /**
     * Registers AdoDb exceptions handler for SQL errors
     * Todo remove this method
     */
    protected function _registerAdoDbExceptionHandler()
    {
        global $ADODB_EXCEPTION;
        $ADODB_EXCEPTION = 'oxAdoDbException';

        include_once __DIR__ . '/adodblite/adodb-exceptions.inc.php';
    }

    /**
     * Setting up connection parameters - sql mode, encoding, logging etc
     * Todo remove this method
     *
     * @deprecated on b-dev (2015-10-23); Use self::prepareDatabaseConnection() instead.
     *
     * @param ADOConnection $connection database connection instance
     */
    protected function _setUp($connection)
    {
        $this->prepareDatabaseConnection($connection);
    }

    /**
     * Setting up connection parameters - sql mode, encoding, logging etc.
     * Todo remove this method
     *
     * @param DatabaseInterface $connection database connection instance
     */
    protected function prepareDatabaseConnection(DatabaseInterface $connection)
    {
        $debugLevel = $this->getConfigParam('iDebug');
        if ($debugLevel == 2 || $debugLevel == 3 || $debugLevel == 4 || $debugLevel == 7) {
            try {
                $connection->execute('truncate table adodb_logsql');
            } catch (ADODB_Exception $e) {
                // nothing
            }
            if (method_exists($connection, "logSQL")) {
                $connection->logSQL(true);
            }
        }

        /**
         * This property does not exist in ADODB lite
         * $connection->cacheSecs = 60 * 10; // 10 minute caching
         */

        /**
         * Reset sql_mode
         */
        $connection->execute('SET @@session.sql_mode = ""');
    }

    /**
     * Returns $mailer instance
     *
     * @param string $email   email address
     * @param string $subject subject
     * @param string $body    email body
     *
     * @return PHPMailer
     */
    protected function sendMail($email, $subject, $body)
    {
        $mailer = new PHPMailer();
        $mailer->isMail();

        $mailer->setFrom($email);
        $mailer->addAddress($email);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        return $mailer->send();
    }

    /**
     * Notify the shop owner about connection problems
     *
     * @param \Exception $exception Database exception
     *
     * @throws DatabaseException
     */
    protected function notifyConnectionErrors(\Exception $exception)
    {
        if (($adminEmail = $this->getConfigParam('sAdminEmail'))) {
            $failedShop = isset($_REQUEST['shp']) ? addslashes($_REQUEST['shp']) : 'Base shop';

            $date = date(DATE_RFC822); // RFC 822 (example: Mon, 15 Aug 05 15:52:01 +0000)
            $script = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            $referrer = $_SERVER['HTTP_REFERER'];

            //sending a message to admin
            $warningSubject = 'Offline warning!';
            $warningBody = "
                Database error in OXID eShop:
                Date: {$date}
                Shop: {$failedShop}

                mysql error: " . $exception->getMessage() . "
                mysql error no: " . $exception->getCode() . "

                Script: {$script}
                Referrer: {$referrer}";

            $this->sendMail($adminEmail, $warningSubject, $warningBody);
        }

        // Re throw the exception
        $message = 'EXCEPTION_CONNECTION_NODB';
        $code = $exception->getCode();
        // @todo Add DatabaseConnectionException, which implements oxConnectionException methods and is used instead
        $exception = new DatabaseException($message, $code, $exception);
        // $exception->setConnectionError(self::_getConfigParam('dbUser') . 's' . getShopBasePath() . $exception->getMessage());
        throw $exception;
    }

    /**
     * In case of connection error - redirects to setup
     * or send notification message for shop owner
     * The exception is not rethrown as the shop tries t6o use the database during its exception handling which will cause an uncaught exception
     *
     * @param DatabaseConnectionException $exception Database exception
     */
    protected function onConnectionError(DatabaseConnectionException $exception)
    {
        /**
         * Log the exception
         */
        $this->logException($exception);

        /**
         * Notify the the admin about the connection error
         */
        $this->notifyConnectionErrors($exception);

        /**
         * Redirect to maintenance page
         */
        $this->redirectToMaintenancePage();
    }

    /**
     * Todo This method is deprecated since v5.2.0 and has to be removed
     *
     * @param array $array
     *
     * @return array
     */
    public function quoteArray(array $array)
    {
        return static::getDb()->quoteArray($array);
    }

    /**
     * @param DatabaseConnectionException $exception
     */
    protected function logException(DatabaseConnectionException $exception)
    {
        $exception->debugOut();
    }

    /**
     * Extracts and returns table metadata from DB.
     * This method is extended in the Enterprise Edition.
     *
     * @param string $tableName
     *
     * @return array
     */
    protected function formTableDescription($tableName)
    {
        return static::getDb()->metaColumns($tableName);
    }

    protected function setSqlMode()
    {
        static::getDb()->execute('SET @@session.sql_mode = ""');
    }
}
