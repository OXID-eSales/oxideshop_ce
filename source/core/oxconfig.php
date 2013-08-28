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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

//max integer
define( 'MAX_64BIT_INTEGER', '18446744073709551615' );

/**
 * Main shop configuration class.
 *
 * @package core
 */
class oxConfig extends oxSuperCfg
{
    // this column of params are defined in config.inc.php file,
    // so for backwards compatibility. names starts without underscore

    /**
     * Database host name
     *
     * @var string
     */
    protected $dbHost = null;

    /**
     * Database name
     *
     * @var string
     */
    protected $dbName = null;

    /**
     * Database user name
     *
     * @var string
     */
    protected $dbUser = null;

    /**
     * Database user password
     *
     * @var string
     */
    protected $dbPwd  = null;

    /**
     * Database driver type
     *
     * @var string
     */
    protected $dbType = null;

    /**
     * Shop Url
     *
     * @var string
     */
    protected $sShopURL = null;

    /**
     * Shop SSL mode Url
     *
     * @var string
     */
    protected $sSSLShopURL = null;

    /**
     * Shops admin SSL mode Url
     *
     * @var string
     */
    protected $sAdminSSLURL = null;

    /**
     * Shops install directory
     *
     * @var string
     */
    protected $sShopDir = null;

    /**
     * Shops compile directory
     *
     * @var string
     */
    protected $sCompileDir = null;

    /**
     * Debug mode (default is 0):
     *  -1 = Logger Messages internal use only
     *   0 = off
     *   1 = smarty
     *   2 = SQL
     *   3 = SQL + smarty
     *   4 = SQL + smarty + shop template data
     *   5 = Delivery Cost calculation info
     *   6 = SMTP Debug Messages
     *   7 = Slow SQL query indication
     *
     * @var int
     */
    protected $iDebug = 0;

    /**
     * Administrator email address, used to send critical notices
     *
     * @var string
     */
    protected $sAdminEmail = null;

    /**
     * Use cookies
     *
     * @var bool
     */
    protected $blSessionUseCookies = null;

    /**
     * Default image loading location.
     * If $blNativeImages is set to true the shop loads images from current domain,
     * otherwise images are loaded from the domain specified in config.inc.php.
     * This is applicable for different domains depending on language or mall
     * if mall mode is available.
     *
     * @var bool
     */
    protected $blNativeImages = true;

    /**
     * Names of tables what are multi-shop
     *
     * @var array
     */
    protected $aMultiShopTables = array( 'oxarticles', 'oxdiscount', 'oxcategories', 'oxattribute',
                                         'oxlinks', 'oxvoucherseries', 'oxmanufacturers',
                                         'oxnews', 'oxselectlist', 'oxwrapping',
                                         'oxdeliveryset', 'oxdelivery', 'oxvendor', 'oxobject2category');

    /**
     * oxConfig instance
     *
     * @var oxConfig
     */
    private static $_instance = null;

    /**
     * Application starter instance
     *
     * @var oxStart
     */
    private $_oStart = null;


    /**
     * Active shop object.
     *
     * @var object
     */
    protected $_oActShop       = null;

    /**
     * Active Views object array. Object has setters/getters for these properties:
     *   _sClass - name of current view class
     *   _sFnc   - name of current action function
     *
     * @var array
     */
    protected $_aActiveViews   = array();

    /**
     * Array of global parameters.
     *
     * @var array
     */
    protected $_aGlobalParams = array();

    /**
     * Shop config parameters storage array
     *
     * @var array
     */
    protected $_aConfigParams = array();

    /**
     * Theme config parameters storage array
     *
     * @var array
     */
    protected $_aThemeConfigParams = array();

    /**
     * Current language Id
     *
     * @var int
     */
    protected $_iLanguageId = null;

    /**
     * Current shop Id
     *
     * @var int
     */
    protected $_iShopId = null;


    /**
     * Out dir name
     *
     * @var string
     */
    protected $_sOutDir = 'out';

    /**
     * Image dir name
     *
     * @var string
     */
    protected $_sImageDir = 'img';

    /**
     * Dyn Image dir name
     *
     * @var string
     */
    protected $_sPictureDir = 'pictures';

    /**
     * Master pictures dir name
     *
     * @var string
     */
    protected $_sMasterPictureDir = 'master';

    /**
     * Template dir name
     *
     * @var string
     */
    protected $_sTemplateDir = 'tpl';

    /**
     * Resource dir name
     *
     * @var string
     */
    protected $_sResourceDir = 'src';

    /**
     * Modules dir name
     *
     * @var string
     */
    protected $_sModulesDir = 'modules';

    /**
     * Whether shop is in SSL mode
     *
     * @var bool
     */
    protected $_blIsSsl = null;

    /**
     * Absolute image dirs for each shops
     *
     * @var array
     */
    protected $_aAbsDynImageDir = array();

    /**
     * Active currency object
     *
     * @var array
     */
    protected $_oActCurrencyObject = null;

    /**
     * Indicates if OxConfig::init() method has been already run.
     * Is checked for loading config variables on demand.
     * Used in OxConfig::getConfigParam() method
     *
     * @var bool
     */
    protected $_blInit = false;

    /**
     * prefix for oxModule field for themes in oxConfig and oxConfigDisplay tables
     *
     * @var string
     */
    const OXMODULE_THEME_PREFIX = 'theme:';

    /**
     * prefix for oxModule field for modules in oxConfig and oxConfigDisplay tables
     *
     * @var string
     */
    const OXMODULE_MODULE_PREFIX = 'module:';

    /**
     * Returns config parameter value if such parameter exists
     *
     * @param string $sName config parameter name
     *
     * @return mixed
     */
    public function getConfigParam( $sName )
    {

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                $sValue = modConfig::$unitMOD->getModConfigParam( $sName );
                if ( $sValue !== null ) {
                    return $sValue;
                }
            }
        }

        $this->init();

        if ( isset ( $this->_aConfigParams[$sName] ) ) {
            return $this->_aConfigParams[$sName];
        }

        if ( isset( $this->$sName ) ) {
            return $this->$sName;
        }

    }

    /**
     * Stores config parameter value in config
     *
     * @param string $sName  config parameter name
     * @param string $sValue config parameter value
     *
     * @return null
     */
    public function setConfigParam( $sName, $sValue )
    {
        if ( isset( $this->$sName ) ) {
            $this->$sName = $sValue;
        } else {
            $this->_aConfigParams[$sName] = $sValue;
        }
    }

    /**
     * Parse SEO url parameters.
     *
     * @return null
     */
    protected function _processSeoCall()
    {
        // TODO: refactor shop bootstrap and parse url params as soon as possible
        if (isSearchEngineUrl()) {
            oxNew('oxSeoDecoder')->processSeoCall();
        }
    }

    /**
     * Starts session manager
     *
     * @throws oxConnectionException
     *
     * @return null
     */
    public function init()
    {
        // Duplicated init protection
        if ($this->_blInit) {
           return;
        }
        $this->_blInit = true;

        $this->_loadVarsFromFile();

        include getShopBasePath().'core/oxconfk.php';

        // setting ADODB timeout
        global  $ADODB_SESS_LIFE;
        $ADODB_SESS_LIFE  = 1;

        // some important defaults
        $this->_setDefaults();

        try {
            $sShopID = $this->getShopId();
            $blConfigLoaded = $this->_loadVarsFromDb( $sShopID );

            // loading shop config
            if ( empty($sShopID) || !$blConfigLoaded ) {
                // if no config values where loaded (some problems with DB), throwing an exception
                $oEx = oxNew( "oxConnectionException" );
                $oEx->setMessage( "Unable to load shop config values from database" );
                throw $oEx;
            }

            // loading theme config options
            $this->_loadVarsFromDb( $sShopID, null, oxConfig::OXMODULE_THEME_PREFIX . $this->getConfigParam('sTheme') );

            // checking if custom theme (which has defined parent theme) config options should be loaded over parent theme (#3362)
            if ( $this->getConfigParam('sCustomTheme') ) {
                $this->_loadVarsFromDb( $sShopID, null, oxConfig::OXMODULE_THEME_PREFIX . $this->getConfigParam('sCustomTheme') );
            }

            // loading modules config
            $this->_loadVarsFromDb( $sShopID, null, oxConfig::OXMODULE_MODULE_PREFIX );


            $this->_processSeoCall();

            //starting up the session
            $this->getSession()->start();

        } catch ( oxConnectionException $oEx ) {

            $oEx->debugOut();
            if ( defined( 'OXID_PHP_UNIT' ) ) {
                return false;
            } elseif ( 0 != $this->iDebug ) {
                oxRegistry::getUtils()->showMessageAndExit( $oEx->getString() );
            } else {
                header( "HTTP/1.1 500 Internal Server Error");
                header( "Location: offline.html");
                header( "Connection: close");
            }
        } catch ( oxCookieException $oEx ) {

            $this->_processSeoCall();

            //starting up the session
            $this->getSession()->start();

            // redirect to start page and display the error
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            oxRegistry::getUtils()->redirect( $this->getShopHomeURL() .'cl=start', true, 302 );
        }


        // Admin handling
        $this->setConfigParam( 'blAdmin', isAdmin() );

        if ( defined('OX_ADMIN_DIR') ) {
            $this->setConfigParam( 'sAdminDir', OX_ADMIN_DIR );
        }

        $this->_loadVarsFromFile();

        //application initialization
        $this->_oStart = new oxStart();
        $this->_oStart->appInit();
    }

    /**
     * Returns oxConfig instance
     *
     * @deprecated since v5.0 (2012-08-10); In order to get oxConfig instance use Registry functionality instead ( oxRegistry::getConfig() )
     *
     * @return oxConfig
     */
    public static function getInstance()
    {
        return oxRegistry::getConfig();
    }

    /**
     * Loads vars from default config file
     *
     * @return null;
     */
    protected function _loadVarsFromFile()
    {
        //config variables from config.inc.php takes priority over the ones loaded from db
        include getShopBasePath().'/config.inc.php';

        //adding trailing slashes
        $oFileUtils = oxRegistry::get("oxUtilsFile");
        $this->sShopDir     = $oFileUtils->normalizeDir($this->sShopDir);
        $this->sCompileDir  = $oFileUtils->normalizeDir($this->sCompileDir);
        $this->sShopURL     = $oFileUtils->normalizeDir($this->sShopURL);
        $this->sSSLShopURL  = $oFileUtils->normalizeDir($this->sSSLShopURL);
        $this->sAdminSSLURL = $oFileUtils->normalizeDir($this->sAdminSSLURL);

        $this->_loadCustomConfig();
    }

    /**
     * Set important defaults.
     *
     * @return null;
     */
    protected function _setDefaults()
    {

        // some important defaults
        if( !$this->getConfigParam( 'sDefaultLang' ) )
            $this->setConfigParam( 'sDefaultLang', 0 );


        $this->setConfigParam( 'sTheme', 'azure' );


        $blLogChangesInAdmin = $this->getConfigParam( 'blLogChangesInAdmin' );
        if( !isset( $blLogChangesInAdmin ) )
            $this->setConfigParam( 'blLogChangesInAdmin', false );

        $blCheckTemplates = $this->getConfigParam( 'blCheckTemplates' );
        if( !isset( $blCheckTemplates ) )
            $this->setConfigParam( 'blCheckTemplates', false );

        $blAllowArticlesubclass = $this->getConfigParam( 'blAllowArticlesubclass' );
        if( !isset( $blAllowArticlesubclass ) )
            $this->setConfigParam( 'blAllowArticlesubclass', false );

        $iAdminListSize = $this->getConfigParam( 'iAdminListSize' );
        if( !isset( $iAdminListSize ) )
            $this->setConfigParam( 'iAdminListSize', 9 );

        // #1173M  for EE - not all pic are deleted
        $iPicCount = $this->getConfigParam( 'iPicCount' );
        if ( !isset( $iPicCount ) )
            $this->setConfigParam( 'iPicCount', 7 );

        $iZoomPicCount = $this->getConfigParam( 'iZoomPicCount' );
        if ( !isset( $iZoomPicCount ) )
            $this->setConfigParam( 'iZoomPicCount', 4 );

        // ADODB cache life time
        $iDBCacheLifeTime = $this->getConfigParam( 'iDBCacheLifeTime' );
        if ( !isset( $iDBCacheLifeTime ) )
            $this->setConfigParam( 'iDBCacheLifeTime', 3600 ); // 1 hour

        $sCoreDir = $this->getConfigParam( 'sShopDir' );
        $this->setConfigParam( 'sCoreDir', $sCoreDir.'/core/' );
    }

    /**
     * Loads vars from custom config file
     *
     * @return null;
     */
    protected function _loadCustomConfig()
    {
        $sCustConfig = getShopBasePath().'/cust_config.inc.php';
        if ( is_readable( $sCustConfig ) ) {
            include $sCustConfig;
        }
    }

    /**
     * Load config values from DB
     *
     * @param string $sShopID   shop ID to load parameters
     * @param array  $aOnlyVars array of params to load (optional)
     * @param string $sModule   module vars to load, empty for base options
     *
     * @return bool
     */
    protected function _loadVarsFromDb( $sShopID, $aOnlyVars = null, $sModule = '' )
    {
        $oDb = oxDb::getDb();

        if ( !empty($sModule) ) {
            $sModuleSql = " oxmodule LIKE " . $oDb->quote($sModule."%");
        } else {
            $sModuleSql = " oxmodule='' ";
        }

        $sQ = "select oxvarname, oxvartype, ".$this->getDecodeValueQuery()." as oxvarvalue from oxconfig where oxshopid = '$sShopID' and " . $sModuleSql;
        // dodger, allow loading from some vars only from baseshop
        if ( $aOnlyVars !== null ) {
            $blSep = false;
            $sIn  = '';
            foreach ( $aOnlyVars as $sField ) {
                if ( $blSep ) {
                    $sIn .= ', ';
                }
                $sIn .= '"'.$sField.'"';
                $blSep = true;
            }
            $sQ .= ' and oxvarname in ( '.$sIn.' ) ';
        }
        $oRs = $oDb->select( $sQ );

        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            while ( !$oRs->EOF ) {
                $sVarName = $oRs->fields[0];
                $sVarType = $oRs->fields[1];
                $sVarVal  = $oRs->fields[2];

                //in sShopURL and sSSLShopURL cases we skip (for admin or when URL values are not set)
                if ( ( $sVarName == 'sShopURL' || $sVarName == 'sSSLShopURL' ) &&
                    ( !$sVarVal || $this->isAdmin() === true ) ) {
                    $oRs->moveNext();
                    continue;
                }

                $this->_setConfVarFromDb($sVarName, $sVarType, $sVarVal);

                //setting theme options array
                if ( $sModule != '' ) {
                    $this->_aThemeConfigParams[$sVarName] = $sModule;
                }

                $oRs->moveNext();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * set config variable to config object, first unserializing it by given type
     *
     * @param string $sVarName variable name
     * @param string $sVarType variable type - arr, aarr, bool or str
     * @param string $sVarVal  serialized by type value
     *
     * @return null
     */
    protected function _setConfVarFromDb($sVarName, $sVarType, $sVarVal)
    {
        switch ( $sVarType ) {
            case 'arr':
            case 'aarr':
                $this->setConfigParam( $sVarName, unserialize( $sVarVal ) );
                break;
            case 'bool':
                $this->setConfigParam( $sVarName, ( $sVarVal == 'true' || $sVarVal == '1' ) );
                break;
            default:
                $this->setConfigParam( $sVarName, $sVarVal );
                break;
        }
    }

    /**
     * Unsets all session data.
     *
     * @return null
     */
    public function pageClose()
    {
        if ( $this->hasActiveViewsChain() ) {
            // do not commit session until active views chain exists
            return;
        }

        return $this->_oStart->pageClose();
    }

    /**
     * Returns value of parameter stored in POST,GET.
     * For security reasons performed checkParamSpecialChars().
     * use $blRaw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $sName Name of parameter
     * @param bool   $blRaw Get unescaped parameter
     *
     * @deprecated since v5.0.0 (2012-08-27); Use public getRequestParameter()
     * @return mixed
     */
    public static function getParameter( $sName, $blRaw = false )
    {
        return oxRegistry::getConfig()->getRequestParameter( $sName, $blRaw );
    }

    /**
     * Returns value of parameter stored in POST,GET.
     * For security reasons performed oxconfig->checkParamSpecialChars().
     * use $blRaw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $sName Name of parameter
     * @param bool   $blRaw Get unescaped parameter
     *
     * @return mixed
     */
    public function getRequestParameter( $sName, $blRaw = false )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                try{
                    $sValue = modConfig::getParameter(  $sName, $blRaw );

                    // TODO: remove this after special chars concept implementation
                    $blIsAdmin = modConfig::getInstance()->isAdmin() || modSession::getInstance()->getVariable(  "blIsAdmin" );
                    if ( $sValue !== null && !$blIsAdmin && (!$blRaw || is_array($blRaw))) {
                        $this->checkParamSpecialChars( $sValue, $blRaw );
                    }

                    return $sValue;
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        $sValue = null;

        if ( isset( $_POST[$sName] ) ) {
            $sValue = $_POST[$sName];
        } elseif ( isset( $_GET[$sName] ) ) {
            $sValue = $_GET[$sName];
        }

        // TODO: remove this after special chars concept implementation
        $blIsAdmin = $this->isAdmin() && $this->getSession()->getVariable( "blIsAdmin" );
        if ( $sValue !== null && !$blIsAdmin && (!$blRaw || is_array($blRaw))) {
            $this->checkParamSpecialChars( $sValue, $blRaw );
        }

        return $sValue;
    }

    /**
     * Returns uploaded file parameter
     *
     * @param array $sParamName param name
     *
     * @return null
     */
    public function getUploadedFile($sParamName)
    {
        return $_FILES[$sParamName];
    }

    /**
     * Sets global parameter value
     *
     * @param string $sName  name of parameter
     * @param mixed  $sValue value to store
     *
     * @return null
     */
    public function setGlobalParameter( $sName, $sValue )
    {
        $this->_aGlobalParams[$sName] = $sValue;
    }

    /**
     * Returns global parameter value
     *
     * @param string $sName name of cached parameter
     *
     * @return mixed
     */
    public function getGlobalParameter( $sName )
    {
        if ( isset( $this->_aGlobalParams[$sName] ) ) {
            return $this->_aGlobalParams[$sName];
        } else {
            return null;
        }
    }

    /**
     * Checks if passed parameter has special chars and replaces them.
     * Returns checked value.
     *
     * @param mixed &$sValue value to process escaping
     * @param array $aRaw    keys of unescaped values
     *
     * @deprecated since v5.0.0 (2012-08-27); Use public checkParamSpecialChars().
     *
     * @return mixed
     */
    public static function checkSpecialChars( & $sValue, $aRaw = null )
    {
        return oxRegistry::getConfig()->checkParamSpecialChars( $sValue, $aRaw );
    }

    /**
     * Checks if passed parameter has special chars and replaces them.
     * Returns checked value.
     *
     * @param mixed &$sValue value to process escaping
     * @param array $aRaw    keys of unescaped values
     *
     * @return mixed
     */
    public function checkParamSpecialChars( & $sValue, $aRaw = null )
    {
        if ( is_object( $sValue ) ) {
            return $sValue;
        }

        if ( is_array( $sValue ) ) {
            $newValue = array();
            foreach ( $sValue as $sKey => $sVal ) {
                $sValidKey = $sKey;
                if ( !$aRaw || !in_array($sKey, $aRaw) ) {
                    $this->checkParamSpecialChars( $sValidKey );
                    $this->checkParamSpecialChars( $sVal );
                    if ($sValidKey != $sKey) {
                        unset ($sValue[$sKey]);
                    }
                }
                $newValue[$sValidKey] = $sVal;
            }
            $sValue = $newValue;
        } elseif ( is_string( $sValue ) ) {
            $sValue = str_replace( array( '&',     '<',    '>',    '"',      "'",      chr(0), '\\' ),
                                   array( '&amp;', '&lt;', '&gt;', '&quot;', '&#039;', '',     '&#092;' ),
                                   $sValue );
        }
        return $sValue;
    }

    /**
     * Returns active shop ID.
     *
     * @return int
     */
    public function getShopId()
    {
        if ( $this->_iShopId !== null )
            return $this->_iShopId;

            $this->_iShopId = $this->getBaseShopId();


        $this->getSession()->setVariable( 'actshop', $this->_iShopId );
        return $this->_iShopId;
    }


    /**
     * Active Shop id setter
     *
     * @param string $sShopId shop id
     *
     * @return null
     */
    public function setShopId( $sShopId )
    {
        $this->getSession()->setVariable( 'actshop', $sShopId );
        $this->_iShopId = $sShopId;
    }



     /**
     * Set is shop url
     *
     * @param bool $blIsSsl - state bool value
     *
     * @return string
     */
    public function setIsSsl( $blIsSsl = false )
    {
        $this->_blIsSsl = $blIsSsl;
    }

    /**
     * Checks if WEB session is SSL.
     *
     * @return null
     */
    protected function _checkSsl()
    {
            $myUtilsServer   = oxRegistry::get("oxUtilsServer");
            $aServerVars     = $myUtilsServer->getServerVar();
            $aHttpsServerVar = $myUtilsServer->getServerVar( 'HTTPS' );

            $this->setIsSsl();
            if (isset( $aHttpsServerVar ) && ($aHttpsServerVar === 'on' || $aHttpsServerVar === 'ON' || $aHttpsServerVar == '1' )) {
                // "1&1" hoster provides "1"
                $this->setIsSsl($this->getConfigParam('sSSLShopURL') || $this->getConfigParam('sMallSSLShopURL')) ;
                if ($this->isAdmin() && !$this->_blIsSsl) {
                    //#4026
                     $this->setIsSsl( !is_null($this->getConfigParam('sAdminSSLURL')) );
                }
            }

            //additional special handling for profihost customers
            if ( isset( $aServerVars['HTTP_X_FORWARDED_SERVER'] ) &&
                 ( strpos( $aServerVars['HTTP_X_FORWARDED_SERVER'], 'ssl' ) !== false ||
                 strpos( $aServerVars['HTTP_X_FORWARDED_SERVER'], 'secure-online-shopping.de' ) !== false ) ) {
                 $this->setIsSsl( true );
            }

    }


    /**
     * Checks if WEB session is SSL. Returns true if yes.
     *
     * @return bool
     */
    public function isSsl()
    {
        if ( is_null( $this->_blIsSsl ) ) {
            $this->_checkSsl();
        }
        return $this->_blIsSsl;
    }

    /**
     * Compares current URL to supplied string
     *
     * @param string $sURL URL
     *
     * @return bool true if $sURL is equal to current page URL
     */
    public function isCurrentUrl( $sURL )
    {
        // Missing protocol, cannot proceed, assuming true.
        if ( !$sURL || (strpos( $sURL, "http" ) !== 0)) {
            return true;
        }

        return oxRegistry::get("oxUtilsServer")->isCurrentUrl($sURL);
    }

    /**
     * Compares current protocol to supplied url string
     *
     * @param string $sURL URL
     *
     * @return bool true if $sURL is equal to current page URL
     */
    public function isCurrentProtocol( $sURL )
    {
        // Missing protocol, cannot proceed, assuming true.
        if ( !$sURL || (strpos( $sURL, "http" ) !== 0)) {
            return true;
        }

        return (strpos( $sURL, "https:" ) === 0) == $this->isSsl();
    }

    /**
     * Returns config sShopURL or sMallShopURL if secondary shop
     *
     * @param int  $iLang   language
     * @param bool $blAdmin if admin
     *
     * @return string
     */
    public function getShopUrl( $iLang = null, $blAdmin = null )
    {
        $blAdmin = isset( $blAdmin ) ? $blAdmin : $this->isAdmin();
        if ( $blAdmin ) {
            return $this->getConfigParam( 'sShopURL' );
        }

        // #680 per language another URL
        $iLang = isset( $iLang ) ? $iLang : oxRegistry::getLang()->getBaseLanguage();
        $aLanguageURLs = $this->getConfigParam( 'aLanguageURLs' );
        if ( isset( $iLang ) && isset( $aLanguageURLs[$iLang] ) && !empty( $aLanguageURLs[$iLang] ) ) {
            $aLanguageURLs[$iLang] = oxRegistry::getUtils()->checkUrlEndingSlash( $aLanguageURLs[$iLang] );
            return $aLanguageURLs[$iLang];
        }

        //normal section
        $sMallShopURL = $this->getConfigParam( 'sMallShopURL' );
        if ( $sMallShopURL ) {
            $sMallShopURL = oxRegistry::getUtils()->checkUrlEndingSlash( $sMallShopURL );
            return $sMallShopURL;
        }

        return $this->getConfigParam( 'sShopURL' );
    }

    /**
     * Returns config sSSLShopURL or sMallSSLShopURL if secondary shop
     *
     * @param int $iLang language (default is null)
     *
     * @return string
     */
    public function getSslShopUrl( $iLang = null )
    {
        // #680 per language another URL
        $iLang = isset( $iLang ) ? $iLang : oxRegistry::getLang()->getBaseLanguage();
        $aLanguageSSLURLs = $this->getConfigParam( 'aLanguageSSLURLs' );
        if ( isset( $iLang ) && isset( $aLanguageSSLURLs[$iLang] ) && !empty( $aLanguageSSLURLs[$iLang] ) ) {
            $aLanguageSSLURLs[$iLang] = oxRegistry::getUtils()->checkUrlEndingSlash( $aLanguageSSLURLs[$iLang] );
            return $aLanguageSSLURLs[$iLang];
        }

        //mall mode
        if ( ( $sMallSSLShopURL = $this->getConfigParam( 'sMallSSLShopURL' ) ) ) {
            $sMallSSLShopURL = oxRegistry::getUtils()->checkUrlEndingSlash( $sMallSSLShopURL );
            return $sMallSSLShopURL;
        }

        if ( ( $sMallShopURL = $this->getConfigParam( 'sMallShopURL' ) ) ) {
            $sMallShopURL = oxRegistry::getUtils()->checkUrlEndingSlash( $sMallShopURL );
            return $sMallShopURL;
        }

        //normal section
        if ( ( $sSSLShopURL = $this->getConfigParam( 'sSSLShopURL' ) ) ) {
            return $sSSLShopURL;
        }

        return $this->getShopUrl( $iLang );
    }

    /**
     * Returns utils dir URL
     *
     * @return string
     */
    public function getCoreUtilsUrl()
    {
        return $this->getCurrentShopUrl().'core/utils/';
    }

    /**
     * Returns SSL or non SSL shop URL without index.php depending on Mall
     * affecting environment is admin mode and current ssl usage status
     *
     * @param bool $blAdmin if admin
     *
     * @return string
     */
    public function getCurrentShopUrl($blAdmin = null)
    {
        if ($blAdmin===null) {
            $blAdmin = $this->isAdmin();
        }
        if ($blAdmin) {
            if ($this->isSsl()) {

                $sUrl = $this->getConfigParam( 'sAdminSSLURL' );
                if ( !$sUrl ) {
                    return $this->getSslShopUrl() . $this->getConfigParam( 'sAdminDir' ) . '/';
                }
                return $sUrl;
            } else {
                return $this->getShopUrl() . $this->getConfigParam( 'sAdminDir' ) . '/';
            }
        } else {
            return $this->isSsl() ? $this->getSslShopUrl() : $this->getShopUrl();
        }
    }

    /**
     * Returns SSL or not SSL shop URL with index.php and sid
     *
     * @param int $iLang language (optional)
     *
     * @return string
     */
    public function getShopCurrentUrl( $iLang = null )
    {
        if ( $this->isSsl() ) {
            $sURL = $this->getSSLShopURL( $iLang );
        } else {
            $sURL = $this->getShopURL( $iLang );
        }

        return oxRegistry::get("oxUtilsUrl")->processUrl( $sURL.'index.php', false );
    }

    /**
     * Returns shop non SSL URL including index.php and sid.
     *
     * @param int  $iLang   language
     * @param bool $blAdmin if admin
     *
     * @return string
     */
    public function getShopHomeUrl( $iLang = null, $blAdmin = null )
    {
        return oxRegistry::get("oxUtilsUrl")->processUrl($this->getShopUrl( $iLang, $blAdmin).'index.php', false );
    }

    /**
     * Returns widget start non SSL URL including widget.php and sid.
     *
     * @param int  $iLang   language
     * @param bool $blAdmin if admin
     *
     * @return string
     */
    public function getWidgetUrl( $iLang = null, $blAdmin = null )
    {
        return oxRegistry::get("oxUtilsUrl")->processUrl($this->getShopUrl( $iLang, $blAdmin).'widget.php', false );
    }

    /**
     * Returns shop SSL URL with index.php and sid.
     *
     * @return string
     */
    public function getShopSecureHomeUrl()
    {
        return  oxRegistry::get("oxUtilsUrl")->processUrl( $this->getSslShopUrl().'index.php', false );
    }

    /**
     * Returns active shop currency.
     *
     * @return string
     */
    public function getShopCurrency()
    {
        $iCurr = null;
        if ( ( null === ( $iCurr = $this->getRequestParameter( 'cur' ) ) ) ) {
            if ( null === ( $iCurr = $this->getRequestParameter( 'currency' ) ) ) {
                $iCurr = $this->getSession()->getVariable( 'currency' );
            }
        }
        return (int) $iCurr;
    }

    /**
     * Returns active shop currency object.
     *
     * @return object
     */
    public function getActShopCurrencyObject()
    {
        //caching currency as it does not change through the script
        //but not for unit tests as ther it changes always
        if ( !defined( 'OXID_PHP_UNIT' ) ) {
            if (!is_null($this->_oActCurrencyObject)) {
                return $this->_oActCurrencyObject;
            }
        }

        $iCur = $this->getShopCurrency();
        $aCurrencies = $this->getCurrencyArray();
        if ( !isset( $aCurrencies[$iCur] ) ) {
            return $this->_oActCurrencyObject = reset( $aCurrencies ); // reset() returns the first element
        }

        return $this->_oActCurrencyObject = $aCurrencies[$iCur];
    }

    /**
     * Sets the actual currency
     *
     * @param int $iCur 0 = EUR, 1 = GBP, 2 = CHF
     *
     * @return null
     */
    public function setActShopCurrency( $iCur )
    {
        $aCurrencies = $this->getCurrencyArray();
        if ( isset( $aCurrencies[$iCur] ) ) {
            $this->getSession()->setVariable( 'currency', $iCur );
            $this->_oActCurrencyObject = null;
        }
    }

    /**
     * Returns path to out dir
     *
     * @param bool $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getOutDir( $blAbsolute = true)
    {
        if ($blAbsolute) {
            return $this->getConfigParam('sShopDir').$this->_sOutDir.'/';
        } else {
            return $this->_sOutDir.'/';
        }
    }

    /**
     * Returns path to out dir
     *
     * @param bool $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getViewsDir( $blAbsolute = true )
    {
        if ($blAbsolute) {
            return $this->getConfigParam('sShopDir'). 'application/views/';
        } else {
            return 'application/views/';
        }
    }

    /**
     * Returns path to translations dir
     *
     * @param string $sFile      File name
     * @param string $sDir       Directory name
     * @param bool   $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getTranslationsDir( $sFile, $sDir, $blAbsolute = true )
    {
        $sPath = $blAbsolute ? $this->getConfigParam( 'sShopDir' ) : '';
        $sPath .= 'application/translations/';
        if ( is_readable( $sPath. $sDir. '/'. $sFile ) ) {
            return $sPath. $sDir. '/'. $sFile;
        }
        return false;
    }

    /**
     * Returns path to out dir
     *
     * @param bool $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getAppDir( $blAbsolute = true )
    {
        if ($blAbsolute) {
            return $this->getConfigParam('sShopDir'). 'application/';
        } else {
            return 'application/';
        }
    }

    /**
     * Returns url to out dir
     *
     * @param bool $blSSL       Whether to force ssl
     * @param bool $blAdmin     Whether to force admin
     * @param bool $blNativeImg Whether to force native image dirs
     *
     * @return string
     */
    public function getOutUrl( $blSSL = null , $blAdmin = null, $blNativeImg = false )
    {
        $blSSL    = is_null($blSSL)?$this->isSsl():$blSSL;
        $blAdmin  = is_null($blAdmin)?$this->isAdmin():$blAdmin;

        if ( $blSSL ) {
            if ($blNativeImg && !$blAdmin) {
                $sUrl = $this->getSslShopUrl();
            } else {
                $sUrl = $this->getConfigParam('sSSLShopURL');
                if (!$sUrl && $blAdmin) {
                    $sUrl = $this->getConfigParam('sAdminSSLURL').'../';
                }
            }
        } else {
            $sUrl = ($blNativeImg && !$blAdmin )?$this->getShopUrl():$this->getConfigParam( 'sShopURL' );
        }

        return $sUrl.$this->_sOutDir.'/';
    }

    /**
     * Finds and returns files or folders path in out dir
     *
     * @param string $sFile        File name
     * @param string $sDir         Directory name
     * @param bool   $blAdmin      Whether to force admin
     * @param int    $iLang        Language id
     * @param int    $iShop        Shop id
     * @param string $sTheme       Theme name
     * @param bool   $blAbsolute   mode - absolute/relative path
     * @param bool   $blIgnoreCust Ignore custom theme
     *
     * @return string
     */
    public function getDir($sFile, $sDir, $blAdmin, $iLang = null, $iShop = null, $sTheme = null, $blAbsolute = true, $blIgnoreCust = false  )
    {
        if ( is_null($sTheme) ) {
            $sTheme = $this->getConfigParam( 'sTheme' );
        }

        if ( $blAdmin ) {
            $sTheme = 'admin';
        }

        if ( $sDir != $this->_sTemplateDir ) {
            $sBase    = $this->getOutDir( $blAbsolute );
            $sAbsBase = $this->getOutDir();
        } else {
            $sBase    = $this->getViewsDir( $blAbsolute );
            $sAbsBase = $this->getViewsDir();
        }

        $sLang = '-';
        // FALSE means skip language folder check
        if ( $iLang !== false ) {
            $oLang = oxRegistry::getLang();

            if ( is_null( $iLang ) ) {
                $iLang = $oLang->getEditLanguage();
            }

            $sLang = $oLang->getLanguageAbbr( $iLang );
        }

        if ( is_null($iShop) ) {
            $iShop = $this->getShopId();
        }

        //Load from
        $sPath = "{$sTheme}/{$iShop}/{$sLang}/{$sDir}/{$sFile}";
        $sCacheKey = $sPath . "_{$blIgnoreCust}{$blAbsolute}";

        if ( ( $sReturn = oxRegistry::getUtils()->fromStaticCache( $sCacheKey ) ) !== null ) {
            return $sReturn;
        }

        $sReturn = false;

        // Check for custom template
        $sCustomTheme = $this->getConfigParam( 'sCustomTheme' );
        if ( !$blAdmin && !$blIgnoreCust && $sCustomTheme && $sCustomTheme != $sTheme) {
            $sReturn = $this->getDir( $sFile, $sDir, $blAdmin, $iLang, $iShop, $sCustomTheme, $blAbsolute );
        }

        //test lang level ..
        if ( !$sReturn && !$blAdmin && is_readable( $sAbsBase.$sPath ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test shop level ..
        $sPath = "$sTheme/$iShop/$sDir/$sFile";
        if ( !$sReturn && !$blAdmin && is_readable( $sAbsBase.$sPath ) ) {
            $sReturn = $sBase . $sPath;
        }


        //test theme language level ..
        $sPath = "$sTheme/$sLang/$sDir/$sFile";
        if ( !$sReturn && $iLang !== false && is_readable( $sAbsBase.$sPath ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test theme level ..
        $sPath = "$sTheme/$sDir/$sFile";
        if ( !$sReturn && is_readable( $sAbsBase.$sPath ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test out language level ..
        $sPath = "$sLang/$sDir/$sFile";
        if ( !$sReturn &&  $iLang !== false && is_readable( $sAbsBase.$sPath ) ) {
            $sReturn = $sBase . $sPath;
        }

        //test out level ..
        $sPath = "$sDir/$sFile";
        if ( !$sReturn && is_readable( $sAbsBase.$sPath ) ) {
            $sReturn = $sBase . $sPath;
        }

        if ( !$sReturn ) {
            // TODO: implement logic to log missing paths
        }

        // to cache
        oxRegistry::getUtils()->toStaticCache( $sCacheKey, $sReturn );

        return $sReturn;
    }

    /**
     * Finds and returns file or folder url in out dir
     *
     * @param string $sFile       File name
     * @param string $sDir        Directory name
     * @param bool   $blAdmin     Whether to force admin
     * @param bool   $blSSL       Whether to force ssl
     * @param bool   $blNativeImg Whether to force native image dirs
     * @param int    $iLang       Language id
     * @param int    $iShop       Shop id
     * @param string $sTheme      Theme name
     *
     * @return string
     */
    public function getUrl($sFile, $sDir , $blAdmin = null, $blSSL = null, $blNativeImg = false, $iLang = null , $iShop = null , $sTheme = null )
    {
        $sUrl = str_replace(
                                $this->getOutDir(),
                                $this->getOutUrl($blSSL, $blAdmin, $blNativeImg),
                                $this->getDir( $sFile, $sDir, $blAdmin, $iLang, $iShop, $sTheme )
                            );

        return $sUrl;
    }

    /**
     * Finds and returns image files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getImagePath( $sFile, $blAdmin = false )
    {
        return $this->getDir( $sFile, $this->_sImageDir, $blAdmin );
    }

    /**
     * Finds and returns image folder url
     *
     * @param bool   $blAdmin     Whether to force admin
     * @param bool   $blSSL       Whether to force ssl
     * @param bool   $blNativeImg Whether to force native image dirs
     * @param string $sFile       Image file name
     *
     * @return string
     */
    public function getImageUrl( $blAdmin = false, $blSSL = null, $blNativeImg = null, $sFile = null )
    {
        $blNativeImg = is_null($blNativeImg)?$this->getConfigParam( 'blNativeImages' ):$blNativeImg;
        return $this->getUrl( $sFile, $this->_sImageDir, $blAdmin, $blSSL, $blNativeImg );
    }

    /**
     * Finds and returns image folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getImageDir( $blAdmin = false )
    {
        return $this->getDir( null, $this->_sImageDir, $blAdmin );
    }

    /**
     * Finds and returns product pictures files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param int    $iLang   Language
     * @param int    $iShop   Shop id
     * @param string $sTheme  theme name
     *
     * @return string
     */
    public function getPicturePath($sFile, $blAdmin = false, $iLang = null , $iShop = null , $sTheme = null)
    {
        return $this->getDir( $sFile, $this->_sPictureDir, $blAdmin, $iLang, $iShop, $sTheme );
    }

    /**
     * Finds and returns master pictures folder path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getMasterPictureDir( $blAdmin = false )
    {
        return $this->getDir( null, $this->_sPictureDir . "/" . $this->_sMasterPictureDir, $blAdmin );
    }

    /**
     * Finds and returns master picture path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getMasterPicturePath( $sFile, $blAdmin = false )
    {
        return $this->getDir( $sFile, $this->_sPictureDir . "/" . $this->_sMasterPictureDir, $blAdmin );
    }

    /**
     * Finds and returns product picture file or folder url
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language
     * @param int    $iShopId Shop id
     * @param string $sDefPic Default (nopic) image path ["0/nopic.jpg"]
     *
     * @return string
     */
    public function getPictureUrl( $sFile, $blAdmin = false, $blSSL = null, $iLang = null, $iShopId = null, $sDefPic = "master/nopic.jpg" )
    {
        if ( $sAltUrl = oxRegistry::get("oxPictureHandler")->getAltImageUrl('/', $sFile, $blSSL) ) {
            return $sAltUrl;
        }

        $blNativeImg = $this->getConfigParam( 'blNativeImages' );
        $sUrl = $this->getUrl( $sFile, $this->_sPictureDir, $blAdmin, $blSSL, $blNativeImg, $iLang, $iShopId );

        //anything is better than empty name, because <img src=""> calls shop once more = x2 SLOW.
        if ( !$sUrl && $sDefPic ) {
            $sUrl = $this->getUrl( $sDefPic, $this->_sPictureDir, $blAdmin, $blSSL, $blNativeImg, $iLang, $iShopId );
        }
        return $sUrl;
    }

    /**
     * Finds and returns product pictures folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getPictureDir( $blAdmin )
    {
        return $this->getDir( null, $this->_sPictureDir, $blAdmin );
    }

    /**
     * Finds and returns templates files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getTemplatePath( $sFile, $blAdmin )
    {
        $sTemplatePath = $this->getDir( $sFile, $this->_sTemplateDir, $blAdmin );

        if (!$sTemplatePath) {
            $sBasePath        = getShopBasePath();
            $aModuleTemplates = $this->getConfigParam('aModuleTemplates');

            $oModulelist = oxNew('oxmodulelist');
            $aActiveModuleInfo = $oModulelist->getActiveModuleInfo();
            if (is_array($aModuleTemplates) && is_array($aActiveModuleInfo)) {
                foreach ($aModuleTemplates as $sModuleId => $aTemplates) {
                    if (isset($aTemplates[$sFile]) && isset($aActiveModuleInfo[$sModuleId])) {
                        $sPath = $aTemplates[$sFile];
                        $sPath = $sBasePath. 'modules/'.  $sPath;
                        if (is_file($sPath) && is_readable($sPath)) {
                            $sTemplatePath =  $sPath;
                        }
                    }
                }
            }
        }

        return $sTemplatePath;
    }

    /**
     * Finds and returns templates folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getTemplateDir( $blAdmin = false )
    {
        return $this->getDir( null, $this->_sTemplateDir, $blAdmin );
    }

    /**
     * Finds and returns template file or folder url
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language id
     *
     * @return string
     */
    public function getTemplateUrl( $sFile = null, $blAdmin = false, $blSSL = null , $iLang = null )
    {
        return $this->getShopMainUrl() . $this->getDir( $sFile, $this->_sTemplateDir, $blAdmin, $iLang, null, null, false );
    }

    /**
     * Finds and returns base template folder url
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getTemplateBase( $blAdmin = false )
    {
        // Base template dir is the parent dir of template dir
        return str_replace( $this->_sTemplateDir.'/', '', $this->getDir( null, $this->_sTemplateDir, $blAdmin, null, null, null, false ));
    }

    /**
     * Finds and returns resource (css, js, etc..) files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getResourcePath($sFile = '', $blAdmin = false )
    {
        return $this->getDir( $sFile, $this->_sResourceDir, $blAdmin );
    }

    /**
     * Returns path to modules dir
     *
     * @param bool $blAbsolute mode - absolute/relative path
     *
     * @return string
     */
    public function getModulesDir( $blAbsolute = true )
    {
        if ($blAbsolute) {
            return $this->getConfigParam('sShopDir') . $this->_sModulesDir . '/';
        } else {
            return $this->_sModulesDir . '/';
        }
    }

    /**
     * Finds and returns resource (css, js, etc..) file or folder url
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language id
     *
     * @return string
     */
    public function getResourceUrl( $sFile = '', $blAdmin = false , $blSSL = null , $iLang = null )
    {
        $blNativeImg = $this->getConfigParam( 'blNativeImages' );
        return $this->getUrl( $sFile, $this->_sResourceDir, $blAdmin, $blSSL, $blNativeImg, $iLang );
    }

    /**
     * Finds and returns resource (css, js, etc..) folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getResourceDir( $blAdmin )
    {
        return $this->getDir( null, $this->_sResourceDir, $blAdmin );
    }

    /**
     * Returns array of available currencies
     *
     * @param integer $iCurrency Active currency number (default null)
     *
     * @return array
     */
    public function getCurrencyArray( $iCurrency = null )
    {
        $aConfCurrencies = $this->getConfigParam( 'aCurrencies' );
        if ( !is_array( $aConfCurrencies ) ) {
            return array();
        }

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                try{
                    $aAltCurrencies = modConfig::getInstance()->getConfigParam( 'modaCurrencies' );
                    if ( isset( $aAltCurrencies ) ) {
                        $aConfCurrencies = $aAltCurrencies;
                    }
                } catch( Exception $e ) {
                    // if exception is thrown, use default
                }
            }
        }

        // processing currency configuration data
        $aCurrencies = array();
        reset( $aConfCurrencies );
        while ( list( $key, $val ) = each( $aConfCurrencies ) ) {
            if ( $val ) {
                $oCur = new stdClass();
                $oCur->id      = $key;
                $sCur = explode( '@', $val);
                $oCur->name     = trim( $sCur[0] );
                $oCur->rate     = trim( $sCur[1] );
                $oCur->dec      = trim( $sCur[2] );
                $oCur->thousand = trim( $sCur[3] );
                $oCur->sign     = trim( $sCur[4] );
                $oCur->decimal  = trim( $sCur[5] );

                // change for US version
                if ( isset( $sCur[6] ) ) {
                    $oCur->side = trim($sCur[6]);
                }

                if ( isset( $iCurrency) && $key == $iCurrency ) {
                    $oCur->selected = 1;
                } else {
                    $oCur->selected = 0;
                }
                $aCurrencies[$key]= $oCur;
            }

            // #861C -  performance, do not load other currencies
            if ( !$this->getConfigParam( 'bl_perfLoadCurrency' ) ) {
                break;
            }
        }
        return $aCurrencies;
    }

    /**
     * Returns currency object.
     *
     * @param string $sName Name of active currency
     *
     * @return object
     */
    public function getCurrencyObject( $sName )
    {
        $aSearch = $this->getCurrencyArray();
        foreach ( $aSearch as $oCur ) {
            if ( $oCur->name == $sName ) {
                return $oCur;
            }
        }
    }

    /**
     * Checks if the shop is in demo mode.
     *
     * @return bool
     */
    public function isDemoShop()
    {
        return $this->getConfigParam('blDemoShop');
    }



    /**
     * Returns OXID eShop edition
     *
     * @return string
     */
    public function getEdition()
    {
            return "CE";


    }

    /**
     * Returns full eShop edition name
     *
     * @return string
     */
    public function getFullEdition()
    {
        $sEdition = $this->getEdition();

            if ($sEdition == "CE") {
                return "Community Edition";
            }



        return $sEdition;
    }

    /**
     * Returns shops version number (eg. '4.4.2')
     *
     * @return string
     */
    public function getVersion()
    {
        $sVersion = $this->getActiveShop()->oxshops__oxversion->value;
        return $sVersion;
    }

    /**
     * Returns build revision number or false on read error.
     *
     * @return int
     */
    public function getRevision()
    {
        $sFileName = $this->getConfigParam( 'sShopDir' ) . "/pkg.rev";
        $sRev = trim(@file_get_contents($sFileName));

        if (!$sRev) {
            return false;
        }

        return $sRev;
    }

    /**
     * Returns build package info file content.
     *
     * @return bool|string
     */
    public function getPackageInfo()
    {
        $sFileName = $this->getConfigParam( 'sShopDir' ) . "/pkg.info";
        $sRev = @file_get_contents($sFileName);
        $sRev = str_replace("\n", "<br>", $sRev);

        if (!$sRev) {
            return false;
        }

        return $sRev;
    }


    /**
     * Checks if shop is MALL. Returns true on success.
     *
     * @return bool
     */
    public function isMall()
    {

            return false;
    }

    /**
     * Checks version of shop, returns:
     *  0 - version is bellow 2.2
     *  1 - Demo or unlicensed
     *  2 - Pro
     *  3 - Enterprise
     *
     * @return int
     */
    public function detectVersion()
    {
    }



    /**
     * Updates or adds new shop configuration parameters to DB.
     * Arrays must be passed not serialized, serialized values are supported just for backward compatibility.
     *
     * @param string $sVarType Variable Type
     * @param string $sVarName Variable name
     * @param mixed  $sVarVal  Variable value (can be string, integer or array)
     * @param string $sShopId  Shop ID, default is current shop
     * @param string $sModule  Module name (empty for base options)
     *
     * @return null
     */
    public function saveShopConfVar( $sVarType, $sVarName, $sVarVal, $sShopId = null, $sModule = '' )
    {
        switch ( $sVarType ) {
            case 'arr':
            case 'aarr':
                $sValue = serialize( $sVarVal );
                break;
            case 'bool':
                //config param
                $sVarVal = (( $sVarVal == 'true' || $sVarVal) && $sVarVal && strcasecmp($sVarVal, "false"));
                //db value
                $sValue  = $sVarVal?"1":"";
                break;
            case 'num':
                //config param
                $sVarVal = $sVarVal != ''? oxRegistry::getUtils()->string2Float( $sVarVal ) : '';
                $sValue = $sVarVal;
                break;
            default:
                $sValue  = $sVarVal;
                break;
        }

        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        // Update value only for current shop
        if ($sShopId == $this->getShopId()) {
            $this->setConfigParam( $sVarName, $sVarVal );
        }

        $oDb = oxDb::getDb();

        $sShopIdQuoted     = $oDb->quote($sShopId);
        $sModuleQuoted     = $oDb->quote($sModule);
        $sVarNameQuoted    = $oDb->quote($sVarName);
        $sVarTypeQuoted    = $oDb->quote($sVarType);
        $sVarValueQuoted   = $oDb->quote($sValue);
        $sConfigKeyQuoted  = $oDb->quote($this->getConfigParam('sConfigKey'));
        $sNewOXIDdQuoted   = $oDb->quote(oxUtilsObject::getInstance()->generateUID());

        $sQ = "delete from oxconfig where oxshopid = $sShopIdQuoted and oxvarname = $sVarNameQuoted and oxmodule = $sModuleQuoted";
        $oDb->execute( $sQ );

        $sQ = "insert into oxconfig (oxid, oxshopid, oxmodule, oxvarname, oxvartype, oxvarvalue)
               values($sNewOXIDdQuoted, $sShopIdQuoted, $sModuleQuoted, $sVarNameQuoted, $sVarTypeQuoted, ENCODE( $sVarValueQuoted, $sConfigKeyQuoted) )";
        $oDb->execute( $sQ );



    }


    /**
     * Retrieves shop configuration parameters from DB.
     *
     * @param string $sVarName Variable name
     * @param string $sShopId  Shop ID
     * @param string $sModule  module identifier
     *
     * @return object - raw configuration value in DB
     */
    public function getShopConfVar( $sVarName, $sShopId = null, $sModule = '' )
    {
        if ( !$sShopId ) {
            $sShopId = $this->getShopId();
        }

        if ( $sShopId === $this->getShopId() && ( !$sModule || $sModule == oxConfig::OXMODULE_THEME_PREFIX . $this->getConfigParam('sTheme') ) ) {
            $sVarValue = $this->getConfigParam( $sVarName );
            if ( $sVarValue !== null ) {
                return $sVarValue;
            }
        }

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $sQ  = "select oxvartype, ".$this->getDecodeValueQuery()." as oxvarvalue from oxconfig where oxshopid = '{$sShopId}' and oxmodule = '{$sModule}' and oxvarname = ".$oDb->quote($sVarName);
        $oRs = $oDb->select( $sQ );

        $sValue = null;
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $sValue = $this->decodeValue( $oRs->fields['oxvartype'], $oRs->fields['oxvarvalue'] );
        }
        return $sValue;
    }

    /**
     * Decodes and returns database value
     *
     * @param string $sType      parameter type
     * @param mixed  $mOrigValue parameter db value
     *
     * @return mixed
     */
    public function decodeValue( $sType, $mOrigValue )
    {
        $sValue = $mOrigValue;
        switch ( $sType ) {
            case 'arr':
            case 'aarr':
                $sValue = unserialize( $mOrigValue );
                break;
            case 'bool':
                $sValue = ( $mOrigValue == 'true' || $mOrigValue == '1' );
                break;
        }

        return $sValue;
    }

    /**
     * Returns decode query part user to decode config field value
     *
     * @param string $sFieldName field name, default "oxvarvalue" [optional]
     *
     * @return string
     */
    public function getDecodeValueQuery( $sFieldName = "oxvarvalue" )
    {
        return " DECODE( {$sFieldName}, '".$this->getConfigParam( 'sConfigKey' )."') ";
    }

    /**
     * Returns true if current active shop is in productive mode or false if not
     *
     * @return bool
     */
    public function isProductiveMode()
    {
        $blProductive = false;

        $blProductive = $this->getConfigParam( 'blProductive' );
        if ( !isset( $blProductive ) ) {
            $sQ = 'select oxproductive from oxshops where oxid = "'.$this->getShopId().'"';
            $blProductive = ( bool ) oxDb::getDb()->getOne( $sQ );
            $this->setConfigParam( 'blProductive', $blProductive );
        }

        return $blProductive;
    }



    /**
     * Function returns default shop ID
     *
     * @return string
     */
    public function getBaseShopId()
    {

            return 'oxbaseshop';
    }

    /**
     * Loads and returns active shop object
     *
     * @return oxShop
     */
    public function getActiveShop()
    {
        if ( $this->_oActShop && $this->_iShopId == $this->_oActShop->getId() &&
             $this->_oActShop->getLanguage() == oxRegistry::getLang()->getBaseLanguage() ) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew( 'oxshop' );
        $this->_oActShop->load( $this->getShopId() );
        return $this->_oActShop;
    }

    /**
     * Returns active view object. If this object was not defined - returns oxubase object
     *
     * @return oxView
     */
    public function getActiveView()
    {
        if ( count( $this->_aActiveViews ) ) {
            $oActView = end( $this->_aActiveViews );
        }
        if ( $oActView == null ) {
            $oActView = oxNew( 'oxubase' );
            $this->_aActiveViews[] = $oActView;
        }

        return $oActView;
    }

    /**
     * Returns top active view object from views chain.
     *
     * @return oxView
     */
    public function getTopActiveView()
    {
        if ( count( $this->_aActiveViews ) ) {
            return reset( $this->_aActiveViews );
        } else {
            return $this->getActiveView();
        }
    }

    /**
     * Returns all active views objects list.
     *
     * @return array
     */
    public function getActiveViewsList()
    {
        return $this->_aActiveViews;
    }

    /**
     * View object setter
     *
     * @param object $oView view object
     *
     * @return null
     */
    public function setActiveView( $oView )
    {
        $this->_aActiveViews[] = $oView;
    }

    /**
     * Drop last active view object
     *
     * @return null
     */
    public function dropLastActiveView()
    {
        array_pop( $this->_aActiveViews );
    }

    /**
     * Check if there is more than one active view
     *
     * @return null
     */
    public function hasActiveViewsChain()
    {
        return ( count( $this->_aActiveViews ) > 1 );
    }

    /**
     * Get active views names list
     *
     * @return array
     */
    public function getActiveViewsNames()
    {
        $aNames = array();

        if ( is_array( $this->getActiveViewsList() ) ) {
            foreach ($this->getActiveViewsList() as $oView ) {
                $aNames[] = $oView->getClassName();
            }
        }

        return $aNames;
    }

    /**
     * Returns true if current installation works in UTF8 mode, or false if not
     *
     * @return bool
     */
    public function isUtf()
    {
        return ( bool ) $this->getConfigParam( 'iUtfMode' );
    }

    /**
     * Returns log files storage path
     *
     * @return string
     */
    public function getLogsDir()
    {
        return $this->getConfigParam( 'sShopDir' ).'log/';
    }

    /**
     * Returns true if option is theme option
     *
     * @param string $sName option name
     *
     * @return bool
     */
    public function isThemeOption( $sName )
    {
        return (bool) isset( $this->_aThemeConfigParams[$sName] );
    }

    /**
     * Returns  SSL or non SSL shop main URL without index.php
     *
     * @return string
     */
    public function getShopMainUrl()
    {
        return $this->_blIsSsl ? $this->getConfigParam( 'sSSLShopURL' ) : $this->getConfigParam( 'sShopURL' );
    }

    /**
     * Get parsed modules
     *
     * @return array
     */
    public function getAllModules()
    {
        return $this->parseModuleChains($this->getConfigParam('aModules'));
    }

    /**
     * Parse array of module chains to nested array
     *
     * @param array $aModules Module array (config format)
     *
     * @return array
     */
    public function parseModuleChains($aModules)
    {
        $aModuleArray = array();

        if (is_array($aModules)) {
            foreach ($aModules as $sClass => $sModuleChain) {
                if (strstr($sModuleChain, '&')) {
                    $aModuleChain = explode('&', $sModuleChain);
                } else {
                    $aModuleChain = array($sModuleChain);
                }
                $aModuleArray[$sClass] = $aModuleChain;
            }
        }

        return $aModuleArray;
    }

    /**
     * Return active shop ids
     *
     * @return boolean
     */
    public function getShopIds()
    {
        return oxDb::getDb()->getCol( "SELECT `oxid` FROM `oxshops`" );
    }

}
