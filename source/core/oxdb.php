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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */


// Including main ADODB include
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

/**
 * Database connection class
 */
class oxDb
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
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    public static $configSet = false;

    /**
     * oxDb instance.
     *
     * @var oxdb
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be renamed in v6.0.
     */
    protected static $_instance = null;

    /**
     * Database connection object
     *
     * @var oxdb
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be renamed in v6.0.
     */
    protected static $_oDB = null;

    /**
     * Database tables descriptions cache array
     *
     * @var array
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be renamed in v6.0.
     */
    protected static $_aTblDescCache = array();

    /**
     * Database type
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_dbType = '';

    /**
     * Database user name
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_dbUser = '';

    /**
     * Database password
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_dbPwd = '';

    /**
     * Database table name
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_dbName = '';

    /**
     * Database hostname
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_dbHost = '';

    /**
     * Debug option value
     *
     * @var int
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_iDebug = 0;

    /**
     * Should changes be logged in admin
     *
     * @var bool
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_blLogChangesInAdmin = false;

    /**
     * UTF mode
     *
     * @var int
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_iUtfMode = 0;

    /**
     * Default database connection value
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_sDefaultDatabaseConnection = null;

    /**
     * Array of slave hosts
     *
     * @var array
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_aSlaveHosts;

    /**
     * Admin email value
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_sAdminEmail;

    /**
     * @deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
     *
     * Value for master slave balance
     *
     * @var int
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_iMasterSlaveBalance;

    /**
     * Local time format  value
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_sLocalTimeFormat;

    /**
     * Local date format value
     *
     * @var string
     *
     * @deprecated since v5.3.5 (2017-10-05); This property will be removed in v6.0.
     */
    private static $_sLocalDateFormat;

    /**
     * Sets configs object with method getVar() and properties needed for successful connection.
     *
     * @param object $oConfig configs.
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     */
    public static function setConfig($oConfig)
    {
        self::$_dbType = $oConfig->getVar('dbType');
        self::$_dbUser = $oConfig->getVar('dbUser');
        self::$_dbPwd = $oConfig->getVar('dbPwd');
        self::$_dbName = $oConfig->getVar('dbName');
        self::$_dbHost = $oConfig->getVar('dbHost');
        self::$_iDebug = $oConfig->getVar('iDebug');
        self::$_blLogChangesInAdmin = $oConfig->getVar('blLogChangesInAdmin');
        self::$_iUtfMode = $oConfig->getVar('iUtfMode');
        self::$_sDefaultDatabaseConnection = $oConfig->getVar('sDefaultDatabaseConnection');
        self::$_aSlaveHosts = $oConfig->getVar('aSlaveHosts');
        //deprecated since v5.3.0 (2016-06-17). Different solution in 6.0.
        self::$_iMasterSlaveBalance = $oConfig->getVar('iMasterSlaveBalance');
        //end deprecated
        self::$_sAdminEmail = $oConfig->getVar('sAdminEmail');
        self::$_sLocalTimeFormat = $oConfig->getVar('sLocalTimeFormat');
        self::$_sLocalDateFormat = $oConfig->getVar('sLocalDateFormat');
    }

    /**
     * Return local config value by given name.
     *
     * @param string $sConfigName returning config name.
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     *
     * @return mixed
     */
    protected static function _getConfigParam($sConfigName)
    {
        if (isset(self::$$sConfigName)) {
            return self::$$sConfigName;
        }

        return null;
    }

    /**
     * Returns Singleton instance
     *
     * @return oxdb
     */
    public static function getInstance()
    {
        // disable caching for test modules
        if (defined('OXID_PHP_UNIT')) {
            self::$_instance = modInstances::getMod(__CLASS__);
        }

        if (!self::$_instance instanceof oxDb) {

            //do not use simple oxNew here as it goes to eternal cycle
            self::$_instance = new oxDb();

            if (defined('OXID_PHP_UNIT')) {
                modInstances::addMod(__CLASS__, self::$_instance);
            }
        }

        return self::$_instance;
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
     * Returns adodb modules string
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     *
     * @return string
     */
    protected function _getModules()
    {
        $_iDebug = self::_getConfigParam('_iDebug');

        $this->_registerAdoDbExceptionHandler();

        $sModules = '';
        if ($_iDebug == 2 || $_iDebug == 3 || $_iDebug == 4 || $_iDebug == 7) {
            $sModules = 'perfmon';
        }

        // log admin changes ?
        if ($this->isAdmin() && self::_getConfigParam('_blLogChangesInAdmin')) {
            $sModules .= ($sModules ? ':' : '') . 'oxadminlog';
        }

        return $sModules;
    }

    /**
     * Registers AdoDb exceptions handler for SQL errors
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     */
    protected function _registerAdoDbExceptionHandler()
    {
        global $ADODB_EXCEPTION;
        $ADODB_EXCEPTION = 'oxAdoDbException';

        include_once getShopBasePath() . 'core/adodblite/adodb-exceptions.inc.php';
    }

    /**
     * Setting up connection parameters - sql mode, encoding, logging etc
     *
     * @param ADOConnection $oDb database connection instance
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     */
    protected function _setUp($oDb)
    {
        // @deprecated since v5.3.0 (2016-06-07).
        // The SQL logging feature is deprecated. This feature will be removed.
        $_iDebug = self::_getConfigParam('_iDebug');
        if ($_iDebug == 2 || $_iDebug == 3 || $_iDebug == 4 || $_iDebug == 7) {
            try {
                $oDb->execute('truncate table adodb_logsql');
            } catch (ADODB_Exception $e) {
                // nothing
            }
            if (method_exists($oDb, "logSQL")) {
                $oDb->logSQL(true);
            }
        }
        // END deprecated

        $oDb->cacheSecs = 60 * 10; // 10 minute caching
        $oDb->execute('SET @@session.sql_mode = ""');

        if (self::_getConfigParam('_iUtfMode')) {
            $oDb->execute('SET NAMES "utf8"');
            $oDb->execute('SET CHARACTER SET utf8');
            $oDb->execute('SET CHARACTER_SET_CONNECTION = utf8');
            $oDb->execute('SET CHARACTER_SET_DATABASE = utf8');
            $oDb->execute('SET character_set_results = utf8');
            $oDb->execute('SET character_set_server = utf8');
        } elseif (($sConn = self::_getConfigParam('_sDefaultDatabaseConnection')) != '') {
            $oDb->execute('SET NAMES "' . $sConn . '"');
        }
    }

    /**
     * Returns $oMailer instance
     *
     * @param string $sEmail   email address
     * @param string $sSubject subject
     * @param string $sBody    email body
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     *
     * @return phpmailer
     */
    protected function _sendMail($sEmail, $sSubject, $sBody)
    {
        include_once getShopBasePath() . 'core/phpmailer/class.phpmailer.php';
        include_once getShopBasePath() . 'core/phpmailer/class.smtp.php';
        $oMailer = new phpmailer();
        $oMailer->isMail();

        $oMailer->From = $sEmail;
        $oMailer->AddAddress($sEmail);
        $oMailer->Subject = $sSubject;
        $oMailer->Body = $sBody;

        return $oMailer->send();
    }

    /**
     * Notifying shop owner about connection problems
     *
     * @param ADOConnection $oDb database connection instance
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     */
    protected function _notifyConnectionErrors($oDb)
    {
        // notifying shop owner about connection problems
        if (($sAdminEmail = self::_getConfigParam('_sAdminEmail'))) {
            $sFailedShop = isset($_REQUEST['shp']) ? addslashes($_REQUEST['shp']) : 'Base shop';

            $sDate = date('l dS of F Y h:i:s A');
            $sScript = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            $sReferer = $_SERVER['HTTP_REFERER'];

            //sending a message to admin
            $sWarningSubject = 'Offline warning!';
            $sWarningBody = "
                Database error in OXID eShop:
                Date: {$sDate}
                Shop: {$sFailedShop}

                mysql error: " . $oDb->errorMsg() . "
                mysql error no: " . $oDb->errorNo() . "

                Script: {$sScript}
                Referer: {$sReferer}";

            $this->_sendMail($sAdminEmail, $sWarningSubject, $sWarningBody);
        }

        //only exception to default construction method
        $oEx = new oxConnectionException();
        $oEx->setMessage('EXCEPTION_CONNECTION_NODB');
        $oEx->setConnectionError(self::_getConfigParam('_dbUser') . 's' . getShopBasePath() . $oDb->errorMsg());
        throw $oEx;
    }

    /**
     * In case of connection error - redirects to setup
     * or send notification message for shop owner
     *
     * @param ADOConnection $oDb database connection instance
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     */
    protected function _onConnectionError($oDb)
    {
        $sVerPrefix = '';
        $sVerPrefix = '_ce';


        $sConfig = join('', file(getShopBasePath() . 'config.inc.php'));

        if (strpos($sConfig, '<dbHost' . $sVerPrefix . '>') !== false &&
            strpos($sConfig, '<dbName' . $sVerPrefix . '>') !== false
        ) {
            // pop to setup as there is something wrong
            //oxRegistry::getUtils()->redirect( "setup/index.php", true, 302 );
            $sHeaderCode = "HTTP/1.1 302 Found";
            header($sHeaderCode);
            header("Location: setup/index.php");
            header("Connection: close");
            exit();
        } else {
            // notifying about connection problems
            $this->_notifyConnectionErrors($oDb);

        }
    }


    /**
     * Returns database instance object for given type
     *
     * @param int $iInstType instance type
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     *
     * @return ADONewConnection
     */
    protected function _getDbInstance($iInstType = false)
    {
        $sHost = self::_getConfigParam("_dbHost");
        $sUser = self::_getConfigParam("_dbUser");
        $sPwd = self::_getConfigParam("_dbPwd");
        $sName = self::_getConfigParam("_dbName");
        $sType = self::_getConfigParam("_dbType");

        $oDb = ADONewConnection($sType, $this->_getModules());


        try {
            $oDb->connect($sHost, $sUser, $sPwd, $sName);
        } catch (oxAdoDbException $e) {
            $this->_onConnectionError($oDb);
        }

        self::_setUp($oDb);

        return $oDb;
    }

    /**
     * Returns database object
     *
     * @param int $iFetchMode - fetch mode default numeric - 0
     *
     * @throws oxConnectionException error while initiating connection to DB
     *
     * @return DatabaseInterface
     */
    public static function getDb($iFetchMode = oxDb::FETCH_MODE_NUM)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (isset(modDB::$unitMOD) && is_object(modDB::$unitMOD)) {
                return modDB::$unitMOD;
            }
        }

        if (self::$_oDB === null) {

            $oInst = self::getInstance();

            //setting configuration on the first call
            $oInst->setConfig(oxRegistry::get("oxConfigFile"));

            // @deprecated since v5.3.0 (2016-06-07).
            // As of v6.0 it is not longer possible to use the database for PHP session handling.
            global $ADODB_SESSION_TBL,
                   $ADODB_SESSION_CONNECT,
                   $ADODB_SESSION_DRIVER,
                   $ADODB_SESSION_USER,
                   $ADODB_SESSION_PWD,
                   $ADODB_SESSION_DB,
                   $ADODB_SESS_LIFE,
                   $ADODB_SESS_DEBUG;

            // session related parameters. don't change.

            //Tomas
            //the default setting is 3000 * 60, but actually changing this will give no effect as now redefinition of this constant
            //appears after OXID custom settings are loaded and $ADODB_SESS_LIFE depends on user settings.
            //You can find the redefinition of ADODB_SESS_LIFE @ oxconfig.php:: line ~ 390.
            $ADODB_SESS_LIFE = 3000 * 60;
            $ADODB_SESSION_TBL = "oxsessions";
            $ADODB_SESSION_DRIVER = self::_getConfigParam('_dbType');
            $ADODB_SESSION_USER = self::_getConfigParam('_dbUser');
            $ADODB_SESSION_PWD = self::_getConfigParam('_dbPwd');
            $ADODB_SESSION_DB = self::_getConfigParam('_dbName');
            $ADODB_SESSION_CONNECT = self::_getConfigParam('_dbHost');
            $ADODB_SESS_DEBUG = false;
            // END deprecated


            $oDb = new oxLegacyDb();
            $oDbInst = $oInst->_getDbInstance();
            $oDb->setConnection($oDbInst);

            self::$_oDB = $oDb;
        }

        self::$_oDB->setFetchMode($iFetchMode);

        return self::$_oDB;
    }

    /**
     * Quotes an array.
     *
     * @param array $aStrArray array of strings to quote
     *
     * @deprecated since v5.2.0 (2014-03-12); use oxLegacyDb::quoteArray()
     *
     * @return array
     */
    public function quoteArray($aStrArray)
    {
        return self::getDb()->quoteArray($aStrArray);
    }

    /**
     * Call to reset table description cache
     *
     * @deprecated since v5.3.0 (2016-06-06); This method will be removed in v6.0.
     */
    public function resetTblDescCache()
    {
        self::$_aTblDescCache = array();
    }

    /**
     * Extracts and returns table metadata from DB.
     *
     * @param string $sTableName Name of table to invest.
     *
     * @return array
     */
    public function getTableDescription($sTableName)
    {
        // simple cache
        if (isset(self::$_aTblDescCache[$sTableName])) {
            return self::$_aTblDescCache[$sTableName];
        }

        $aFields = self::getDb()->MetaColumns($sTableName);

        self::$_aTblDescCache[$sTableName] = $aFields;

        return $aFields;
    }

    /**
     * Checks if given string is valid database field name.
     * It must contain from alphanumeric plus dot and underscore symbols
     *
     * @param string $sField field name
     *
     * @deprecated since v5.3.0 (2016-06-06); This method will be removed in v6.0.
     *
     * @return bool
     */
    public function isValidFieldName($sField)
    {
        return ( boolean ) getStr()->preg_match("#^[\w\d\._]*$#", $sField);
    }

    /**
     * Get connection ID
     *
     * @deprecated since v5.3.5 (2017-10-05); This method will be removed in v6.0.
     *
     * @return link identifier
     */
    protected function _getConnectionId()
    {
        return self::getDb()->getDb()->connectionId;
    }

    /**
     * Escape string for using in mysql statements
     *
     * @param string $sString string which will be escaped
     *
     * @deprecated since v5.3.0 (2016-06-06); This method will be removed in v6.0. Use oxLegacyDb::quote() to quote values.
     *             As of v6.0 there will be a new method quoteIdentifier() to quote strings in order to use them as identifiers.
     *
     * @return string
     */
    public function escapeString($sString)
    {
        if ('mysql' == self::_getConfigParam("_dbType")) {
            return mysql_real_escape_string($sString, $this->_getConnectionId());
        } elseif ('mysqli' == self::_getConfigParam("_dbType")) {
            return mysqli_real_escape_string($this->_getConnectionId(), $sString);
        } else {
            return mysql_real_escape_string($sString, $this->_getConnectionId());
        }
    }
}
