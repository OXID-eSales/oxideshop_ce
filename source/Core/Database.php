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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\Eshop\Core;

use ADOConnection;
use ADODB_Exception;
use mysql_driver_ADOConnection;
use mysqli;
use mysqli_driver_ADOConnection;
use oxAdoDbException;
use oxConnectionException;
use oxDb;
use OxidEsales\Eshop\Core\exception\DatabaseException;
use oxLegacyDb;
use oxRegistry;
use PHPMailer;

// Including main ADODB include
require_once __DIR__ . '/adodblite/adodb.inc.php';

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
    const FETCH_MODE_NUM = ADODB_FETCH_NUM;

    /**
     * Fetch mode - associative
     *
     * @var int
     */
    const FETCH_MODE_ASSOC = ADODB_FETCH_ASSOC;

    /**
     * Configuration value
     *
     * @var mixed
     */
    public static $configSet = false;

    /**
     * oxDb instance.
     *
     * @var Database
     */
    protected static $_instance = null;

    /**
     * Database connection object
     *
     * @var Database
     */
    protected static $_oDB = null;

    /**
     * Database tables descriptions cache array
     *
     * @var array
     */
    protected static $_aTblDescCache = array();

    /**
     * Database type
     *
     * @var string
     */
    private static $_dbType = '';

    /**
     * Database user name
     *
     * @var string
     */
    private static $_dbUser = '';

    /**
     * Database password
     *
     * @var string
     */
    private static $_dbPwd = '';

    /**
     * Database table name
     *
     * @var string
     */
    private static $_dbName = '';

    /**
     * Database hostname
     *
     * @var string
     */
    private static $_dbHost = '';

    /**
     * Debug option value
     *
     * @var int
     */
    private static $_iDebug = 0;

    /**
     * Should changes be logged in admin
     *
     * @var bool
     */
    private static $_blLogChangesInAdmin = false;

    /**
     * UTF mode
     *
     * @var int
     */
    private static $_iUtfMode = 0;

    /**
     * Default database connection value
     *
     * @var string
     */
    private static $_sDefaultDatabaseConnection = null;

    /**
     * Array of slave hosts
     *
     * @var array
     */
    private static $_aSlaveHosts;

    /**
     * Admin email value
     *
     * @var string
     */
    private static $_sAdminEmail;

    /**
     * Value for master slave balance
     *
     * @var int
     */
    private static $_iMasterSlaveBalance;

    /**
     * Local time format  value
     *
     * @var string
     */
    private static $_sLocalTimeFormat;

    /**
     * Local date format value
     *
     * @var string
     */
    private static $_sLocalDateFormat;

    /**
     * Returns Singleton instance
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (!self::$_instance instanceof Database) {
            //do not use simple oxNew here as it goes to eternal cycle
            self::$_instance = new oxDb();
        }

        return self::$_instance;
    }

    /**
     * Returns database object
     *
     * @param int $fetchMode - fetch mode default numeric - 0
     *
     * @throws oxConnectionException error while initiating connection to DB
     *
     * @return oxLegacyDb
     */
    public static function getDb($fetchMode = oxDb::FETCH_MODE_NUM)
    {
        if (self::$_oDB === null) {
            self::$_oDB = static::createDatabase();
        }
        self::$_oDB->setFetchMode($fetchMode);

        return self::$_oDB;
    }

    /**
     * Sets configs object with method getVar() and properties needed for successful connection.
     *
     * @param object $config configs.
     */
    public static function setConfig($config)
    {
        self::$_dbType = $config->getVar('dbType');
        self::$_dbUser = $config->getVar('dbUser');
        self::$_dbPwd = $config->getVar('dbPwd');
        self::$_dbName = $config->getVar('dbName');
        self::$_dbHost = $config->getVar('dbHost');
        self::$_iDebug = $config->getVar('iDebug');
        self::$_blLogChangesInAdmin = $config->getVar('blLogChangesInAdmin');
        self::$_iUtfMode = $config->getVar('iUtfMode');
        self::$_sDefaultDatabaseConnection = $config->getVar('sDefaultDatabaseConnection');
        self::$_aSlaveHosts = $config->getVar('aSlaveHosts');
        self::$_iMasterSlaveBalance = $config->getVar('iMasterSlaveBalance');
        self::$_sAdminEmail = $config->getVar('sAdminEmail');
        self::$_sLocalTimeFormat = $config->getVar('sLocalTimeFormat');
        self::$_sLocalDateFormat = $config->getVar('sLocalDateFormat');
    }

    /**
     * Setter for database connection object
     *
     * @param Database $newDbObject
     */
    public static function setDbObject($newDbObject)
    {
        self::$_oDB = $newDbObject;
    }

    /**
     * Database connection object getter
     *
     * @return Database
     */
    public static function getDbObject()
    {
        return self::$_oDB;
    }

    /**
     * Quotes an array.
     *
     * @param array $arrayOfStrings array of strings to quote
     *
     * @deprecated since v5.2.0 (2014-03-12); use oxLegacyDb::quoteArray()
     *
     * @return array
     */
    public function quoteArray($arrayOfStrings)
    {
        return self::getDb()->quoteArray($arrayOfStrings);
    }

    /**
     * Call to reset table description cache
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
     *
     * @param string $string string which will be escaped
     *
     * @return string
     */
    public function escapeString($string)
    {
        $result = trim(self::getDb()->quote($string), "'");
        return $result;
    }

    /**
     * Cal function is admin from oxFunction. Need to mock in tests.
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
     * @param string $configName returning config name.
     *
     * @return mixed
     */
    protected static function _getConfigParam($configName)
    {
        if (isset(self::$$configName)) {
            return self::$$configName;
        }

        return null;
    }

    /**
     * Creates database instance and returns it.
     *
     * @return oxLegacyDb
     */
    protected static function createDatabase()
    {
        $databaseFactory = self::getInstance();

        // Setting configuration on the first call
        $databaseFactory->setConfig(oxRegistry::get("oxConfigFile"));

        // Session related parameters. Don't change.
        global $ADODB_SESSION_TBL,
               $ADODB_SESSION_CONNECT,
               $ADODB_SESSION_DRIVER,
               $ADODB_SESSION_USER,
               $ADODB_SESSION_PWD,
               $ADODB_SESSION_DB,
               $ADODB_SESS_LIFE,
               $ADODB_SESS_DEBUG;

        // The default setting is 3000 * 60, but actually changing this will give no effect as now redefinition of this constant
        // appears after OXID custom settings are loaded and $ADODB_SESS_LIFE depends on user settings.
        // You can find the redefinition of ADODB_SESS_LIFE @ oxconfig.php:: line ~ 390.
        $ADODB_SESS_LIFE = 3000 * 60;
        $ADODB_SESSION_TBL = "oxsessions";
        $ADODB_SESSION_DRIVER = self::_getConfigParam('_dbType');
        $ADODB_SESSION_USER = self::_getConfigParam('_dbUser');
        $ADODB_SESSION_PWD = self::_getConfigParam('_dbPwd');
        $ADODB_SESSION_DB = self::_getConfigParam('_dbName');
        $ADODB_SESSION_CONNECT = self::_getConfigParam('_dbHost');
        $ADODB_SESS_DEBUG = false;

        $database = new oxLegacyDb();
        $databaseConnection = $databaseFactory->_getDbInstance();
        $database->setConnection($databaseConnection);

        return $database;
    }

    /**
     * Returns database instance object for given type
     *
     * @deprecated on b-dev (2015-10-23); Use self::createDatabaseConnection() instead.
     *
     * @param bool $instanceType instance type
     *
     * @return mysql_driver_ADOConnection|mysqli_driver_ADOConnection
     */
    protected function _getDbInstance($instanceType = false)
    {
        return $this->createDatabaseConnection($instanceType);
    }

    /**
     * Returns database instance object for given type
     *
     * @param bool $instanceType instance type
     *
     * @return mysql_driver_ADOConnection|mysqli_driver_ADOConnection
     */
    protected function createDatabaseConnection($instanceType)
    {
        $databaseType = self::_getConfigParam("_dbType");

        /** @var mysql_driver_ADOConnection|mysqli_driver_ADOConnection $connection */
        $connection = ADONewConnection($databaseType, $this->_getModules());

        try {
            $this->connectToDatabase($connection, $instanceType);
        } catch (oxAdoDbException $e) {
            $this->_onConnectionError($e);
        }
        self::_setUp($connection);

        return $connection;
    }

    /**
     * Returns which AdoDbLite modules should be loaded when creating database connection.
     *
     * @return string
     */
    protected function _getModules()
    {
        $debugLevel = self::_getConfigParam('_iDebug');

        $this->_registerAdoDbExceptionHandler();

        $modules = '';
        if ($debugLevel == 2 || $debugLevel == 3 || $debugLevel == 4 || $debugLevel == 7) {
            $modules = 'perfmon';
        }

        if ($this->isAdmin() && self::_getConfigParam('_blLogChangesInAdmin')) {
            $modules .= ($modules ? ':' : '') . 'oxadminlog';
        }

        return $modules;
    }

    /**
     * Initiates actual database connection.
     *
     * @param mysql_driver_ADOConnection|mysqli_driver_ADOConnection $connection
     * @param bool                                                   $instanceType
     */
    protected function connectToDatabase($connection, $instanceType)
    {
        $host = self::_getConfigParam("_dbHost");
        $user = self::_getConfigParam("_dbUser");
        $password = self::_getConfigParam("_dbPwd");
        $databaseName = self::_getConfigParam("_dbName");

        $connection->connect($host, $user, $password, $databaseName);
    }

    /**
     * Registers AdoDb exceptions handler for SQL errors
     */
    protected function _registerAdoDbExceptionHandler()
    {
        global $ADODB_EXCEPTION;
        $ADODB_EXCEPTION = 'oxAdoDbException';

        include_once __DIR__ . '/adodblite/adodb-exceptions.inc.php';
    }

    /**
     * Setting up connection parameters - sql mode, encoding, logging etc
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
     *
     * @param ADOConnection $connection database connection instance
     */
    protected function prepareDatabaseConnection($connection)
    {
        $debugLevel = self::_getConfigParam('_iDebug');
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

        $connection->cacheSecs = 60 * 10; // 10 minute caching
        $connection->execute('SET @@session.sql_mode = ""');

        if (self::_getConfigParam('_iUtfMode')) {
            $connection->execute('SET NAMES "utf8"');
            $connection->execute('SET CHARACTER SET utf8');
            $connection->execute('SET CHARACTER_SET_CONNECTION = utf8');
            $connection->execute('SET CHARACTER_SET_DATABASE = utf8');
            $connection->execute('SET character_set_results = utf8');
            $connection->execute('SET character_set_server = utf8');
        } elseif (($encoding = self::_getConfigParam('_sDefaultDatabaseConnection')) != '') {
            $connection->execute('SET NAMES "' . $encoding . '"');
        }
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
    protected function _sendMail($email, $subject, $body)
    {
        $mailer = new PHPMailer();
        $mailer->isMail();

        $mailer->From = $email;
        $mailer->AddAddress($email);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        return $mailer->send();
    }

    /**
     * Notifying shop owner about connection problems
     *
     * @param \Exception $exception Database exception
     *
     * @throws DatabaseException
     */
    protected function _notifyConnectionErrors(\Exception $exception)
    {
        // notifying shop owner about connection problems
        if (($adminEmail = self::_getConfigParam('_sAdminEmail'))) {
            $failedShop = isset($_REQUEST['shp']) ? addslashes($_REQUEST['shp']) : 'Base shop';

            $date = date('l dS of F Y h:i:s A');
            $script = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            $referer = $_SERVER['HTTP_REFERER'];

            //sending a message to admin
            $warningSubject = 'Offline warning!';
            $warningBody = "
                Database error in OXID eShop:
                Date: {$date}
                Shop: {$failedShop}

                mysql error: " . $exception->getMessage() . "
                mysql error no: " . $exception->getCode() . "

                Script: {$script}
                Referer: {$referer}";

            $this->_sendMail($adminEmail, $warningSubject, $warningBody);
        }

        // Re throw the exception
        $message = 'EXCEPTION_CONNECTION_NODB';
        $code = $exception->getCode();
        // @todo Add DatabaseConnectionException, which implements oxConnectionException methods and is used instead
        //$exception = oxNew('\OxidEsales\Eshop\Core\exception\DatabaseException', $message, $code, $exception);
        $exception = oxNew('oxConnectionException', $message, $code, $exception);

        // $exception->setConnectionError(self::_getConfigParam('_dbUser') . 's' . getShopBasePath() . $exception->getMessage());
        throw $exception;
    }

    /**
     * In case of connection error - redirects to setup
     * or send notification message for shop owner
     *
     * @param \Exception $exception Database exception
     */
    protected function _onConnectionError(\Exception $exception)
    {
        $config = join('', file(getShopBasePath() . 'config.inc.php'));

        if (strpos($config, '<dbHost>') !== false &&
            strpos($config, '<dbName>') !== false
        ) {
            // pop to setup as there is something wrong
            //oxRegistry::getUtils()->redirect( "setup/index.php", true, 302 );
            $headerCode = "HTTP/1.1 302 Found";
            header($headerCode);
            header("Location: Setup/index.php");
            header("Connection: close");
            exit();
        } else {
            // notifying about connection problems
            $this->_notifyConnectionErrors($exception);
        }
    }

    /**
     * Extracts and returns table metadata from DB.
     *
     * @param string $tableName
     *
     * @return array
     */
    protected function formTableDescription($tableName)
    {
        return self::getDb()->MetaColumns($tableName);
    }
}
