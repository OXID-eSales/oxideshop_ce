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

namespace OxidEsales\EshopCommunity\Core;

use oxBase;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use oxSystemComponentException;
use oxUtilsObject;
use ReflectionClass;
use ReflectionException;

/**
 * Object Factory implementation (oxNew() method is implemented in this class).
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class UtilsObject
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
     * @var UtilsObject instance
     */
    private static $_instance = null;

    /** @var ClassNameProvider */
    private $classNameProvider;

    /** @var ModuleChainsGenerator */
    private $moduleChainsGenerator;

    /** @var ShopIdCalculator */
    private $shopIdCalculator;

    /**
     * @param ClassNameProvider     $classNameProvider
     * @param ModuleChainsGenerator $moduleChainsGenerator
     * @param ShopIdCalculator      $shopIdCalculator
     */
    public function __construct($classNameProvider = null, $moduleChainsGenerator = null, $shopIdCalculator = null)
    {
        if (!$classNameProvider) {
            $classMapProvider = new ClassMapProvider(new EditionSelector());
            $classNameProvider = new ClassNameProvider($classMapProvider->getOverridableClassMap());
        }
        $this->classNameProvider = $classNameProvider;

        if (!$shopIdCalculator) {
            $moduleVariablesCache = new FileCache();

            $editionSelector = new EditionSelector();

            if ($editionSelector->getEdition() === $editionSelector::ENTERPRISE) {
                $shopIdCalculator = new \OxidEsales\EshopEnterprise\Core\ShopIdCalculator($moduleVariablesCache);
            } else {
                $shopIdCalculator = new ShopIdCalculator($moduleVariablesCache);
            }
        }
        $this->shopIdCalculator = $shopIdCalculator;

        if (!$moduleChainsGenerator) {
            $subShopSpecificCache = new SubShopSpecificFileCache($shopIdCalculator);
            $moduleVariablesLocator = new ModuleVariablesLocator($subShopSpecificCache, $shopIdCalculator);
            $moduleChainsGenerator = new ModuleChainsGenerator($moduleVariablesLocator);
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

        if (!self::$_instance instanceof UtilsObject) {
            $oUtilsObject = new UtilsObject();
            // set the not overloaded(by modules) version early so oxnew can be used internally
            self::$_instance = $oUtilsObject;
            // null for classNameProvider because it is generated in the constructor
            $classNameProvider = null;

            $moduleVariablesCache = $oUtilsObject->oxNew('oxFileCache');
            $shopIdCalculator = $oUtilsObject->oxNew('oxShopIdCalculator', $moduleVariablesCache);

            $subShopSpecific = $oUtilsObject->oxNew('oxSubShopSpecificFileCache', $shopIdCalculator);
            $moduleVariablesLocator = $oUtilsObject->oxNew('oxModuleVariablesLocator', $subShopSpecific, $shopIdCalculator);
            $moduleChainsGenerator = $oUtilsObject->oxNew('oxModuleChainsGenerator', $moduleVariablesLocator);

            //generate UtilsObject again by oxnew to allow overloading by modules
            self::$_instance = $oUtilsObject->oxNew('oxUtilsObject', $classNameProvider, $moduleChainsGenerator, $shopIdCalculator);
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
        if (!self::isNamespacedClass($className)) {
            $className = strtolower($className);
        }
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
     * @deprecated use ModuleVariablesLocator::getModuleVariable()
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
     * @deprecated use ModuleVariablesLocator::setModuleVariable()
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
        if (!self::isNamespacedClass($className)) {
            $className = strtolower($className);
        }

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
                /** @var $exception oxSystemComponentException */
                $exception = oxNew("oxSystemComponentException");
                $exception->setMessage('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND');
                $exception->setComponent($className);
                $exception->debugOut();
                throw $exception;
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
        return md5(uniqid('', true) . '|' . microtime());
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
        $classNameProvider = $this->getClassNameProvider();

        $class = $classNameProvider->getClassName($classAlias);

        return $this->getModuleChainsGenerator()->createClassChain($class, $classAlias);
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
        return $this->getClassNameProvider()->getClassAliasName($className);
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
     * @deprecated use ModuleVariablesLocator::resetModuleVars instead.
     */
    public static function resetModuleVars()
    {
        ModuleVariablesLocator::resetModuleVariables();
    }

    /**
     * Disables module
     *
     * @param string $sModule
     *
     * @deprecated use ModuleChainsGenerator::disableModule instead.
     */
    protected function _disableModule($sModule)
    {
        $this->getModuleChainsGenerator()->disableModule($sModule);
    }

    /**
     * @return ClassNameProvider
     */
    protected function getClassNameProvider()
    {
        return $this->classNameProvider;
    }

    /**
     * @return ModuleChainsGenerator
     */
    protected function getModuleChainsGenerator()
    {
        return $this->moduleChainsGenerator;
    }

    /**
     * @return ShopIdCalculator
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
     * @param array  $arguments
     *
     * @return bool
     */
    protected function shouldCacheObject($className, $arguments)
    {
        return count($arguments) < 2 && (!isset($arguments[0]) || is_scalar($arguments[0]));
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private static function isNamespacedClass($className)
    {
        return strpos($className, '\\') !== false;
    }
}
