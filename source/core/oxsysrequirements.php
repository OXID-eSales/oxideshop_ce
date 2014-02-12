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

/**
 * System requirements class.
 * @package core
 */
class oxSysRequirements
{
    /**
     * System required modules
     *
     * @var array
     */
    protected $_aRequiredModules = null;

    /**
     * System requirements status
     *
     * @var bool
     */
    protected $_blSysReqStatus = null;

    /**
     * Columns that should not be check for collation
     *
     * @var array
     */
    protected $_aException = array( 'OXDELIVERY'   => 'OXDELTYPE',
                                    'OXSELECTLIST' => 'OXIDENT');

    /**
     * Columns to check for collation
     *
     * @var array
     */
    protected $_aColumns = array( 'OXID',
                                  'OXOBJECTID',
                                  'OXARTICLENID',
                                  'OXACTIONID',
                                  'OXARTID',
                                  'OXUSERID',
                                  'OXADDRESSUSERID',
                                  'OXCOUNTRYID',
                                  'OXSESSID',
                                  'OXITMID',
                                  'OXPARENTID',
                                  'OXAMITEMID',
                                  'OXAMTASKID',
                                  'OXVENDORID',
                                  'OXMANUFACTURERID',
                                  'OXROOTID',
                                  'OXATTRID',
                                  'OXCATID',
                                  'OXDELID',
                                  'OXDELSETID',
                                  'OXITMARTID',
                                  'OXFIELDID',
                                  'OXROLEID',
                                  'OXCNID',
                                  'OXANID',
                                  'OXARTICLENID',
                                  'OXCATNID',
                                  'OXDELIVERYID',
                                  'OXDISCOUNTID',
                                  'OXGROUPSID',
                                  'OXLISTID',
                                  'OXPAYMENTID',
                                  'OXDELTYPE',
                                  'OXROLEID',
                                  'OXSELNID',
                                  'OXBILLCOUNTRYID',
                                  'OXDELCOUNTRYID',
                                  'OXPAYMENTID',
                                  'OXCARDID',
                                  'OXPAYID',
                                  'OXIDENT',
                                  'OXDEFCAT',
                                  'OXBASKETID',
                                  'OXPAYMENTSID',
                                  'OXORDERID',
                                  'OXVOUCHERSERIEID');

    /**
     * Installation info url
     *
     * @var string
     */
    protected $_sReqInfoUrl = "http://www.oxidforge.org/wiki/Installation";

    /**
     * Module or system configuration mapping with installation info url anchor
     *
     * @var array
     */
    protected $_aInfoMap    = array( "php_version"        => "PHP_version_at_least_5.2.10",
                                     "lib_xml2"           => "LIB_XML2",
                                     "php_xml"            => "DOM",
                                     "open_ssl"           => "OpenSSL",
                                     "soap"               => "SOAP",
                                     "j_son"              => "JSON",
                                     "i_conv"             => "ICONV",
                                     "tokenizer"          => "Tokenizer",
                                     "mysql_connect"      => "MySQL_client_connector_for_MySQL_5",
                                     "gd_info"            => "GDlib_v2_.5Bv1.5D_incl._JPEG_support",
                                     "mb_string"          => "mbstring",
                                     "bc_math"            => "BCMath",
                                     "allow_url_fopen"    => "allow_url_fopen_or_fsockopen_to_port_80",
                                     "php4_compat"        => "Zend_compatibility_mode_must_be_off",
                                     "request_uri"        => "REQUEST_URI_set",
                                     "ini_set"            => "ini_set_allowed",
                                     "register_globals"   => "register_globals_must_be_off",
                                     "memory_limit"       => "PHP_Memory_limit_.28min._14MB.2C_30MB_recommended.29",
                                     "unicode_support"    => "UTF-8_support",
                                     "file_uploads"       => "file_uploads_on",
                                     "mod_rewrite"        => "apache_mod_rewrite_module",
                                     "server_permissions" => "Files_.26_Folder_Permission_Setup",
                                     "zend_optimizer"     => "Zend_Optimizer",
                                     "bug53632"           => "Not_recommended_PHP_versions",
                                     "session_autostart"  => "session.auto_start_must_be_off",
                                     // "zend_platform_or_server"
                                      );

    /**
     * Returns PHP consntant PHP_INT_SIZE
     *
     * @return integer
     */
    protected function _getPhpIntSize()
    {
        return PHP_INT_SIZE;
    }

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
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

    /**
     * Returns config instance
     *
     * @return oxConfig
     */
    public function getConfig()
    {
        return oxRegistry::getConfig();
    }

    /**
     * Possibility to mock isAdmin() function as we do not extend oxsuperconfig.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return isAdmin();
    }

    /**
     * Sets system required modules
     *
     * @return array
     */
    public function getRequiredModules()
    {
        if ( $this->_aRequiredModules == null ) {
            $aRequiredPHPExtensions = array(
                                          'php_version',
                                          'lib_xml2',
                                          'php_xml',
                                          'j_son',
                                          'i_conv',
                                          'tokenizer',
                                          'mysql_connect',
                                          'gd_info',
                                          'mb_string',
                                          'curl',
                                          'bc_math',
                                          'open_ssl',
                                          'soap',
                                      );

            $aRequiredPHPConfigs = array(
                                       'allow_url_fopen',
                                       'php4_compat',
                                       'request_uri',
                                       'ini_set',
                                       'register_globals',
                                       'memory_limit',
                                       'unicode_support',
                                       'file_uploads',
                                       'session_autostart',
                                   );

            $aRequiredServerConfigs = array(
                                          'mod_rewrite',
                                          'server_permissions',
                                          'bug53632'
                                      );


            if ( $this->isAdmin() ) {
                $aRequiredServerConfigs[] = 'mysql_version';
            }
            $this->_aRequiredModules = array_fill_keys( $aRequiredPHPExtensions, 'php_extennsions' ) +
                                       array_fill_keys( $aRequiredPHPConfigs, 'php_config' ) +
                                       array_fill_keys( $aRequiredServerConfigs, 'server_config' );
        }
        return $this->_aRequiredModules;
    }

    /**
     * Version check for http://bugs.php.net/53632
     * Assumme that PHP versions < 5.3.5 and < 5.2.17 may have this issue, so
     * informing users about possible issues
     * PHP version 5.3.7 has security bug too.
     *
     * @return int
     */
    public function checkBug53632()
    {
        if ( $this->_getPhpIntSize() > 4 ) {
            return 2;
        }

        $iState = 1;
        if ( version_compare( PHP_VERSION, "5.3", ">=" ) ) {
            if ( version_compare( PHP_VERSION, "5.3.5", ">=" ) && version_compare( PHP_VERSION, "5.3.7", "!=" ) ) {
                $iState = 2;
            }
        } elseif ( version_compare( PHP_VERSION, '5.2', ">=" ) ) {
            $iState = version_compare( PHP_VERSION, "5.2.17", ">=" ) ? 2 : $iState;
        }
        return $iState;
    }

    /**
     * Checks if curl extension is loaded
     *
     * @return integer
     */
    public function checkCurl()
    {
        return extension_loaded( 'curl' ) ? 2 : 1;
    }

    /**
     * Checks if mbstring extension is loaded
     *
     * @return integer
     */
    public function checkMbString()
    {
        return extension_loaded( 'mbstring' ) ? 2 : 1;
    }

    /**
     * Checks if permissions on servers are correctly setup
     *
     * @param string $sPath    check path [optional]
     * @param int    $iMinPerm min permission level, default 777 [optional]
     *
     * @return int
     */
    public function checkServerPermissions( $sPath = null, $iMinPerm = 777 )
    {
        $sVerPrefix = '';

        clearstatcache();
        $sPath = $sPath ? $sPath : getShopBasePath();

        // special config file check
        $sFullPath = $sPath . "config.inc.php";
        if ( !is_readable( $sFullPath ) ||
             ( $this->isAdmin() && is_writable( $sFullPath ) ) ||
             ( !$this->isAdmin() && !is_writable( $sFullPath ) )
           ) {
            return 0;
        }

        $sTmp = "$sPath/tmp$sVerPrefix/";
        if (class_exists('oxConfig')) {
            $sCfgTmp = $this->getConfig()->getConfigParam('sCompileDir');
            if (strpos($sCfgTmp, '<sCompileDir_') === false) {
                $sTmp = $sCfgTmp;
            }
        }

        $aPathsToCheck = array(
                            $sPath."out/pictures{$sVerPrefix}/promo/",
                            $sPath."out/pictures{$sVerPrefix}/master/",
                            $sPath."out/pictures{$sVerPrefix}/generated/",
                            $sPath."out/pictures{$sVerPrefix}/media/", // @deprecated, use out/media instead
                            $sPath."out/media/",
                            $sPath."log/",
                            $sTmp
                            );
        $iModStat = 2;
        $sPathToCheck = reset( $aPathsToCheck );
        while ( $sPathToCheck ) {
            // missing file/folder?
            if ( !file_exists( $sPathToCheck ) ) {
                $iModStat = 0;
                break;
            }

            if ( is_dir( $sPathToCheck ) ) {
                // adding subfolders
                $aSubF = glob( $sPathToCheck."*", GLOB_ONLYDIR );
                if (is_array($aSubF)) {
                    foreach ( $aSubF as $sNewFolder ) {
                        $aPathsToCheck[] = $sNewFolder . "/";
                    }
                }
            }

            // testing if file permissions >= $iMinPerm
            //if ( ( (int) substr( decoct( fileperms( $sFullPath ) ), 2 ) ) < $iMinPerm ) {
            if ( !is_readable( $sPathToCheck ) || !is_writable( $sPathToCheck ) ) {
                $iModStat = 0;
                break;
            }

            $sPathToCheck = next( $aPathsToCheck );
        }

        return $iModStat;
    }

    /**
     * returns host, port, base dir, ssl information as assotiative array, false on error
     * takes this info from eShop config.inc.php (via oxConfig class)
     *
     * @return array
     */
    protected function _getShopHostInfoFromConfig()
    {
        $sShopURL = $this->getConfig()->getConfigParam( 'sShopURL' );
        if (preg_match('#^(https?://)?([^/:]+)(:([0-9]+))?(/.*)?$#i', $sShopURL, $m)) {
            $sHost = $m[2];
            $iPort = (int)$m[4];
            $blSsl = (strtolower($m[1])=='https://');
            if (!$iPort) {
                $iPort = $blSsl?443:80;
            }
            $sScript = rtrim($m[5], '/').'/';
            return array(
                    'host'=>$sHost,
                    'port'=>$iPort,
                    'dir'=>$sScript,
                    'ssl'=>$blSsl,
            );
        } else {
            return false;
        }
    }

    /**
     * returns host, port, base dir, ssl information as assotiative array, false on error
     * takes this info from eShop config.inc.php (via oxConfig class)
     *
     * @return array
     */
    protected function _getShopSSLHostInfoFromConfig()
    {
        $sSSLShopURL = $this->getConfig()->getConfigParam( 'sSSLShopURL' );
        if (preg_match('#^(https?://)?([^/:]+)(:([0-9]+))?(/.*)?$#i', $sSSLShopURL, $m)) {
            $sHost = $m[2];
            $iPort = (int)$m[4];
            $blSsl = (strtolower($m[1])=='https://');
            if (!$iPort) {
                $iPort = $blSsl?443:80;
            }
            $sScript = rtrim($m[5], '/').'/';
            return array(
                    'host'=>$sHost,
                    'port'=>$iPort,
                    'dir'=>$sScript,
                    'ssl'=>$blSsl,
            );
        }
        return false;
    }

    /**
     * returns host, port, base dir, ssl information as assotiative array, false on error
     * takes this info from _SERVER variable
     *
     * @return array
     */
    protected function _getShopHostInfoFromServerVars()
    {
        // got here from setup dir
        $sScript = $_SERVER['SCRIPT_NAME'];
        $iPort = (int) $_SERVER['SERVER_PORT'];
        $blSsl = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on'));
        if (!$iPort) {
            $iPort = $blSsl?443:80;
        }
        $sScript = rtrim(dirname(dirname( $sScript )), '/').'/';
        return array(
                    'host'=>$_SERVER['HTTP_HOST'],
                    'port'=>$iPort,
                    'dir'=>$sScript,
                    'ssl'=>$blSsl,
        );
    }

    /**
     * returns host, port, current script, ssl information as assotiative array, false on error
     *
     * @return array
     */
    protected function _getShopHostInfo()
    {
        if ( $this->isAdmin() ) {
            return $this->_getShopHostInfoFromConfig();
        } else {
            return $this->_getShopHostInfoFromServerVars();
        }
    }

    /**
     * returns host, port, current script, ssl information as assotiative array, false on error
     * Takes ssl address from config so important only in admin.
     *
     * @return array
     */
    protected function _getShopSSLHostInfo()
    {
        if ( $this->isAdmin() ) {
            return $this->_getShopSSLHostInfoFromConfig();
        }
        return false;
    }

    /**
     * Checks if mod_rewrite extension is loaded.
     * Checks for all address.
     *
     * @return integer
     */
    public function checkModRewrite()
    {
        $iModStat = null;
        $aHostInfo = $this->_getShopHostInfo();
        $iModStat = $this->_checkModRewrite( $aHostInfo );

        $aSSLHostInfo = $this->_getShopSSLHostInfo();
        // Don't need to check if mod status is already failed.
        if ( 0 != $iModStat && $aSSLHostInfo ) {
            $iSSLModStat = $this->_checkModRewrite( $aSSLHostInfo );

            // Send if failed, even if couldn't check another
            if ( 0 == $iSSLModStat ) {
                return 0;
            } elseif ( 1 == $iSSLModStat || 1 == $iModStat ) {
                return 1;
            }

            return min( $iModStat, $iSSLModStat );
        }

        return $iModStat;
    }

    /**
     * Checks if mod_rewrite extension is loaded.
     * Checks for one address.
     *
     * @param array $aHostInfo host info to open socket
     *
     * @return integer
     */
    protected function _checkModRewrite($aHostInfo)
    {
        if ( $rFp = @fsockopen( ($aHostInfo['ssl']?'ssl://':'').$aHostInfo['host'], $aHostInfo['port'], $iErrNo, $sErrStr, 10 ) ) {
            $sReq  = "POST {$aHostInfo['dir']}oxseo.php?mod_rewrite_module_is=off HTTP/1.1\r\n";
            $sReq .= "Host: {$aHostInfo['host']}\r\n";
            $sReq .= "User-Agent: OXID eShop setup\r\n";
            $sReq .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $sReq .= "Content-Length: 0\r\n"; // empty post
            $sReq .= "Connection: close\r\n\r\n";

            $sOut = '';
            fwrite( $rFp, $sReq );
            while ( !feof( $rFp ) ) {
                $sOut .= fgets( $rFp, 100 );
            }
            fclose( $rFp );

            $iModStat = ( strpos( $sOut, 'mod_rewrite_on' ) !== false ) ? 2 : 0;
        } else {
            if ( function_exists( 'apache_get_modules' ) ) {
                // it does not assure that mod_rewrite is enabled on current host, so setting 1
                $iModStat = in_array( 'mod_rewrite', apache_get_modules() ) ? 1 : 0;
            } else {
                $iModStat = -1;
            }
        }
        return $iModStat;
    }

    /**
     * Checks if activated allow_url_fopen or fsockopen on port 80 possible
     *
     * @return integer
     */
    public function checkAllowUrlFopen()
    {
        $iModStat = @ini_get('allow_url_fopen');
        $iModStat = ( $iModStat && strcasecmp( '1', $iModStat ) ) ? 2 : 1;
        if ( $iModStat == 1 ) {
            $iErrNo  = 0;
            $sErrStr = '';
            if ( $oRes = @fsockopen( 'www.example.com', 80, $iErrNo, $sErrStr, 10 ) ) {
                $iModStat = 2;
                fclose( $oRes );
            }
        }
        $iModStat = ( !$iModStat ) ? 1 : $iModStat;
        return $iModStat;
    }

    /**
     * PHP4 compatibility mode must be set off:
     * zend.ze1_compatibility_mode = Off
     *
     * @return integer
     */
    public function checkPhp4Compat()
    {
        $sZendStatus = ( strtolower( (string) @ini_get( 'zend.ze1_compatibility_mode' ) ) );
        return in_array( $sZendStatus, array( 'on', '1' ) ) ? 0 : 2;
    }

    /**
     * Checks PHP version.
     * < PHP 5.2.0 - red.
     * PHP 5.2.0-5.2.9 - yellow.
     * PHP 5.2.10 or higher - green.
     *
     * @return integer
     */
    public function checkPhpVersion()
    {
        $iModStat = ( version_compare( PHP_VERSION, '5.2', '<' ) ) ? 0 : false;
        $iModStat = ( $iModStat !== false ) ? $iModStat : ( (version_compare( PHP_VERSION, '5.2.0', '>=' ) && version_compare( PHP_VERSION, '5.2.10', '<' )) ? 1 : false );
        $iModStat = ( $iModStat !== false ) ? $iModStat : ( version_compare( PHP_VERSION, '5.2.10', '>=' ) ? 2 : 1 );
        return $iModStat;
    }

    /**
     * Checks if apache server variables REQUEST_URI or SCRIPT_URI are set
     *
     * @return integer
     */
    public function checkRequestUri()
    {
        return ( isset( $_SERVER['REQUEST_URI'] ) || isset( $_SERVER['SCRIPT_URI'] ) ) ? 2 : 0;
    }

    /**
     * Checks if libxml2 is activated
     *
     * @return integer
     */
    public function checkLibXml2()
    {
        return class_exists( 'DOMDocument' ) ? 2 : 0;
    }

    /**
     * Checks if php-xml is activated ???
     *
     * @return integer
     */
    public function checkPhpXml()
    {
        return class_exists( 'DOMDocument' ) ? 2 : 0;
    }

    /**
     * Checks if JSON extension is loaded
     *
     * @return integer
     */
    public function checkJSon()
    {
        return extension_loaded( 'json' ) ? 2 : 0;
    }

    /**
     * Checks if iconv extension is loaded
     *
     * @return integer
     */
    public function checkIConv()
    {
        return extension_loaded( 'iconv' ) ? 2 : 0;
    }

    /**
     * Checks if tokenizer extension is loaded
     *
     * @return integer
     */
    public function checkTokenizer()
    {
        return extension_loaded( 'tokenizer' ) ? 2 : 0;
    }

    /**
     * Checks if bcmath extension is loaded
     *
     * @return integer
     */
    public function checkBcMath()
    {
        return extension_loaded( 'bcmath' ) ? 2 : 1;
    }

    /**
     * Checks if openssl extension is loaded
     *
     * @return integer
     */
    public function checkOpenSsl()
    {
        return extension_loaded( 'openssl' ) ? 2 : 1;
    }

    /**
     * Checks if SOAP extension is loaded
     *
     * @return integer
     */
    public function checkSoap()
    {
        return extension_loaded( 'soap' ) ? 2 : 1;
    }

    /**
     * Checks if mysql5 extension is loaded.
     *
     * @return integer
     */
    public function checkMysqlConnect()
    {
        // MySQL module for MySQL5
        $iModStat = ( extension_loaded( 'mysql' ) || extension_loaded( 'mysqli' ) || extension_loaded( 'pdo_mysql' ) ) ? 2 : 0;

        // client version must be >=5
        if ( $iModStat ) {
            $sClientVersion = mysql_get_client_info();
            if (version_compare( $sClientVersion, '5', '<' )) {
                $iModStat = 1;
                if (version_compare( $sClientVersion, '4', '<' )) {
                    $iModStat = 0;
                }
            } elseif (version_compare($sClientVersion, '5.0.36', '>=') && version_compare($sClientVersion, '5.0.38', '<')) {
                // mantis#0001003: Problems with MySQL version 5.0.37
                $iModStat = 0;
            } elseif (version_compare($sClientVersion, '5.0.40', '>') && version_compare($sClientVersion, '5.0.42', '<')) {
                // mantis#0001877: Exclude MySQL 5.0.41 from system requirements as not fitting
                $iModStat = 0;
            }
            if ( strpos($sClientVersion, 'mysqlnd') !== false ) {
                // PHP 5.3 includes new mysqlnd extension
                $iModStat = 2;
            }
        }
        return $iModStat;
    }

    /**
     * Checks if current mysql version matches requirements ( >=5 )
     *
     * @param string $sVersion MySQL version
     *
     * @return int
     */
    public function checkMysqlVersion( $sVersion = null )
    {
        if ( $sVersion === null ) {
            $aRez = oxDb::getDb()->getAll( "SHOW VARIABLES LIKE 'version'" );
            foreach ( $aRez as $aRecord ) {
                $sVersion = $aRecord[1];
                break;
            }
        }

        $iModStat = 0;
        if ( version_compare( $sVersion, '5.0.3', '>=' ) && version_compare( $sVersion, '5.0.37', '<>' ) ) {
            $iModStat = 2;
        }

        return $iModStat;
    }

    /**
     * Checks if GDlib extension is loaded
     *
     * @return integer
     */
    public function checkGdInfo()
    {
        $iModStat = extension_loaded( 'gd' ) ? 1 : 0;
        $iModStat = function_exists( 'imagecreatetruecolor' ) ? 2 : $iModStat;
        $iModStat = function_exists( 'imagecreatefromjpeg' ) ? $iModStat : 0;
        return $iModStat;
    }

    /**
     * Checks if ini set is allowed
     *
     * @return integer
     */
    public function checkIniSet()
    {
        return ( @ini_set('session.name', 'sid' ) !== false ) ? 2 : 0;
    }

    /**
     * Checks if register_globals are off/on. Should be off.
     *
     * @return integer
     */
    public function checkRegisterGlobals()
    {
        $sGlobStatus = ( strtolower( (string) @ini_get( 'register_globals' ) ) );
        return in_array( $sGlobStatus, array( 'on', '1' ) ) ? 0 : 2;
    }

    /**
     * Checks memory limit.
     *
     * @return integer
     */
    public function checkMemoryLimit()
    {
        if ( $sMemLimit = @ini_get('memory_limit') ) {
                // CE - PE at least to 14 MB. We recomend a memory_limit of 30MB.
                $sDefLimit = '14M';
                $sRecLimit = '30M';


            $iMemLimit = $this->_getBytes( $sMemLimit );
            $iModStat = ( $iMemLimit >= $this->_getBytes( $sDefLimit ) ) ? 1 : 0;
            $iModStat = $iModStat ? ( ( $iMemLimit >= $this->_getBytes( $sRecLimit ) ) ? 2 : $iModStat ) : $iModStat;

        } else {
            $iModStat = -1;
        }
        return $iModStat;
    }

    /**
     * Checks if Zend Optimizer extension is loaded
     *
     * @return integer
     */
    public function checkZendOptimizer()
    {
        /**
         * Just for displaying "green" light, because if ZEND Optimizer/Guard loader is not
         * installed you will see some info screen instead of default setup.
         */
        return 2;
    }

    /**
     * Checks if ZEND Platform Version 3.5 or Zend Server with Data Cache is installed
     *
     * @return integer
     */
    public function checkZendPlatformOrServer()
    {
        if (function_exists( 'output_cache_get' )) {
            return 2;
        }
        if (function_exists( 'zend_disk_cache_fetch' )) {
            return 2;
        }
        if (function_exists( 'zend_shm_cache_fetch' )) {
            return 2;
        }
        return 1;
    }

    /**
     * Additional sql: do not check collation for oxsysrequirements::$_aException columns
     *
     * @return string
     */
    protected function _getAdditionalCheck()
    {
        $sSelect = '';
        foreach ( $this->_aException as $sTable => $sColumn ) {
            $sSelect .= 'and ( t.TABLE_NAME != "'.$sTable.'" and c.COLUMN_NAME != "'.$sColumn.'" ) ';
        }
        return $sSelect;
    }

    /**
     * Checks tables and columns (oxsysrequirements::$_aColumns) collation
     *
     * @return array
     */
    public function checkCollation()
    {
        $myConfig = $this->getConfig();

        $aCollations = array();
        $sCollation = '';

        $sSelect = 'select t.TABLE_NAME, c.COLUMN_NAME, c.COLLATION_NAME from INFORMATION_SCHEMA.tables t ' .
                   'LEFT JOIN INFORMATION_SCHEMA.columns c ON t.TABLE_NAME = c.TABLE_NAME  ' .
                   'where t.TABLE_SCHEMA = "'.$myConfig->getConfigParam( 'dbName' ).'" ' .
                   'and c.TABLE_SCHEMA = "'.$myConfig->getConfigParam( 'dbName' ).'" ' .
                   'and c.COLUMN_NAME in ("'.implode('", "', $this->_aColumns).'") ' . $this->_getAdditionalCheck() .
                   ' ORDER BY (t.TABLE_NAME = "oxarticles") DESC';
        $aRez = oxDb::getDb()->getAll($sSelect);
        foreach ( $aRez as $aRetTable ) {
            if ( !$sCollation ) {
                $sCollation = $aRetTable[2];
            } else {
                if ( $aRetTable[2] && $sCollation != $aRetTable[2]) {
                    $aCollations[$aRetTable[0]][$aRetTable[1]] = $aRetTable[2];
                }
            }
        }

        if ( $this->_blSysReqStatus === null ) {
            $this->_blSysReqStatus = true;
        }
        if ( count($aCollations) > 0 ) {
            $this->_blSysReqStatus = false;
        }
        return $aCollations;
    }

    /**
     * Checks if database cluster is installed
     *
     * @return integer
     */
    public function checkDatabaseCluster()
    {
        return 2;
    }

    /**
     * Checks if PCRE unicode support is turned off/on. Should be on.
     *
     * @return integer
     */
    public function checkUnicodeSupport()
    {
        return (@preg_match('/\pL/u', 'a') == 1) ? 2 : 1;
    }

    /**
     * Checks if php_admin_flag file_uploads is ON
     *
     * @return integer
     */
    public function checkFileUploads()
    {
        $dUploadFile = -1;
        $sFileUploads = @ini_get('file_uploads');
        if ( $sFileUploads !== false ) {
            if ( $sFileUploads && ( $sFileUploads == '1' || strtolower($sFileUploads) == 'on') ) {
                $dUploadFile = 2;
            } else {
                $dUploadFile = 1;
            }
        }
        return $dUploadFile;
    }

    /**
     * Checks system requirements status
     *
     * @return bool
     */
    public function getSysReqStatus()
    {
        if ( $this->_blSysReqStatus == null ) {
            $this->_blSysReqStatus = true;
            $this->getSystemInfo();
            $this->checkCollation();
        }
        return $this->_blSysReqStatus;
    }

    /**
     * Runs through modules array and checks if current system fits requirements.
     * Returns array with module info:
     *   array( $sGroup, $sModuleName, $sModuleState ):
     *     $sGroup       - group of module
     *     $sModuleName  - name of checked module
     *     $sModuleState - module state:
     *       -1 - unable to datect, should not block
     *        0 - missing, blocks setup
     *        1 - fits min requirements
     *        2 - exists required or better
     *
     * @return array $aSysInfo
     */
    public function getSystemInfo()
    {
        $aSysInfo = array();
        $aRequiredModules = $this->getRequiredModules();
        $this->_blSysReqStatus = true;
        foreach ( $aRequiredModules as $sModule => $sGroup ) {
            if ( isset($aSysInfo[$sGroup]) && !$aSysInfo[$sGroup] ) {
                $aSysInfo[$sGroup] = array();
            }
            $iModuleState = $this->getModuleInfo( $sModule );
            $aSysInfo[$sGroup][$sModule] = $iModuleState;
            $this->_blSysReqStatus = $this->_blSysReqStatus && ( bool ) abs( $iModuleState );
        }
        return $aSysInfo;
    }

    /**
     * Returns passed module state
     *
     * @param string $sModule module name to check
     *
     * @return integer $iModStat
     */
    public function getModuleInfo( $sModule = null )
    {
        if ( $sModule ) {
            $iModStat = null;
            $sCheckFunction = "check".str_replace(" ", "", ucwords(str_replace("_", " ", $sModule)));
            $iModStat = $this->$sCheckFunction();

            return $iModStat;
        }
    }

    /**
     * Returns or prints url for info about missing web service configuration
     *
     * @param string $sIdent Module identifier
     *
     * @return mixed
     */
    public function getReqInfoUrl( $sIdent)
    {
        $sUrl = $this->_sReqInfoUrl;
        $aInfoMap = $this->_aInfoMap;

        // only known will be anchored
        if ( isset( $aInfoMap[$sIdent] ) ) {
            $sUrl .= "#".$aInfoMap[$sIdent];
        }

        return $sUrl;
    }

    /**
     * Parses and calculates given string form byte syze value
     *
     * @param string $sBytes string form byte value (64M, 32K etc)
     *
     * @return int
     */
    protected function _getBytes( $sBytes )
    {
        $sBytes = trim( $sBytes );
        $sLast = strtolower($sBytes[strlen($sBytes)-1]);
        switch( $sLast ) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $sBytes *= 1024;
            case 'm':
                $sBytes *= 1024;
            case 'k':
                $sBytes *= 1024;
                break;
        }

        return $sBytes;
    }

    /**
     * check if given template contains the given block
     *
     * @param string $sTemplate  template file name
     * @param string $sBlockName block name
     *
     * @see getMissingTemplateBlocks
     *
     * @return bool
     */
    protected function _checkTemplateBlock($sTemplate, $sBlockName)
    {
        $sTplFile = $this->getConfig()->getTemplatePath($sTemplate, false);
        if (!$sTplFile || !file_exists($sTplFile)) {
            // check if file is in admin theme
            $sTplFile = $this->getConfig()->getTemplatePath($sTemplate, true);
            if (!$sTplFile || !file_exists($sTplFile)) {
                return false;
            }
        }

        $sFile = file_get_contents($sTplFile);
        $sBlockNameQuoted = preg_quote($sBlockName, '/');
        return (bool)preg_match('/\[\{\s*block\s+name\s*=\s*([\'"])'.$sBlockNameQuoted.'\1\s*\}\]/is', $sFile);
    }

    /**
     * returns array of missing template block files:
     *  1. checks db for registered blocks
     *  2. checks each block if it exists in currently used theme templates
     * returned array components are of form array(module name, block name, template file)
     * only active (oxactive==1) blocks are checked
     *
     * @return array
     */
    public function getMissingTemplateBlocks()
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $aCache = array();
        $oConfig = $this->getConfig();

        $sShpIdParam = $oDb->quote($oConfig->getShopId());
        $sSql = "select * from oxtplblocks where oxactive=1 and oxshopid=$sShpIdParam";
        $rs = $oDb->execute($sSql);


        $aRet = array();
        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                $blStatus = false;
                if (isset($aCache[$rs->fields['OXTEMPLATE']]) && isset($aCache[$rs->fields['OXTEMPLATE']][$rs->fields['OXBLOCKNAME']])) {
                    $blStatus = $aCache[$rs->fields['OXTEMPLATE']][$rs->fields['OXBLOCKNAME']];
                } else {
                    $blStatus = $this->_checkTemplateBlock($rs->fields['OXTEMPLATE'], $rs->fields['OXBLOCKNAME']);
                    $aCache[$rs->fields['OXTEMPLATE']][$rs->fields['OXBLOCKNAME']] = $blStatus;
                }

                if (!$blStatus) {
                    $aRet[] = array(
                                'module'   => $rs->fields['OXMODULE'],
                                'block'    => $rs->fields['OXBLOCKNAME'],
                                'template' => $rs->fields['OXTEMPLATE'],
                            );
                }
                $rs->moveNext();
            }
        }

        return $aRet;
    }

    /**
     * Check if correct AutoStart setting.
     *
     * @return bool
     */
    public function checkSessionAutostart()
    {
        $sStatus = ( strtolower( (string) @ini_get( 'session.auto_start' ) ) );
        return in_array( $sStatus, array( 'on', '1' ) ) ? 0 : 2;
    }
}
