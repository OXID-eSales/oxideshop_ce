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
 * @copyright (C) OXID eSales AG 2003-2014
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
     * @var int
     */
    const FETCH_MODE_NUM = ADODB_FETCH_NUM;

    /**
     * Fetch mode - associative
     * @var int
     */
    const FETCH_MODE_ASSOC = ADODB_FETCH_ASSOC;

    /**
     * Configuration value
     * @var mixed
     */
    public static $configSet = false;

    /**
     * oxDb instance.
     *
     * @var oxdb
     */
    protected static $_instance = null;

    /**
     * Database connection object
     *
     * @var oxdb
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
     * @var string
     */
    private static $_dbType = '';

    /**
     * Database user name
     * @var string
     */
    private static $_dbUser = '';

    /**
     * Database password
     * @var string
     */
    private static $_dbPwd  = '';

    /**
     * Database table name
     * @var string
     */
    private static $_dbName = '';

    /**
     * Database hostname
     * @var string
     */
    private static $_dbHost = '';

    /**
     * Debug option value
     * @var int
     */
    private static $_iDebug = 0;

    /**
     * Should changes be logged in admin
     * @var bool
     */
    private static $_blLogChangesInAdmin = false;

    /**
     * UTF mode
     * @var int
     */
    private static $_iUtfMode = 0;

    /**
     * Default database connection value
     * @var string
     */
    private static $_sDefaultDatabaseConnection = null;

    /**
     * Array of slave hosts
     * @var array
     */
    private static $_aSlaveHosts;

    /**
     * Admin email value
     * @var string
     */
    private static $_sAdminEmail;

    /**
     * Value for master slave balance
     * @var int
     */
    private static $_iMasterSlaveBalance;

    /**
     * Local time format  value
     * @var string
     */
    private static $_sLocalTimeFormat;

    /**
     * Local date format value
     * @var string
     */
    private static $_sLocalDateFormat;

    /**
     * Sets configs object with method getVar() and properties needed for successful connection.
     *
     * @param object $oConfig configs.
     *
     * @return void
     */
    public static function setConfig( $oConfig )
    {
        self::$_dbType                     = $oConfig->getVar( 'dbType' );
        self::$_dbUser                     = $oConfig->getVar( 'dbUser' );
        self::$_dbPwd                      = $oConfig->getVar( 'dbPwd' );
        self::$_dbName                     = $oConfig->getVar( 'dbName' );
        self::$_dbHost                     = $oConfig->getVar( 'dbHost' );
        self::$_iDebug                     = $oConfig->getVar( 'iDebug' );
        self::$_blLogChangesInAdmin        = $oConfig->getVar( 'blLogChangesInAdmin' );
        self::$_iUtfMode                   = $oConfig->getVar( 'iUtfMode' );
        self::$_sDefaultDatabaseConnection = $oConfig->getVar( 'sDefaultDatabaseConnection' );
        self::$_aSlaveHosts                = $oConfig->getVar( 'aSlaveHosts' );
        self::$_iMasterSlaveBalance        = $oConfig->getVar( 'iMasterSlaveBalance' );
        self::$_sAdminEmail                = $oConfig->getVar( 'sAdminEmail' );
        self::$_sLocalTimeFormat           = $oConfig->getVar( 'sLocalTimeFormat' );
        self::$_sLocalDateFormat           = $oConfig->getVar( 'sLocalDateFormat' );
    }

    /**
     * Return local config value by given name.
     *
     * @param string $sConfigName returning config name.
     *
     * @return mixed
     */
    protected static function _getConfigParam( $sConfigName )
    {
        if ( isset( self::$$sConfigName ) ) {
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
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            self::$_instance = modInstances::getMod( __CLASS__ );
        }

        if ( !self::$_instance instanceof oxDb ) {

            //do not use simple oxNew here as it goes to eternal cycle
            self::$_instance = new oxDb();

            if ( defined( 'OXID_PHP_UNIT' ) ) {
                modInstances::addMod( __CLASS__, self::$_instance);
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
     * @return string
     */
    protected function _getModules()
    {
        //adding exception handler for SQL errors
        $_iDebug = self::_getConfigParam( '_iDebug' );

        global $ADODB_EXCEPTION;
        $ADODB_EXCEPTION = 'oxAdoDbException';
        include_once getShopBasePath() . 'core/adodblite/adodb-exceptions.inc.php';

        $sModules = '';
        if (  $_iDebug == 2 || $_iDebug == 3 || $_iDebug == 4 || $_iDebug == 7  ) {
            $sModules = 'perfmon';
        }

        // log admin changes ?
        if ( $this->isAdmin() && self::_getConfigParam( '_blLogChangesInAdmin' ) ) {
            $sModules .= ( $sModules ? ':' : '' ) . 'oxadminlog';
        }

        return $sModules;
    }

    /**
     * Setting up connection parameters - sql mode, encoding, logging etc
     *
     * @param ADOConnection $oDb database connection instance
     *
     * @return null
     */
    protected function _setUp( $oDb )
    {
        $_iDebug = self::_getConfigParam( '_iDebug' );
        if ( $_iDebug == 2 || $_iDebug == 3 || $_iDebug == 4  || $_iDebug == 7 ) {
            try {
                $oDb->execute( 'truncate table adodb_logsql' );
            } catch ( ADODB_Exception $e ) {
                // nothing
            }
            if ( method_exists( $oDb, "logSQL" ) ) {
                $oDb->logSQL( true );
            }
        }

        $oDb->cacheSecs = 60 * 10; // 10 minute caching
        $oDb->execute( 'SET @@session.sql_mode = ""' );

        if ( self::_getConfigParam( '_iUtfMode' ) ) {
            $oDb->execute( 'SET NAMES "utf8"' );
            $oDb->execute( 'SET CHARACTER SET utf8' );
            $oDb->execute( 'SET CHARACTER_SET_CONNECTION = utf8' );
            $oDb->execute( 'SET CHARACTER_SET_DATABASE = utf8' );
            $oDb->execute( 'SET character_set_results = utf8' );
            $oDb->execute( 'SET character_set_server = utf8' );
        } elseif ( ( $sConn = self::_getConfigParam('_sDefaultDatabaseConnection') ) != '' ) {
            $oDb->execute( 'SET NAMES "' . $sConn . '"' );
        }
    }

    /**
     * Returns $oMailer instance
     *
     * @param string $sEmail   email address
     * @param string $sSubject subject
     * @param string $sBody    email body
     *
     * @return phpmailer
     */
    protected function _sendMail( $sEmail, $sSubject, $sBody )
    {
        include_once getShopBasePath() . 'core/phpmailer/class.phpmailer.php';
        $oMailer = new phpmailer();
        $oMailer->isMail();

        $oMailer->From = $sEmail;
        $oMailer->AddAddress( $sEmail );
        $oMailer->Subject = $sSubject;
        $oMailer->Body = $sBody;
        return $oMailer->send();
    }

    /**
     * Notifying shop owner about connection problems
     *
     * @param ADOConnection $oDb database connection instance
     *
     * @return null
     */
    protected function _notifyConnectionErrors( $oDb )
    {
        // notifying shop owner about connection problems
        if ( ( $sAdminEmail = self::_getConfigParam( '_sAdminEmail' ) ) ) {
            $sFailedShop = isset( $_REQUEST['shp'] ) ? addslashes( $_REQUEST['shp'] ) : 'Base shop';

            $sDate = date( 'l dS of F Y h:i:s A');
            $sScript  = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            $sReferer = $_SERVER['HTTP_REFERER'];

            //sending a message to admin
            $sWarningSubject = 'Offline warning!';
            $sWarningBody = "
                Database error in OXID eShop:
                Date: {$sDate}
                Shop: {$sFailedShop}

                mysql error: " . $oDb->errorMsg()."
                mysql error no: " . $oDb->errorNo()."

                Script: {$sScript}
                Referer: {$sReferer}";

            $this->_sendMail( $sAdminEmail, $sWarningSubject, $sWarningBody );
        }

        //only exception to default construction method
        $oEx = new oxConnectionException();
        $oEx->setMessage( 'EXCEPTION_CONNECTION_NODB' );
        $oEx->setConnectionError( self::_getConfigParam( '_dbUser' ) . 's' . getShopBasePath() . $oDb->errorMsg() );
        throw $oEx;
    }

    /**
     * In case of connection error - redirects to setup
     * or send notification message for shop owner
     *
     * @param ADOConnection $oDb database connection instance
     *
     * @return null
     */
    protected function _onConnectionError( $oDb )
    {
        $sVerPrefix = '';
            $sVerPrefix = '_ce';



        $sConfig = join( '', file( getShopBasePath().'config.inc.php' ) );

        if ( strpos( $sConfig, '<dbHost'.$sVerPrefix.'>' ) !== false &&
             strpos( $sConfig, '<dbName'.$sVerPrefix.'>' ) !== false ) {
            // pop to setup as there is something wrong
            //oxRegistry::getUtils()->redirect( "setup/index.php", true, 302 );
            $sHeaderCode = "HTTP/1.1 302 Found";
            header( $sHeaderCode );
            header( "Location: setup/index.php" );
            header( "Connection: close" );
            exit();
        } else {
            // notifying about connection problems
            $this->_notifyConnectionErrors( $oDb );

        }
    }


    /**
     * Returns database instance object for given type
     *
     * @param int $iInstType instance type
     *
     * @return ADONewConnection
     */
    protected function _getDbInstance( $iInstType = false )
    {
        $sHost = self::_getConfigParam( "_dbHost" );
        $sUser = self::_getConfigParam( "_dbUser" );
        $sPwd  = self::_getConfigParam( "_dbPwd" );
        $sName = self::_getConfigParam( "_dbName" );
        $sType = self::_getConfigParam( "_dbType" );

        $oDb = ADONewConnection( $sType, $this->_getModules() );


            try {
                $oDb->connect( $sHost, $sUser, $sPwd, $sName );
            } catch( oxAdoDbException $e ) {
                $this->_onConnectionError( $oDb );
            }

        self::_setUp( $oDb );

        return $oDb;
    }

    /**
     * Returns database object
     *
     * @param int $iFetchMode - fetch mode default numeric - 0
     *
     * @throws oxConnectionException error while initiating connection to DB
     *
     * @return oxLegacyDb
     */
    public static function getDb( $iFetchMode = oxDb::FETCH_MODE_NUM )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modDB::$unitMOD ) && is_object( modDB::$unitMOD ) ) {
                return modDB::$unitMOD;
            }
        }

        if ( self::$_oDB === null ) {

            $oInst = self::getInstance();

            //setting configuration on the first call
            $oInst->setConfig( oxRegistry::get("oxConfigFile") );

             global  $ADODB_SESSION_TBL,
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
            $ADODB_SESS_LIFE       = 3000 * 60;
            $ADODB_SESSION_TBL     = "oxsessions";
            $ADODB_SESSION_DRIVER  = self::_getConfigParam( '_dbType' );
            $ADODB_SESSION_USER    = self::_getConfigParam( '_dbUser' );
            $ADODB_SESSION_PWD     = self::_getConfigParam( '_dbPwd' );
            $ADODB_SESSION_DB      = self::_getConfigParam( '_dbName' );
            $ADODB_SESSION_CONNECT = self::_getConfigParam( '_dbHost' );
            $ADODB_SESS_DEBUG      = false;

            $oDb = new oxLegacyDb();
            $oDbInst = $oInst->_getDbInstance();
            $oDb->setConnection( $oDbInst );

            self::$_oDB = $oDb;
        }

        self::$_oDB->setFetchMode( $iFetchMode );

        return self::$_oDB;
    }

    /**
     * Quotes an array.
     *
     * @param array $aStrArray array of strings to quote
     *
     * @return array
     */
    public function quoteArray( $aStrArray )
    {
        $oDb = self::getDb();

        foreach ( $aStrArray as $sKey => $sString ) {
            $aStrArray[$sKey] = $oDb->quote( $sString );
        }
        return $aStrArray;
    }

    /**
     * Call to reset table description cache
     *
     * @return null
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
    public function getTableDescription( $sTableName )
    {
        // simple cache
        if ( isset( self::$_aTblDescCache[$sTableName] ) ) {
            return self::$_aTblDescCache[$sTableName];
        }

            $aFields = self::getDb()->MetaColumns( $sTableName );

        self::$_aTblDescCache[$sTableName] = $aFields;

        return $aFields;
    }

    /**
     * Bidirectional converter for date/datetime field
     *
     * @param object $oObject       data field object
     * @param bool   $blToTimeStamp set TRUE to format MySQL compatible value
     * @param bool   $blOnlyDate    set TRUE to format "date" type field
     *
     * @deprecated from 2012-11-21, use oxRegistry::get('oxUtilsDate')->convertDBDateTime()
     *
     * @return string
     */
    public function convertDBDateTime( $oObject, $blToTimeStamp = false, $blOnlyDate = false )
    {
        return oxRegistry::get('oxUtilsDate')->convertDBDateTime( $oObject, $blToTimeStamp, $blOnlyDate );
    }

    /**
     * Bidirectional converter for timestamp field
     *
     * @param object $oObject       oxField type object that keeps db field info
     * @param bool   $blToTimeStamp if true - converts value to database compatible timestamp value
     *
     * @deprecated from 2012-11-21, use oxRegistry::get('oxUtilsDate')->convertDBTimestamp()
     *
     * @return string
     */
    public function convertDBTimestamp( $oObject, $blToTimeStamp = false )
    {
        return oxRegistry::get('oxUtilsDate')->convertDBTimestamp( $oObject, $blToTimeStamp );
    }

    /**
     * Bidirectional converter for date field
     *
     * @param object $oObject       oxField type object that keeps db field info
     * @param bool   $blToTimeStamp if true - converts value to database compatible timestamp value
     *
     * @deprecated from 2012-11-21, use oxRegistry::get('oxUtilsDate')->convertDBDate()
     *
     * @return string
     */
    public function convertDBDate( $oObject, $blToTimeStamp = false )
    {
        return oxRegistry::get('oxUtilsDate')->convertDBDate( $oObject, $blToTimeStamp );
    }

    /**
     * Checks if given string is valid database field name.
     * It must contain from alphanumeric plus dot and underscore symbols
     *
     * @param string $sField field name
     *
     * @return bool
     */
    public function isValidFieldName( $sField )
    {
        return ( boolean ) getStr()->preg_match( "#^[\w\d\._]*$#", $sField );
    }

    /**
     * sets default formatted value
     *
     * @param object $oObject          date field object
     * @param string $sDate            prefered date
     * @param string $sLocalDateFormat input format
     * @param string $sLocalTimeFormat local format
     * @param bool   $blOnlyDate       marker to format only date field (no time)
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _setDefaultFormatedValue( $oObject, $sDate, $sLocalDateFormat, $sLocalTimeFormat, $blOnlyDate )
    {
    }

    /**
     * defines and checks default time values
     *
     * @param bool $blToTimeStamp -
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _defineAndCheckDefaultTimeValues( $blToTimeStamp )
    {
    }

    /**
     * defines and checks default date values
     *
     * @param bool $blToTimeStamp marker how to format
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _defineAndCheckDefaultDateValues( $blToTimeStamp )
    {
    }

    /**
     * sets default date pattern
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _defaultDatePattern()
    {
    }

    /**
     * sets default time pattern
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _defaultTimePattern()
    {
    }

    /**
     * regular expressions to validate date input
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _regexp2ValidateDateInput()
    {
    }

    /**
     * regular expressions to validate time input
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _regexp2ValidateTimeInput()
    {
    }

    /**
     * define date formatting rules
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return array
     */
    protected function _defineDateFormattingRules()
    {
        // date formatting rules
        $aDFormats  = array("ISO" => array("Y-m-d", array(2, 3, 1), "0000-00-00"),
                            "EUR" => array("d.m.Y", array(2, 1, 3), "00.00.0000"),
                            "USA" => array("m/d/Y", array(1, 2, 3), "00/00/0000")
                           );
        return $aDFormats;
    }

    /**
     * defines time formatting rules
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return array
     */
    protected function _defineTimeFormattingRules()
    {
        // time formatting rules
        $aTFormats  = array("ISO" => array("H:i:s",   array(1, 2, 3 ), "00:00:00"),
                            "EUR" => array("H.i.s",   array(1, 2, 3 ), "00.00.00"),
                            "USA" => array("h:i:s A", array(1, 2, 3 ), "00:00:00 AM")
                           );
        return $aTFormats;
    }

    /**
     * Sets default date time value
     *
     * @param object $oObject          date field object
     * @param string $sLocalDateFormat input format
     * @param string $sLocalTimeFormat local format
     * @param bool   $blOnlyDate       marker to format only date field (no time)
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxUtilsDate
     *
     * @return null
     */
    protected function _setDefaultDateTimeValue( $oObject, $sLocalDateFormat, $sLocalTimeFormat, $blOnlyDate )
    {
        $aDFormats  = $this->_defineDateFormattingRules();
        $aTFormats  = $this->_defineTimeFormattingRules();

        $sReturn = $aDFormats[$sLocalDateFormat][2];
        if ( !$blOnlyDate) {
            $sReturn .= " ".$aTFormats[$sLocalTimeFormat][2];
        }

        if ($oObject instanceof oxField) {
            $oObject->setValue(trim($sReturn));
        } else {
            $oObject->value = trim($sReturn);
        }
        // increasing(decreasing) field length
        $oObject->fldmax_length = strlen( $oObject->value);
    }

    /**
     * sets date
     *
     * @param object $oObject      date field object
     * @param string $sDateFormat  date format
     * @param array  $aDFields     days
     * @param array  $aDateMatches new date as array (month, year)
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _setDate( $oObject, $sDateFormat, $aDFields, $aDateMatches )
    {
        // formatting correct time value
        $iTimestamp = mktime( 0, 0, 0, $aDateMatches[$aDFields[0]],
                              $aDateMatches[$aDFields[1]],
                              $aDateMatches[$aDFields[2]]);

        if ($oObject instanceof oxField) {
            $oObject->setValue(@date( $sDateFormat, $iTimestamp ));
        } else {
            $oObject->value = @date( $sDateFormat, $iTimestamp );
        }
        // we should increase (decrease) field length
        $oObject->fldmax_length = strlen( $oObject->value );
    }

    /**
     * Formatting correct time value
     *
     * @param object $oObject      data field object
     * @param string $sDateFormat  date format
     * @param string $sTimeFormat  time format
     * @param array  $aDateMatches new new date
     * @param array  $aTimeMatches new time
     * @param array  $aTFields     defines the time fields
     * @param array  $aDFields     defines the date fields
     *
     * @deprecated from 2012-11-21, not used here anymore. All date formatting moved to oxutilsdate
     *
     * @return null
     */
    protected function _formatCorrectTimeValue( $oObject, $sDateFormat, $sTimeFormat, $aDateMatches, $aTimeMatches, $aTFields, $aDFields )
    {
        // formatting correct time value
        $iTimestamp = @mktime( (int) $aTimeMatches[$aTFields[0]],
                               (int) $aTimeMatches[$aTFields[1]],
                               (int) $aTimeMatches[$aTFields[2]],
                               (int) $aDateMatches[$aDFields[0]],
                               (int) $aDateMatches[$aDFields[1]],
                               (int) $aDateMatches[$aDFields[2]] );

        if ($oObject instanceof oxField) {
            $oObject->setValue(trim( @date( $sDateFormat." ".$sTimeFormat, $iTimestamp ) ));
        } else {
            $oObject->value = trim( @date( $sDateFormat." ".$sTimeFormat, $iTimestamp ) );
        }

        // we should increase (decrease) field length
        $oObject->fldmax_length = strlen( $oObject->value );
    }

    /**
     * Get connection ID
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
     * @return string
     */
    public function escapeString( $sString )
    {
        if ( 'mysql' == self::_getConfigParam( "_dbType" )) {
            return mysql_real_escape_string( $sString, $this->_getConnectionId() );
        } elseif ( 'mysqli' == self::_getConfigParam( "_dbType" )) {
            return mysqli_real_escape_string( $this->_getConnectionId(), $sString );
        } else {
            return mysql_real_escape_string( $sString, $this->_getConnectionId() );
        }
    }

    /**
     * Updates shop views
     *
     * @param array $aTables If you need to update specific tables, just pass its names as array [optional]
     *
     * @deprecated since v5.0.1 (2012-11-05); Use public oxDbMetaDataHandler::updateViews().
     *
     * @return bool
     */
    public function updateViews( $aTables = null )
    {
        $oMetaData = oxNew('oxDbMetaDataHandler');
        return $oMetaData->updateViews();
    }
}
