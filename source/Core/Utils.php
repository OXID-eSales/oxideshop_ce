<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Str;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\ApplicationExitEvent;
use stdClass;
use OxidEsales\Eshop\Core\Registry;
use Symfony\Component\Filesystem\Path;

/**
 * General utils class
 */
class Utils extends \OxidEsales\Eshop\Core\Base
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
    protected $_aLockedFileHandles = [];

    /**
     * Local cache
     *
     * @var array
     */
    protected $_aFileCacheContents = [];

    /**
     * Search engine indicator
     *
     * @var bool
     */
    protected $_blIsSe = null;

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
     * Returns string witch "." symbols were replaced with "__".
     *
     * @param string $sName String to search replaceable char
     *
     * @return string
     */
    public function getArrFldName($sName)
    {
        return str_replace(".", "__", $sName);
    }

    /**
     * Takes a string and assign all values, returns array with values.
     *
     * @param string $sIn  Initial string
     * @param float  $dVat Article VAT (optional)
     *
     * @return array
     */
    public function assignValuesFromText($sIn, $dVat = null)
    {
        $aRet = [];
        $aPieces = explode('@@', $sIn);
        foreach ($aPieces as $sVal) {
            if ($sVal) {
                $aName = explode('__', $sVal);
                if (isset($aName[0]) && isset($aName[1])) {
                    $aRet[] = $this->fillExplodeArray($aName, $dVat);
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
    public function assignValuesToText($aIn)
    {
        $sRet = "";
        reset($aIn);
        foreach ($aIn as $sKey => $sVal) {
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
    public function currency2Float($sValue)
    {
        $fRet = $sValue;
        $iPos = strrpos($sValue, ".");
        if ($iPos && ((strlen($sValue) - 1 - $iPos) < 2 + 1)) {
            // replace decimal with ","
            $fRet = substr_replace($fRet, ",", $iPos, 1);
        }
        // remove thousands
        $fRet = str_replace([" ", "."], "", $fRet);

        return (float) str_replace(",", ".", $fRet);
    }

    /**
     * Returns formatted float, according to formatting standards.
     *
     * @param string $sValue Formatted price
     *
     * @return float
     */
    public function string2Float($sValue)
    {
        $fRet = str_replace(" ", "", $sValue);
        $iCommaPos = strpos($fRet, ",");
        $iDotPos = strpos($fRet, ".");
        if (!$iDotPos xor !$iCommaPos) {
            if (substr_count($fRet, ",") > 1 || substr_count($fRet, ".") > 1) {
                $fRet = str_replace([",", "."], "", $fRet);
            } else {
                $fRet = str_replace(",", ".", $fRet);
            }
        } else {
            if ($iDotPos < $iCommaPos) {
                $fRet = str_replace(".", "", $fRet);
                $fRet = str_replace(",", ".", $fRet);
            }
        }

        // remove thousands
        return (float) str_replace([" ", ","], "", $fRet);
    }

    /**
     * Checks if current web client is Search Engine. Returns true on success.
     *
     * @param string $sClient user browser agent
     *
     * @return bool
     */
    public function isSearchEngine($sClient = null)
    {
        if (is_null($this->_blIsSe)) {
            $this->setSearchEngine(null, $sClient);
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
    public function setSearchEngine($blIsSe = null, $sClient = null)
    {
        if (isset($blIsSe)) {
            $this->_blIsSe = $blIsSe;

            return;
        }
        startProfile("isSearchEngine");

        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $blIsSe = false;

        if (!(ContainerFacade::getParameter('oxid_debug_mode') && $this->isAdmin())) {
            $aRobots = $myConfig->getConfigParam('aRobots');
            $aRobots = is_array($aRobots) ? $aRobots : [];

            $aRobotsExcept = $myConfig->getConfigParam('aRobotsExcept');
            $aRobotsExcept = is_array($aRobotsExcept) ? $aRobotsExcept : [];

            $sClient = $sClient ?: strtolower(getenv('HTTP_USER_AGENT'));
            $aRobots = array_merge($aRobots, $aRobotsExcept);
            foreach ($aRobots as $sRobot) {
                if (strpos($sClient, $sRobot) !== false) {
                    $blIsSe = true;
                    break;
                }
            }
        }

        $this->_blIsSe = $blIsSe;

        stopProfile("isSearchEngine");
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
        if (is_array($aInterfaceProfiles)) {
            //checking for previous profiles
            $sPrevProfile = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('oxidadminprofile');
            if (isset($sPrevProfile)) {
                $aPrevProfile = @explode("@", trim($sPrevProfile));
            }

            //array to store profiles
            $aProfiles = [];
            foreach ($aInterfaceProfiles as $iPos => $sProfile) {
                $aProfileSettings = [$iPos, $sProfile];
                $aProfiles[] = $aProfileSettings;
            }
            // setting previous used profile as active
            if (isset($aPrevProfile[0]) && isset($aProfiles[$aPrevProfile[0]])) {
                $aProfiles[$aPrevProfile[0]][2] = 1;
            }

            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("aAdminProfiles", $aProfiles);

            return $aProfiles;
        }

        return null;
    }

    /**
     * Rounds the value to currency cents. This method does NOT format the number.
     *
     * @param string $value the value that should be rounded
     * @param object $currency
     *
     * @return float
     */
    public function fRound($value, $currency = null)
    {
        startProfile('fround');
        //cached currency precision, this saves about 1% of execution time
        if (is_null($this->_iCurPrecision)) {
            $currency = $currency ?: Registry::getConfig()->getActShopCurrencyObject();
            $this->_iCurPrecision = $currency->decimal;
        }
        $roundedValue = round((float)$value, $this->_iCurPrecision);
        stopProfile('fround');

        return $roundedValue;
    }

    /**
     * Alphanumeric oxid and pure numeric oxid that start with the numeric part and only differ
     * in postfixed alphabetical characters (e.g. "123" and "123X") are cast to the wrong type
     * php internally which might result in wrong array_search results.
     *
     * Wrapper for php internal array_search function, ony usable for string search.
     * In case we get unclear results make sure we typecast all data
     * to string before performing array search.
     *
     * @param string $needle
     * @param array  $haystack
     *
     * @return mixed
     */
    public function arrayStringSearch($needle, $haystack)
    {
        $result = array_search((string) $needle, $haystack);
        $second = array_search((string) $needle, $haystack, true);

        //got a different result when using strict and not strict?
        //do a detail check
        if ($result != $second) {
            $stringstack = [];
            foreach ($haystack as $value) {
                $stringstack[] = (string) $value;
            }
            $result = array_search((string) $needle, $stringstack, true);
        }

        return $result;
    }

    /**
     * Stores something into static cache to avoid double loading
     *
     * @param string $sName    name of the content
     * @param mixed  $sContent the content
     * @param string $sKey     optional key, where to store the content
     */
    public function toStaticCache($sName, $sContent, $sKey = null)
    {
        // if it's an array then we add
        if ($sKey) {
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
    public function fromStaticCache($sName)
    {
        if (isset($this->_aStaticCache[$sName])) {
            return $this->_aStaticCache[$sName];
        }
    }

    /**
     * Cleans all or specific data from static cache
     *
     * @param string $sCacheName Cache name
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
     */
    public function toPhpFileCache($sKey, $mContents)
    {
        //only simple arrays are supported
        if (is_array($mContents) && ($sCachePath = $this->getCacheFilePath($sKey, false, 'php'))) {
            // setting meta
            $this->setCacheMeta($sKey, ["serialize" => false, "cachepath" => $sCachePath]);

            // caching..
            $this->toFileCache($sKey, $mContents);
        }
    }

    /**
     * Includes cached php file and loads stored contents.
     *
     * @param string $sKey Cache key.
     *
     * @return null
     */
    public function fromPhpFileCache($sKey)
    {
        // setting meta
        $this->setCacheMeta($sKey, ["include" => true, "cachepath" => $this->getCacheFilePath($sKey, false, 'php')]);

        return $this->fromFileCache($sKey);
    }

    /**
     * If available returns cache meta data array
     *
     * @param string $sKey meta data/cache key
     *
     * @return mixed
     */
    public function getCacheMeta($sKey)
    {
        return isset($this->_aFileCacheMeta[$sKey]) ? $this->_aFileCacheMeta[$sKey] : false;
    }

    /**
     * Saves cache meta data (information)
     *
     * @param string $sKey  meta data/cache key
     * @param array  $aMeta meta data array
     */
    public function setCacheMeta($sKey, $aMeta)
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
    public function toFileCache($sKey, $mContents, $iTtl = 0)
    {
        $aCacheData['content'] = $mContents;
        $aMeta = $this->getCacheMeta($sKey);
        if ($iTtl) {
            $aCacheData['ttl'] = $iTtl;
            $aCacheData['timestamp'] = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        }
        $this->_aFileCacheContents[$sKey] = $aCacheData;

        // looking for cache meta
        $sCachePath = isset($aMeta["cachepath"]) ? $aMeta["cachepath"] : $this->getCacheFilePath($sKey);

        return (bool) $this->lockFile($sCachePath, $sKey);
    }

    /**
     * Fetches contents from file cache.
     *
     * @param string $sKey Cache key
     *
     * @return mixed
     */
    public function fromFileCache($sKey)
    {
        if (!array_key_exists($sKey, $this->_aFileCacheContents)) {
            $aMeta = $this->getCacheMeta($sKey);
            $sCachePath = isset($aMeta["cachepath"]) ? $aMeta["cachepath"] : $this->getCacheFilePath($sKey);

            clearstatcache();
            if (is_readable($sCachePath)) {
                $this->lockFile($sCachePath, $sKey, LOCK_SH);

                $blInclude = isset($aMeta["include"]) ? $aMeta["include"] : false;
                $sRes = $blInclude ? $this->includeFile($sCachePath) : $this->readFile($sCachePath);

                if (isset($sRes['ttl']) && $sRes['ttl'] != 0) {
                    $iTimestamp = $sRes['timestamp'];
                    $iTtl = $sRes['ttl'];

                    $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
                    if ($iTime > $iTimestamp + $iTtl) {
                        return null;
                    }
                }

                $this->_aFileCacheContents[$sKey] = $sRes;

                $this->releaseFile($sKey, LOCK_SH);
            }
        }

        return isset($this->_aFileCacheContents[$sKey]) ? $this->_aFileCacheContents[$sKey]['content'] : null;
    }

    /**
     * Reads and returns cache file contents
     *
     * @param string $sFilePath cache fiel path
     *
     * @return string
     */
    protected function readFile($sFilePath)
    {
        $sRes = file_get_contents($sFilePath);

        return $sRes ? unserialize($sRes) : null;
    }

    /**
     * Includes cache file
     *
     * @param string $sFilePath cache file path
     *
     * @return mixed
     */
    protected function includeFile($sFilePath)
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
    protected function processCache($sKey, $mContents)
    {
        // looking for cache meta
        $aCacheMeta = $this->getCacheMeta($sKey);
        $blSerialize = isset($aCacheMeta["serialize"]) ? $aCacheMeta["serialize"] : true;

        if ($blSerialize) {
            $mContents = serialize($mContents);
        } else {
            $mContents = "<?php\n//automatically generated file\n//" . date("Y-m-d H:i:s") . "\n\n\$_aCacheContents = " . var_export($mContents, true) . "\n?>";
        }

        return $mContents;
    }

    /**
     * Writes all cache contents to file at once. This method was introduced due to possible
     * race conditions. Cache is cleaned up after commit
     */
    public function commitFileCache()
    {
        if (!empty($this->_aLockedFileHandles[LOCK_EX])) {
            startProfile("!__SAVING CACHE__! (warning)");
            foreach ($this->_aLockedFileHandles[LOCK_EX] as $sKey => $rHandle) {
                if ($rHandle !== false && isset($this->_aFileCacheContents[$sKey])) {
                    // #0002931A truncate file once more before writing
                    ftruncate($rHandle, 0);

                    // writing cache
                    fwrite($rHandle, $this->processCache($sKey, $this->_aFileCacheContents[$sKey]));

                    // releasing locks
                    $this->releaseFile($sKey);
                }
            }

            stopProfile("!__SAVING CACHE__! (warning)");

            //empty buffer
            $this->_aFileCacheContents = [];
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
    protected function lockFile($sFilePath, $sIdent, $iLockMode = LOCK_EX)
    {
        $rHandle = isset($this->_aLockedFileHandles[$iLockMode][$sIdent]) ? $this->_aLockedFileHandles[$iLockMode][$sIdent] : null;
        if ($rHandle === null) {
            $blLocked = false;
            $rHandle = @fopen($sFilePath, "a+");

            if ($rHandle !== false) {
                if (flock($rHandle, $iLockMode | LOCK_NB)) {
                    if ($iLockMode === LOCK_EX) {
                        // truncate file
                        $blLocked = ftruncate($rHandle, 0);
                    } else {
                        // move to a start position
                        $blLocked = fseek($rHandle, 0) === 0;
                    }
                }

                // on failure - closing and setting false..
                if (!$blLocked) {
                    fclose($rHandle);
                    $rHandle = false;
                }
            }

            // in case system does not support file locking
            if (!$blLocked && $iLockMode === LOCK_EX) {
                // clearing on first call
                if (count($this->_aLockedFileHandles) == 0) {
                    clearstatcache();
                }

                // start a blank file to inform other processes we are dealing with it.
                if (!(file_exists($sFilePath) && !filesize($sFilePath) && abs(time() - filectime($sFilePath) < 40))) {
                    $rHandle = @fopen($sFilePath, "w");
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
    protected function releaseFile($sIdent, $iLockMode = LOCK_EX)
    {
        $blSuccess = true;
        if (
            isset($this->_aLockedFileHandles[$iLockMode][$sIdent]) &&
            $this->_aLockedFileHandles[$iLockMode][$sIdent] !== false
        ) {
            // release the lock and close file
            $blSuccess = flock($this->_aLockedFileHandles[$iLockMode][$sIdent], LOCK_UN) &&
                         fclose($this->_aLockedFileHandles[$iLockMode][$sIdent]);
            unset($this->_aLockedFileHandles[$iLockMode][$sIdent]);
        }

        return $blSuccess;
    }

    /**
     * Removes most files stored in cache (default 'tmp') folder. Some files
     * e.g. table files names description, are left. Excluded cache file name
     * patterns are defined in \OxidEsales\Eshop\Core\Utils::_sPermanentCachePattern parameter
     */
    public function oxResetFileCache(bool $includePermanentCache = false)
    {
        $cacheFilePath = $this->getCacheFilePath(null, true);
        $files = $cacheFilePath ?? glob($cacheFilePath . '*');
        if (is_array($files)) {
            if (!$includePermanentCache) {
                // delete all the files, except cached tables field names
                $files = preg_grep($this->_sPermanentCachePattern, $files, PREG_GREP_INVERT);
            }

            foreach ($files as $file) {
                @unlink($file);
            }
        }
    }

    /**
     * Removes language constant cache
     */
    public function resetLanguageCache()
    {
        $aFiles = glob($this->getCacheFilePath(null, true) . '*');
        if (is_array($aFiles)) {
            // delete all language cache files
            $sPattern = $this->_sLanguageCachePattern;
            $aFiles = preg_grep($sPattern, $aFiles);
            foreach ($aFiles as $sFile) {
                @unlink($sFile);
            }
        }
    }

    /**
     * Removes admin menu cache
     */
    public function resetMenuCache()
    {
        $aFiles = glob($this->getCacheFilePath(null, true) . '*');
        if (is_array($aFiles)) {
            // delete all menu cache files
            $sPattern = $this->_sMenuCachePattern;
            $aFiles = preg_grep($sPattern, $aFiles);
            foreach ($aFiles as $sFile) {
                @unlink($sFile);
            }
        }
    }

    /**
     * Checks if preview mode is ON
     *
     * @return bool
     */
    public function canPreview()
    {
        $blCan = null;
        if (
            ($sPrevId = Registry::getRequest()->getRequestEscapedParameter('preview')) &&
            ($sAdminSid = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('admin_sid'))
        ) {
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $sTable = $tableViewNameGenerator->getViewName('oxuser');
            $sQ = "SELECT 1 FROM $sTable WHERE MD5( CONCAT( :adminsid, {$sTable}.oxid, {$sTable}.oxpassword, {$sTable}.oxrights ) ) = :previd";
            $blCan = (bool) \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sQ, [
                ':adminsid' => $sAdminSid,
                ':previd'   => $sPrevId
            ]);
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
        $sAdminSid = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('admin_sid');
        if (($oUser = $this->getUser())) {
            return md5($sAdminSid . $oUser->getId() . $oUser->oxuser__oxpassword->value . $oUser->oxuser__oxrights->value);
        }
    }

    /**
     * This function checks if logged in user has access to admin or not
     *
     * @return bool
     */
    public function checkAccessRights()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $blIsAuth = false;

        $sUserID = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("auth");

        // deleting admin marker
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("malladmin", 0);
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("blIsAdmin", 0);
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("blIsAdmin");
        $myConfig->setConfigParam('blMallAdmin', false);
        //#1552T
        $myConfig->setConfigParam('blAllowInheritedEdit', false);

        if ($sUserID) {
            // escaping
            $sRights = $this->fetchRightsForUser($sUserID);

            if ($sRights != "user") {
                // malladmin ?
                if ($sRights == "malladmin") {
                    \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("malladmin", 1);
                    $myConfig->setConfigParam('blMallAdmin', true);

                    //#1552T
                    //So far this blAllowSharedEdit is Equal to blMallAdmin but in future to be solved over rights and roles
                    $myConfig->setConfigParam('blAllowSharedEdit', true);

                    $sShop = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("actshop");
                    if (!isset($sShop)) {
                        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("actshop", $myConfig->getBaseShopId());
                    }
                    $blIsAuth = true;
                } else {
                    // Shopadmin... check if this shop is valid and exists
                    $sShopID = $this->fetchShopAdminById($sRights);
                    if (isset($sShopID) && $sShopID) {
                        // success, this shop exists

                        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("actshop", $sRights);
                        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("currentadminshop", $sRights);
                        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("shp", $sRights);

                        // check if this subshop admin is evil.
                        if ('chshp' == Registry::getRequest()->getRequestEscapedParameter('fnc')) {
                            // dont allow this call
                            $blIsAuth = false;
                        } else {
                            $blIsAuth = true;

                            $aShopIdVars = ['actshop', 'shp', 'currentadminshop'];
                            foreach ($aShopIdVars as $sShopIdVar) {
                                if ($sGotShop = Registry::getRequest()->getRequestEscapedParameter($sShopIdVar)) {
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
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("blIsAdmin", 1);
            }
        }

        return $blIsAuth;
    }

    /**
     * Fetch the rights for the user given by its oxid
     *
     * @param string $userOxId The oxId of the user we want the rights for.
     *
     * @return mixed The rights
     */
    protected function fetchRightsForUser($userOxId)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        return $database->getOne("SELECT oxrights FROM oxuser WHERE oxid = :oxid ", [
            ':oxid' => $userOxId
        ]);
    }

    /**
     * Fetch the oxId from the oxshops table.
     *
     * @param string $oxId The oxId of the shop.
     *
     * @return mixed The oxId of the shop with the given oxId.
     */
    protected function fetchShopAdminById($oxId)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        return $database->getOne("SELECT oxid FROM oxshops WHERE oxid = :oxid", [
            ':oxid' => $oxId
        ]);
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
    public function seoIsActive($blReset = false, $sShopId = null, $iActLang = null)
    {
        if (!is_null($this->_blSeoIsActive) && !$blReset) {
            return $this->_blSeoIsActive;
        }

        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        if (($this->_blSeoIsActive = $myConfig->getConfigParam('blSeoMode')) === null) {
            $this->_blSeoIsActive = true;

            $aSeoModes = $myConfig->getconfigParam('aSeoModes');
            $sActShopId = $sShopId ? $sShopId : $myConfig->getActiveShop()->getId();
            $iActLang = $iActLang ? $iActLang : (int) \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();

            // checking special config param for active shop and language
            if (is_array($aSeoModes) && isset($aSeoModes[$sActShopId]) && isset($aSeoModes[$sActShopId][$iActLang])) {
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
    public function isValidAlpha($sField)
    {
        return (bool) Str::getStr()->preg_match('/^[a-zA-Z0-9_]*$/', $sField);
    }

    /**
     * redirects browser to given url, nothing else done just header send
     * may be used for redirection in case of an exception or similar things
     *
     * @param string $sUrl        the URL to redirect to
     * @param string $sHeaderCode code to add to the header(e.g. "HTTP/1.1 301 Moved Permanently", or "HTTP/1.1 500 Internal Server Error"
     */
    protected function simpleRedirect($sUrl, $sHeaderCode)
    {
        $oHeader = oxNew(\OxidEsales\Eshop\Core\Header::class);
        $oHeader->setHeader($sHeaderCode);
        $oHeader->setHeader("Location: $sUrl");
        $oHeader->setHeader("Connection: close");
        $oHeader->sendHeader();
    }

    /**
     * Shows offline page.
     * Directly displays the offline page to the client (browser)
     * with a 500 status code header.
     */
    public function showOfflinePage()
    {
        \oxTriggerOfflinePageDisplay();
        $this->showMessageAndExit('');
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
    public function redirect($sUrl, $blAddRedirectParam = true, $iHeaderCode = 302)
    {
        //preventing possible cyclic redirection
        //#M341 and check only if redirect parameter must be added
        if ($blAddRedirectParam && Registry::getRequest()->getRequestEscapedParameter('redirected')) {
            return;
        }

        if ($blAddRedirectParam) {
            $sUrl = $this->addUrlParameters($sUrl, ['redirected' => 1]);
        }

        $sUrl = str_ireplace("&amp;", "&", $sUrl);

        switch ($iHeaderCode) {
            case 301:
                $sHeaderCode = "HTTP/1.1 301 Moved Permanently";
                break;
            case 500:
                $sHeaderCode = "HTTP/1.1 500 Internal Server Error";
                break;
            case 302:
            default:
                $sHeaderCode = "HTTP/1.1 302 Found";
        }

        $this->simpleRedirect($sUrl, $sHeaderCode);

        try { //may occur in case db is lost
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            $session->freeze();
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            \OxidEsales\Eshop\Core\Registry::getLogger()->error($exception->getMessage(), [$exception]);
            //do nothing else to make sure the redirect takes place
        }

        $this->showMessageAndExit('');
    }

    /**
     * shows given message and quits
     * message might be whole content like 404 page.
     *
     * @param string $sMsg message to show
     */
    public function showMessageAndExit($sMsg)
    {
        $this->prepareToExit();
        exit($sMsg);
    }

    /**
     * helper with commands to run before exit action
     */
    protected function prepareToExit()
    {
        $session = \OxidEsales\Eshop\Core\Registry::getSession();
        $session->freeze();
        $this->commitFileCache();

        ContainerFacade::dispatch(new ApplicationExitEvent());

        if ($this->isSearchEngine()) {
            $header = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Header::class);
            $header->setNonCacheable();
        }

        //Send headers that have been registered
        $header = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Header::class);
        $header->sendHeader();
    }

    /**
     * set header sent to browser
     *
     * @param string $sHeader header to sent
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
    protected function addUrlParameters($sUrl, $aParams)
    {
        $sDelimiter = ((Str::getStr()->strpos($sUrl, '?') !== false)) ? '&' : '?';
        foreach ($aParams as $sName => $sVal) {
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
    protected function fillExplodeArray($aName, $dVat = null)
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $oObject = new stdClass();
        $aPrice = explode('!P!', $aName[0]);

        if (($myConfig->getConfigParam('bl_perfLoadSelectLists') && $myConfig->getConfigParam('bl_perfUseSelectlistPrice') && isset($aPrice[0]) && isset($aPrice[1])) || $this->isAdmin()) {
            // yes, price is there
            $oObject->price = isset($aPrice[1]) ? $aPrice[1] : 0;
            $aName[0] = isset($aPrice[0]) ? $aPrice[0] : '';

            $iPercPos = Str::getStr()->strpos($oObject->price, '%');
            if ($iPercPos !== false) {
                $oObject->priceUnit = '%';
                $oObject->fprice = $oObject->price;
                $oObject->price = substr($oObject->price, 0, $iPercPos);
            } else {
                $oCur = $myConfig->getActShopCurrencyObject();
                $oObject->price = str_replace(',', '.', $oObject->price);
                $oObject->fprice = \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($oObject->price * $oCur->rate, $oCur);
                $oObject->priceUnit = 'abs';
            }

            // add price info into list
            if (!$this->isAdmin() && $oObject->price != 0) {
                $aName[0] .= " ";

                $dPrice = $this->preparePrice($oObject->price, $dVat);

                if ($oObject->price > 0) {
                    $aName[0] .= "+";
                }
                //V FS#2616
                if ($dVat != null && $oObject->priceUnit == 'abs') {
                    $oPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
                    $oPrice->setPrice($oObject->price, $dVat);
                    $aName[0] .= \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($dPrice * $oCur->rate, $oCur);
                } else {
                    $aName[0] .= $oObject->fprice;
                }
                if ($oObject->priceUnit == 'abs') {
                    $aName[0] .= " " . $oCur->sign;
                }
            }
        } elseif (isset($aPrice[0]) && isset($aPrice[1])) {
            // A. removing unused part of information
            $aName[0] = Str::getStr()->preg_replace("/!P!.*/", "", $aName[0]);
        }

        $oObject->name = $aName[0];
        $oObject->value = $aName[1];

        return $oObject;
    }

    /**
     * Prepares price depending what options are used(show as net, brutto, etc.) for displaying
     *
     * @param double $dPrice Price
     * @param double $dVat   VAT
     *
     * @return float
     */
    protected function preparePrice($dPrice, $dVat)
    {
        $blCalculationModeNetto = $this->isPriceViewModeNetto();

        $oCurrency = \OxidEsales\Eshop\Core\Registry::getConfig()->getActShopCurrencyObject();

        $blEnterNetPrice = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blEnterNetPrice');
        if ($blCalculationModeNetto && !$blEnterNetPrice) {
            $dPrice = round(\OxidEsales\Eshop\Core\Price::brutto2Netto($dPrice, $dVat), $oCurrency->decimal);
        } elseif (!$blCalculationModeNetto && $blEnterNetPrice) {
            $dPrice = round(\OxidEsales\Eshop\Core\Price::netto2Brutto($dPrice, $dVat), $oCurrency->decimal);
        }

        return $dPrice;
    }

    /**
     * Checks and return true if price view mode is netto.
     *
     * @return bool
     */
    protected function isPriceViewModeNetto()
    {
        $blResult = (bool) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blShowNetPrice');
        $oUser = $this->getArticleUser();
        if ($oUser) {
            $blResult = $oUser->isPriceViewModeNetto();
        }

        return $blResult;
    }

    /**
     * Return article user.
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    protected function getArticleUser()
    {
        if (isset($this->_oUser) && $this->_oUser) {
            return $this->_oUser;
        }

        return $this->getUser();
    }

    /**
     * returns manually set mime types
     *
     * @param string $sFileName the file
     *
     * @return string
     */
    public function oxMimeContentType($sFileName)
    {
        $sFileName = strtolower($sFileName);
        $iLastDot = strrpos($sFileName, '.');

        $sType = false;
        if ($iLastDot !== false) {
            $sType = substr($sFileName, $iLastDot + 1);
            $sType = match ($sType) {
                'gif'         => 'image/gif',
                'jpeg', 'jpg' => 'image/jpeg',
                'png'         => 'image/png',
                'webp'        => 'image/webp',
                default       => false,
            };
        }

        return $sType;
    }

    /**
     * Returns full path (including file name) to cache file
     *
     * @todo: test
     *
     * @param string $sCacheName cache file name
     * @param bool   $blPathOnly if TRUE, name parameter will be ignored and only cache folder will be returned (default FALSE)
     * @param string $sExtension cache file extension
     *
     * @return string
     */
    public function getCacheFilePath($sCacheName, $blPathOnly = false, $sExtension = 'txt')
    {
        $versionPrefix = $this->getEditionCacheFilePrefix();

        $sPath = realpath(ContainerFacade::getParameter('oxid_build_directory'));

        if (!$sPath) {
            return false;
        }

        return $blPathOnly ? "{$sPath}/" : "{$sPath}/ox{$versionPrefix}c_{$sCacheName}." . $sExtension;
    }

    /**
     * Get current edition prefix
     * @return string
     */
    public function getEditionCacheFilePrefix()
    {
        return '';
    }

    /**
     * Tries to load lang cache array from cache file
     *
     * @param string $sCacheName cache file name
     *
     * @return array
     */
    public function getLangCache($sCacheName)
    {
        $aLangCache = null;
        $sFilePath = $this->getCacheFilePath($sCacheName);
        if (file_exists($sFilePath) && is_readable($sFilePath)) {
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
    public function setLangCache($sCacheName, $aLangCache)
    {
        $sCache = "<?php\n\$aLangCache = " . var_export($aLangCache, true) . ";\n?>";
        $sFileName = $this->getCacheFilePath($sCacheName);

        $tmpFile = Path::join(
            ContainerFacade::getParameter('oxid_build_directory'),
            basename($sFileName) . uniqid('.temp', true) . '.txt'
        );
        $blRes = file_put_contents($tmpFile, $sCache, LOCK_EX);

        rename($tmpFile, $sFileName);

        return $blRes;
    }

    /**
     * Checks if url has ending slash / - if not, adds it
     *
     * @param string $sUrl url string
     *
     * @return string
     */
    public function checkUrlEndingSlash($sUrl)
    {
        if (!Str::getStr()->preg_match("/\/$/", $sUrl)) {
            $sUrl .= '/';
        }

        return $sUrl;
    }

    /**
     * handler for 404 (page not found) error
     *
     * @param string $sUrl url which was given, can be not specified in some cases
     */
    public function handlePageNotFoundError($sUrl = '')
    {
        $this->setHeader("HTTP/1.0 404 Not Found");
        $this->setHeader("Content-Type: text/html; charset=UTF-8");

        $sReturn = "Page not found.";
        $oView = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
        $oView->init();
        $oView->render();
        $oView->setClassKey('oxUBase');
        $oView->addTplParam('sUrl', $sUrl);
        if ($sRet = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getTemplateOutput('message/err_404', $oView)) {
            $sReturn = $sRet;
        }
        $this->showMessageAndExit($sReturn);
    }

    /**
     * Extracts domain name from given host
     *
     * @param string $sHost host name
     *
     * @return string
     */
    public function extractDomain($sHost)
    {
        $oStr = Str::getStr();
        if (
            !$oStr->preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $sHost) &&
            ($iLastDot = strrpos($sHost, '.')) !== false
        ) {
            $iLen = $oStr->strlen($sHost);
            if (($iNextDot = strrpos($sHost, '.', ($iLen - $iLastDot + 1) * -1)) !== false) {
                $sHost = trim($oStr->substr($sHost, $iNextDot), '.');
            }
        }

        return $sHost;
    }
}
