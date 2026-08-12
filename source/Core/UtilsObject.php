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
     * UtilsObject class instance.
     *
     * @var UtilsObject instance
     */
    protected static $_instance = null;

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
        static::$_aClassInstances[$className] = $instance;
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

        if ($shouldUseCache) {
            $cacheKey = ($argumentsCount) ? $className . md5(serialize($arguments)) : $className;
            if (isset(static::$_aInstanceCache[$cacheKey])) {
                return clone static::$_aInstanceCache[$cacheKey];
            }
        }

        if (!defined('OXID_PHP_UNIT') && isset($this->_aClassNameCache[$className])) {
            $realClassName = $this->_aClassNameCache[$className];
        } else {
            $realClassName = $this->getClassName($className);
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
     * @deprecated use Id::generate() instead
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
     * @param string $className Class name
     *
     * @return string
     */
    public function getClassName($className)
    {
        return $this->getModuleChainsGenerator()->createClassChain($className);
    }

    /**
     * @return ModuleChainsGenerator
     */
    protected function getModuleChainsGenerator()
    {
        if (is_null($this->moduleChainsGenerator)) {
            $this->moduleChainsGenerator = new \OxidEsales\Eshop\Core\Module\ModuleChainsGenerator();
        }
        return $this->moduleChainsGenerator;
    }

    /**
     * @return ShopIdCalculator
     */
    protected function getShopIdCalculator()
    {
        if (is_null($this->shopIdCalculator)) {
            $this->shopIdCalculator = new ShopIdCalculator(
                new \OxidEsales\Eshop\Core\FileCache(),
                new \OxidEsales\Eshop\Core\UtilsServer()
            );
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
