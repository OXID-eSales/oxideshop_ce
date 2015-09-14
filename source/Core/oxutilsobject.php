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

use OxidEsales\Core\ClassMapProvider;
use OxidEsales\Core\ClassNameProvider;
use OxidEsales\Core\EditionSelector;

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

    /** @var ClassNameProvider */
    private $editionCodeHandler;

    /** @var oxModuleChainsGenerator */
    private $moduleChainsGenerator;

    /** @var oxShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param ClassNameProvider       $editionCodeHandler
     * @param oxModuleChainsGenerator $moduleChainsGenerator
     * @param oxShopIdCalculator      $shopIdCalculator
     */
    public function __construct($editionCodeHandler = null, $moduleChainsGenerator = null, $shopIdCalculator = null)
    {
        if (!$editionCodeHandler) {
            $classMapProvider = new ClassMapProvider(new EditionSelector());
            $editionCodeHandler = new ClassNameProvider($classMapProvider->getOverridableClassMap());
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

            $classMapProvider = new ClassMapProvider(new EditionSelector());
            $editionCodeHandler = new ClassNameProvider($classMapProvider->getOverridableClassMap());

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
     * @param string $className Class name expected to be later supplied over oxNew
     * @param object $instance  Instance object
     */
    public static function setClassInstance($className, $instance)
    {
        $className = strtolower($className);
        self::$_aClassInstances[$className] = $instance;
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
     * @param string $className class name in the cache
     *
     * @return null;
     */
    public function resetInstanceCache($className = null)
    {
        if ($className && isset(self::$_aInstanceCache[$className])) {
            unset(self::$_aInstanceCache[$className]);

            return;
        }

        //looping due to possible memory "leak".
        if (is_array(self::$_aInstanceCache)) {
            foreach (self::$_aInstanceCache as $key => $instance) {
                unset(self::$_aInstanceCache[$key]);
            }
        }

        self::$_aInstanceCache = array();
    }

    /**
     * Returns module variable value from configuration by given name.
     *
     * @param string $variableName
     *
     * @deprecated use oxModuleVariablesLocator::getModuleVariable()
     *
     * @return mixed
     */
    public function getModuleVar($variableName)
    {
        return $this->getModuleChainsGenerator()->getModuleVariablesLocator()->getModuleVariable($variableName);
    }

    /**
     * Sets module variable value to configuration by given name.
     *
     * @param string $variableName
     * @param mixed  $value
     *
     * @deprecated use oxModuleVariablesLocator::setModuleVariable()
     */
    public function setModuleVar($variableName, $value)
    {
        $this->getModuleChainsGenerator()->getModuleVariablesLocator()->setModuleVariable($variableName, $value);
    }

    /**
     * Creates and returns new object. If creation is not available, dies and outputs
     * error message.
     *
     * @param string $className Name of class
     *
     * @throws oxSystemComponentException in case that class does not exists
     *
     * @return object
     */
    public function oxNew($className)
    {
        $arguments = func_get_args();
        array_shift($arguments);
        $argumentsCount = count($arguments);
        $shouldUseCache = $this->shouldCacheObject($className, $arguments);
        $className = strtolower($className);

        if (isset(self::$_aClassInstances[$className])) {
            return self::$_aClassInstances[$className];
        }
        if (!defined('OXID_PHP_UNIT') && $shouldUseCache) {
            $cacheKey = ($argumentsCount) ? $className . md5(serialize($arguments)) : $className;
            if (isset(self::$_aInstanceCache[$cacheKey])) {
                return clone self::$_aInstanceCache[$cacheKey];
            }
        }

        if (!defined('OXID_PHP_UNIT') && isset($this->_aClassNameCache[$className])) {
            $realClassName = $this->_aClassNameCache[$className];
        } else {
            $realClassName = $this->getClassName($className);
            //expect __autoload() (oxfunctions.php) to do its job when class_exists() is called
            if (!class_exists($realClassName)) {
                /** @var $oEx oxSystemComponentException */
                $oEx = oxNew("oxSystemComponentException");
                $oEx->setMessage('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND');
                $oEx->setComponent($className);
                $oEx->debugOut();
                throw $oEx;
            }

            $this->_aClassNameCache[$className] = $realClassName;
        }

        $object = $this->_getObject($realClassName, $argumentsCount, $arguments);
        if ($shouldUseCache && $object instanceof oxBase) {
            self::$_aInstanceCache[$cacheKey] = clone $object;
        }

        return $object;
    }

    /**
     * Creates object with dynamic constructor parameters.
     * If parameter count > 5 - uses reflection class to create object.
     *
     * @param string $className      class name
     * @param int    $argumentsCount argument count
     * @param array  $arguments      constructor parameters
     *
     * @throws oxSystemComponentException in case parameters count > 5
     *
     * @return mixed
     */
    protected function _getObject($className, $argumentsCount, $arguments)
    {
        // dynamic creation (if parameter count < 4) gives more performance for regular objects
        switch ($argumentsCount) {
            case 0:
                $object = new $className();
                break;
            case 1:
                $object = new $className($arguments[0]);
                break;
            case 2:
                $object = new $className($arguments[0], $arguments[1]);
                break;
            case 3:
                $object = new $className($arguments[0], $arguments[1], $arguments[2]);
                break;
            default:
                try {
                    // unlimited constructor arguments support
                    $reflection = new ReflectionClass($className);
                    $object = $reflection->newInstanceArgs($arguments);
                } catch (ReflectionException $reflectionException) {
                    // something went wrong?
                    $systemComponentException = oxNew("oxSystemComponentException");
                    $systemComponentException->setMessage($reflectionException->getMessage());
                    $systemComponentException->setComponent($className);
                    $systemComponentException->debugOut();

                    throw $systemComponentException;
                }
        }

        return $object;
    }

    /**
     * Returns generated unique ID.
     *
     * @return string
     */
    public function generateUId()
    {
        return substr(md5(uniqid('', true) . '|' . microtime()), 0, 32);
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
        $editionCodeHandler = $this->getEditionCodeHandler();

        $class = $editionCodeHandler->getClassName($classAlias);

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
     * @return ClassNameProvider
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
