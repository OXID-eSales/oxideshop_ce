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
 * Object Factory implementation (oxNew() method is implemented in this class).
 */
class oxUtilsObject
{

    /**
     * Cache file prefix
     */
    const CACHE_FILE_PREFIX = "config";

    /**
     * Cache class names
     *
     * @var array
     */
    protected $_aClassNameCache = array();

    /**
     * The array of already loaded articles
     *
     * @var array
     */
    protected static $_aLoadedArticles = array();

    /**
     * The array of already initialised instances
     *
     * @var array
     */
    protected static $_aInstanceCache = array();

    /**
     * Module information variables
     *
     * @var array
     */
    protected static $_aModuleVars = array();

    /**
     * Class instance array
     *
     * @var array
     */
    protected static $_aClassInstances = array();

    /**
     * oxUtilsObject class instance.
     *
     * @var oxUtils* instance
     */
    private static $_instance = null;

    /**
     * Returns object instance
     *
     * @return oxUtilsObject
     */
    public static function getInstance()
    {
        // disable caching for test modules
        if ( defined( 'OXID_PHP_UNIT' ) ) {
            self::$_instance = null;
        }

        if ( !self::$_instance instanceof oxUtilsObject ) {

            // allow modules
            $oUtilsObject = new oxUtilsObject();
            self::$_instance = $oUtilsObject->oxNew( 'oxUtilsObject' );
        }
        return self::$_instance;
    }

    /**
     * Factory instance setter. Sets the instance to be returned over later called oxNew().
     * This method is mostly intended to be used by phpUnit tests.
     *
     * @param string $sClassName Class name expected to be later supplied over oxNew
     * @param object $oInstance  Instance object
     *
     * @return null;
     */
    public static function setClassInstance( $sClassName, $oInstance )
    {
        $sClassName = strtolower( $sClassName );
        self::$_aClassInstances[$sClassName] = $oInstance;
    }

    /**
     * Resets previously set instances
     *
     * @return null;
     */
    public static function resetClassInstances()
    {
        self::$_aClassInstances = array();
    }

    /**
     * Resets previously set module information.
     *
     * @static
     *
     * @return null;
     */
    public static function resetModuleVars()
    {
        self::$_aModuleVars = array();

        $sMask = oxRegistry::get("oxConfigFile")->getVar("sCompileDir") . "/" . self::CACHE_FILE_PREFIX . ".*.txt" ;
        $aFiles = glob( $sMask );
        if ( is_array( $aFiles ) ) {
            foreach ( $aFiles as $sFile ) {
                if (is_file($sFile)) {
                    @unlink($sFile);
                }
            }
        }
    }

    /**
     * Creates and returns new object. If creation is not available, dies and outputs
     * error message.
     *
     * @param string $sClassName Name of class
     *
     * @throws oxSystemComponentException in case that class does not exists
     *
     * @return object
     */
    public function oxNew( $sClassName )
    {
        $aArgs = func_get_args();
        array_shift( $aArgs );
        $iArgCnt = count( $aArgs );
        $blCacheObj = $iArgCnt < 2;
        $sClassName = strtolower( $sClassName );

        if ( isset( self::$_aClassInstances[$sClassName] ) ) {
            return self::$_aClassInstances[$sClassName];
        }
        if ( !defined( 'OXID_PHP_UNIT' ) && $blCacheObj ) {
            $sCacheKey  = ( $iArgCnt )?$sClassName.md5( serialize( $aArgs ) ):$sClassName;
            if ( isset( self::$_aInstanceCache[$sCacheKey] ) ) {
                return clone self::$_aInstanceCache[$sCacheKey];
            }
        }

        // performance
        if ( !defined( 'OXID_PHP_UNIT' ) && isset( $this->_aClassNameCache[$sClassName] ) ) {
            $sActionClassName = $this->_aClassNameCache[$sClassName];
        } else {
            $sActionClassName = $this->getClassName( $sClassName );
            //expect __autoload() (oxfunctions.php) to do its job when class_exists() is called
            if ( !class_exists( $sActionClassName ) ) {
                /**
                * @var $oEx oxSystemComponentException
                */
                $oEx = oxNew( "oxSystemComponentException" );
                $oEx->setMessage('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND');
                $oEx->setComponent($sClassName);
                $oEx->debugOut();
                throw $oEx;
            }
            // performance
            $this->_aClassNameCache[$sClassName] = $sActionClassName;
        }

        $oActionObject = $this->_getObject( $sActionClassName, $iArgCnt, $aArgs );
        if ( $blCacheObj && $oActionObject instanceof oxBase ) {
            self::$_aInstanceCache[$sCacheKey] = clone $oActionObject;
        }

        return $oActionObject;
    }

    /**
     * Creates object with dynamic constructor parameters.
     * If parameter count > 5 - exception is thrown
     *
     * @param string $sClassName class name
     * @param int    $iArgCnt    argument count
     * @param array  $aParams    constructor parameters
     *
     * @throws oxSystemComponentException in case parameters count > 5
     *
     * @return mixed
     */
    protected function _getObject( $sClassName, $iArgCnt, $aParams )
    {
        // dynamic creation (if parameter count < 4) gives more performance for regular objects
        switch( $iArgCnt ) {
            case 0:
                $oObj = new $sClassName();
                break;
            case 1:
                $oObj = new $sClassName( $aParams[0] );
                break;
            case 2:
                $oObj = new $sClassName( $aParams[0], $aParams[1] );
                break;
            case 3:
                $oObj = new $sClassName( $aParams[0], $aParams[1], $aParams[2] );
                break;
            default:
                try {
                    // unlimited constructor arguments support
                    $oRo = new ReflectionClass( $sClassName );
                    $oObj = $oRo->newInstanceArgs( $aParams );
                } catch ( ReflectionException $oRefExcp ) {
                    // something went wrong?
                    /**
                     * @var $oEx oxSystemComponentException
                     */
                    $oEx = oxNew( "oxSystemComponentException" );
                    $oEx->setMessage( $oRefExcp->getMessage() );
                    $oEx->setComponent( $sClassName );
                    $oEx->debugOut();
                    throw $oEx;
                }
        }

        return $oObj;
    }

    /**
     * Creates and returns oxArticle (or subclass) object.
     *
     * @param string $sOxID       ID to load subclass type from database
     * @param array  $aProperties array of properties to assign
     *
     * @deprecated since v4.7.5-5.0.5 (2013-03-29); use oxNew
     *
     * @return object
     */
    public function oxNewArticle( $sOxID, $aProperties = array())
    {
        if ( $sOxID && isset( self::$_aLoadedArticles[$sOxID] ) ) {
            return self::$_aLoadedArticles[$sOxID];
        }

        $oActionObject = $this->oxNew( 'oxarticle' );

        // adding object prioperties
        foreach ( $aProperties as $sPropertyName => $sPropertyVal ) {
            $oActionObject->$sPropertyName = $sPropertyVal;
        }

        $oActionObject->load( $sOxID );

        self::$_aLoadedArticles[$sOxID] = $oActionObject;
        return $oActionObject;
    }

    /**
     * Resests instance cache
     *
     * @param string $sClassName class name in the cache
     *
     * @return null;
     */
    public function resetInstanceCache($sClassName = null)
    {
        if ($sClassName && isset(self::$_aInstanceCache[$sClassName])) {
            unset(self::$_aInstanceCache[$sClassName]);
            return;
        }

        //looping due to possible memory "leak".
        if (is_array(self::$_aInstanceCache)) {
            foreach (self::$_aInstanceCache as $sKey => $oInstance) {
                unset(self::$_aInstanceCache[$sKey]);
            }
        }

        self::$_aInstanceCache = array();
    }

    /**
     * Returns generated unique ID.
     *
     * @return string
     */
    public function generateUId()
    {
        return /*substr( $this->getSession()->getId(), 0, 3 ) . */ substr( md5( uniqid( '', true ).'|'.microtime() ), 0, 32 );
    }



    /**
     * Returns name of class file, according to class name.
     *
     * @param string $sClassName Class name
     *
     * @return string
     */
    public function getClassName( $sClassName )
    {
        //$aModules = $this->getConfig()->getConfigParam( 'aModules' );
        $aModules = $this->getModuleVar('aModules');
        $aClassChain = array();


        if (is_array( $aModules )) {

            $aModules = array_change_key_case( $aModules );

            if ( array_key_exists( $sClassName, $aModules ) ) {
                //multiple inheritance implementation
                //in case we have multiple modules:
                //like oxoutput => sub/suboutput1&sub/suboutput2&sub/suboutput3
                $aClassChain = explode( "&", $aModules[$sClassName] );
                $aClassChain = $this->_getActiveModuleChain( $aClassChain );
            }

            if (count($aClassChain)) {
                $sParent = $sClassName;

                //security: just preventing string termination
                $sParent = str_replace(chr(0), '', $sParent);

                //building middle classes if needed
                $sClassName = $this->_makeSafeModuleClassParents( $aClassChain, $sParent );
            }
        }

        // check if there is a path, if yes, remove it
        $sClassName = basename( $sClassName );

        return $sClassName;
    }

    /**
     * Checks if module is disabled, added to aDisabledModules config.
     *
     * @param array $aClassChain Module names
     *
     * @return array
     */
    protected function _getActiveModuleChain( $aClassChain )
    {
        $aDisabledModules = $this->getModuleVar( 'aDisabledModules' );
        $aModulePaths     = $this->getModuleVar( 'aModulePaths' );

        if (is_array( $aDisabledModules ) && count($aDisabledModules) > 0) {
            foreach ($aDisabledModules as $sId) {
                $sPath = $aModulePaths[$sId];
                if (!isset($sPath)) {
                    $sPath = $sId;
                }
                foreach ( $aClassChain as $sKey => $sModuleClass ) {
                    if ( strpos($sModuleClass, $sPath."/" ) === 0 ) {
                        unset( $aClassChain[$sKey] );
                    }
                    // If module consists of one file without own dir (getting module.php as id, instead of module)
                    elseif ( strpos( $sPath, "."  ) ) {
                        if ( strpos( $sPath, strtolower( $sModuleClass ) ) === 0 ) {
                            unset( $aClassChain[$sKey] );
                        }
                    }
                }
            }
        }

        return $aClassChain;
    }

    /**
     * Disables module, adds to aDisabledModules config.
     *
     * @param array $sModule Module name
     *
     * @return null
     */
    protected function _disableModule( $sModule )
    {
        /**
         * @var oxModule $oModule
         */
        $oModule = oxNew("oxModule");
        $sModuleId = $oModule->getIdByPath($sModule);
        $oModule->deactivate($sModuleId);
    }

    /**
     * Creates middle classes if needed.
     *
     * @param array  $aClassChain Module names
     * @param string $sBaseModule Oxid base class
     *
     * @throws oxSystemComponentException missing system component exception
     *
     * @return string
     */
    protected function _makeSafeModuleClassParents( $aClassChain, $sBaseModule )
    {
        $sParent = $sBaseModule;
        $sClassName = $sBaseModule;

        //building middle classes if needed
        foreach ($aClassChain as $sModule) {
            //creating middle classes
            //e.g. class suboutput1_parent extends oxoutput {}
            //     class suboutput2_parent extends suboutput1 {}
            //$sModuleClass = $this->getClassName($sModule);

            //security: just preventing string termination
            $sModule = str_replace(chr(0), '', $sModule);

            //get parent and module class names from sub/suboutput2
            $sModuleClass = basename($sModule);

            if ( !class_exists( $sModuleClass, false ) ) {
                $sParentClass       = basename($sParent);
                $sModuleParentClass = $sModuleClass."_parent";

                //initializing middle class
                if (!class_exists($sModuleParentClass, false)) {
                    // If possible using alias instead if eval (since php 5.3).
                    if (function_exists('class_alias')) {
                        class_alias($sParentClass, $sModuleParentClass);
                    } else {
                        eval("abstract class $sModuleParentClass extends $sParentClass {}");
                    }
                }
                $sParentPath = oxRegistry::get( "oxConfigFile" )->getVar( "sShopDir" ) . "/modules/".$sModule.".php";

                //including original file
                if ( file_exists( $sParentPath ) ) {
                    include_once $sParentPath;
                } elseif ( !class_exists( $sModuleClass ) ) {
                    // special case is when oxconfig class is extended: we cant call "_disableModule" as it requires valid config object
                    // but we can't create it as module class extending it does not exist. So we will use orginal oxConfig object instead.
                    if ( $sParentClass == "oxconfig" ) {
                        $oConfig = $this->_getObject( "oxconfig", 0, null );
                        oxRegistry::set( "oxconfig", $oConfig );
                    }

                    // disable module if extended class is not found
                    $blDisableModuleOnError = !oxRegistry::get( "oxConfigFile" )->getVar( "blDoNotDisableModuleOnError" );
                    if ( $blDisableModuleOnError ) {
                        $this->_disableModule( $sModule );
                    } else {
                        //to avoid problems with unitest and only throw a exception if class does not exists MAFI
                        $oEx = oxNew( "oxSystemComponentException" );
                        $oEx->setMessage("EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND");
                        $oEx->setComponent( $sModuleClass );
                        throw $oEx;
                    }
                    continue;
                }
            }
            $sParent = $sModule;
            $sClassName = $sModule;
        }

        //returning the last module from the chain
        return $sClassName;
    }

    /**
     * Returns active shop id. This method works independently from other classes.
     *
     * @return string
     */
    public function getShopId()
    {
            return 'oxbaseshop';
    }

    /**
     * Retrieves module configuration variable for the base shop.
     * Currently getModuleVar() is expected to be called with one of the values: aModules | aDisabledModules | aModulePaths
     * This method is independent from oxConfig functionality.
     *
     * @param string $sModuleVarName Configuration array name
     *
     * @return array
     */
    public function getModuleVar( $sModuleVarName )
    {
        //static cache
        if ( isset(self::$_aModuleVars[$sModuleVarName]) ) {
            return self::$_aModuleVars[$sModuleVarName];
        }

        //first try to get it from cache
        //we do not use any of our cache APIs, as we want to prevent any class dependencies here
        $aValue = $this->_getFromCache( $sModuleVarName );

        if ( is_null( $aValue ) ) {
            $aValue = $this->_getModuleVarFromDB($sModuleVarName);
            $this->_setToCache( $sModuleVarName, $aValue);
        }

        //static cache
        self::$_aModuleVars[$sModuleVarName] = $aValue;

        return $aValue;
    }

    /**
     * Sets module information variable. The variable is set statically and is not saved for future.
     *
     * @param string $sModuleVarName Configuration array name
     * @param array  $aValues        Module name values
     *
     * @return null
     */
    public function setModuleVar( $sModuleVarName, $aValues )
    {
        if ( is_null( $aValues) ) {
            self::$_aModuleVars = null;
        } else {
            self::$_aModuleVars[$sModuleVarName] = $aValues;
        }

        $this->_setToCache($sModuleVarName, $aValues);
    }

    /**
     * Returns configuration key. This method is independent from oxConfig functionality.
     *
     * @return string
     */
    protected function _getConfKey()
    {
        $sFileName = dirname( __FILE__ ) . "/oxconfk.php";
        $sCfgFile = new oxConfigFile( $sFileName );
        return $sCfgFile->getVar("sConfigKey");
    }

    /**
     * Returns shop url to id map from config.
     *
     * @return array
     */
    protected function _getShopUrlMap( )
    {

        //get from static cache
        if (isset( self::$_aModuleVars["urlMap"] )) {
            return self::$_aModuleVars["urlMap"];
        }

        //get from file cache
        $aMap = $this->_getFromCache("urlMap", false);
        if (!is_null($aMap)) {
            self::$_aModuleVars["urlMap"] = $aMap;
            return $aMap;
        }

        $aMap = array();

        $oDb = oxDb::getDb();
        $sConfKey = $this->_getConfKey();

        $sSelect = "SELECT oxshopid, oxvarname, DECODE( oxvarvalue , ".$oDb->quote($sConfKey)." ) as oxvarvalue ".
                   "FROM oxconfig WHERE oxvarname in ('aLanguageURLs','sMallShopURL','sMallSSLShopURL')";

        $oRs = $oDb->select($sSelect, false, false);

        if ( $oRs && $oRs->recordCount() > 0) {
            while ( !$oRs->EOF ) {
                $iShp = (int) $oRs->fields[0];
                $sVar = $oRs->fields[1];
                $sURL = $oRs->fields[2];

                if ($sVar == 'aLanguageURLs') {
                    $aUrls = unserialize($sURL);
                    if (is_array($aUrls) && count($aUrls)) {
                        $aUrls = array_filter($aUrls);
                        $aUrls = array_fill_keys($aUrls, $iShp);
                        $aMap  = array_merge($aMap, $aUrls);
                    }
                } elseif ($sURL) {
                    $aMap[$sURL] = $iShp;
                }

                $oRs->moveNext();
            }
        }

        //save to cache
        $this->_setToCache("urlMap", $aMap, false);
        self::$_aModuleVars["urlMap"] = $aMap;

        return $aMap;
    }

    /**
     * Gets cache directory
     *
     * @return string
     */
    protected function _getCacheDir()
    {
        $sDir = oxRegistry::get("oxConfigFile")->getVar("sCompileDir");
        return $sDir;
    }

    /**
     * Returns module file cache name.
     *
     * @param string $sModuleVarName Module variable name
     * @param int    $sShopId        Shop id
     *
     * @return string
     */
    protected function _getCacheFileName($sModuleVarName, $sShopId = null)
    {
        if (is_null($sShopId)) {
            $sShopId = $this->getShopId();
        }

        $sDir = $this->_getCacheDir();
        $sVar  = strtolower( basename ($sModuleVarName) );
        $sShop = strtolower( basename ($sShopId) );

        $sFileName = $sDir . "/" . self::CACHE_FILE_PREFIX . "." . $sShop . '.' . $sVar . ".txt";

        return $sFileName;
    }

    /**
     * Returns shop module variable value directly from database.
     *
     * @param string $sModuleVarName Module variable name
     *
     * @return string
     */
    protected function _getModuleVarFromDB( $sModuleVarName )
    {
        $oDb = oxDb::getDb();

        $sShopId  = $this->getShopId();
        $sConfKey = $this->_getConfKey();

        $sSelect = "SELECT DECODE( oxvarvalue , ".$oDb->quote($sConfKey)." ) FROM oxconfig ".
                   "WHERE oxvarname = ".$oDb->quote( $sModuleVarName )." AND oxshopid = ".$oDb->quote($sShopId);

        $sModuleVarName = $oDb->getOne($sSelect, false, false);

        $sModuleVarName = unserialize( $sModuleVarName );

        return $sModuleVarName;
    }

    /**
     * Returns shop module variable value from cache.
     * This method is independent from oxConfig class and does not use database.
     *
     * @param string $sModuleVarName    Module variable name
     * @param bool   $blSubshopSpecific Indicates should cache be shop specific or not
     *
     * @return string
     */
    protected function _getFromCache( $sModuleVarName, $blSubshopSpecific = true )
    {
        $sShopId = null;
        if ( !$blSubshopSpecific ) {
            $sShopId = "all";
        }

        $sFileName = $this->_getCacheFileName($sModuleVarName, $sShopId);
        $sValue = null;
        if ( is_readable( $sFileName ) ) {
            $sValue = file_get_contents( $sFileName );
            if ( $sValue == serialize( false ) ) {
                return false;
            }

            $sValue = unserialize( $sValue );
            if ( $sValue === false ) {
                $sValue = null;
            }
        }

        return $sValue ;
    }

    /**
     * Writes shop module variable information to cache.
     *
     * @param string $sVarName          Variable name
     * @param string $sValue            Variable value.
     * @param bool   $blSubshopSpecific Indicates should cache be shop specific or not
     *
     * @return null;
     */
    protected function _setToCache( $sVarName, $sValue,  $blSubshopSpecific = true )
    {
        $sShopId = null;
        if ( !$blSubshopSpecific ) {
            $sShopId = "all";
        }

        $sFileName = $this->_getCacheFileName($sVarName, $sShopId);
        file_put_contents( $sFileName, serialize($sValue), LOCK_EX );
    }

}
