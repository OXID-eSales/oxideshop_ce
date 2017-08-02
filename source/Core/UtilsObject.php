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

use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Module\ModuleChainsGenerator;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;

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
     * This class is a singleton and should be instantiated with getInstance().
     *
     * @deprecated in v6.0 (2017-02-27) The constructor will be protected in the future. Use getInstance() instead.
     *
     * @param BackwardsCompatibleClassNameProvider $classNameProvider
     * @param ModuleChainsGenerator                $moduleChainsGenerator
     * @param ShopIdCalculator                     $shopIdCalculator
     */
    public function __construct($classNameProvider = null, $moduleChainsGenerator = null, $shopIdCalculator = null)
    {
        $this->classNameProvider = $classNameProvider;
        $this->shopIdCalculator = $shopIdCalculator;
        $this->moduleChainsGenerator = $moduleChainsGenerator;
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
        static::$_aClassInstances = array();
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

        static::$_aInstanceCache = array();
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
     * @throws SystemComponentException in case that class does not exists
     *
     * @return object
     */
    public function oxNew($className)
    {
        $arguments = func_get_args();
        array_shift($arguments);
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
                $exception->debugOut();
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
     * Returns shop id.
     *
     * @deprecated use \OxidEsales\Eshop\Core\Config::getShopId() or \OxidEsales\Eshop\Core\ShopIdCalculator::getShopId instead.
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
