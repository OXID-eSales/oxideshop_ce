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
 * Includes Smarty engine class.
 */
require_once getShopBasePath()."core/smarty/Smarty.class.php";

/**
 * General utils class
 *
 */
class oxUtils extends oxSuperCfg
{
    /**
     * Cached currency precision
     *
     * @var int
     */
    protected $_iCurPrecision = null;

    /**
     * Some files, like object structure should not be deleted, because they are changed rarely
     * and each regeneration eats additional page load time. This array keeps patterns of file
     * names which should not be deleted on regular cache cleanup
     *
     * @var string
     */
    protected $_sPermanentCachePattern = "/c_fieldnames_|c_tbdsc_|_allfields_/";

    /**
     * Pattern used to filter needed to remove language cache files.
     *
     * @var string
     */
    protected $_sLanguageCachePattern = "/c_langcache_/i";

    /**
     * Pattern used to filter needed to remove admin menu cache files.
     *
     * @var string
     */
    protected $_sMenuCachePattern = "/c_menu_/i";

    /**
     * File cache contents.
     *
     * @var array
     */
    protected $_aLockedFileHandles = array();

    /**
     * Local cache
     *
     * @var array
     */
    protected $_aFileCacheContents = array();

    /**
     * Search engine indicator
     *
     * @var bool
     */
    protected $_blIsSe = null;

    /**
     * Return a single instance of this class
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::getUtils() instead.
     *
     * @return oxUtils
     */
    public static function getInstance()
    {
        return oxRegistry::getUtils();
    }

    /**
     * Statically cached data
     *
     * @var array
     */
    protected $_aStaticCache;

    /**
     * Seo mode marker - SEO is active or not
     *
     * @var bool
     */
    protected $_blSeoIsActive = null;

    /**
     * Strips magic quotes
     *
     * @return null
     */
    public function stripGpcMagicQuotes()
    {
        if (!get_magic_quotes_gpc()) {
            return;
        }
        $_REQUEST = self::_stripQuotes($_REQUEST);
        $_POST = self::_stripQuotes($_POST);
        $_GET = self::_stripQuotes($_GET);
        $_COOKIE = self::_stripQuotes($_COOKIE);
    }

    /**
     * OXID specific string manipulation method
     *
     * @param string $sVal string
     * @param string $sKey key
     *
     * @return string
     */
    public function strMan( $sVal, $sKey = null )
    {
        $sKey = $sKey ? $sKey : $this->getConfig()->getConfigParam('sConfigKey');
        $sVal = "ox{$sVal}id";

        $sKey = str_repeat( $sKey, strlen( $sVal ) / strlen( $sKey ) + 5 );
        $sVal = $this->strRot13( $sVal );
        $sVal = $sVal ^ $sKey;
        $sVal = base64_encode ( $sVal );
        $sVal = str_replace( "=", "!", $sVal );

        return "ox_$sVal";
    }

    /**
     * OXID specific string manipulation method
     *
     * @param string $sVal string
     * @param string $sKey key
     *
     * @return string
     */
    public function strRem( $sVal, $sKey = null )
    {
        $sKey = $sKey ? $sKey : $this->getConfig()->getConfigParam('sConfigKey');
        $sKey = str_repeat( $sKey, strlen( $sVal ) / strlen( $sKey ) + 5 );

        $sVal = substr( $sVal, 3 );
        $sVal = str_replace( '!', '=', $sVal );
        $sVal = base64_decode( $sVal );
        $sVal = $sVal ^ $sKey;
        $sVal = $this->strRot13( $sVal );

        return substr( $sVal, 2, -2 );
    }

    /**
     * Returns string witch "." symbols were replaced with "__".
     *
     * @param string $sName String to search replaceable char
     *
     * @return string
     */
    public function getArrFldName( $sName )
    {
        return str_replace( ".", "__", $sName);
    }

    /**
     * Takes a string and assign all values, returns array with values.
     *
     * @param string $sIn  Initial string
     * @param float  $dVat Article VAT (optional)
     *
     * @return array
     */
    public function assignValuesFromText( $sIn, $dVat = null )
    {
        $aRet = array();
        $aPieces = explode( '@@', $sIn );
        while ( list( $sKey, $sVal ) = each( $aPieces ) ) {
            if ( $sVal ) {
                $aName = explode( '__', $sVal );
                if ( isset( $aName[0] ) && isset( $aName[1] ) ) {
                    $aRet[] = $this->_fillExplodeArray( $aName, $dVat );
                }
            }
        }
        return $aRet;
    }

    /**
     * Takes an array and builds again a string. Returns string with values.
     *
     * @param array $aIn Initial array of strings
     *
     * @return string
     */
    public function assignValuesToText( $aIn)
    {
        $sRet = "";
        reset( $aIn );
        while (list($sKey, $sVal) = each($aIn)) {
            $sRet .= $sKey;
            $sRet .= "__";
            $sRet .= $sVal;
            $sRet .= "@@";
        }
        return $sRet;
    }

    /**
     * Returns formatted currency string, according to formatting standards.
     *
     * @param string $sValue Formatted price
     *
     * @return float
     */
    public function currency2Float( $sValue)
    {
        $fRet = $sValue;
        $iPos = strrpos( $sValue, ".");
        if ($iPos && ((strlen($sValue)-1-$iPos) < 2+1)) {
            // replace decimal with ","
            $fRet = substr_replace( $fRet, ",", $iPos, 1);
        }
        // remove thousands
        $fRet = str_replace( array(" ","."), "", $fRet);

        $fRet = str_replace( ",", ".", $fRet);
        return (float) $fRet;
    }

    /**
     * Returns formatted float, according to formatting standards.
     *
     * @param string $sValue Formatted price
     *
     * @return float
     */
    public function string2Float( $sValue)
    {
        $fRet = str_replace( " ", "", $sValue);
        $iCommaPos = strpos( $fRet, ",");
        $iDotPos = strpos( $fRet, ".");
        if (!$iDotPos xor !$iCommaPos) {
            if (substr_count( $fRet, ",") > 1 || substr_count( $fRet, ".") > 1) {
                $fRet = str_replace( array(",","."), "", $fRet);
            } else {
                $fRet = str_replace( ",", ".", $fRet);
            }
        } else if ( $iDotPos < $iCommaPos ) {
            $fRet = str_replace( ".", "", $fRet);
            $fRet = str_replace( ",", ".", $fRet);
        }
        // remove thousands
        $fRet = str_replace( array(" ",","), "", $fRet);
        return (float) $fRet;
    }

    /**
     * Checks if current web client is Search Engine. Returns true on success.
     *
     * @param string $sClient user browser agent
     *
     * @return bool
     */
    public function isSearchEngine( $sClient = null )
    {
        if (is_null($this->_blIsSe)) {
            $this->setSearchEngine( null, $sClient );
        }
        return $this->_blIsSe;
    }

    /**
     * Sets if current web client is Search Engine.
     *
     * @param bool   $blIsSe  sets if Search Engine is on
     * @param string $sClient user browser agent
     *
     * @return null
     */
    public function setSearchEngine( $blIsSe = null, $sClient = null )
    {
        if (isset($blIsSe)) {
            $this->_blIsSe = $blIsSe;
            return;
        }
        startProfile("isSearchEngine");

        $myConfig = $this->getConfig();
        $blIsSe   = false;

        if ( !( $myConfig->getConfigParam( 'iDebug' ) && $this->isAdmin() ) ) {
            $aRobots = $myConfig->getConfigParam( 'aRobots' );
            $aRobots = is_array( $aRobots )?$aRobots:array();

            $aRobotsExcept = $myConfig->getConfigParam( 'aRobotsExcept' );
            $aRobotsExcept = is_array( $aRobotsExcept )?$aRobotsExcept:array();

            $sClient = $sClient?$sClient:strtolower( getenv( 'HTTP_USER_AGENT' ) );
            $blIsSe  = false;
            $aRobots = array_merge( $aRobots, $aRobotsExcept );
            foreach ( $aRobots as $sRobot ) {
                if ( strpos( $sClient, $sRobot ) !== false ) {
                    $blIsSe = true;
                    break;
                }
            }
        }

        $this->_blIsSe = $blIsSe;

        stopProfile("isSearchEngine");
    }

    /**
     * User email validation function. Returns true if email is OK otherwise - false;
     * Syntax validation is performed only.
     *
     * @param string $sEmail user email
     *
     * @return bool
     */
    public function isValidEmail( $sEmail )
    {
        $blValid = true;
        if ( $sEmail != 'admin' ) {
            $sEmailTpl = "/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/i";
            $blValid = ( getStr()->preg_match( $sEmailTpl, $sEmail ) != 0 );
        }

        return $blValid;
    }

    /**
     * Parses profile configuration, loads stored info in cookie
     *
     * @param array $aInterfaceProfiles ($myConfig->getConfigParam( 'aInterfaceProfiles' ))
     *
     * @return null
     */
    public function loadAdminProfile($aInterfaceProfiles)
    {
        // improved #533
        // checking for available profiles list
        if ( is_array( $aInterfaceProfiles ) ) {
            //checking for previous profiles
            $sPrevProfile = oxRegistry::get("oxUtilsServer")->getOxCookie('oxidadminprofile');
            if (isset($sPrevProfile)) {
                $aPrevProfile = @explode("@", trim($sPrevProfile));
            }

            //array to store profiles
            $aProfiles = array();
            foreach ( $aInterfaceProfiles as $iPos => $sProfile) {
                $aProfileSettings = array($iPos, $sProfile);
                $aProfiles[] = $aProfileSettings;
            }
            // setting previous used profile as active
            if (isset($aPrevProfile[0]) && isset($aProfiles[$aPrevProfile[0]])) {
                $aProfiles[$aPrevProfile[0]][2] = 1;
            }

            oxSession::setVar("aAdminProfiles", $aProfiles);
            return $aProfiles;
        }
        return null;
    }

    /**
     * Rounds the value to currency cents. This method does NOT format the number.
     *
     * @param string $sVal the value that should be rounded
     * @param object $oCur Currency Object
     *
     * @return float
     */
    public function fRound($sVal, $oCur = null)
    {
        startProfile('fround');

        //cached currency precision, this saves about 1% of execution time
        $iCurPrecision = null;
        if (! defined('OXID_PHP_UNIT')) {
            $iCurPrecision = $this->_iCurPrecision;
        }

        if (is_null($iCurPrecision)) {
            if ( !$oCur ) {
                $oCur = $this->getConfig()->getActShopCurrencyObject();
            }

            $iCurPrecision = $oCur->decimal;
            $this->_iCurPrecision = $iCurPrecision;
        }

        // if < 5.3.x this is a workaround for #36008 bug in php - incorrect round() & number_format() result (R)
        static $dprez = null;
        if (!$dprez) {
            $prez = @ini_get("precision");
            if (!$prez || $prez > 12 ) {
               $prez = 12;
            }
            $dprez = pow(10, -$prez);
        }
        stopProfile('fround');
        return round($sVal + $dprez * ( $sVal >= 0 ? 1 : -1 ), $iCurPrecision);
    }

    /**
     * Stores something into static cache to avoid double loading
     *
     * @param string $sName    name of the content
     * @param mixed  $sContent the content
     * @param string $sKey     optional key, where to store the content
     *
     * @return null
     */
    public function toStaticCache( $sName, $sContent, $sKey = null )
    {
        // if it's an array then we add
        if ( $sKey ) {
            $this->_aStaticCache[$sName][$sKey] = $sContent;
        } else {
            $this->_aStaticCache[$sName] = $sContent;
        }
    }

    /**
     * Retrieves something from static cache
     *
     * @param string $sName name under which the content is stored in the static cache
     *
     * @return mixed
     */
    public function fromStaticCache( $sName)
    {
        if ( isset( $this->_aStaticCache[$sName])) {
            return $this->_aStaticCache[$sName];
        }
        return null;
    }

    /**
     * Cleans all or specific data from static cache
     *
     * @param string $sCacheName Cache name
     *
     * @return null
     */
    public function cleanStaticCache($sCacheName = null)
    {
        if ($sCacheName) {
            unset($this->_aStaticCache[$sCacheName]);
        } else {
            $this->_aStaticCache = null;
        }
    }

    /**
     * Generates php file, which could later be loaded as include instead of parsed data.
     * Currently this method supports simple arrays only.
     *
     * @param string $sKey      Cache key
     * @param mixed  $mContents Cache contents. At this moment only simple array type is supported.
     *
     * @return null;
     */
    public function toPhpFileCache( $sKey, $mContents )
    {
        //only simple arrays are supported
        if ( is_array( $mContents ) && ( $sCachePath = $this->getCacheFilePath( $sKey, false, 'php' ) ) ) {

            // setting meta
            $this->setCacheMeta( $sKey, array( "serialize" => false, "cachepath" => $sCachePath ) );

            // caching..
            $this->toFileCache( $sKey, $mContents );
        }
    }

    /**
     * Includes cached php file and loads stored contents.
     *
     * @param string $sKey Cache key.
     *
     * @return null;
     */
    public function fromPhpFileCache( $sKey )
    {
        // setting meta
        $this->setCacheMeta( $sKey, array( "include" => true, "cachepath" => $this->getCacheFilePath( $sKey, false, 'php' ) ) );
        return $this->fromFileCache( $sKey );
    }

    /**
     * If available returns cache meta data array
     *
     * @param string $sKey meta data/cache key
     *
     * @return mixed
     */
    public function getCacheMeta( $sKey )
    {
        return isset( $this->_aFileCacheMeta[$sKey] ) ? $this->_aFileCacheMeta[$sKey] : false;
    }

    /**
     * Saves cache meta data (information)
     *
     * @param string $sKey  meta data/cache key
     * @param array  $aMeta meta data array
     *
     * @return null
     */
    public function setCacheMeta( $sKey, $aMeta )
    {
        // cache meta data
        $this->_aFileCacheMeta[$sKey] = $aMeta;
    }

    /**
     * Adds contents to cache contents by given key. Returns true on success.
     * All file caches are supposed to be written once by commitFileCache() method.
     *
     * @param string $sKey      Cache key
     * @param mixed  $mContents Contents to cache
     * @param int    $iTtl      Time to live in seconds (0 for forever).
     *
     * @return bool
     */
    public function toFileCache( $sKey, $mContents, $iTtl = 0 )
    {
        $aCacheData['content'] = $mContents;
        $aMeta = $this->getCacheMeta( $sKey );
        if ( $iTtl ) {
            $aCacheData['ttl'] = $iTtl;
            $aCacheData['timestamp'] = oxRegistry::get("oxUtilsDate")->getTime();
        }
        $this->_aFileCacheContents[$sKey] = $aCacheData;

        // looking for cache meta
        $sCachePath = isset( $aMeta["cachepath"] ) ? $aMeta["cachepath"] : $this->getCacheFilePath( $sKey );
        return ( bool ) $this->_lockFile( $sCachePath, $sKey );
    }

    /**
     * Fetches contents from file cache.
     *
     * @param string $sKey Cache key
     *
     * @return mixed
     */
    public function fromFileCache( $sKey )
    {
        if ( !array_key_exists( $sKey, $this->_aFileCacheContents ) ) {
            $sRes = null;

            $aMeta = $this->getCacheMeta( $sKey );
            $blInclude  = isset( $aMeta["include"] ) ? $aMeta["include"] : false;
            $sCachePath = isset( $aMeta["cachepath"] ) ? $aMeta["cachepath"] : $this->getCacheFilePath( $sKey );

            // trying to lock
            $this->_lockFile( $sCachePath, $sKey, LOCK_SH );

            clearstatcache();
            if ( is_readable( $sCachePath ) ) {
                $sRes = $blInclude ? $this->_includeFile( $sCachePath ) : $this->_readFile( $sCachePath );
            }

            if ( isset( $sRes['ttl'] ) && $sRes['ttl'] != 0 ) {
                $iTimestamp = $sRes['timestamp'];
                $iTtl = $sRes['ttl'];

                $iTime = oxRegistry::get("oxUtilsDate")->getTime();
                if ( $iTime > $iTimestamp + $iTtl ) {
                    return null;
                }
            }
            // release lock
            $this->_releaseFile( $sKey, LOCK_SH );

            // caching
            $this->_aFileCacheContents[$sKey] = $sRes;
        }

        return $this->_aFileCacheContents[$sKey]['content'];
    }

    /**
     * Reads and returns cache file contents
     *
     * @param string $sFilePath cache fiel path
     *
     * @return string
     */
    protected function _readFile( $sFilePath )
    {
        $sRes = file_get_contents( $sFilePath );
        return $sRes ? unserialize( $sRes ) : null;
    }

    /**
     * Includes cache file
     *
     * @param string $sFilePath cache file path
     *
     * @return mixed
     */
    protected function _includeFile( $sFilePath )
    {
        $_aCacheContents = null;
        include $sFilePath;
        return $_aCacheContents;
    }

    /**
     * Serializes or writes php array for class file cache
     *
     * @param string $sKey      cache key
     * @param mixed  $mContents cache data
     *
     * @return mixed
     */
    protected function _processCache( $sKey, $mContents )
    {
        // looking for cache meta
        $aCacheMeta  = $this->getCacheMeta( $sKey );
        $blSerialize = isset( $aCacheMeta["serialize"] ) ? $aCacheMeta["serialize"] : true;

        if ( $blSerialize ) {
            $mContents = serialize( $mContents );
        } else {
            $mContents = "<?php\n//automatically generated file\n//" . date( "Y-m-d H:i:s" ) . "\n\n\$_aCacheContents = " . var_export( $mContents, true ) . "\n?>";
        }

        return $mContents;
    }

    /**
     * Writes all cache contents to file at once. This method was introduced due to possible
     * race conditions. Cache is cleaned up after commit
     *
     * @return null;
     */
    public function commitFileCache()
    {
        if ( count( $this->_aLockedFileHandles[LOCK_EX] ) ) {
            startProfile("!__SAVING CACHE__! (warning)");
            foreach ( $this->_aLockedFileHandles[LOCK_EX] as $sKey => $rHandle ) {
                if ( $rHandle !== false && isset( $this->_aFileCacheContents[$sKey] ) ) {

                    // #0002931A truncate file once more before writing
                    ftruncate( $rHandle, 0 );

                    // writing cache
                    fwrite( $rHandle, $this->_processCache( $sKey, $this->_aFileCacheContents[$sKey] ) );

                    // releasing locks
                    $this->_releaseFile( $sKey );
                }
            }

            stopProfile("!__SAVING CACHE__! (warning)");

            //empty buffer
            $this->_aFileCacheContents = array();
        }
    }

    /**
     * Locks cache file and returns its handle on success or false on failure
     *
     * @param string $sFilePath name of file to lock
     * @param string $sIdent    lock identifier
     * @param int    $iLockMode lock mode - LOCK_EX/LOCK_SH
     *
     * @return mixed lock file resource or false on error
     */
    protected function _lockFile( $sFilePath, $sIdent, $iLockMode = LOCK_EX )
    {
        $rHandle = isset( $this->_aLockedFileHandles[$iLockMode][$sIdent] ) ? $this->_aLockedFileHandles[$iLockMode][$sIdent] : null;
        if ( $rHandle === null ) {

            $blLocked = false;
            $rHandle = @fopen( $sFilePath, "a+" );

            if ( $rHandle !== false ) {

                if ( flock( $rHandle, $iLockMode | LOCK_NB ) ) {
                    if ( $iLockMode === LOCK_EX ) {
                        // truncate file
                        $blLocked = ftruncate( $rHandle, 0 );
                    } else {
                        // move to a start position
                        $blLocked = fseek( $rHandle, 0 ) === 0;
                    }
                }

                // on failure - closing and setting false..
                if ( !$blLocked ) {
                    fclose( $rHandle );
                    $rHandle = false;
                }
            }

            // in case system does not support file locking
            if ( !$blLocked && $iLockMode === LOCK_EX ) {

                // clearing on first call
                if ( count( $this->_aLockedFileHandles ) == 0 ) {
                    clearstatcache();
                }

                // start a blank file to inform other processes we are dealing with it.
                if (!( file_exists( $sFilePath ) && !filesize( $sFilePath ) && abs( time() - filectime( $sFilePath ) < 40 ) ) ) {
                    $rHandle = @fopen( $sFilePath, "w" );
                }
            }

            $this->_aLockedFileHandles[$iLockMode][$sIdent] = $rHandle;
        }

        return $rHandle;
    }

    /**
     * Releases file lock and returns release state
     *
     * @param string $sIdent    lock ident
     * @param int    $iLockMode lock mode
     *
     * @return bool
     */
    protected function _releaseFile( $sIdent, $iLockMode = LOCK_EX )
    {
        $blSuccess = true;
        if ( isset( $this->_aLockedFileHandles[$iLockMode][$sIdent] ) &&
             $this->_aLockedFileHandles[$iLockMode][$sIdent] !== false ) {

             // release the lock and close file
            $blSuccess = flock( $this->_aLockedFileHandles[$iLockMode][$sIdent], LOCK_UN ) &&
                         fclose( $this->_aLockedFileHandles[$iLockMode][$sIdent] );
            unset( $this->_aLockedFileHandles[$iLockMode][$sIdent] );
        }

        return $blSuccess;
    }

    /**
     * Removes most files stored in cache (default 'tmp') folder. Some files
     * e.g. table files names description, are left. Excluded cache file name
     * patterns are defined in oxUtils::_sPermanentCachePattern parameter
     *
     * @return null
     */
    public function oxResetFileCache()
    {
        $aFiles = glob( $this->getCacheFilePath( null, true ) . '*' );
        if ( is_array( $aFiles ) ) {
            // delete all the files, except cached tables field names
            $aFiles = preg_grep( $this->_sPermanentCachePattern, $aFiles, PREG_GREP_INVERT );
            foreach ( $aFiles as $sFile ) {
                @unlink( $sFile );
            }
        }
    }

    /**
     * Removes smarty template cache for given templates
     *
     * @param array $aTemplates Template name array
     *
     * @return null
     */
    public function resetTemplateCache($aTemplates)
    {
        $sSmartyDir = oxRegistry::get("oxUtilsView")->getSmartyDir();
        //$aFiles = glob( $this->getCacheFilePath( null, true ) . '*' );
        $aFiles = glob( $sSmartyDir . '*' );

        if ( is_array( $aFiles ) && is_array( $aTemplates ) && count($aTemplates) ) {
            // delete all template cache files
            foreach ($aTemplates as &$sTemplate) {
                $sTemplate = preg_quote(basename(strtolower($sTemplate), '.tpl'));
            }

            $sPattern = sprintf("/%%(%s)\.tpl\.php$/i", implode('|', $aTemplates));
            $aFiles = preg_grep( $sPattern, $aFiles );

            if (is_array( $aFiles ) ) {
                foreach ( $aFiles as $sFile ) {
                    @unlink( $sFile );
                }
            }
        }

    }

    /**
     * Removes language constant cache
     *
     * @return null
     */
    public function resetLanguageCache()
    {
        $aFiles = glob( $this->getCacheFilePath( null, true ) . '*' );
        if ( is_array( $aFiles ) ) {
            // delete all language cache files
            $sPattern = $this->_sLanguageCachePattern;
            $aFiles = preg_grep( $sPattern, $aFiles );
            foreach ( $aFiles as $sFile ) {
                @unlink( $sFile );
            }
        }
    }

    /**
     * Removes admin menu cache
     *
     * @return null
     */
    public function resetMenuCache()
    {
        $aFiles = glob( $this->getCacheFilePath( null, true ) . '*' );
        if ( is_array( $aFiles ) ) {
            // delete all menu cache files
            $sPattern = $this->_sMenuCachePattern;
            $aFiles = preg_grep( $sPattern, $aFiles );
            foreach ( $aFiles as $sFile ) {
                @unlink( $sFile );
            }
        }
    }

    /**
     * If $sLocal file is older than 24h or does not exist, tries to
     * download it from $sRemote and save it as $sLocal
     *
     * @param string $sRemote the file
     * @param string $sLocal  the address of the remote source
     *
     * @return mixed
     */
    public function getRemoteCachePath($sRemote, $sLocal)
    {
        clearstatcache();
        if ( file_exists( $sLocal ) && filemtime( $sLocal ) && filemtime( $sLocal ) > time() - 86400 ) {
            return $sLocal;
        }
        $hRemote = @fopen( $sRemote, "rb");
        $blSuccess = false;
        if ( isset( $hRemote) && $hRemote ) {
            $hLocal = fopen( $sLocal, "wb");
            stream_copy_to_stream($hRemote, $hLocal);
            fclose($hRemote);
            fclose($hLocal);
            $blSuccess = true;
        } else {
            // try via fsockopen
            $aUrl = @parse_url( $sRemote);
            if ( !empty( $aUrl["host"])) {
                $sPath = $aUrl["path"];
                if ( empty( $sPath ) ) {
                    $sPath = "/";
                }
                $sHost = $aUrl["host"];

                $hSocket = @fsockopen( $sHost, 80, $iErrorNumber, $iErrStr, 5);
                if ( $hSocket) {
                    fputs( $hSocket, "GET ".$sPath." HTTP/1.0\r\nHost: $sHost\r\n\r\n");
                    $headers = stream_get_line($hSocket, 4096, "\r\n\r\n");
                    if ( ( $hLocal = @fopen( $sLocal, "wb") ) !== false ) {
                        rewind($hLocal);
                        // does not copy all the data
                        // stream_copy_to_stream($hSocket, $hLocal);
                        fwrite ( $hLocal, stream_get_contents( $hSocket ) );
                        fclose( $hLocal );
                        fclose( $hSocket );
                        $blSuccess = true;
                    }
                }
            }
        }
        if ( $blSuccess || file_exists( $sLocal ) ) {
            return $sLocal;
        }
        return false;
    }

    /**
     * Checks if preview mode is ON
     *
     * @return bool
     */
    public function canPreview()
    {
        $blCan = null;
        if ( ( $sPrevId = oxConfig::getParameter( 'preview' ) ) &&
             ( $sAdminSid = oxRegistry::get("oxUtilsServer")->getOxCookie( 'admin_sid' ) ) ) {

            $sTable = getViewName( 'oxuser' );
            $oDb = oxDb::getDb();
            $sQ = "select 1 from $sTable where MD5( CONCAT( ".$oDb->quote($sAdminSid).", {$sTable}.oxid, {$sTable}.oxpassword, {$sTable}.oxrights ) ) = ".oxDb::getDb()->quote($sPrevId);
            $blCan = (bool) $oDb->getOne( $sQ );
        }

        return $blCan;
    }

    /**
     * Returns id which is used for product preview in shop during administration
     *
     * @return string
     */
    public function getPreviewId()
    {
        $sAdminSid = oxRegistry::get("oxUtilsServer")->getOxCookie( 'admin_sid' );
        if ( ( $oUser = $this->getUser() ) ) {
            return md5( $sAdminSid . $oUser->getId() . $oUser->oxuser__oxpassword->value . $oUser->oxuser__oxrights->value );
        }
    }

    /**
     * This function checks if logged in user has access to admin or not
     *
     * @return bool
     */
    public function checkAccessRights()
    {
        $myConfig  = $this->getConfig();

        $blIsAuth = false;

        $sUserID = oxSession::getVar( "auth");

        // deleting admin marker
        oxSession::setVar( "malladmin", 0);
        oxSession::setVar( "blIsAdmin", 0);
        oxSession::deleteVar( "blIsAdmin" );
        $myConfig->setConfigParam( 'blMallAdmin', false );
        //#1552T
        $myConfig->setConfigParam( 'blAllowInheritedEdit', false );

        if ( $sUserID) {
            // escaping
            $oDb = oxDb::getDb();
            $sRights = $oDb->getOne("select oxrights from oxuser where oxid = ".$oDb->quote($sUserID));

            if ( $sRights != "user") {
                // malladmin ?
                if ( $sRights == "malladmin") {
                    oxSession::setVar( "malladmin", 1);
                    $myConfig->setConfigParam( 'blMallAdmin', true );

                    //#1552T
                    //So far this blAllowSharedEdit is Equal to blMallAdmin but in future to be solved over rights and roles
                    $myConfig->setConfigParam( 'blAllowSharedEdit', true );

                    $sShop = oxSession::getVar( "actshop");
                    if ( !isset($sShop)) {
                        oxSession::setVar( "actshop", $myConfig->getBaseShopId());
                    }
                    $blIsAuth = true;
                } else {
                    // Shopadmin... check if this shop is valid and exists
                    $sShopID = $oDb->getOne("select oxid from oxshops where oxid = " . $oDb->quote( $sRights ) );
                    if ( isset( $sShopID) && $sShopID) {
                        // success, this shop exists

                        oxSession::setVar( "actshop", $sRights);
                        oxSession::setVar( "currentadminshop", $sRights);
                        oxSession::setVar( "shp", $sRights);

                        // check if this subshop admin is evil.
                        if ('chshp' == oxConfig::getParameter( 'fnc' )) {
                            // dont allow this call
                            $blIsAuth = false;
                        } else {
                            $blIsAuth = true;

                            $aShopIdVars = array('actshop', 'shp', 'currentadminshop');
                            foreach ($aShopIdVars as $sShopIdVar) {
                                if ($sGotShop = oxConfig::getParameter( $sShopIdVar )) {
                                    if ($sGotShop != $sRights) {
                                        $blIsAuth = false;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                // marking user as admin
                oxSession::setVar( "blIsAdmin", 1);
            }
        }
        return $blIsAuth;
    }

    /**
     * Checks if Seo mode should be used
     *
     * @param bool   $blReset  used to reset cached SEO mode
     * @param string $sShopId  shop id (optional; if not passed active session shop id will be used)
     * @param int    $iActLang language id (optional; if not passed active session language will be used)
     *
     * @return bool
     */
    public function seoIsActive( $blReset = false, $sShopId = null, $iActLang = null )
    {
        if ( !is_null( $this->_blSeoIsActive ) && !$blReset ) {
            return $this->_blSeoIsActive;
        }

        $myConfig = $this->getConfig();

        if ( ( $this->_blSeoIsActive = $myConfig->getConfigParam( 'blSeoMode' ) ) === null ) {
            $this->_blSeoIsActive = true;

            $aSeoModes  = $myConfig->getconfigParam( 'aSeoModes' );
            $sActShopId = $sShopId ? $sShopId : $myConfig->getActiveShop()->getId();
            $iActLang   = $iActLang ? $iActLang : (int) oxRegistry::getLang()->getBaseLanguage();

            // checking special config param for active shop and language
            if ( is_array( $aSeoModes ) && isset( $aSeoModes[$sActShopId] ) && isset( $aSeoModes[$sActShopId][$iActLang] ) ) {
                $this->_blSeoIsActive = (bool) $aSeoModes[$sActShopId][$iActLang];
            }
        }

        return $this->_blSeoIsActive;
    }

    /**
     * Checks if string is only alpha numeric  symbols
     *
     * @param string $sField field name to test
     *
     * @return bool
     */
    public function isValidAlpha( $sField )
    {
        return (boolean) getStr()->preg_match( '/^[a-zA-Z0-9_]*$/', $sField );
    }

    /**
     * redirects browser to given url, nothing else done just header send
     * may be used for redirection in case of an exception or similar things
     *
     * @param string $sUrl        code to add to the header(e.g. "HTTP/1.1 301 Moved Permanently", or "HTTP/1.1 500 Internal Server Error"
     * @param string $sHeaderCode the URL to redirect to
     *
     * @return null
     */
    protected function _simpleRedirect( $sUrl, $sHeaderCode )
    {
        $oHeader = oxNew( "oxHeader" );
        $oHeader->setHeader( $sHeaderCode );
        $oHeader->setHeader( "Location: $sUrl" );
        $oHeader->setHeader( "Connection: close" );
        $oHeader->sendHeader();
    }

    /**
     * redirect user to the specified URL
     *
     * @param string $sUrl               URL to be redirected
     * @param bool   $blAddRedirectParam add "redirect" param
     * @param int    $iHeaderCode        header code, default 302
     *
     * @return null or exit
     */
    public function redirect( $sUrl, $blAddRedirectParam = true, $iHeaderCode = 302 )
    {
        //preventing possible cyclic redirection
        //#M341 and check only if redirect parameter must be added
        if ( $blAddRedirectParam && oxConfig::getParameter( 'redirected' ) ) {
            return;
        }

        if ( $blAddRedirectParam ) {
            $sUrl = $this->_addUrlParameters( $sUrl, array( 'redirected' => 1 ) );
        }

        $sUrl = str_ireplace( "&amp;", "&", $sUrl );

        $sHeaderCode = '';
        switch ($iHeaderCode) {
            case 301:
                $sHeaderCode = "HTTP/1.1 301 Moved Permanently";
                break;
            case 302:
            default:
                $sHeaderCode = "HTTP/1.1 302 Found";
        }

        $this->_simpleRedirect( $sUrl, $sHeaderCode );

        try {//may occur in case db is lost
            $this->getSession()->freeze();
        } catch( oxException $oEx ) {
            $oEx->debugOut();
            //do nothing else to make sure the redirect takes place
        }

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            return;
        }

        $this->showMessageAndExit( '' );
    }

    /**
     * shows given message and quits
     * message might be whole content like 404 page.
     *
     * @param string $sMsg message to show
     *
     * @return null dies
     */
    public function showMessageAndExit( $sMsg )
    {
        $this->getSession()->freeze();
        $this->commitFileCache();

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            return;
        }


        exit( $sMsg );
    }

    /**
     * set header sent to browser
     *
     * @param string $sHeader header to sent
     *
     * @return null
     */
    public function setHeader($sHeader)
    {
        header($sHeader);
    }

    /**
     * adds the given parameters at the end of the given url
     *
     * @param string $sUrl    a url
     * @param array  $aParams the params which will be added
     *
     * @return string
     */
    protected function _addUrlParameters( $sUrl, $aParams )
    {
        $sDelimiter = ( ( getStr()->strpos( $sUrl, '?' ) !== false ) )?'&':'?';
        foreach ( $aParams as $sName => $sVal ) {
            $sUrl = $sUrl . $sDelimiter . $sName . '=' . $sVal;
            $sDelimiter = '&';
        }

        return $sUrl;
    }

    /**
     * Fill array.
     *
     * @param array $aName Initial array of strings
     * @param float $dVat  Article VAT
     *
     * @return string
     *
     * @todo rename function more closely to actual purpose
     * @todo finish refactoring
     */
    protected function _fillExplodeArray( $aName, $dVat = null)
    {
        $myConfig = $this->getConfig();
        $oObject = new stdClass();
        $aPrice = explode( '!P!', $aName[0]);

        if ( ( $myConfig->getConfigParam( 'bl_perfLoadSelectLists' ) && $myConfig->getConfigParam( 'bl_perfUseSelectlistPrice' ) && isset( $aPrice[0] ) && isset( $aPrice[1] ) ) || $this->isAdmin() ) {

            // yes, price is there
            $oObject->price = isset( $aPrice[1] ) ? $aPrice[1] : 0;
            $aName[0] = isset( $aPrice[0] ) ? $aPrice[0] : '';

            $iPercPos = getStr()->strpos( $oObject->price, '%' );
            if ( $iPercPos !== false ) {
                $oObject->priceUnit = '%';
                $oObject->fprice = $oObject->price;
                $oObject->price  = substr( $oObject->price, 0, $iPercPos );
            } else {
                $oCur = $myConfig->getActShopCurrencyObject();
                $oObject->price = str_replace(',', '.', $oObject->price);
                $oObject->fprice = oxRegistry::getLang()->formatCurrency( $oObject->price  * $oCur->rate, $oCur);
                $oObject->priceUnit = 'abs';
            }

            // add price info into list
            if ( !$this->isAdmin() && $oObject->price != 0 ) {
                $aName[0] .= " ";

                $dPrice = $this->_preparePrice( $oObject->price, $dVat );

                if ( $oObject->price > 0 ) {
                    $aName[0] .= "+";
                }
                //V FS#2616
                if ( $dVat != null && $oObject->priceUnit == 'abs' ) {
                    $oPrice = oxNew('oxPrice');
                    $oPrice->setPrice($oObject->price, $dVat);
                    $aName[0] .= oxRegistry::getLang()->formatCurrency( $dPrice * $oCur->rate, $oCur);
                } else {
                    $aName[0] .= $oObject->fprice;
                }
                if ( $oObject->priceUnit == 'abs' ) {
                    $aName[0] .= " ".$oCur->sign;
                }
            }
        } elseif ( isset( $aPrice[0] ) && isset($aPrice[1] ) ) {
            // A. removing unused part of information
            $aName[0] = getStr()->preg_replace( "/!P!.*/", "", $aName[0] );
        }

        $oObject->name  = $aName[0];
        $oObject->value = $aName[1];
        return $oObject;
    }

    /**
     * Prepares price depending what options are used(show as net, brutto, etc.) for displaying
     *
     * @param $dPrice
     * @param $dVat
     *
     * @return float
     */
    protected function _preparePrice( $dPrice, $dVat )
    {
        $blCalculationModeNetto = (bool) $this->getConfig()->getConfigParam('blShowNetPrice');

        $oCurrency = $this->getConfig()->getActShopCurrencyObject();

        $blEnterNetPrice = $this->getConfig()->getConfigParam('blEnterNetPrice');
        if ( $blCalculationModeNetto && !$blEnterNetPrice ) {
            $dPrice = round( oxPrice::brutto2Netto( $dPrice, $dVat ), $oCurrency->decimal );
        } elseif ( !$blCalculationModeNetto && $blEnterNetPrice ) {
            $dPrice = round( oxPrice::netto2Brutto( $dPrice, $dVat ), $oCurrency->decimal );
        }
        return $dPrice;
    }
    /**
     * returns manually set mime types
     *
     * @param string $sFileName the file
     *
     * @return string
     */
    public function oxMimeContentType( $sFileName )
    {
        $sFileName = strtolower( $sFileName );
        $iLastDot  = strrpos( $sFileName, '.' );

        if ( $iLastDot !== false ) {
            $sType = substr( $sFileName, $iLastDot + 1 );
            switch ( $sType ) {
                case 'gif':
                    $sType = 'image/gif';
                    break;
                case 'jpeg':
                case 'jpg':
                    $sType = 'image/jpeg';
                    break;
                case 'png':
                    $sType = 'image/png';
                    break;
                default:
                    $sType = false;
                    break;
            }
        }
        return $sType;
    }

    /**
     * Processes logging.
     *
     * @param string $sText     Log message text
     * @param bool   $blNewline If true, writes message to new line (default false)
     *
     * @return null
     */
    public function logger( $sText, $blNewline = false )
    {   $myConfig = $this->getConfig();

        if ( $myConfig->getConfigParam( 'iDebug' ) == -2) {
            if ( gettype( $sText ) != 'string' ) {
                $sText = var_export( $sText, true);
            }
            $sLogMsg = "----------------------------------------------\n{$sText}".( ( $blNewline ) ?"\n":"" )."\n";
            $this->writeToLog( $sLogMsg, "log.txt" );
        }

    }

    /**
     * Recursively removes slashes from arrays
     *
     * @param mixed $mInput the input from which the slashes should be removed
     *
     * @return mixed
     */
    protected function _stripQuotes($mInput)
    {
        return is_array($mInput) ? array_map( array( $this, '_stripQuotes' ), $mInput) : stripslashes( $mInput );
    }

    /**
    * Applies ROT13 encoding to $sStr
    *
    * @param string $sStr to encoding string
    *
    * @return string
    */
    public function strRot13( $sStr )
    {
        $sFrom = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sTo   = 'nopqrstuvwxyzabcdefghijklmNOPQRSTUVWXYZABCDEFGHIJKLM';

        return strtr( $sStr, $sFrom, $sTo );
    }

    /**
     * Returns full path (including file name) to cache file
     *
     * @param string $sCacheName cache file name
     * @param bool   $blPathOnly if TRUE, name parameter will be ignored and only cache folder will be returned (default FALSE)
     * @param string $sExtension cache file extension
     *
     * @return string
     */
    public function getCacheFilePath( $sCacheName, $blPathOnly = false, $sExtension = 'txt' )
    {

            $sVersionPrefix = 'pe';

        $sPath = realpath($this->getConfig()->getConfigParam( 'sCompileDir' ));

        if (!$sPath) {
            return false;
        }

        return $blPathOnly ? "{$sPath}/" : "{$sPath}/ox{$sVersionPrefix}c_{$sCacheName}." . $sExtension;
    }

    /**
     * Tries to load lang cache array from cache file
     *
     * @param string $sCacheName cache file name
     *
     * @return array
     */
    public function getLangCache( $sCacheName )
    {
        $aLangCache = null;
        $sFilePath = $this->getCacheFilePath( $sCacheName );
        if ( file_exists( $sFilePath ) && is_readable( $sFilePath ) ) {
            include $sFilePath;
        }
        return $aLangCache;
    }

    /**
     * Writes language array to file cache
     *
     * @param string $sCacheName name of cache file
     * @param array  $aLangCache language array
     *
     * @return null
     */
    public function setLangCache( $sCacheName, $aLangCache )
    {
        $sCache = "<?php\n\$aLangCache = ".var_export( $aLangCache, true ).";\n?>";
        $blRes = file_put_contents($this->getCacheFilePath($sCacheName), $sCache, LOCK_EX);
        return $blRes;
    }

    /**
     * Checks if url has ending slash / - if not, adds it
     *
     * @param string $sUrl url string
     *
     * @return string
     */
    public function checkUrlEndingSlash( $sUrl )
    {
        if ( !getStr()->preg_match("/\/$/", $sUrl) ) {
            $sUrl .= '/';
        }

        return $sUrl;
    }

    /**
     * Writes given log message. Returns write state
     *
     * @param string $sLogMessage  log message
     * @param string $sLogFileName log file name
     *
     * @return bool
     */
    public function writeToLog( $sLogMessage, $sLogFileName )
    {
        $sLogDist = $this->getConfig()->getLogsDir().$sLogFileName;
        $blOk = false;

        if ( ( $oHandle = fopen( $sLogDist, 'a' ) ) !== false ) {
            fwrite( $oHandle, $sLogMessage );
            $blOk = fclose( $oHandle );
        }

        return $blOk;
    }

    /**
     * handler for 404 (page not found) error
     *
     * @param string $sUrl url which was given, can be not specified in some cases
     *
     * @return void
     */
    public function handlePageNotFoundError($sUrl = '')
    {
        $this->setHeader("HTTP/1.0 404 Not Found");
        if ( oxRegistry::getConfig()->isUtf() ) {
            $this->setHeader("Content-Type: text/html; charset=UTF-8");
        }

        $sReturn = "Page not found.";
        try {
            $oView = oxNew('oxUBase');
            $oView->init();
            $oView->render();
            $oView->setClassName( 'oxUBase' );
            $oView->addTplParam('sUrl', $sUrl);
            if ($sRet = oxRegistry::get("oxUtilsView")->getTemplateOutput('message/err_404.tpl', $oView)) {
                $sReturn = $sRet;
            }
        } catch (Exception $e) {
        }
        $this->showMessageAndExit( $sReturn );
    }

    /**
     * Extracts domain name from given host
     *
     * @param string $sHost host name
     *
     * @return string
     */
    public function extractDomain( $sHost )
    {
        $oStr = getStr();
        if ( !$oStr->preg_match( '/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $sHost ) &&
             ( $iLastDot = strrpos( $sHost, '.' ) ) !== false ) {
            $iLen = $oStr->strlen( $sHost );
            if ( ( $iNextDot = strrpos( $sHost, '.', ( $iLen - $iLastDot + 1 ) * - 1 ) ) !== false ) {
                $sHost = trim( $oStr->substr( $sHost, $iNextDot ), '.' );
            }
        }

        return $sHost;
    }
}
