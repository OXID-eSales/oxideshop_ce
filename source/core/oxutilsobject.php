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

/**
 * Object Factory implementation (oxNew() method is implemented in this class).
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxUtilsObject
{
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

    /** @var oxEditionCodeHandler */
    private $editionCodeHandler;

    /** @var oxModuleChainsGenerator */
    private $moduleChainsGenerator;

    /** @var oxShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param oxEditionCodeHandler    $editionCodeHandler
     * @param oxModuleChainsGenerator $moduleChainsGenerator
     * @param oxShopIdCalculator      $shopIdCalculator
     */
    public function __construct($editionCodeHandler = null, $moduleChainsGenerator = null, $shopIdCalculator = null)
    {
        if (!$editionCodeHandler) {
            $editionCodeHandler = new oxEditionCodeHandler();
        }
        $this->editionCodeHandler = $editionCodeHandler;

        if (!$shopIdCalculator) {
            $moduleVariablesCache = new oxFileCache();
            $shopIdCalculator = new oxShopIdCalculator($moduleVariablesCache);
        }
        $this->shopIdCalculator = $shopIdCalculator;

        if (!$moduleChainsGenerator) {
            $subShopSpecificCache = new oxSubShopSpecificFileCache($shopIdCalculator);
            $moduleVariablesLocator = new oxModuleVariablesLocator($subShopSpecificCache, $shopIdCalculator);
            $moduleChainsGenerator = new oxModuleChainsGenerator($moduleVariablesLocator);
        }
        $this->moduleChainsGenerator = $moduleChainsGenerator;
    }

    /**
     * Returns object instance
     *
     * @return oxUtilsObject
     */
    public static function getInstance()
    {
        // disable caching for test modules
        if (defined('OXID_PHP_UNIT')) {
            self::$_instance = null;
        }

        if (!self::$_instance instanceof oxUtilsObject) {
            // allow modules
            $oUtilsObject = new oxUtilsObject();

            $editionCodeHandler = $oUtilsObject->oxNew('oxEditionCodeHandler');

            $moduleVariablesCache = $oUtilsObject->oxNew('oxFileCache');
            $shopIdCalculator = $oUtilsObject->oxNew('oxShopIdCalculator', $moduleVariablesCache);

            $subShopSpecific = $oUtilsObject->oxNew('oxSubShopSpecificFileCache', $shopIdCalculator);
            $moduleVariablesLocator = $oUtilsObject->oxNew('oxModuleVariablesLocator', $subShopSpecific, $shopIdCalculator);
            $moduleChainsGenerator = $oUtilsObject->oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

            self::$_instance = $oUtilsObject->oxNew('oxUtilsObject', $editionCodeHandler, $moduleChainsGenerator, $shopIdCalculator);
        }

        return self::$_instance;
    }

    /**
     * Factory instance setter. Sets the instance to be returned over later called oxNew().
     * This method is mostly intended to be used by phpUnit tests.
     *
     * @param string $sClassName Class name expected to be later supplied over oxNew
     * @param object $oInstance  Instance object
     */
    public static function setClassInstance($sClassName, $oInstance)
    {
        $sClassName = strtolower($sClassName);
        self::$_aClassInstances[$sClassName] = $oInstance;
    }

    /**
     * Resets previously set instances
     */
    public static function resetClassInstances()
    {
        self::$_aClassInstances = array();
    }

    /**
     * Resets instance cache
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
     * @deprecated
     * @param $sModuleVarName
     *
     * @return array
     */
    public function getModuleVar($sModuleVarName)
    {
        return $this->getModuleChainsGenerator()->getModuleVariablesLocator()->getModuleVar($sModuleVarName);
    }

    /**
     * @deprecated
     * @param $sModuleVarName
     * @param $aValues
     *
     * @return array
     */
    public function setModuleVar($sModuleVarName, $aValues)
    {
        $this->getModuleChainsGenerator()->getModuleVariablesLocator()->setModuleVar($sModuleVarName, $aValues);
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
    public function oxNew($sClassName)
    {
        $aArgs = func_get_args();
        array_shift($aArgs);
        $iArgCnt = count($aArgs);
        $blCacheObj = $this->shouldCacheObject($sClassName, $aArgs);
        $sClassName = strtolower($sClassName);

        if (isset(self::$_aClassInstances[$sClassName])) {
            return self::$_aClassInstances[$sClassName];
        }
        if (!defined('OXID_PHP_UNIT') && $blCacheObj) {
            $sCacheKey = ($iArgCnt) ? $sClassName . md5(serialize($aArgs)) : $sClassName;
            if (isset(self::$_aInstanceCache[$sCacheKey])) {
                return clone self::$_aInstanceCache[$sCacheKey];
            }
        }

        // performance
        if (!defined('OXID_PHP_UNIT') && isset($this->_aClassNameCache[$sClassName])) {
            $sActionClassName = $this->_aClassNameCache[$sClassName];
        } else {
            $sActionClassName = $this->getClassName($sClassName);
            //expect __autoload() (oxfunctions.php) to do its job when class_exists() is called
            if (!class_exists($sActionClassName)) {
                /** @var $oEx oxSystemComponentException */
                $oEx = oxNew("oxSystemComponentException");
                $oEx->setMessage('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND');
                $oEx->setComponent($sClassName);
                $oEx->debugOut();
                throw $oEx;
            }
            // performance
            $this->_aClassNameCache[$sClassName] = $sActionClassName;
        }

        $oActionObject = $this->_getObject($sActionClassName, $iArgCnt, $aArgs);
        if ($blCacheObj && $oActionObject instanceof oxBase) {
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
    protected function _getObject($sClassName, $iArgCnt, $aParams)
    {
        // dynamic creation (if parameter count < 4) gives more performance for regular objects
        switch ($iArgCnt) {
            case 0:
                $oObj = new $sClassName();
                break;
            case 1:
                $oObj = new $sClassName($aParams[0]);
                break;
            case 2:
                $oObj = new $sClassName($aParams[0], $aParams[1]);
                break;
            case 3:
                $oObj = new $sClassName($aParams[0], $aParams[1], $aParams[2]);
                break;
            default:
                try {
                    // unlimited constructor arguments support
                    $oRo = new ReflectionClass($sClassName);
                    $oObj = $oRo->newInstanceArgs($aParams);
                } catch (ReflectionException $oRefExcp) {
                    // something went wrong?
                    /** @var $oEx oxSystemComponentException */
                    $oEx = oxNew("oxSystemComponentException");
                    $oEx->setMessage($oRefExcp->getMessage());
                    $oEx->setComponent($sClassName);
                    $oEx->debugOut();
                    throw $oEx;
                }
        }

        return $oObj;
    }

    /**
     * Returns generated unique ID.
     *
     * @return string
     */
    public function generateUId()
    {
        return /*substr( $this->getSession()->getId(), 0, 3 ) . */
            substr(md5(uniqid('', true) . '|' . microtime()), 0, 32);
    }


    /**
     * Returns name of class file, according to class name.
     *
     * @param string $classAlias Class name
     *
     * @return string
     */
    public function getClassName($classAlias)
    {
        $editionCodeHandler = new oxEditionCodeHandler();

        $class = $editionCodeHandler->getRealClassName($classAlias);

        $class = $this->getModuleChainsGenerator()->createClassChain($class, $classAlias);

        return $class;
    }

    /**
     * Method returns class alias by given class name.
     *
     * @param string $className with namespace.
     *
     * @return string|null
     */
    public function getClassAliasName($className)
    {
        return $this->getEditionCodeHandler()->getClassAliasName($className);
    }
    
    /**
     * Returns shop id.
     *
     * @deprecated use oxConfig::getShopId() or oxShopIdCalculator::getShopId instead.
     *
     * @return string
     */
    public function getShopId()
    {
        return $this->getShopIdCalculator()->getShopId();
    }

    /**
     * Resets module variables cache.
     *
     * @deprecated use oxModuleVariablesLocator::resetModuleVars instead.
     */
    public static function resetModuleVars()
    {
        oxModuleVariablesLocator::resetModuleVariables();
    }

    /**
     * Disables module
     *
     * @param string $sModule
     *
     * @deprecated use oxModuleChainsGenerator::disableModule instead.
     */
    protected function _disableModule($sModule)
    {
        $this->getModuleChainsGenerator()->disableModule($sModule);
    }

    /**
     * @return oxEditionCodeHandler
     */
    protected function getEditionCodeHandler()
    {
        return $this->editionCodeHandler;
    }

    /**
     * @return oxModuleChainsGenerator
     */
    protected function getModuleChainsGenerator()
    {
        return $this->moduleChainsGenerator;
    }

    /**
     * @return oxShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        return $this->shopIdCalculator;
    }

    /**
     * Checks whether class with arguments should be cached.
     * Cache only when object has none or one scalar argument.
     *
     * @param string $className
     * @param array $arguments
     *
     * @return bool
     */
    protected function shouldCacheObject($className, $arguments)
    {
        return count($arguments) < 2 && (!isset($arguments[0]) || is_scalar($arguments[0]));
    }
}
