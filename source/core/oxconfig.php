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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

define( 'MAX_64BIT_INTEGER', '18446744073709551615' );

/**
 * Main shop configuration class.
 *
 * @package core
 */
class oxConfig extends oxSuperCfg
{
    // this column of params are defined in config.inc.php file,
    // so for backwards compat. names starts without underscore

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
     *   4 = SQL + smarty + shoptemplate data
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
     * Default max article count in select lists
     *
     * @var int
     */
    //protected $iMaxArticles = 6000;

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
     * Names of tables what are multishop
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
     * @var oxconfig
     */
    private static $_instance = null;

    /**
     * Application starter instance
     *
     * @var oxstart
     */
    private $_oStart = null;


    /**
     * Active shop object.
     *
     * @var object
     */
    protected $_oActShop       = null;

    /**
     * Active View object. Object has setters/getters for these properties:
     *   _sClass - name of current view class
     *   _sFnc   - name of current action function
     *
     * @var object
     */
    protected $_oActView       = null;

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
     * prefix for oxmodule field for themes in oxconfig and oxconfigdisplay tables
     *
     * @var string
     */
    const OXMODULE_THEME_PREFIX = 'theme:';

    /**
     * prefix for oxmodule field for modules in oxconfig and oxconfigdisplay tables
     *
     * @var string
     */
    const OXMODULE_MODULE_PREFIX = 'module:';

    /**
     * The biggest amount of possible subshops
     *
     * @var integer
     */
    const OXMAX_SHOP_COUNT = 256;

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

        if ( isset( $this->$sName ) ) {
            return $this->$sName;
        } elseif ( isset ( $this->_aConfigParams[$sName] ) ) {
            return $this->_aConfigParams[$sName];
        }
    }

    /**
     * Stores config parameter value in cofig
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
        $this->_loadVarsFromFile();

        include getShopBasePath().'core/oxconfk.php';


        // some important defaults
        if( !$this->getConfigParam( 'sDefaultLang' ) )
            $this->setConfigParam( 'sDefaultLang', 0 );


        $this->setConfigParam( 'sTheme', 'basic' );


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

        //max shop id default value
        $iMaxShopId = $this->getConfigParam( 'iMaxShopId' );
        if ( !isset( $iMaxShopId ) ) {
            $this->setConfigParam( 'iMaxShopId', 128 );
        } elseif ( $iMaxShopId > self::OXMAX_SHOP_COUNT ) {
            $this->setConfigParam( 'iMaxShopId', self::OXMAX_SHOP_COUNT );
        }

        // disabling caching according to DODGER #655 : disable Caching as it doesnt work good enought
        $this->setConfigParam( 'blTemplateCaching', false );

        //setting ADODB timeout
        global  $ADODB_SESS_LIFE;
        $ADODB_SESS_LIFE  = 1;

        // ADODB cachelifetime
        $iDBCacheLifeTime = $this->getConfigParam( 'iDBCacheLifeTime' );
        if ( !isset( $iDBCacheLifeTime ) )
            $this->setConfigParam( 'iDBCacheLifeTime', 3600 ); // 1 hour

        $sCoreDir = $this->getConfigParam( 'sShopDir' );
        $this->setConfigParam( 'sCoreDir', $sCoreDir.'/core/' );

        try {
            $sShopID = $this->getShopId();
            $blConfigLoaded = $this->_loadVarsFromDb( $sShopID );

            // loading shop config
            if ( empty($sShopID) || !$blConfigLoaded ) {
                // if no config values where loaded (some problmems with DB), throwing an exception
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


            $this->_loadVarsFromFile();

            //application initialization
            $this->_oStart = new oxStart();
            $this->_oStart->appInit();

        } catch ( oxConnectionException $oEx ) {
            return $this->_handleDbConnectionException( $oEx );
        } catch ( oxCookieException $oEx ) {
            return $this->_handleCookieException( $oEx );
        }
    }

    /**
     * Returns singleton oxConfig object instance or create new if needed
     *
     * @return oxConfig
     */
    public static function getInstance()
    {

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                return modConfig::$unitMOD;
            }
        }

        if ( !self::$_instance instanceof oxConfig ) {
                //exceptions from here go directly to global exception handler
                //if no init is possible whole application has to die!
                self::$_instance = new oxConfig();
                self::$_instance->init();
        }
        return self::$_instance;
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
        $oFileUtils = oxUtilsFile::getInstance();
        $this->sShopDir     = $oFileUtils->normalizeDir($this->sShopDir);
        $this->sCompileDir  = $oFileUtils->normalizeDir($this->sCompileDir);
        $this->sShopURL     = $oFileUtils->normalizeDir($this->sShopURL);
        $this->sSSLShopURL  = $oFileUtils->normalizeDir($this->sSSLShopURL);
        $this->sAdminSSLURL = $oFileUtils->normalizeDir($this->sAdminSSLURL);
        
        $this->_loadCustomConfig();
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
        return $this->_oStart->pageClose();
    }

    /**
     * Returns value of parameter stored in POST,GET.
     * For security reasons performed oxconfig::checkSpecialChars().
     * use $blRaw very carefully if you want to get unescaped
     * parameter.
     *
     * @param string $sName Name of parameter
     * @param bool   $blRaw Get unescaped parameter
     *
     * @return mixed
     */
    public static function getParameter(  $sName, $blRaw = false )
    {
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            if ( isset( modConfig::$unitMOD ) && is_object( modConfig::$unitMOD ) ) {
                try{
                    $sValue = modConfig::getParameter(  $sName, $blRaw );

                    // TODO: remove this after special chars concept implementation
                    $blIsAdmin = modConfig::getInstance()->isAdmin() || isAdmin();
                    if ( $sValue !== null && !$blIsAdmin && (!$blRaw || is_array($blRaw))) {
                        self::checkSpecialChars( $sValue, $blRaw );
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
        $blIsAdmin = oxConfig::getInstance()->isAdmin() && oxSession::getVar("blIsAdmin");
        if ( $sValue !== null && !$blIsAdmin && (!$blRaw || is_array($blRaw))) {
            self::checkSpecialChars( $sValue, $blRaw );
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
     * @return mixed
     */
    public static function checkSpecialChars( & $sValue, $aRaw = null )
    {
        if ( is_object( $sValue ) ) {
            return $sValue;
        }

        if ( is_array( $sValue ) ) {
            $newValue = array();
            foreach ( $sValue as $sKey => $sVal ) {
                $sValidKey = $sKey;
                if ( !$aRaw || !in_array($sKey, $aRaw) ) {
                    self::checkSpecialChars( $sValidKey );
                    self::checkSpecialChars( $sVal );
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


        oxSession::setVar( 'actshop', $this->_iShopId );
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
        oxSession::setVar( 'actshop', $sShopId );
        $this->_iShopId = $sShopId;
    }


    /**
     * Checks if WEB session is SSL. Returns true if yes.
     *
     * @return bool
     */
    public function isSsl()
    {
        if ( is_null( $this->_blIsSsl ) ) {

            $myUtilsServer   = oxUtilsServer::getInstance();
            $aServerVars     = $myUtilsServer->getServerVar();
            $aHttpsServerVar = $myUtilsServer->getServerVar( 'HTTPS' );

            $this->_blIsSsl = false;
            if (isset( $aHttpsServerVar ) && ($aHttpsServerVar === 'on' || $aHttpsServerVar === 'ON' || $aHttpsServerVar == '1' )) {
                // "1&1" hoster provides "1"
                $this->_blIsSsl = ($this->getConfigParam('sSSLShopURL') || $this->getConfigParam('sMallSSLShopURL'));
                if ($this->isAdmin() && !$this->_blIsSsl) {
                    //#4026
                    $this->_blIsSsl = !is_null($this->getConfigParam('sAdminSSLURL')) ? true : false;
                }
            }

            //additional special handling for profihost customers
            if ( isset( $aServerVars['HTTP_X_FORWARDED_SERVER'] ) &&
                 ( strpos( $aServerVars['HTTP_X_FORWARDED_SERVER'], 'ssl' ) !== false ||
                 strpos( $aServerVars['HTTP_X_FORWARDED_SERVER'], 'secure-online-shopping.de' ) !== false ) ) {
                $this->_blIsSsl = true;
            }
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
        if ( !$sURL ) {
            return false;
        }

        $oUtilsServer = oxUtilsServer::getInstance();

        // #4010: force_sid added in https to every link
        preg_match("/^(https?:\/\/)?([^\/]+)/i", $sURL, $matches);
        $sUrlHost = $matches[2];

        // #4010: force_sid added in https to every link
        preg_match("/^(https?:\/\/)?([^\/]+)/i", $oUtilsServer->getServerVar( 'HTTP_HOST' ), $matches);
        $sRealHost = $matches[2];

        $sCurrentHost = preg_replace( '/\/\w*\.php.*/', '', $oUtilsServer->getServerVar( 'HTTP_HOST' ) . $oUtilsServer->getServerVar( 'SCRIPT_NAME' ) );

        //remove double slashes all the way
        $sCurrentHost = str_replace( '/', '', $sCurrentHost );
        $sURL = str_replace( '/', '', $sURL );

        if ( getStr()->strpos( $sURL, $sCurrentHost ) !== false ) {

            //bug fix #0002991
            if ( $sUrlHost == $sRealHost ) {
                return true;
            }
        }

        return false;
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
        $iLang = isset( $iLang ) ? $iLang : oxLang::getInstance()->getBaseLanguage();
        $aLanguageURLs = $this->getConfigParam( 'aLanguageURLs' );
        if ( isset( $iLang ) && isset( $aLanguageURLs[$iLang] ) && !empty( $aLanguageURLs[$iLang] ) ) {
            $aLanguageURLs[$iLang] = oxUtils::getInstance()->checkUrlEndingSlash( $aLanguageURLs[$iLang] );
            return $aLanguageURLs[$iLang];
        }

        //normal section
        $sMallShopURL = $this->getConfigParam( 'sMallShopURL' );
        if ( $sMallShopURL ) {
            $sMallShopURL = oxUtils::getInstance()->checkUrlEndingSlash( $sMallShopURL );
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
        $iLang = isset( $iLang ) ? $iLang : oxLang::getInstance()->getBaseLanguage();
        $aLanguageSSLURLs = $this->getConfigParam( 'aLanguageSSLURLs' );
        if ( isset( $iLang ) && isset( $aLanguageSSLURLs[$iLang] ) && !empty( $aLanguageSSLURLs[$iLang] ) ) {
            $aLanguageSSLURLs[$iLang] = oxUtils::getInstance()->checkUrlEndingSlash( $aLanguageSSLURLs[$iLang] );
            return $aLanguageSSLURLs[$iLang];
        }

        //mall mode
        if ( ( $sMallSSLShopURL = $this->getConfigParam( 'sMallSSLShopURL' ) ) ) {
            $sMallSSLShopURL = oxUtils::getInstance()->checkUrlEndingSlash( $sMallSSLShopURL );
            return $sMallSSLShopURL;
        }

        if ( ( $sMallShopURL = $this->getConfigParam( 'sMallShopURL' ) ) ) {
            $sMallShopURL = oxUtils::getInstance()->checkUrlEndingSlash( $sMallShopURL );
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

        return oxUtilsUrl::getInstance()->processUrl( $sURL.'index.php', false );
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
        return oxUtilsUrl::getInstance()->processUrl($this->getShopUrl( $iLang, $blAdmin).'index.php', false );
    }

    /**
     * Returns shop SSL URL with index.php and sid.
     *
     * @return string
     */
    public function getShopSecureHomeUrl()
    {
        return  oxUtilsUrl::getInstance()->processUrl( $this->getSslShopUrl().'index.php', false );
    }

    /**
     * Returns active shop currency.
     *
     * @return string
     */
    public function getShopCurrency()
    {
        $iCurr = null;
        if ( ( null === ( $iCurr = oxConfig::getParameter( 'cur' ) ) ) ) {
            if ( null === ( $iCurr = oxConfig::getParameter( 'currency' ) ) ) {
                $iCurr = oxSession::getVar( 'currency' );
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
            oxSession::setVar( 'currency', $iCur );
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
    public function getDir($sFile, $sDir, $blAdmin, $iLang = null, $iShop = null, $sTheme = null, $blAbsolute = true, $blIgnoreCust = false )
    {
        if ( is_null($sTheme) ) {
            $sTheme = $this->getConfigParam( 'sTheme' );
        }

        if ( $blAdmin ) {
            $sTheme = 'admin';
        }

        $sBase    = $this->getOutDir( $blAbsolute );
        $sAbsBase = $this->getOutDir();

        $sLang = '-';
        // FALSE means skip language folder check
        if ( $iLang !== false ) {
            $oLang = oxLang::getInstance();

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

        if ( ( $sReturn = oxutils::getInstance()->fromStaticCache( $sCacheKey ) ) !== null ) {
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
        oxutils::getInstance()->toStaticCache( $sCacheKey, $sReturn );

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
        if ( $sAltUrl = oxPictureHandler::getInstance()->getAltImageUrl('/', $sFile, $blSSL) ) {
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
     * Finds and returns product, category icon file
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param bool   $blSSL   Whether to force ssl
     * @param int    $iLang   Language
     * @param int    $iShopId Shop id
     * @param string $sDefPic Default (nopic) image path ["icon/nopic_ico.jpg"]
     *
     * @deprecated
     *
     * @return string
     */
    public function getIconUrl( $sFile, $blAdmin = false , $blSSL = null , $iLang = null, $iShopId = null, $sDefPic = "master/nopic.jpg" )
    {
        return $this->getPictureUrl( $sFile, $blAdmin, $blSSL, $iLang, $iShopId, $sDefPic );
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
        return $this->getUrl( $sFile, $this->_sTemplateDir, $blAdmin, $blSSL, false, $iLang );
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
     * Finds and returns resouce (css, js, etc..) files or folders path
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
     * Finds and returns resouce (css, js, etc..) file or folder url
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
     * Finds and returns resouce (css, js, etc..) folders path
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
     * Finds and returns language files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param int    $iLang   Language id
     * @param int    $iShop   Shop id
     * @param string $sTheme  Theme name
     *
     * @deprecated,  should not be used any more (2011.07.06)
     *
     * @return string
     */
    public function getLanguagePath( $sFile, $blAdmin, $iLang = null, $iShop = null, $sTheme = null )
    {
        return $this->getDir( $sFile, oxLang::getInstance()->getLanguageAbbr( $iLang ), $blAdmin, $iLang, $iShop, $sTheme );
    }

    /**
     * Returns standard (current theme) language files or folders path
     *
     * @param string $sFile   File name
     * @param bool   $blAdmin Whether to force admin
     * @param int    $iLang   Language id
     *
     * @deprecated,  should not be used any more (2011.07.06)
     *
     * @return string
     */
    public function getStdLanguagePath( $sFile, $blAdmin, $iLang = null )
    {
        $sDir = null;
        if ( $iLang !== false ) {
            $sDir = oxLang::getInstance()->getLanguageAbbr( $iLang );
        }

        return $this->getDir( $sFile, $sDir, $blAdmin, $iLang, null, $this->getConfigParam( "sTheme" ), true, true );
    }

    /**
     * Finds and returns language folders path
     *
     * @param bool $blAdmin Whether to force admin
     *
     * @return string
     */
    public function getLanguageDir( $blAdmin )
    {
        return $this->getDir( null, null, $blAdmin );
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
                $oCur = new oxStdClass();
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
        $iRev = (int) trim(@file_get_contents($sFileName));

        if (!$iRev) {
            return false;
        }

        return $iRev;
    }

    /**
     * Returns build package info file content.
     *
     * @return bool|string
     */
    public function getPackageInfo()
    {
        $sFileName = $this->getConfigParam( 'sShopDir' ) . "/pkg.info";
        $iRev = @file_get_contents($sFileName);
        $iRev = str_replace("\n", "<br>", $iRev);

        if (!$iRev) {
            return false;
        }

        return $iRev;
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
                if (is_array($sVarVal)) {
                    $sValue = serialize( $sVarVal );
                } else {
                    // Deprecated functionality
                    $sValue  = $sVarVal ;
                    $sVarVal = unserialize( $sVarVal );
                }
                break;
            case 'num':
                //config param
                $sVarVal = $sVarVal != ''? oxUtils::getInstance()->string2Float( $sVarVal ) : '';
                $sValue = $sVarVal;
                break;
            case 'bool':
                //config param
                $sVarVal = (( $sVarVal == 'true' || $sVarVal) && $sVarVal && strcasecmp($sVarVal, "false"));
                //db value
                $sValue  = $sVarVal?"1":"";
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
     * @return oxshop
     */
    public function getActiveShop()
    {
        if ( $this->_oActShop && $this->_iShopId == $this->_oActShop->getId() &&
             $this->_oActShop->getLanguage() == oxLang::getInstance()->getBaseLanguage() ) {
            return $this->_oActShop;
        }

        $this->_oActShop = oxNew( 'oxshop' );
        $this->_oActShop->load( $this->getShopId() );
        return $this->_oActShop;
    }

    /**
     * Returns active view object. If this object was not defined - returns oxview object
     *
     * @return oxview
     */
    public function getActiveView()
    {
        if ( $this->_oActView != null ) {
            return $this->_oActView;
        }

        $this->_oActView = oxNew( 'oxubase' );
        return $this->_oActView;
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
        $this->_oActView = $oView;
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
        return $this->isSsl() ? $this->getConfigParam( 'sSSLShopURL' ) : $this->getConfigParam( 'sShopURL' );
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
     * Shows exception message if debug mode is enabled, redirects otherwise.
     *
     * @param oxException $oEx message to show on exit
     * @return bool
     */
    protected function _handleDbConnectionException( $oEx )
    {
        $oEx->debugOut();

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            return false;
        } elseif ( 0 != $this->iDebug ) {
            oxUtils::getInstance()->showMessageAndExit( $oEx->getString() );
        } else {
            header( "HTTP/1.1 500 Internal Server Error");
            header( "Location: offline.html");
            header( "Connection: close");
        }
    }

    /**
     * Redirect to start page and display the error
     *
     * @param oxException $oEx message to show on exit
     * @return bool
     */
    protected function _handleCookieException( $oEx )
    {
        $this->_processSeoCall();

        $this->getSession()->start();

        oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
        oxUtils::getInstance()->redirect( $this->getShopHomeURL() .'cl=start', true, 302 );
    }
}
