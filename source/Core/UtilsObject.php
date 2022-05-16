<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Module\ModuleChainsGenerator;

/**
 * Object Factory implementation (oxNew() method is implemented in this class).
 *
 * @internal Do not make a module extension for this class.
 */
class UtilsObject
{
    /**
     * Cache class names
     *
     * @var array
     */
    protected $_aClassNameCache = [];

    /**
     * The array of already loaded articles
     *
     * @var array
     */
    protected static $_aLoadedArticles = [];

    /**
     * The array of already initialised instances
     *
     * @var array
     */
    protected static $_aInstanceCache = [];

    /**
     * Class instance array
     *
     * @var array
     */
    protected static $_aClassInstances = [];

    /**
     * UtilsObject class instance.
     *
     * @var UtilsObject instance
     */
    protected static $_instance = null;

    /** @var BackwardsCompatibleClassNameProvider */
    private $classNameProvider = null;

    /** @var ModuleChainsGenerator */
    private $moduleChainsGenerator = null;

    /** @var ShopIdCalculator */
    private $shopIdCalculator = null;

    /**
     * This class is a singleton and should be instantiated with getInstance()
     */
    private function __construct()
    {
    }

    /**
     * Returns object instance
     *
     * @return UtilsObject
     */
    public static function getInstance()
    {
        // disable caching for test modules
        if (defined('OXID_PHP_UNIT')) {
            static::$_instance = null;
        }

        if (null === static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
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
        //Get storage key as the class might be aliased.
        $storageKey = Registry::getStorageKey($className);

        static::$_aClassInstances[$storageKey] = $instance;
    }

    /**
     * Resets previously set instances
     */
    public static function resetClassInstances()
    {
        static::$_aClassInstances = [];
    }

    /**
     * Resets instance cache
     *
     * @param string $className class name in the cache
     *
     * @return null
     */
    public function resetInstanceCache($className = null)
    {
        if ($className && isset(static::$_aInstanceCache[$className])) {
            unset(static::$_aInstanceCache[$className]);
            return;
        }

        //Get storage key as the class might be aliased.
        $storageKey = Registry::getStorageKey($className);

        if ($className && isset(static::$_aInstanceCache[$storageKey])) {
            unset(static::$_aInstanceCache[$storageKey]);
            return;
        }

        //looping due to possible memory "leak".
        if (is_array(static::$_aInstanceCache)) {
            foreach (static::$_aInstanceCache as $key => $instance) {
                unset(static::$_aInstanceCache[$key]);
            }
        }

        static::$_aInstanceCache = [];
    }

    /**
     * Creates and returns new object. If creation is not available, dies and outputs
     * error message.
     *
     * @param string $className Name of class
     * @param array  $arguments constructor arguments
     *
     * @throws SystemComponentException in case that class does not exists
     *
     * @return object
     */
    public function oxNew($className, ...$arguments)
    {
        $argumentsCount = count($arguments);
        $shouldUseCache = $this->shouldCacheObject($className, $arguments);
        if (!\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass($className)) {
            $className = strtolower($className);
        }

        //Get storage key as the class might be aliased.
        $storageKey = Registry::getStorageKey($className);

        //UtilsObject::$_aClassInstances is only intended to be used in unit tests.
        if (defined('OXID_PHP_UNIT') && isset(static::$_aClassInstances[$storageKey])) {
            return static::$_aClassInstances[$storageKey];
        }
        if (!defined('OXID_PHP_UNIT') && $shouldUseCache) {
            $cacheKey = ($argumentsCount) ? $storageKey . md5(serialize($arguments)) : $storageKey;
            if (isset(static::$_aInstanceCache[$cacheKey])) {
                return clone static::$_aInstanceCache[$cacheKey];
            }
        }

        if (!defined('OXID_PHP_UNIT') && isset($this->_aClassNameCache[$className])) {
            $realClassName = $this->_aClassNameCache[$className];
        } else {
            $realClassName = $this->getClassName($className);
            //expect __autoload() (oxfunctions.php) to do its job when class_exists() is called
            if (!class_exists($realClassName)) {
                $exception =  new \OxidEsales\Eshop\Core\Exception\SystemComponentException();
                /** Use setMessage here instead of passing it in constructor in order to test exception message */
                $exception->setMessage('EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND' . ' ' . $realClassName);
                throw $exception;
            }

            $this->_aClassNameCache[$className] = $realClassName;
        }

        $object = new $realClassName(...$arguments);
        if (isset($cacheKey) && $shouldUseCache && $object instanceof \OxidEsales\Eshop\Core\Model\BaseModel) {
            static::$_aInstanceCache[$cacheKey] = clone $object;
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
        /**
         * Backwards compatibility for ox... classes,
         * when a class is instance build upon the unified namespace
         */
        if ($class == $classAlias) {
            $classAlias = $classNameProvider->getClassAliasName($class);
        }

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
     * @return BackwardsCompatibleClassNameProvider
     */
    protected function getClassNameProvider()
    {
        if (is_null($this->classNameProvider)) {
            $backwardsCompatibleClassMap = include 'Autoload/BackwardsCompatibilityClassMap.php';
            $this->classNameProvider = new BackwardsCompatibleClassNameProvider($backwardsCompatibleClassMap);
        }
        return $this->classNameProvider;
    }

    /**
     * @return ModuleChainsGenerator
     */
    protected function getModuleChainsGenerator()
    {
        if (is_null($this->moduleChainsGenerator)) {
            $subShopSpecificCache = new \OxidEsales\Eshop\Core\SubShopSpecificFileCache($this->getShopIdCalculator());
            $moduleVariablesLocator = new \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator($subShopSpecificCache, $this->getShopIdCalculator());
            $this->moduleChainsGenerator = new \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator($moduleVariablesLocator);
        }
        return $this->moduleChainsGenerator;
    }

    /**
     * @return ShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        if (is_null($this->shopIdCalculator)) {
            $moduleVariablesCache = new \OxidEsales\Eshop\Core\FileCache();
            $this->shopIdCalculator = new \OxidEsales\Eshop\Core\ShopIdCalculator($moduleVariablesCache);
        }
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
}
