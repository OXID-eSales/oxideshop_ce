<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   setup
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: lang.php 25584 2010-02-03 12:11:40Z arvydas $
 */

if ( !function_exists( 'isAdmin' ) ) {
    /**
     * Returns false, marking non admin state
     *
     * @return bool
     */
    function isAdmin()
    {
        return false;
    }
}

if ( !function_exists( 'getShopBasePath' ) ) {
    /**
     * Returns class responsible for system requirements check
     *
     * @return string
     */
    function getShopBasePath()
    {
        return dirname(__FILE__).'/../';
    }
}

if ( !function_exists( 'getInstallPath' ) ) {
    /**
     * Returns shop installation directory
     *
     * @return string
     */
    function getInstallPath()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            return getShopBasePath();
        } else {
            return "../";
        }
    }
}

if ( !function_exists( 'getSystemReqCheck' ) ) {
    /**
     * Returns class responsible for system requirements check
     *
     * @return oxSysRequirements
     */
    function getSystemReqCheck()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            include_once getShopBasePath()."core/oxsysrequirements.php";
        } else {
            include_once getInstallPath()."core/oxsysrequirements.php";
        }
        return new oxSysRequirements();
    }
}

if ( !function_exists( 'getCountryList' ) ) {
    /**
     * Includes country list for setup
     *
     * @return null
     */
    function getCountryList()
    {
        $aCountries = array();
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            include getShopBasePath()."application/controllers/admin/shop_countries.php";
        } else {
            include getInstallPath()."application/controllers/admin/shop_countries.php";
        }
        return $aCountries;
    }
}

if ( !function_exists( 'getLocation' ) ) {
    /**
     * Includes country list for setup
     *
     * @return null
     */
    function getLocation()
    {
        $aLocationCountries = array();
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            include getShopBasePath()."application/controllers/admin/shop_countries.php";
        } else {
            include getInstallPath()."application/controllers/admin/shop_countries.php";
        }
        return $aLocationCountries;
    }
}

if ( !function_exists( 'getLanguages' ) ) {
    /**
     * Includes country list for setup
     *
     * @return null
     */
    function getLanguages()
    {
        $aLanguages = array();
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            include getShopBasePath()."application/controllers/admin/shop_countries.php";
        } else {
            include getInstallPath()."application/controllers/admin/shop_countries.php";
        }
        return $aLanguages;
    }
}

if ( !function_exists( 'getDefaultFileMode' ) ) {
    /**
     * Returns mode which must be set for files or folders
     *
     * @return int
     */
    function getDefaultFileMode()
    {
        return 0755;
    }
}

if ( !function_exists( 'getDefaultConfigFileMode' ) ) {
    /**
     * Returns mode which must be set for config file
     *
     * @return int
     */
    function getDefaultConfigFileMode()
    {
        return 0444;
    }
}


if ( !class_exists( "Config" ) ) {
/**
 * Config file loader class
 */
class Config
{
    /**
     * Class constructor, loads config file data
     *
     * @return null
     */
    public function __construct()
    {
        include getInstallPath()."config.inc.php";;
    }
}
}

if ( !class_exists( "Conf" ) ) {
/**
 * Config key loader class
 */
class Conf
{
    /**
     * Class constructor, loads config key
     *
     * @return null
     */
    public function __construct()
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            include getShopBasePath()."core/oxconfk.php";
        } else {
            include getInstallPath()."core/oxconfk.php";
        }
    }
}
}

/**
 * Core setup class, setup instance holder
 */
class oxSetupCore
{
    /**
     * Keeps instance cache
     *
     * @var array
     */
    protected static $_aInstances = array();

    /**
     * Returns requested instance object
     *
     * @param string $sInstanceName instance name
     *
     * @return oxSetupCore
     */
    public function getInstance( $sInstanceName )
    {
        $sInstanceName = strtolower( $sInstanceName );
        if ( !isset( oxSetupCore::$_aInstances[$sInstanceName] ) ) {
            oxSetupCore::$_aInstances[$sInstanceName] = new $sInstanceName();
        }
        return oxSetupCore::$_aInstances[$sInstanceName];
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
     *
     * @return string
     */
    public function __call( $sMethod, $aArgs )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( substr( $sMethod, 0, 4) == "UNIT" ) {
                $sMethod = str_replace( "UNIT", "_", $sMethod );
            }
            if ( method_exists( $this, $sMethod)) {
                return call_user_func_array( array( & $this, $sMethod ), $aArgs );
            }
        }

        throw new oxSystemComponentException( "Function '$sMethod' does not exist or is not accessible! (" . get_class($this) . ")".PHP_EOL);
    }
}

/**
 * Setup manager class
 */
class OxSetup extends oxSetupCore
{
    /**
     * Current setup step title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Installation process status message
     *
     * @var string
     */
    protected $_sMessage = null;

    /**
     * Current setup step index
     *
     * @var int
     */
    protected $_iCurrStep = null;

    /**
     * Setup steps index array
     *
     * @var array
     */
    protected $_aSetupSteps = array(
                                    'STEP_SYSTEMREQ'   => 100,  // 0
                                    'STEP_WELCOME'     => 200,  // 1
                                    'STEP_LICENSE'     => 300,  // 2
                                    'STEP_DB_INFO'     => 400,  // 3
                                    'STEP_DB_CONNECT'  => 410,  // 31
                                    'STEP_DB_CREATE'   => 420,  // 32
                                    'STEP_DIRS_INFO'   => 500,  // 4
                                    'STEP_DIRS_WRITE'  => 510,  // 41
                                    'STEP_FINISH'      => 700,  // 6
                                   );

    /**
     * Returns current setup step title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_sTitle;
    }

    /**
     * Current setup step title setter
     *
     * @param string $sTitle title
     *
     * @return null
     */
    public function setTitle( $sTitle )
    {
        $this->_sTitle = $sTitle;
    }

    /**
     * Returns installation process status message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_sMessage;
    }

    /**
     * Sets installation process status message
     *
     * @param string $sMsg status message
     *
     * @return null
     */
    public function setMessage( $sMsg )
    {
        $this->_sMessage = $sMsg;
    }

    /**
     * Returns current setup step index
     *
     * @return int
     */
    public function getCurrentStep()
    {
        if ( $this->_iCurrStep === null ) {
            if ( ( $this->_iCurrStep = $this->getInstance( "oxSetupUtils" )->getRequestVar( "istep" ) ) === null ) {
                $this->_iCurrStep = $this->getStep( 'STEP_SYSTEMREQ' );
            }
            $this->_iCurrStep = (int) $this->_iCurrStep;
        }
        return $this->_iCurrStep;
    }

    /**
     * Returns next setup step ident
     *
     * @return int
     */
    public function getNextStep()
    {
        return $this->_iNextStep;
    }

    /**
     * Current setup step setter
     *
     * @param int $iStep current setup step index
     *
     * @return null
     */
    public function setNextStep( $iStep )
    {
        $this->_iNextStep = $iStep;
    }


    /**
     * Checks if config file is alleady filled with data
     *
     * @return bool
     */
    public function alreadySetUp()
    {
        $blSetUp = false;
        $sConfig = join( "", file( getInstallPath() . "config.inc.php" ) );
        if ( strpos( $sConfig, "<dbHost".$this->getVersionPrefix().">" ) === false ) {
            $blSetUp = true;
        }
        return $blSetUp;
    }

    /**
     * Returns default shop id
     *
     * @return mixed
     */
    public function getShopId()
    {
        $sBaseShopId = 'oxbaseshop';


        return $sBaseShopId;
    }

    /**
     * Returns setup steps index array
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_aSetupSteps;
    }

    /**
     * Returns setup step index
     *
     * @param string $sStepId setup step identifier
     *
     * @return int
     */
    public function getStep( $sStepId )
    {
        return isset( $this->_aSetupSteps[$sStepId] ) ? $this->_aSetupSteps[$sStepId] : null;
    }

    /**
     * Returns version prefix
     *
     * @return string
     */
    public function getVersionPrefix()
    {
        $sVerPrefix = '';

        return $sVerPrefix;
    }

    /**
     * $iModuleState - module status:
     * -1 - unable to datect, should not block
     *  0 - missing, blocks setup
     *  1 - fits min requirements
     *  2 - exists required or better
     *
     * @param int $iModuleState module state
     *
     * @return string
     */
    public function getModuleClass( $iModuleState )
    {
        switch ( $iModuleState ) {
            case 2:
                $sClass = 'pass';
                break;
            case 1:
                $sClass = 'pmin';
                break;
            case -1:
                $sClass = 'null';
                break;
            default:
                $sClass = 'fail';
                break;
        }
        return $sClass;
    }
}

/**
 * Setup language manager class
 */
class OxSetupLang extends oxSetupCore
{
    /**
     * Language translations array
     *
     * @var array
     */
    protected $_aLangData = null;

    /**
     * Returns setup interface language id
     *
     * @return striong
     */
    public function getSetupLang()
    {
        $oSession = $this->getInstance( "oxSetupSession" );
        $oUtils   = $this->getInstance( "oxSetupUtils" );

        $iSetupLang = $oUtils->getRequestVar( "setup_lang", "post" );

        if ( isset( $iSetupLang ) ) {
            $oSession->setSessionParam( 'setup_lang', $iSetupLang );
            $iSetupLangSubmit = $oUtils->getRequestVar( "setup_lang_submit", "post" );
            if ( isset( $iSetupLangSubmit ) ) {
                //updating setup language, so disabling redirect to next step, just reloading same step
                $_GET['istep'] = $_POST['istep'] = $this->getInstance( "oxSetup" )->getStep( 'STEP_WELCOME' );
            }
        } elseif ( $oSession->getSessionParam('setup_lang' ) === null ) {
            $aLangs = array( 'en', 'de' );
            $sBrowserLang = strtolower( substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) );
            $sBrowserLang = ( in_array($sBrowserLang, $aLangs) ) ? $sBrowserLang : $aLangs[0];
            $oSession->setSessionParam( 'setup_lang', $sBrowserLang );
        }

        return $oSession->getSessionParam( 'setup_lang' );
    }

    /**
     * Translates passed index
     *
     * @param string $sTextIdent translation index
     *
     * @return string
     */
    public function getText( $sTextIdent )
    {
        if ( $this->_aLangData === null ) {
            $this->_aLangData = array();
            $sLangFilePath  = getInstallPath()."setup/".$this->getSetupLang() . '/lang.php';
            if ( file_exists( $sLangFilePath ) && is_readable( $sLangFilePath ) ) {
                include $sLangFilePath;
                $this->_aLangData = $aLang;
            }
        }

        return isset( $this->_aLangData[ $sTextIdent ] ) ? $this->_aLangData[ $sTextIdent ] : null;
    }

    /**
     * Translates module name
     *
     * @param string $sModuleName name of module
     *
     * @return string
     */
    public function getModuleName( $sModuleName )
    {
        return $this->getText( 'MOD_'.strtoupper( $sModuleName ) );
    }
}

/**
 * Setup session manager class
 */
class OxSetupSession extends oxSetupCore
{
    /**
     * Session data array
     *
     * @var array
     */
    protected $_aSessionData = null;

    /**
     * Session ID
     *
     * @var string
     */
    protected $_sSid = null;

    /**
     * Session name
     *
     * @var string
     */
    protected $_sSessionName = 'setup_sid';

    /**
     * Is new session
     *
     * @var bool
     */
    protected $_blNewSession = false;

    /**
     * Initialize session class
     *
     * @return null
     */
    public function __construct()
    {
        ini_set('session.use_cookies', 0);

        // initialize session
        $this->_startSession();
        $this->_initSessionData();
    }

    /**
     * Start session
     *
     * @return null
     */
    protected function _startSession()
    {
        session_name($this->_sSessionName);

        $oUtils = $this->getInstance( "oxSetupUtils" );
        $sSid = $oUtils->getRequestVar('sid', 'get');

        if (empty($sSid)) {
            $sSid = $oUtils->getRequestVar('sid', 'post');
        }

        if (!empty($sSid)) {
            session_id($sSid);
        }

        session_start();
        $sSid = $this->_validateSession();
        $this->setSid($sSid);
    }

    /**
     * Validate if session is started by setup script, if not, generate new session.
     *
     * @return string Session ID
     */
    protected function _validateSession()
    {
        if ($this->_blNewSession === true) {
            $this->setSessionParam('setup_session', true);
        } elseif ($this->getSessionParam('setup_session') !== true) {
            $sNewSid = $this->_getNewSessionID();
            session_write_close();

            session_id($sNewSid);
            session_start();
            $this->setSessionParam('setup_session', true);
        }

        return session_id();
    }

    /**
     * Generate new unique session ID
     *
     * @return string
     */
    protected function _getNewSessionID()
    {
        session_regenerate_id(false);
        $this->_blNewSession = true;
        return session_id();
    }

    /**
     * Returns session id, which is used in forms and urls
     * (actually this id keeps all session data)
     *
     * @return string
     */
    public function getSid()
    {
        return $this->_sSid;
    }

    /**
     * Sets current session ID
     *
     * @param string $sSid session ID
     *
     * @return null
     */
    public function setSid($sSid)
    {
        $this->_sSid = $sSid;
    }

    /**
     * Initializes setup session data array
     *
     * @return array
     */
    protected function _initSessionData()
    {
        $oUtils = $this->getInstance( "oxSetupUtils" );

            //storring country value settings to session
            $sLocationLang = $oUtils->getRequestVar( "location_lang", "post" );
            if ( isset( $sLocationLang ) ) {
                $this->setSessionParam( 'location_lang', $sLocationLang );
            }

            //storring country value settings to session
            $sCountryLang = $oUtils->getRequestVar( "country_lang", "post" );
            if ( isset( $sCountryLang ) ) {
                $this->setSessionParam( 'country_lang', $sCountryLang );
            }

            //storring shop language value settings to session
            $sShopLang = $oUtils->getRequestVar( "sShopLang", "post" );
            if ( isset( $sShopLang ) ) {
                $this->setSessionParam( 'sShopLang', $sShopLang );
            }

            //storring dyn pages settings to session
            $blUseDynPages = $oUtils->getRequestVar( "use_dynamic_pages", "post" );
            if ( isset( $blUseDynPages ) ) {
                $this->setSessionParam( 'use_dynamic_pages', $blUseDynPages  );
            }

            //storring dyn pages settings to session
            $blCheckForUpdates = $oUtils->getRequestVar( "check_for_updates", "post" );
            if ( isset( $blCheckForUpdates ) ) {
                $this->setSessionParam( 'check_for_updates', $blCheckForUpdates  );
            }

            // store eula to session
            $iEula = $oUtils->getRequestVar( "iEula", "post" );
            if ( isset( $iEula ) ) {
                $this->setSessionParam( 'eula', $iEula  );
            }
    }

    /**
     * Return session object reference.
     *
     * @return array
     */
    protected function &_getSessionData()
    {
        return $_SESSION;
    }

    /**
     * Returns session parameter value
     *
     * @param string $sParamName parameter name
     *
     * @return mixed
     */
    public function getSessionParam( $sParamName )
    {
        $aSessionData = & $this->_getSessionData();
        if ( isset( $aSessionData[$sParamName] ) ) {
            return $aSessionData[$sParamName];
        }
    }

    /**
     * Sets session parameter value
     *
     * @param string $sParamName  parameter name
     * @param mixed  $sParamValue parameter value
     *
     * @return null
     */
    public function setSessionParam( $sParamName, $sParamValue  )
    {
        $aSessionData = & $this->_getSessionData();
        $aSessionData[$sParamName] = $sParamValue;
    }
}

/**
 * Setup database manager class
 */
class OxSetupDb extends oxSetupCore
{
    /**
     * Connection resource object
     *
     * @var object
     */
    protected $_oConn = null;

    /**
     * Error while opening sql file
     *
     * @var int
     */
    const ERROR_OPENING_SQL_FILE = 1;

    /**
     * Error while opening db connection
     *
     * @var int
     */
    const ERROR_DB_CONNECT = 1;

    /**
     * Error while creating db
     *
     * @var int
     */
    const ERROR_COULD_NOT_CREATE_DB = 2;

    /**
     * MySQL version does not fir requirements
     *
     * @var int
     */
    const ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS = 3;

    /**
     * Executes sql query. Returns query execution resource object
     *
     * @param string $sQ query to execute
     *
     * @throws Exception exception is thrown if error occured during sql execution
     *
     * @return object
     */
    public function execSql( $sQ )
    {
        $rReturn = mysql_query( $sQ, $this->getConnection() );
        if ( $rReturn === false ) {
            throw new Exception( $this->getInstance( "oxSetupLang" )->getText('ERROR_BAD_SQL' ) . "( $sQ ): " . mysql_error( $this->getConnection() ) . "\n" );
        }
        return $rReturn;
    }

    /**
     * Testing if no error occurs while creating views
     *
     * @throws Exception exception is thrown if error occured during view creation
     *
     * @return null
     */
    public function testCreateView()
    {
        // testing creation
        $sQ = "create or replace view oxviewtest as select 1";
        $rReturn = mysql_query( $sQ, $this->getConnection() );
        if ( $rReturn === false ) {
            throw new Exception( $this->getInstance( "oxSetupLang" )->getText('ERROR_VIEWS_CANT_CREATE' ) . " " . mysql_error( $this->getConnection() ) . "\n" );
        }

        // testing data selection
        $sQ = "select * from oxviewtest";
        $rReturn = mysql_query( $sQ, $this->getConnection() );
        if ( $rReturn === false ) {
            throw new Exception( $this->getInstance( "oxSetupLang" )->getText('ERROR_VIEWS_CANT_SELECT' ) . " " . mysql_error( $this->getConnection() ) . "\n" );
        }

        // testing view dropping
        $sQ = "drop view oxviewtest";
        $rReturn = mysql_query( $sQ, $this->getConnection() );
        if ( $rReturn === false ) {
            throw new Exception( $this->getInstance( "oxSetupLang" )->getText('ERROR_VIEWS_CANT_DROP' ) . " " . mysql_error( $this->getConnection() ) . "\n" );
        }
    }

    /**
     * Executes queries stored in passed file
     *
     * @param string $sFilename file name where queries are stored
     *
     * @return null
     */
    public function queryFile( $sFilename )
    {
        $fp = @fopen( $sFilename, "r" );
        if ( !$fp ) {
            $oSetup = $this->getInstance( "oxSetup" );
            // problems with file
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
            throw new Exception( sprintf( $this->getInstance( "oxSetupLang" )->getText( 'ERROR_OPENING_SQL_FILE' ), $sFilename ), oxSetupDb::ERROR_OPENING_SQL_FILE );
        }

        $sQuery = fread( $fp, filesize( $sFilename ) );
        fclose( $fp );

        if ( version_compare( $this->getDatabaseVersion(), "5" ) > 0 ) {
            //disable STRICT db mode if there are set any (mysql >= 5).
            $this->execSql( "SET @@session.sql_mode = ''" );
        }

        $aQueries = $this->parseQuery( $sQuery );
        foreach ( $aQueries as $sQuery) {
            $this->execSql( $sQuery );
        }
    }

    /**
     * Returns database version
     *
     * @return string
     */
    function getDatabaseVersion()
    {
        $rRecords = $this->execSql( "SHOW VARIABLES LIKE 'version'" );
        $aRow = mysql_fetch_row( $rRecords );

        return $aRow[1];
    }

    /**
     * Returns connection resource object
     *
     * @return object
     */
    public function getConnection()
    {
        if ( $this->_oConn === null ) {
            $this->_oConn = $this->openDatabase( null );
        }
        return $this->_oConn;
    }

    /**
     * Opens database connection and returns connection resource object
     *
     * @param array $aParams database connection parameters array
     *
     * @throws Exception exception is thrown if connection failed or was unable to select database
     *
     * @return object
     */
    public function openDatabase( $aParams )
    {
        $aParams = ( is_array( $aParams ) && count( $aParams ) ) ? $aParams : $this->getInstance( "oxSetupSession" )->getSessionParam( 'aDB' );
        if ( $this->_oConn === null) {
            // ok open DB
            $this->_oConn = @mysql_connect( $aParams['dbHost'], $aParams['dbUser'], $aParams['dbPwd'] );
            if ( !$this->_oConn ) {
                $oSetup = $this->getInstance( "oxSetup" );
                $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
                throw new Exception( $this->getInstance( "oxSetupLang" )->getText( 'ERROR_DB_CONNECT' ) . " - " . mysql_error(), oxSetupDb::ERROR_DB_CONNECT );
            }

            // testing version
            $oSysReq = getSystemReqCheck();
            if ( !$oSysReq->checkMysqlVersion( $this->getDatabaseVersion() ) ) {
                throw new Exception( $this->getInstance( "oxSetupLang" )->getText( 'ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS' ), oxSetupDb::ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS );
            }
            if ( !( @mysql_select_db( $aParams['dbName'], $this->_oConn ) ) ) {
                throw new Exception( $this->getInstance( "oxSetupLang" )->getText( 'ERROR_COULD_NOT_CREATE_DB' ) . " - " . mysql_error(), oxSetupDb::ERROR_COULD_NOT_CREATE_DB );
            }
        }
        return $this->_oConn;
    }

    /**
     * Creates database
     *
     * @param object $sDbName database name
     *
     * @throws Exception exception is thrown if database creation failed
     *
     * @return null
     */
    public function createDb( $sDbName )
    {
        if ( !$this->execSql( "create database `". $sDbName . "`" ) ) {
            // no success !
            $oSetup = $this->getInstance( "oxSetup" );
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
            throw new Exception( sprintf( $this->getInstance( "oxSetupLang" )->getText('ERROR_COULD_NOT_CREATE_DB'), $sDbName) . " - ". mysql_error() );
        }
    }

    /**
     * Saves dyn pages settings parameters
     *
     * @param array $aParams parameters to save to db
     *
     * @return null
     */
    public function saveDynPagesSettings( $aParams )
    {
        $oUtils   = $this->getInstance( "oxSetupUtils" );
        $oSession = $this->getInstance( "oxSetupSession" );

        $oConfk = new Conf();

            $sBaseOut = 'oxbaseshop';
            // disabling usage of dynamic pages if shop country is international
            if ( $oSession->getSessionParam( 'location_lang') === null ) {
                $oSession->setSessionParam( 'use_dynamic_pages', 'false' );
            }

        $blUseDynPages = isset( $aParams["use_dyn_pages"] ) ? $aParams["use_dyn_pages"] : $oSession->getSessionParam( 'use_dynamic_pages' );
        $sLocationLang  = isset( $aParams["location_lang"] ) ? $aParams["location_lang"] : $oSession->getSessionParam( 'location_lang' );
        $blCheckForUpdates = isset( $aParams["check_for_updates"] ) ? $aParams["check_for_updates"] : $oSession->getSessionParam( 'check_for_updates' );
        $sCountryLang  = isset( $aParams["country_lang"] ) ? $aParams["country_lang"] : $oSession->getSessionParam( 'country_lang' );
        $sShopLang  = isset( $aParams["sShopLang"] ) ? $aParams["sShopLang"] : $oSession->getSessionParam( 'sShopLang' );
        $sBaseShopId = $this->getInstance( "oxSetup" )->getShopId();

        $this->execSql( "update oxcountry set oxactive = '0'" );
        $this->execSql( "update oxcountry set oxactive = '1' where oxid = '$sCountryLang'" );

        // if it is international eshop, setting admin user country to selected one
        if ( $oSession->getSessionParam('location_lang') != "de" ) {
             $this->execSql( "UPDATE oxuser SET oxcountryid = '$sCountryLang' where oxid='oxdefaultadmin'" );
        }

        $this->execSql( "delete from oxconfig where oxvarname = 'blLoadDynContents'" );
        $this->execSql( "delete from oxconfig where oxvarname = 'sShopCountry'" );
        $this->execSql( "delete from oxconfig where oxvarname = 'blCheckForUpdates'" );
       // $this->execSql( "delete from oxconfig where oxvarname = 'aLanguageParams'" );

        $sID1 = $oUtils->generateUid();
        $this->execSql( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                 values('$sID1', '$sBaseShopId', 'blLoadDynContents', 'bool', ENCODE( '$blUseDynPages', '".$oConfk->sConfigKey."'))" );

        $sID2 = $oUtils->generateUid();
        $this->execSql( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                 values('$sID2', '$sBaseShopId', 'sShopCountry', 'str', ENCODE( '$sLocationLang', '".$oConfk->sConfigKey."'))" );

        $sID3 = $oUtils->generateUid();
        $this->execSql( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                 values('$sID3', '$sBaseShopId', 'blCheckForUpdates', 'bool', ENCODE( '$blCheckForUpdates', '".$oConfk->sConfigKey."'))" );

        //set only one active language
        $aRes = $this->execSql( "select oxvarname, oxvartype, DECODE( oxvarvalue, '".$oConfk->sConfigKey."') AS oxvarvalue from oxconfig where oxvarname='aLanguageParams'" );
        if ($aRes) {
            if ( $aRow = mysql_fetch_assoc( $aRes ) ) {
                if ( $aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr' ) {
                    $aRow['oxvarvalue'] = unserialize( $aRow['oxvarvalue'] );
                }
                $aLanguageParams = $aRow['oxvarvalue'];
            }
            foreach ($aLanguageParams as $sKey => $aLang) {
                $aLanguageParams[$sKey]["active"] = "0";
            }
            $aLanguageParams[$sShopLang]["active"] = "1";

            $sValue = serialize( $aLanguageParams );
            $sID4 = $oUtils->generateUid();
            $this->execSql( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                     values('$sID4', '$sBaseShopId', 'aLanguageParams', 'aarr', ENCODE( '$sValue', '".$oConfk->sConfigKey."'))" );
        }
    }


    /**
     * Converts config table values to utf8
     *
     * @return null
     */
    public function convertConfigTableToUtf()
    {
        $oConfk = new Conf();
        $oUtils = $this->getInstance( "oxSetupUtils" );

        $sSql = "SELECT oxvarname, oxvartype, DECODE( oxvarvalue, '".$oConfk->sConfigKey."') AS oxvarvalue FROM oxconfig WHERE oxvartype IN ('str', 'arr', 'aarr') ";
        $aRes = $this->execSql( $sSql );

        $aConverted = array();

        while ( $aRow = mysql_fetch_assoc( $aRes ) ) {

            if ( $aRow['oxvartype'] == 'arr' || $aRow['oxvartype'] == 'aarr' ) {
                $aRow['oxvarvalue'] = unserialize( $aRow['oxvarvalue'] );
            }

            $aRow['oxvarvalue'] = $oUtils->convertToUtf8( $aRow['oxvarvalue'] );
            $aConverted[] = $aRow;
        }

        $oConn = $this->getConnection();
        foreach ( $aConverted as $sKey => $sValue ) {

            if ( is_array( $sValue['oxvarvalue'] ) ) {
                $sVarValue = mysql_real_escape_string( serialize( $sValue['oxvarvalue'] ), $oConn );
            } else {
                $sVarValue = is_string( $sValue['oxvarvalue'] ) ? mysql_real_escape_string( $sValue['oxvarvalue'], $oConn ) : $sValue['oxvarvalue'];
            }

            $sSql = "UPDATE oxconfig SET oxvarvalue = ENCODE( '".$sVarValue."', '".$oConfk->sConfigKey."') WHERE oxvarname = '" . $sValue['oxvarname'] . "'; ";
            $this->execSql( $sSql );
        }
    }

    /**
     * Parses query string into sql sentences
     *
     * @param string $sSQL query string (usually reqd from *.sql file)
     *
     * @return array
     */
    public function parseQuery( $sSQL )
    {
        // parses query into single pieces
        $aRet       = array();
        $blComment  = false;
        $blQuote    = false;
        $sThisSQL   = "";

        $aLines = explode( "\n", $sSQL);

        // parse it
        foreach ( $aLines as $sLine) {
            $iLen = strlen( $sLine);
            for ( $i = 0; $i < $iLen; $i++) {
                if ( !$blQuote && ( $sLine[$i] == '#' || ( $sLine[0] == '-' && $sLine[1] == '-'))) {
                    $blComment = true;
                }

                // add this char to current command
                if ( !$blComment) {
                    $sThisSQL .= $sLine[$i];
                }

                // test if quote on
                if ( ($sLine[$i] == '\'' && $sLine[$i-1] != '\\') ) {
                    $blQuote = !$blQuote;   // toggle
                }

                // now test if command end is reached
                if ( !$blQuote && $sLine[$i] == ';') {
                    // add this
                    $sThisSQL = trim( $sThisSQL);
                    if ( $sThisSQL) {
                        $sThisSQL = str_replace( "\r", "", $sThisSQL);
                        $aRet[] = $sThisSQL;
                    }
                    $sThisSQL = "";
                }
            }
            // comments and quotes can't run over newlines
            $blComment  = false;
            $blQuote    = false;
        }

        return $aRet;
    }

    /**
     * Sets various connection collation parameters
     *
     * @param int $iUtfMode utf8 mode
     *
     * @return null
     */
    public function setMySqlCollation( $iUtfMode )
    {
        if ( $iUtfMode ) {
            $this->execSql( "ALTER SCHEMA CHARACTER SET utf8 COLLATE utf8_general_ci" );
            $this->execSql( "set names 'utf8'" );
            $this->execSql( "set character_set_database=utf8" );
            $this->execSql( "SET CHARACTER SET latin1" );
            $this->execSql( "SET CHARACTER_SET_CONNECTION = utf8" );
            $this->execSql( "SET character_set_results = utf8" );
            $this->execSql( "SET character_set_server = utf8" );
        } else {
            $this->execSql( "ALTER SCHEMA CHARACTER SET latin1 COLLATE latin1_general_ci" );
            $this->execSql( "SET CHARACTER SET latin1" );
        }
    }

    /**
     * Writes utf mode config parameter to db
     *
     * @param int $iUtfMode utf mode
     *
     * @return null
     */
    public function writeUtfMode( $iUtfMode )
    {
        $sBaseShopId = $this->getInstance( "oxSetup" )->getShopId();
        $oConfk = new Conf();
        $sQ = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue) values('iSetUtfMode', '$sBaseShopId', 'iSetUtfMode', 'str', ENCODE( '{$iUtfMode}', '".$oConfk->sConfigKey."') )";

        $this->execSql( $sQ );
    }

    /**
     * Updates default admin user login name and password
     *
     * @param string $sLoginName admin user login name
     * @param string $sPassword  admin user login password
     *
     * @return null
     */
    public function writeAdminLoginData( $sLoginName, $sPassword )
    {
        $sPassSalt = $this->getInstance( "OxSetupUtils" )->generateUID();
        $sPassword = md5( $sPassword . $sPassSalt );

        $sQ = "update oxuser set oxusername='{$sLoginName}', oxpassword='{$sPassword}', oxpasssalt=HEX('{$sPassSalt}') where oxid='oxdefaultadmin'";
        $this->execSql( $sQ );

        $sQ = "update oxnewssubscribed set oxemail='{$sLoginName}' where oxuserid='oxdefaultadmin'";
        $this->execSql( $sQ );
    }
}

/**
 * Setup utilities class
 */
class OxSetupUtils extends oxSetupCore
{
    /**
     * Unable to find file
     *
     * @var int
     */
    const ERROR_COULD_NOT_FIND_FILE = 1;

    /**
     * File is not readable
     *
     * @var int
     */
    const ERROR_COULD_NOT_READ_FILE = 2;

    /**
     * File is not writable
     *
     * @var int
     */
    const ERROR_COULD_NOT_WRITE_TO_FILE = 3;

    /**
     * Email validation regular expression
     *
     * @var string
     */
    protected $_sEmailTpl = "/^([-!#\$%&'*+.\/0-9=?A-Z^_`a-z{|}~\177])+@([-!#\$%&'*+\/0-9=?A-Z^_`a-z{|}~\177]+\\.)+[a-zA-Z]{2,6}\$/i";

    /**
     * Converts given data array from iso-8859-15 to utf-8
     *
     * @param array $aData data to convert
     *
     * @return array
     */
    public function convertToUtf8( $aData )
    {
        if ( is_array( $aData ) ) {

            $aKeys = array_keys( $aData );
            $aValues = array_values( $aData );

            //converting keys
            if ( count( $aData ) > 1 ) {
                foreach ( $aKeys as $sKeyIndex => $sKeyValue ) {
                    if ( is_string( $sKeyValue ) ) {
                        $aKeys[$sKeyIndex] = iconv( 'iso-8859-15', 'utf-8', $sKeyValue );
                    }
                }

                $aData = array_combine( $aKeys, $aValues );

                //converting values
                foreach ( $aData as $sKey => $sValue ) {
                    if ( is_array( $sValue ) ) {
                        $this->convertToUtf8( $sValue );
                    }

                    if ( is_string( $sValue ) ) {
                        $aData[$sKey] = iconv( 'iso-8859-15', 'utf-8', $sValue );
                    }
                }
            }
        } else {
            $aData = iconv( 'iso-8859-15', 'utf-8', $aData );
        }

        return $aData;
    }

    /**
     * Generates unique id
     *
     * @return string
     */
    public function generateUID()
    {
        return md5( uniqid( rand(), true ) );
    }

    /**
     * Recursivelly removes given path files and folders
     *
     * @param string $sPath           path to remove
     * @param bool   $blDeleteSuccess removal state marker
     * @param int    $iMode           remove mode: 0 files and folders, 1 - files only
     * @param array  $aSkipFiles      files which should not be deleted (default null)
     * @param array  $aSkipFolders    folders which should not be deleted (default null)
     *
     * @return bool
     */
    public function removeDir( $sPath, $blDeleteSuccess, $iMode = 0, $aSkipFiles = array(), $aSkipFolders = array() )
    {

        if ( is_file( $sPath ) || is_dir( $sPath ) ) {
            // setting path to remove
            $d = dir( $sPath );
            $d->handle;
            while ( false !== ( $sEntry = $d->read() ) ) {
                if ( $sEntry != "." &&  $sEntry != ".." ) {

                    $sFilePath = $sPath."/".$sEntry;
                    if ( is_file( $sFilePath ) ) {
                        if ( !in_array( basename( $sFilePath ), $aSkipFiles ) ) {
                            $blDeleteSuccess = $blDeleteSuccess * @unlink ( $sFilePath );
                        }
                    } elseif ( is_dir( $sFilePath ) ) {
                        // removing direcotry contents
                        $this->removeDir( $sFilePath, $blDeleteSuccess, $iMode, $aSkipFiles, $aSkipFolders );
                        if ( $iMode === 0 && !in_array( basename( $sFilePath ), $aSkipFolders ) ) {
                            $blDeleteSuccess = $blDeleteSuccess * @rmdir ( $sFilePath );
                        }
                    } else {
                        // there are some other objects ?
                        $blDeleteSuccess = $blDeleteSuccess * false;
                    }
                }
            }
            $d->close();
        } else {
            $blDeleteSuccess = false;
        }

        return $blDeleteSuccess;
    }

    /**
     * Checks if given path (file or folder) exists, is writable and changes its mode to 0755
     *
     * @param string $sPath path or file to checl
     *
     * @throws Exception exception is thrown if file does not exist, is not writable or its mode cannot be changed
     *
     * @return null
     */
    public function checkFileOrDirectory( $sPath )
    {
        $oLang  = $this->getInstance( "oxSetupLang" );
        $oSetup = $this->getInstance( "oxSetup" );

        if ( !file_exists( $sPath ) ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            throw new Exception( sprintf( $oLang->getText( 'ERROR_NOT_AVAILABLE' ), $sPath ) );
        }
        if ( !is_writable( $sPath ) || !is_readable( $sPath ) ) {
            // try to set permissions and check again
            @chmod( $sPath, getDefaultFileMode() );
            clearstatcache();
        }
        if ( !is_writable( $sPath ) || !is_readable( $sPath ) ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            throw new Exception( sprintf( $oLang->getText( 'ERROR_NOT_WRITABLE' ), $sPath ) );
        }
    }

    /**
     * Extracts install path
     *
     * @param string $aPath path info array
     *
     * @return string
     */
    protected function _extractPath( $aPath )
    {
        $sExtPath = '';
        $blBuildPath = false;
        for ( $i = count( $aPath ); $i > 0; $i-- ) {
            $sDir = $aPath[$i-1];
            if ( $blBuildPath ) {
                $sExtPath = $sDir . '/' . $sExtPath;
            }
            if ( stristr( $sDir, "setup" ) ) {
                $blBuildPath = true;
            }
        }
        return $sExtPath;
    }

    /**
     * Returns path parameters for standard setup (non APS)
     *
     * @return array
     */
    public function getDefaultPathParams()
    {
        // default values
        $aParams['sShopDir'] = "";
        $aParams['sShopURL'] = "";

        // try path translated
        if ( isset( $_SERVER['PATH_TRANSLATED']) && ($_SERVER['PATH_TRANSLATED'] != '')) {
            $sFilepath = $_SERVER['PATH_TRANSLATED'];
        } else {
            $sFilepath = $_SERVER['SCRIPT_FILENAME'];
        }

        $aParams['sShopDir'] = str_replace( "\\", "/", $this->_extractPath( preg_split( "/\\\|\//", $sFilepath ) ) );
        $aParams['sCompileDir'] = $aParams['sShopDir'] . "tmp/";

        // try referer
        $sFilepath = @$_SERVER['HTTP_REFERER'];
        if ( !isset( $sFilepath ) || !$sFilepath ) {
            $sFilepath = "http://" . @$_SERVER['HTTP_HOST'] . @$_SERVER['SCRIPT_NAME'];
        }
        $aParams['sShopURL'] = ltrim( $this->_extractPath( explode( "/", $sFilepath) ), "/" );

        return $aParams;
    }

    /**
     * Returns base picture dir path
     *
     * @return string
     */
    public function getBasePictureDir()
    {
        $sBasePic = 'out/pictures';

        return $sBasePic;
    }

    /**
     * Performs various path checks
     *
     * @param array $aParams initial path parameters
     *
     * @return null
     */
    public function checkPaths( $aParams )
    {
        $sBasePic = $this->getBasePictureDir();
        $aPaths = array(
            $aParams['sShopDir']."/config.inc.php",
            $aParams['sShopDir']."/log",
            $aParams['sCompileDir'],

            // promo & media
            $aParams['sShopDir']."/$sBasePic/promo",
            $aParams['sShopDir']."/$sBasePic/media", // deprecated, use out/media instead
            $aParams['sShopDir']."/out/media",

            // Master
                // product required paths
                $aParams['sShopDir']."/$sBasePic/master/product/1",
                $aParams['sShopDir']."/$sBasePic/master/product/2",
                $aParams['sShopDir']."/$sBasePic/master/product/3",
                $aParams['sShopDir']."/$sBasePic/master/product/4",
                $aParams['sShopDir']."/$sBasePic/master/product/5",
                $aParams['sShopDir']."/$sBasePic/master/product/6",
                $aParams['sShopDir']."/$sBasePic/master/product/7",
                $aParams['sShopDir']."/$sBasePic/master/product/8",
                $aParams['sShopDir']."/$sBasePic/master/product/9",
                $aParams['sShopDir']."/$sBasePic/master/product/10",
                $aParams['sShopDir']."/$sBasePic/master/product/11",
                $aParams['sShopDir']."/$sBasePic/master/product/12",
                $aParams['sShopDir']."/$sBasePic/master/product/icon",
                $aParams['sShopDir']."/$sBasePic/master/product/thumb",
                // category required paths
                $aParams['sShopDir']."/$sBasePic/master/category/icon",
                $aParams['sShopDir']."/$sBasePic/master/category/promo_icon",
                $aParams['sShopDir']."/$sBasePic/master/category/thumb",
                // manufacturer required paths
                $aParams['sShopDir']."/$sBasePic/master/manufacturer/icon",
                // vendor required paths
                $aParams['sShopDir']."/$sBasePic/master/vendor/icon",
                // wrapping required paths
                $aParams['sShopDir']."/$sBasePic/master/wrapping",

            // Generated
                // product required paths
                $aParams['sShopDir']."/$sBasePic/generated/product/1",
                $aParams['sShopDir']."/$sBasePic/generated/product/2",
                $aParams['sShopDir']."/$sBasePic/generated/product/3",
                $aParams['sShopDir']."/$sBasePic/generated/product/4",
                $aParams['sShopDir']."/$sBasePic/generated/product/5",
                $aParams['sShopDir']."/$sBasePic/generated/product/6",
                $aParams['sShopDir']."/$sBasePic/generated/product/icon",
                $aParams['sShopDir']."/$sBasePic/generated/product/thumb",
                // category required paths
                $aParams['sShopDir']."/$sBasePic/generated/category/icon",
                $aParams['sShopDir']."/$sBasePic/generated/category/promo_icon",
                $aParams['sShopDir']."/$sBasePic/generated/category/thumb",
                // manufacturer required paths
                $aParams['sShopDir']."/$sBasePic/generated/manufacturer/icon",
           );

        foreach ( $aPaths as $sPath) {
            $this->checkFileOrDirectory( $sPath );
        }
    }

    /**
     * Updates config.inc.php file contents
     *
     * @param array $aParams paths parameters
     *
     * @throws Exception exception is thrown is file cant be open for reading or can not be written
     *
     * @return null
     */
    public function updateConfigFile( $aParams )
    {
        $sConfPath  = $aParams['sShopDir']."/config.inc.php";
        $sVerPrefix = $this->getInstance( "oxSetup" )->getVersionPrefix();

        $oLang  = $this->getInstance( "oxSetupLang" );

        clearstatcache();
        @chmod( $sConfPath, getDefaultFileMode() );
        if ( ( $fp = fopen( $sConfPath, "r" ) ) ) {
            $sConfFile = fread( $fp, filesize( $sConfPath ) );
            fclose( $fp );
        } else {
            throw new Exception( sprintf( $oLang->getText('ERROR_COULD_NOT_OPEN_CONFIG_FILE'), $sConfPath ) );
        }

        // overwriting settings
        foreach ( $aParams as $sParamName => $sParamValue ) {
            // non integer type variables must be surrounded by quotes
            if ( $sParamName[0] != 'i' ) {
                $sParamValue = "'{$sParamValue}'";
            }
            $sConfFile = preg_replace( "/(this->{$sParamName}).*". preg_quote( $sVerPrefix ) .".*;/", "\\1 = ".$sParamValue.";", $sConfFile );
        }

        if ( ( $fp = fopen( $sConfPath, "w" ) ) ) {
            fwrite( $fp, $sConfFile );
            fclose( $fp );
            @chmod( $sConfPath, getDefaultConfigFileMode() );
        } else {
            // error ? strange !?
            throw new Exception( sprintf($oLang->getText('ERROR_CONFIG_FILE_IS_NOT_WRITABLE'), $aParams['sShopDir'] ) );
        }
    }

    /**
     * Updates default htaccess file with user defined params
     *
     * @param array  $aParams    various setup parameters
     * @param string $sSubFolder in case you need to update non default, but e.g. admin file, you must add its folder
     *
     * @return null
     */
    public function updateHtaccessFile( $aParams, $sSubFolder = "" )
    {
        $oLang  = $this->getInstance( "oxSetupLang" );

        // preparing rewrite base param
        if ( !isset( $aParams["sBaseUrlPath"] ) || !$aParams["sBaseUrlPath"] ) {
            $aParams["sBaseUrlPath"] = "";
        }

        if ( $sSubFolder ) {
            $sSubFolder = $this->preparePath( "/".$sSubFolder );
        }

        $aParams["sBaseUrlPath"] = trim( $aParams["sBaseUrlPath"].$sSubFolder, "/" );
        $aParams["sBaseUrlPath"] = "/".$aParams["sBaseUrlPath"];

        $sHtaccessPath = $this->preparePath( $aParams["sShopDir"] ).$sSubFolder."/.htaccess";

        clearstatcache();
        if ( !file_exists( $sHtaccessPath ) ) {
            throw new Exception( sprintf( $oLang->getText('ERROR_COULD_NOT_FIND_FILE'), $sHtaccessPath ), OxSetupUtils::ERROR_COULD_NOT_FIND_FILE );
        }

        @chmod( $sHtaccessPath, getDefaultFileMode() );
        if ( is_readable( $sHtaccessPath ) && ( $fp = fopen( $sHtaccessPath, "r" ) ) ) {
            $sHtaccessFile = fread( $fp, filesize( $sHtaccessPath ) );
            fclose( $fp );
        } else {
            throw new Exception( sprintf( $oLang->getText('ERROR_COULD_NOT_READ_FILE'), $sHtaccessPath ), OxSetupUtils::ERROR_COULD_NOT_READ_FILE );
        }

        // overwriting settings
        $sHtaccessFile = preg_replace( "/RewriteBase.*/", "RewriteBase ".$aParams["sBaseUrlPath"], $sHtaccessFile );
        if ( is_writable( $sHtaccessPath ) && ( $fp = fopen( $sHtaccessPath, "w" ) ) ) {
            fwrite( $fp, $sHtaccessFile );
            fclose( $fp );
        } else {
            // error ? strange !?
            throw new Exception( sprintf($oLang->getText('ERROR_COULD_NOT_WRITE_TO_FILE'), $sHtaccessPath ), OxSetupUtils::ERROR_COULD_NOT_WRITE_TO_FILE );
        }
    }

    /**
     * Returns the value of an environment variable
     *
     * @param string $sVarName variable name
     *
     * @return mixed
     */
    public function getEnvVar( $sVarName )
    {
        if ( ( $sVarVal = getenv( $sVarName ) ) !== false ) {
            return $sVarVal;
        }
    }

    /**
     * Returns variable from request
     *
     * @param string $sVarName     variable name
     * @param string $sRequestType request type - "post", "get", "cookie" [optional]
     *
     * @return mixed
     */
    public function getRequestVar( $sVarName, $sRequestType = null )
    {
        $sValue = null;
        switch ( $sRequestType ) {
            case 'post':
                if ( isset( $_POST[$sVarName] ) ) {
                    $sValue = $_POST[$sVarName];
                }
                break;
            case 'get':
                if ( isset( $_GET[$sVarName] ) ) {
                    $sValue = $_GET[$sVarName];
                }
                break;
            case 'cookie':
                if ( isset( $_COOKIE[$sVarName] ) ) {
                    $sValue = $_COOKIE[$sVarName];
                }
                break;
            default:
                if ($sValue === null) {
                    $sValue = $this->getRequestVar($sVarName, 'post');
                }

                if ($sValue === null) {
                    $sValue = $this->getRequestVar($sVarName, 'get');
                }

                if ($sValue === null) {
                    $sValue = $this->getRequestVar($sVarName, 'cookie');
                }
                break;
        }
        return $sValue;
    }

    /**
     * Sets cookie
     *
     * @param string $sName       name of the cookie
     * @param string $sValue      value of the cookie
     * @param int    $iExpireDate time the cookie expires
     * @param string $sPath       path on the server in which the cookie will be available on.
     *
     * @return null
     */
    public function setCookie( $sName, $sValue, $iExpireDate, $sPath )
    {
        setcookie( $sName, $sValue, $iExpireDate, $sPath );
    }

    /**
     * Returns file contents if file is readable
     *
     * @param string $sFile path to file
     *
     * @return string | mixed
     */
    public function getFileContents( $sFile )
    {
        $sContents = null;
        if ( file_exists( $sFile ) && is_readable( $sFile ) ) {
            $sContents = file_get_contents( $sFile );
        }
        return $sContents;
    }

    /**
     * Prepares given path parameter to ce compatible with unix format
     *
     * @param string $sPath path to prepare
     *
     * @return string
     */
    public function preparePath( $sPath )
    {
        return rtrim( str_replace( "\\", "/", $sPath ), "/" );
    }

    /**
     * Extracts rewrite base path from url
     *
     * @param string $sUrl user defined shop install url
     *
     * @return string
     */
    public function extractRewriteBase( $sUrl )
    {
         $sPath = "/";
         if ( ( $aPathInfo = @parse_url( $sUrl ) ) !== false ) {
             if ( isset( $aPathInfo["path"] ) ) {
                 $sPath = $this->preparePath( $aPathInfo["path"] );
             }
         }

         return $sPath;
    }

    /**
     * Email validation function. Returns true if email is OK otherwise - false;
     * Syntax validation is performed only.
     *
     * @param string $sEmail user email
     *
     * @return bool
     */
    public function isValidEmail( $sEmail )
    {
        return preg_match( $this->_sEmailTpl, $sEmail ) != 0;
    }
}

/**
 * Setup view class
 */
class oxSetupView extends oxSetupCore
{
    /**
     * View title
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Messages which should be displayed in current view
     *
     * @var array
     */
    protected $_aMessages = array();

    /**
     * View parameters array
     *
     * @var array
     */
    protected $_aViewParams = array();


    /**
     * Displayes current setup step template
     *
     * @param string $sTemplate name of template to display
     *
     * @return null
     */
    public function display( $sTemplate )
    {
        ob_start();
        include "tpl/{$sTemplate}";
        ob_end_flush();
    }

    /**
     * Returns current page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getText( $this->_sTitle, false );
    }

    /**
     * Sets current page title id
     *
     * @param string $sTitleId title id
     *
     * @return null
     */
    public function setTitle( $sTitleId )
    {
        $this->_sTitle = $sTitleId;
    }

    /**
     * Returns messages array
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_aMessages;
    }

    /**
     * Sets message to view
     *
     * @param string $sMessage   message to write to view
     * @param bool   $blOverride if TRUE cleanups previously defined messages [optional]
     *
     * @return null
     */
    public function setMessage( $sMessage, $blOverride = false )
    {
        if ( $blOverride ) {
            $this->_aMessages = array();
        }

        $this->_aMessages[] = $sMessage;
    }

    /**
     * Translates text
     *
     * @param string $sTextId translation ident
     * @param bool   $blPrint if true - prints requested value [optional]
     *
     * @return string
     */
    public function getText( $sTextId, $blPrint = true )
    {
        $sText = $this->getInstance( "oxSetupLang" )->getText( $sTextId );;
        return $blPrint ? print( $sText ) : $sText;
    }

    /**
     * Prints session id
     *
     * @param bool $blPrint if true - prints requested value [optional]
     *
     * @return null
     */
    public function getSid( $blPrint = true )
    {
        $sSid = $this->getInstance( "oxSetupSession" )->getSid();
        return $blPrint ? print( $sSid ) : $sSid;
    }

    /**
     * Sets view parameter value
     *
     * @param string $sName  parameter name
     * @param mixed  $sValue parameter value
     *
     * @return null
     */
    public function setViewParam( $sName, $sValue )
    {
        $this->_aViewParams[$sName] = $sValue;
    }

    /**
     * Returns view parameter value
     *
     * @param string $sName view parameter name
     *
     * @return mixed
     */
    public function getViewParam( $sName )
    {
        $sValue = null;
        if ( isset( $this->_aViewParams[$sName] ) ) {
            $sValue = $this->_aViewParams[$sName];
        }
        return $sValue;
    }

    /**
     * Returns passed setup step number
     *
     * @param string $sStepId setup step id
     * @param bool   $blPrint if true - prints requested value [optional]
     *
     * @return int
     */
    public function getSetupStep( $sStepId, $blPrint = true )
    {
        $sStep = $this->getInstance( "oxSetup" )->getStep( $sStepId );
        return $blPrint ? print( $sStep ) : $sStep;
    }

    /**
     * Returns next setup step id
     *
     * @return int
     */
    public function getNextSetupStep()
    {
        return $this->getInstance( "oxSetup" )->getNextStep();
    }

    /**
     * Returns current setup step id
     *
     * @return null
     */
    public function getCurrentSetupStep()
    {
        return $this->getInstance( "oxSetup" )->getCurrentStep();
    }

    /**
     * Returns all setup process steps
     *
     * @return array
     */
    public function getSetupSteps()
    {
        return $this->getInstance( "oxSetup" )->getSteps();
    }

    /**
     * Returns image file path
     *
     * @return string
     */
    public function getImageDir()
    {
        return getInstallPath().'out/admin/img';
    }

    /**
     * If demo data installation is OFF, tries to delete demo pictures also
     * checks if setup deletion is ON and deletes setup files if possible,
     * return deletion status
     *
     * @return bool
     */
    public function isDeletedSetup()
    {
        //finalizing installation
        $blDeleted = true;
        $oSession  = $this->getInstance( "OxSetupSession" );
        $oUtils    = $this->getInstance( "oxSetupUtils" );
        $sPath    = getInstallPath();

        $aDemoConfig = $oSession->getSessionParam( "aDB" );
        if ( !isset( $aDemoConfig['dbiDemoData'] ) || $aDemoConfig['dbiDemoData'] != '1' ) {
            $sPrfx  = $this->getInstance( "oxSetup" )->getVersionPrefix();

            // "/generated" cleanup
            $oUtils->removeDir( $sPath . "out/pictures{$sPrfx}/generated", true );

            // "/master" cleanup, leaving nopic
            $oUtils->removeDir( $sPath . "out/pictures{$sPrfx}/master", true, 1, array( "nopic.jpg" ) );
        }

        $aSetupConfig = $oSession->getSessionParam( "aSetupConfig" );
        if ( isset( $aSetupConfig['blDelSetupDir'] ) && $aSetupConfig['blDelSetupDir'] ) {
            // removing setup files
            $blDeleted = $oUtils->removeDir( $sPath . "setup", true );
        }
        return $blDeleted;
    }

    /**
     * Returns or prints url for info about missing web service configuration
     *
     * @param string $sIdent  module identifier
     * @param bool   $blPrint prints result if TRUE
     *
     * @return mixed
     */
    public function getReqInfoUrl( $sIdent, $blPrint = true )
    {
        $oSysReq = getSystemReqCheck();
        $sUrl = $oSysReq->getReqInfoUrl($sIdent);

        return $blPrint ? print( $sUrl ) : $sUrl;
    }
}

/**
 * Class holds scripts (controllers) needed to perform shop setup steps
 */
class oxSetupController extends oxSetupCore
{
    /**
     * Returns view object
     *
     * @return oxSetupView
     */
    public function getView()
    {
        if ( $this->_oView == null ) {
            $this->_oView = new oxSetupView();
        }
        return $this->_oView;
    }

    // ---- controllers ----
    /**
     * First page with system requirements check
     *
     * @return string
     */
    public function systemReq()
    {
        $oSetup     = $this->getInstance( "oxSetup" );
        $oSetupLang = $this->getInstance( "oxSetupLang" );
        $oUtils     = $this->getInstance( "oxSetupUtils" );
        $oView      = $this->getView();

        $blContinue = true;
        $aGroupModuleInfo = array();

        $blHtaccessUpdateError = false;
        try {
            $aPath = $oUtils->getDefaultPathParams();
            $aPath['sBaseUrlPath'] = $oUtils->extractRewriteBase( $aPath['sShopURL'] );
            //$oUtils->updateHtaccessFile( $aPath, "admin" );
            $oUtils->updateHtaccessFile( $aPath );
        } catch ( Exception $oExcp ) {
            //$oView->setMessage( $oExcp->getMessage() );
            $blHtaccessUpdateError = true;
        }

        $oSysReq = getSystemReqCheck();
        $aInfo = $oSysReq->getSystemInfo();
        foreach ( $aInfo as $sGroup => $aModules ) {
            // translating
            $sGroupName = $oSetupLang->getModuleName( $sGroup );
            foreach ( $aModules as $sModule => $iModuleState ) {
                // translating
                $blContinue = $blContinue && ( bool ) abs( $iModuleState );

                // was unable to update htaccess file for mod_rewrite check
                if ( $blHtaccessUpdateError && $sModule == 'server_permissions') {
                    $sClass = $oSetup->getModuleClass( 0 );
                    $blContinue = false;
                } else {
                    $sClass = $oSetup->getModuleClass( $iModuleState );
                }
                $aGroupModuleInfo[$sGroupName][] = array( 'module' => $sModule,
                                                          'class'  => $sClass,
                                                          'modulename' => $oSetupLang->getModuleName( $sModule ) );
            }
        }

        $oView->setTitle( 'STEP_0_TITLE' );
        $oView->setViewParam( "blContinue", $blContinue );
        $oView->setViewParam( "aGroupModuleInfo", $aGroupModuleInfo );
        $oView->setViewParam( "aLanguages", getLanguages() );
        $oView->setViewParam( "sSetupLang", $this->getInstance( "oxSetupSession" )->getSessionParam( 'setup_lang' ) );
        return "systemreq.php";
    }

    /**
     * Welcome page
     *
     * @return string
     */
    public function welcome()
    {
        $oSession = $this->getInstance( "oxSetupSession" );

        //setting admin area default language
        $sAdminLang = $oSession->getSessionParam('setup_lang');
        $this->getInstance( "oxSetupUtils" )->setCookie( "oxidadminlanguage", $sAdminLang, time() + 31536000, "/" );

        $oView = $this->getView();
        $oView->setTitle( 'STEP_1_TITLE' );
        $oView->setViewParam( "aCountries", getCountryList() );
        $oView->setViewParam( "aLocations", getLocation() );
        $oView->setViewParam( "aLanguages", getLanguages() );
        $oView->setViewParam( "sShopLang", $oSession->getSessionParam( 'sShopLang' ) );
        $oView->setViewParam( "sSetupLang", $this->getInstance( "oxSetupLang" )->getSetupLang() );
        $oView->setViewParam( "sLocationLang", $oSession->getSessionParam('location_lang') );
        $oView->setViewParam( "sCountryLang", $oSession->getSessionParam('country_lang') );
        return "welcome.php";
    }

    /**
     * License confirmation page
     *
     * @return string
     */
    public function license()
    {
        $sLicenseFile = "lizenz.txt";

        $oView = $this->getView();
        $oView->setTitle( 'STEP_2_TITLE' );
        $oView->setViewParam( "aLicenseText", $this->getInstance( "oxSetupUtils" )->getFileContents( $this->getInstance( "oxSetupLang" )->getSetupLang() . "/" . $sLicenseFile ) );
        return "license.php";
    }

    /**
     * DB info entry page
     *
     * @return string
     */
    public function dbInfo()
    {
        $oView    = $this->getView();
        $oSession = $this->getInstance( "oxSetupSession" );

        $iEula = $this->getInstance( "oxSetupUtils" )->getRequestVar( "iEula", "post" );
        $iEula = (int) ( $iEula ? $iEula : $oSession->getSessionParam( "eula" ) );
        if ( !$iEula ) {
            $oSetup = $this->getInstance( "oxSetup" );
            $oSetup->setNextStep( $oSetup->getStep( "STEP_WELCOME" ) );
            $oView->setMessage( $this->getInstance( "oxSetupLang" )->getText( "ERROR_SETUP_CANCELLED" ) );
            return "licenseerror.php";
        }

        $oView->setTitle( 'STEP_3_TITLE' );
        $aDB = $oSession->getSessionParam( 'aDB' );
        if ( !isset( $aDB ) ) {
            // default values
            $aDB['dbHost'] = "localhost";
            $aDB['dbUser'] = "";
            $aDB['dbPwd']  = "";
            $aDB['dbName'] = "";
            $aDB['dbiDemoData'] = 1;
        }
        $oView->setViewParam( "aDB", $aDB );

        // mb string library info
        $oSysReq = getSystemReqCheck();
        $oView->setViewParam( "blMbStringOn", $oSysReq->getModuleInfo( 'mb_string' ) );
        $oView->setViewParam( "blUnicodeSupport", $oSysReq->getModuleInfo( 'unicode_support' ) );

        return "dbinfo.php";
    }

    /**
     * Setup paths info entry page
     *
     * @return string
     */
    public function dirsInfo()
    {
        $oSession = $this->getInstance( "oxSetupSession" );
        $oView    = $this->getView();
        $oView->setTitle( 'STEP_4_TITLE' );
        $oView->setViewParam( "aSetupConfig", $oSession->getSessionParam('aSetupConfig') );
        $oView->setViewParam( "aAdminData", $oSession->getSessionParam('aAdminData') );
        $oView->setViewParam( "aPath", $this->getInstance( "oxSetupUtils" )->getDefaultPathParams() );
        return "dirsinfo.php";
    }

    /**
     * Testing database connection
     *
     * @return string
     */
    public function dbConnect()
    {
        $oSetup   = $this->getInstance( "oxSetup" );
        $oSession = $this->getInstance( "oxSetupSession" );
        $oLang    = $this->getInstance( "oxSetupLang" );

        $oView = $this->getView();
        $oView->setTitle( 'STEP_3_1_TITLE' );

        $aDB = $this->getInstance( "oxSetupUtils" )->getRequestVar( "aDB", "post" );
        if ( !isset( $aDB['iUtfMode'] ) ) {
            $aDB['iUtfMode'] = 0;
        }
        $oSession->setSessionParam( 'aDB', $aDB );

        // check if iportant parameters are set
        if ( !$aDB['dbHost'] || !$aDB['dbName'] ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
            $oView->setMessage( $oLang->getText( 'ERROR_FILL_ALL_FIELDS' ) );
            return "default.php";
        }

        try {
            // ok check DB Connection
            $oDb = $this->getInstance( "oxSetupDb" );
            $oDb->openDatabase( $aDB );
        } catch ( Exception $oExcp ) {
            if ( $oExcp->getCode() === oxSetupDb::ERROR_DB_CONNECT ) {
                $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
                $oView->setMessage( $oLang->getText( 'ERROR_DB_CONNECT' ) . " - ". mysql_error() );
                return "default.php";
            } elseif ( $oExcp->getCode() === oxSetupDb::ERROR_MYSQL_VERSION_DOES_NOT_FIT_REQUIREMENTS ) {
                $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
                $oView->setMessage( $oExcp->getMessage() );
                return "default.php";
            } else {
                try {
                    // if database is not there, try to create it
                    $oDb->createDb( $aDB['dbName'] );
                } catch ( Exception $oExcp ) {
                    $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
                    $oView->setMessage( $oExcp->getMessage() );
                    return "default.php";
                }
                $oView->setViewParam( "blCreated", 1 );
            }
        }

        $oView->setViewParam( "aDB", $aDB );
        $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_CREATE' ) );
        return "dbconnect.php";
    }

    /**
     * Creating database
     *
     * @return string
     */
    public function dbCreate()
    {
        $oSetup   = $this->getInstance( "oxSetup" );
        $oSession = $this->getInstance( "oxSetupSession" );
        $oLang    = $this->getInstance( "oxSetupLang" );

        $oView = $this->getView();
        $oView->setTitle( 'STEP_3_2_TITLE' );

        $aDB = $oSession->getSessionParam('aDB');
        $blOverwrite = $this->getInstance( "oxSetupUtils" )->getRequestVar( "ow", "get" );
        if ( !isset( $blOverwrite ) ) {
            $blOverwrite = false;
        }

        $oDb = $this->getInstance( "oxSetupDb" );
        $oDb->openDatabase( $aDB );

        // testing if views can be created
        try {
            $oDb->testCreateView();
        } catch ( Exception $oExcp ) {
            // Views can not be created
            $oView->setMessage( $oExcp->getMessage() );
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DB_INFO' ) );
            return "default.php";
        }

        // check if DB is already UP and running
        if ( !$blOverwrite ) {

            try {
                $blDbExists = true;
                $oDb->execSql( "select * from oxconfig" );
            } catch ( Exception $oExcp ) {
                $blDbExists = false;
            }

            if ( $blDbExists ) {
                // DB already UP ?
                $oView->setMessage( sprintf( $oLang->getText('ERROR_DB_ALREADY_EXISTS'), $aDB['dbName'] ) .
                                    "<br><br>" . $oLang->getText('STEP_3_2_CONTINUE_INSTALL_OVER_EXISTING_DB') . " <a href=\"index.php?sid=".$oSession->getSid()."&istep=".$oSetup->getStep('STEP_DB_CREATE')."&ow=1\" id=\"step3Continue\" style=\"text-decoration: underline;\">" . $oLang->getText('HERE') . "</a>" );
                return "default.php";
            }
        }

        $sqlDir = 'sql';

        //settting database collation
        $iUtfMode = isset( $aDB['iUtfMode'] )?( (int) $aDB['iUtfMode'] ):0;
        $oDb->setMySqlCollation( $iUtfMode );

        try {
            $oDb->queryFile( "$sqlDir/database.sql" );
        } catch ( Exception $oExcp ) {
            $oView->setMessage( $oExcp->getMessage() );
            return "default.php";
        }

        if ( $aDB['dbiDemoData'] == '1') {
            // install demodata
            try {
                $oDb->queryFile( "$sqlDir/demodata.sql" );
            } catch ( Exception $oExcp ) {
                // there where problems with queries
                $oView->setMessage( $oLang->getText('ERROR_BAD_DEMODATA') . "<br><br>".$oExcp->getMessage() );
                return "default.php";
            }
        }

        //swap database to english
        if ( $oSession->getSessionParam('location_lang') != "de" ) {
            try {
                $oDb->queryFile( "$sqlDir/en.sql" );
            } catch ( Exception $oExcp ) {
                $oView->setMessage( $oLang->getText('ERROR_BAD_DEMODATA') . "<br><br>".$oExcp->getMessage() );
                return "default.php";
            }
        }

        //update dyn pages / shop country config options (from first step)
        $oDb->saveDynPagesSettings( array() );

        //applying utf-8 specific queries

        if ( $iUtfMode ) {
            $oDb->queryFile(  "$sqlDir/latin1_to_utf8.sql" );

            //converting oxconfig table field 'oxvarvalue' values to utf
            $oDb->setMySqlCollation( 0 );
            $oDb->convertConfigTableToUtf();
        }

        $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
        $oView->setMessage( $oLang->getText('STEP_3_2_CREATING_DATA') );
        return "default.php";
    }

    /**
     * Writing config info
     *
     * @return string
     */
    public function dirsWrite()
    {
        $oView = $this->getView();

        $oSetup   = $this->getInstance( "oxSetup" );
        $oSession = $this->getInstance( "oxSetupSession" );
        $oLang    = $this->getInstance( "oxSetupLang" );
        $oUtils   = $this->getInstance( "oxSetupUtils" );

        $oView->setTitle( 'STEP_4_1_TITLE' );

        $aPath = $oUtils->getRequestVar( "aPath", "post" );
        $aSetupConfig = $oUtils->getRequestVar( "aSetupConfig", "post" );
        $aAdminData   = $oUtils->getRequestVar( "aAdminData", "post" );

        // correct them
        $aPath['sShopURL'] = $oUtils->preparePath( $aPath['sShopURL'] );
        $aPath['sShopDir'] = $oUtils->preparePath( $aPath['sShopDir'] );
        $aPath['sCompileDir'] = $oUtils->preparePath( $aPath['sCompileDir'] );
        $aPath['sBaseUrlPath'] = $oUtils->extractRewriteBase( $aPath['sShopURL'] );

        // using same array to pass additional setup variable
        if ( isset( $aSetupConfig['blDelSetupDir']) && $aSetupConfig['blDelSetupDir'] ) {
            $aSetupConfig['blDelSetupDir'] = 1;
        } else {
            $aSetupConfig['blDelSetupDir'] = 0;
        }

        $oSession->setSessionParam('aPath', $aPath );
        $oSession->setSessionParam('aSetupConfig', $aSetupConfig );
        $oSession->setSessionParam( 'aAdminData', $aAdminData );

        // check if important parameters are set
        if ( !$aPath['sShopURL'] || !$aPath['sShopDir'] || !$aPath['sCompileDir'] ||
             !$aAdminData['sLoginName'] || !$aAdminData['sPassword'] || !$aAdminData['sPasswordConfirm'] ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            $oView->setMessage( $oLang->getText('ERROR_FILL_ALL_FIELDS') );
            return "default.php";
        }

        // check if passwords match
        if ( strlen( $aAdminData['sPassword'] ) < 6 ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            $oView->setMessage( $oLang->getText('ERROR_PASSWORD_TOO_SHORT') );
            return "default.php";
        }

        // check if passwords match
        if ( $aAdminData['sPassword'] != $aAdminData['sPasswordConfirm'] ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            $oView->setMessage( $oLang->getText('ERROR_PASSWORDS_DO_NOT_MATCH') );
            return "default.php";
        }

        // check if email matches pattern
        if ( !$oUtils->isValidEmail( $aAdminData['sLoginName'] ) ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            $oView->setMessage( $oLang->getText('ERROR_USER_NAME_DOES_NOT_MATCH_PATTERN') );
            return "default.php";
        }

        try {
            // creating admin user
            $this->getInstance( "oxSetupDb" )->writeAdminLoginData( $aAdminData['sLoginName'], $aAdminData['sPassword'] );
            $oUtils->checkPaths( $aPath );
        } catch ( Exception $oExcp ) {
            $oView->setMessage( $oExcp->getMessage() );
            return "default.php";
        }

        // write it now
        try {
            $aParams = array_merge( ( array ) $oSession->getSessionParam('aDB'), $aPath );

            // updating config file
            $oUtils->updateConfigFile( $aParams );

            // updating regular htaccess file
            $oUtils->updateHtaccessFile( $aParams );

            // updating admin htaccess file
            //$oUtils->updateHtaccessFile( $aParams, "admin" );
        } catch ( Exception $oExcp ) {
            $oSetup->setNextStep( $oSetup->getStep( 'STEP_DIRS_INFO' ) );
            $oView->setMessage( $oExcp->getMessage() );
            return "default.php";
        }


            $oSetup->setNextStep( $oSetup->getStep( 'STEP_FINISH' ) );

        $oView->setMessage( $oLang->getText('STEP_4_1_DATA_WAS_WRITTEN' ) );
        $oView->setViewParam( "aPath", $aPath );
        $oView->setViewParam( "aSetupConfig", $aSetupConfig );
        return "default.php";
    }


    /**
     * Final setup step
     *
     * @return string
     */
    public function finish()
    {
        $oSession = $this->getInstance( "oxSetupSession" );
        $aPath = $oSession->getSessionParam( "aPath" );

        $oView = $this->getView();
        $oView->setTitle( "STEP_6_TITLE" );
        $oView->setViewParam( "aPath", $aPath );
        $oView->setViewParam( "aSetupConfig", $oSession->getSessionParam( "aSetupConfig" ) );
        $oView->setViewParam( "blWritableConfig", is_writable( $aPath['sShopDir']."/config.inc.php" ) );
        return "finish.php";
    }
}

/**
 * Chooses and executes controller action which must be executec to render expected view
 */
class oxSetupDispatcher extends oxSetupCore
{
    /**
     * Executes current controller action
     *
     * @return null
     */
    public function run()
    {
        // choosing which controller action must be executed
        $sAction = $this->_chooseCurrentAction();

        // executing action which returns name of template to render
        $oController = $this->getInstance( "oxSetupController" );

        // displaying output
        $oController->getView()->display( $oController->$sAction() );
    }

    /**
     * Returns name of controller action script to perform
     *
     * @return string | null
     */
    protected function _chooseCurrentAction()
    {
        $oSetup = $this->getInstance( "oxSetup" );
        $iCurrStep = $oSetup->getCurrentStep();

        $sName = null;
        foreach ( $oSetup->getSteps() as $sStepName => $sStepId ) {
            if ( $sStepId == $iCurrStep ) {
                $sActionName = str_ireplace( "step_", "", $sStepName );
                $sName = str_replace( "_", "", $sActionName );
                break;
            }
        }
        return $sName;
    }
}

/**
 * APS setup class
 */
class oxSetupAps extends oxSetupCore
{
    /**
     * Unknown setup command
     *
     * @var int
     */
    const ERROR_UNKNOWN_COMMAND = 1;

    /**
     * Setup execution handler
     *
     * @param string $sCommand command
     *
     * @return null
     */
    public function execute( $sCommand )
    {
        switch ( $sCommand ) {
            case "install":
                $this->install();
                break;
            case "remove":
                $this->remove();
                break;
            case "configure":
                $this->configure();
                break;
            case "upgrade":
                $this->upgrade();
                break;
            default:
                throw new Exception( "Error: unknown command $sCommand.\n", oxSetupAps::ERROR_UNKNOWN_COMMAND );
                break;
        }
    }

    /**
     * Performs application installation process
     *
     * @return null
     */
    public function install()
    {
        $oUtils = $this->getInstance( "oxSetupUtils" );

        // --
        // collecting data

        // db host
        $sDbPort = $oUtils->getEnvVar( "DB_main_PORT" );
        $aParams['dbHost'] = $oUtils->getEnvVar( "DB_main_HOST" ) . ( $sDbPort ? ":".$sDbPort : "" );

        // db user
        $aParams['dbUser'] = $oUtils->getEnvVar( "DB_main_LOGIN" );

        // db pass
        $aParams['dbPwd']  = $oUtils->getEnvVar( "DB_main_PASSWORD" );

        // db name
        $aParams['dbName'] = $oUtils->getEnvVar( "DB_main_NAME" );

        // install demo data ?
        $blInstallDemoData = $oUtils->getEnvVar( "SETTINGS_install_demodata" );

        // utf mode?
        $aParams['iUtfMode'] = $iUtfMode = (int) $oUtils->getEnvVar( "SETTINGS_utf8_mode" );

        // shop url
        $aParams["sShopURL"] = rtrim( $oUtils->getEnvVar( "BASE_URL_HOST" ), "/" );
        if ( ( strpos( $aParams["sShopURL"], 'http://' ) || strpos( $aParams["sShopURL"], 'https://' ) ) === false ) {
            $aParams["sShopURL"] = rtrim( $oUtils->getEnvVar( "BASE_URL_SCHEME" ), "://" )."://".$aParams["sShopURL"];
        }

        $aParams["sBaseUrlPath"] = $oUtils->getEnvVar( "BASE_URL_PATH" );
        if ( $aParams["sBaseUrlPath"] && ( $aParams["sBaseUrlPath"] = trim( $aParams["sBaseUrlPath"] ) ) ) {
            $aParams["sShopURL"] .= "/".rtrim( $aParams["sBaseUrlPath"], "/" );
        }

        // install path
        $aParams["sShopDir"] = getInstallPath();

        // temp file folder
        $aParams["sCompileDir"] = $aParams["sShopDir"]."tmp";

        // check for updates?
        $aParams["check_for_updates"] = $oUtils->getEnvVar( "SETTINGS_check_for_updates" );

        // default country language
        $aParams["setup_lang"] = $oUtils->getEnvVar( "SETTINGS_location_lang" );
        $aParams["location_lang"] = $oUtils->getEnvVar( "SETTINGS_location_lang" );
        $aParams["country_lang"] = $oUtils->getEnvVar( "SETTINGS_country_lang" );

        // enable dyn content?
        $aParams["use_dyn_pages"] = (int) $oUtils->getEnvVar( "SETTINGS_use_dynamic_pages" );

        // --
        // installing

        // db connection instance
        $oDb = $this->getInstance( "oxSetupDb" );

        // initializing connection
        $oDb->openDatabase( $aParams );

        // setting connection collation
        $oDb->setMySqlCollation( $iUtfMode );

        // setupping db
        $oDb->queryFile( "database.sql" );

        // install demo data?
        if ( $blInstallDemoData ) {
            $oDb->queryFile( "demodata.sql" );
        }

        //swap database to english
        if ( $aParams["location_lang"] != "de" ) {
            $oDb->queryFile( "en.sql" );
        }

        //update dyn pages / shop country config options (from first step)
        $oDb->saveDynPagesSettings( $aParams );

        //applying utf-8 specific queries
        if ( $iUtfMode ) {
            $oDb->queryFile(  "latin1_to_utf8.sql" );

            // setting connection collation
            $oDb->setMySqlCollation( 0 );

            //converting oxconfig table field 'oxvarvalue' values to utf
            $oDb->convertConfigTableToUtf();
        }

        // updating admin user
        $oDb->writeAdminLoginData( $oUtils->getEnvVar( "SETTINGS_admin_user_name" ), $oUtils->getEnvVar( "SETTINGS_admin_user_password" ) );

        // testing install paths
        $oUtils->checkPaths( $aParams );

        // updating config file
        $oUtils->updateConfigFile( $aParams );

        $oUtils->updateHtaccessFile( $aParams );

    }

    /**
     * Performs application removal process
     *
     * @return null
     */
    public function remove()
    {
        // cleanup and remove tmp folder
        $oUtils = $this->getInstance( "oxSetupUtils" );

        $sCompileDir = getInstallPath()."tmp/";

        $oUtils->removeDir( $sCompileDir, true );

        // seems like APS removes rest of files/db itself
        return;
        /*
        // --
        // collecting data

        // db host
        $sDbPort = $oUtils->getEnvVar("DB_main_PORT");
        $aParams['dbHost'] = $oUtils->getEnvVar( "DB_main_HOST" ) . ( $sDbPort ? ":".$sDbPort : "" );

        // db user
        $aParams['dbUser'] = $oUtils->getEnvVar( "DB_main_LOGIN" );

        // db pass
        $aParams['dbPwd']  = $oUtils->getEnvVar( "DB_main_PASSWORD" );

        // db name
        $aParams['dbName'] = $oUtils->getEnvVar( "DB_main_NAME" );

        // --
        // removing

        // db connection instance
        $oDb = $this->getInstance( "oxSetupDb" );

        // initializing connection
        $oDb->openDatabase( $aParams );

        // setupping db
        $oDb->queryFile( "remove.sql" );
        */
    }

    /**
     * Shop configuration script
     *
     * @return null
     */
    public function configure()
    {
        $oUtils = $this->getInstance( "oxSetupUtils" );

        // --
        // collecting data

        // shop url
        //$aParams["sShopURL"] = $oUtils->getEnvVar( "BASE_URL_HOST" ); //? not clear its name
        //if ( ( strpos( $aParams["sShopURL"], 'http://' ) || strpos( $aParams["sShopURL"], 'https://' ) ) === false ) {
        //    $aParams["sShopURL"] = $oUtils->getEnvVar( "BASE_URL_SCHEME" )."://".$aParams["sShopURL"];
        //}
        //if ( ( $sBaseUrlPath = $oUtils->getEnvVar( "BASE_URL_PATH" ) ) ) {
        //    $aParams["sShopURL"] .= "/".$sBaseUrlPath;
        //}

        //
        // db host
        $sDbPort = $oUtils->getEnvVar("DB_main_PORT");
        $aDbParams['dbHost'] = $oUtils->getEnvVar( "DB_main_HOST" ) . ( $sDbPort ? ":".$sDbPort : "" );

        // db user
        $aDbParams['dbUser'] = $oUtils->getEnvVar( "DB_main_LOGIN" );

        // db pass
        $aDbParams['dbPwd']  = $oUtils->getEnvVar( "DB_main_PASSWORD" );

        // db name
        $aDbParams['dbName'] = $oUtils->getEnvVar( "DB_main_NAME" );

        // db connection instance
        $oDb = $this->getInstance( "oxSetupDb" );

        // initializing connection
        $oDb->openDatabase( $aDbParams );

        // check for updates?
        $blCheckForUpdates = ( bool ) $oUtils->getEnvVar( "SETTINGS_check_for_updates" );

        // enable dyn content?
        $blUseDynPages = (int) $oUtils->getEnvVar( "SETTINGS_use_dynamic_pages" );

        $sBaseShopId = $this->getInstance( "oxSetup" )->getShopId();
        $oConfk = new Conf();

        $oDb->execSql( "delete from oxconfig where oxvarname = 'blCheckForUpdates'" );
        $oDb->execSql( "delete from oxconfig where oxvarname = 'blLoadDynContents'" );
        $sUid = $oUtils->generateUid();
        $oDb->execSql( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                 values('$sUid', '$sBaseShopId', 'blCheckForUpdates', 'bool', ENCODE( '$blCheckForUpdates', '".$oConfk->sConfigKey."'))" );

        $sUid = $oUtils->generateUid();
        $oDb->execSql( "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue)
                                 values('$sUid', '$sBaseShopId', 'blLoadDynContents', 'bool', ENCODE( '$blUseDynPages', '".$oConfk->sConfigKey."'))" );
        // updating config file
        //$oUtils->updateConfigFile( $aParams );
    }

    /**
     * Shop upgrade script
     *
     * @return null
     */
    public function upgrade()
    {
    }
}
