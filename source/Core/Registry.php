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

use oxConfig;
use oxLang;
use oxSession;
use oxUtils;

/**
 * Object registry design pattern implementation. Stores the instances of objects
 */
class Registry
{
    /**
     * Instance array
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Hold BC class to virtual namespace class map
     *
     * @var null| array
     */
    protected static $backwardsCompatibilityClassMap = null;

    /**
     * Hold virtual namespace to class map
     *
     * @var null| array
     */
    protected static $virtualNameSpaceClassMap = null;

    /**
     * Instance getter. Return existing instance or initializes the new one
     *
     * @param string $className Class name
     *
     * @static
     *
     * @return object
     */
    public static function get($className)
    {
        $key = self::getStorageKey($className);
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        } else {
            self::$instances[$key] = self::createObject($key, $className);

            return self::$instances[$key];
        }
    }

    /**
     * Instance setter
     *
     * @param string $className Class name
     * @param object $instance  Object instance
     *
     * @static
     *
     * @return null
     */
    public static function set($className, $instance)
    {
        $key = self::getStorageKey($className);

        if (is_null($instance)) {
            unset(self::$instances[$key]);

            return;
        }

        self::$instances[$key] = $instance;
    }

    /**
     * Returns oxConfig instance
     *
     * @static
     *
     * @return oxConfig
     */
    public static function getConfig()
    {
        return self::get("oxConfig");
    }

    /**
     * Returns oxSession instance
     *
     * @static
     *
     * @return oxSession
     */
    public static function getSession()
    {
        return self::get("oxSession");
    }

    /**
     * Returns oxLang instance
     *
     * @static
     *
     * @return oxLang
     */
    public static function getLang()
    {
        return self::get("oxLang");
    }

    /**
     * Returns oxUtils instance
     *
     * @static
     *
     * @return oxUtils
     */
    public static function getUtils()
    {
        return self::get("oxUtils");
    }

    /**
     * Returns UtilsObject instance
     *
     * @static
     *
     * @return OxidEsales\Eshop\Core\UtilsObject
     */
    public static function getUtilsObject()
    {
        return self::get('oxUtilsObject');
    }

    /**
     * Returns ControllerClassNameProvider instance
     *
     * @static
     *
     * @return OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver
     */
    public static function getControllerClassNameResolver()
    {
        return self::get(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class);
    }

    /**
     * Return set instances.
     *
     * @return array
     */
    public static function getKeys()
    {
        return array_keys(self::$instances);
    }

    /**
     * Checks if instance with given key is set.
     *
     * @param string $className
     *
     * @return bool
     */
    public static function instanceExists($className)
    {
        $key = self::getStorageKey($className);
        return isset(self::$instances[$key]);
    }

    /**
     * Get backwardsCompatibilityClassMap
     *
     * @return array
     */
    public static function getBackwardsCompatibilityClassMap()
    {
        if (is_null(self::$backwardsCompatibilityClassMap)) {
            $classMap = include CORE_AUTOLOADER_PATH . 'BackwardsCompatibilityClassMap.php';
            self::$backwardsCompatibilityClassMap = array_flip(array_map('strtolower', $classMap));
        }

        return self::$backwardsCompatibilityClassMap;
    }

    /**
     * @return array
     */
    public static function getVirtualNameSpaceClassMap()
    {
        if (is_null(self::$virtualNameSpaceClassMap)) {
            $classMap = new \OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMap;
            self::$virtualNameSpaceClassMap = $classMap->getClassMap();
        }

        return self::$virtualNameSpaceClassMap;
    }

    /**
     * Figure out which key to use for instance cache.
     *
     * @param string $className
     *
     * @return string
     */
    public static function getStorageKey($className)
    {
        $key = strtolower($className);
        if (!\OxidEsales\EshopCommunity\Core\UtilsObject::isNamespacedClass($className)) {
            $bcMap = self::getBackwardsCompatibilityClassMap();
            $virtualKey = isset($bcMap[$key]) ? $bcMap[$key] : $key;
            $key = $virtualKey;
        }
        return strtolower($key);
    }

    /**
     * Special case handling: The recommended way to get an instance of OxUtilsObject is to use Registry::getUtilsObject
     * IMPORTANT: the utilsobject is not delivered from Registry::instances this way, so Registry::set
     *           will have no effect on which UtilsObject is delivered.
     *           Also Registry::instanceExists will always return false for UtilsObject.
     * This does only affect BC classname and virtual namespace, not the edition own classes atm.
     *
     * @param string $key       Class key used for instance caching.
     * @param string $className Class name.
     *
     * @return object
     */
    protected static function createObject($key, $className)
    {
        if (('oxutilsobject' == $key) || (strtolower(\OxidEsales\Eshop\Core\UtilsObject::class) == $key)) {
            $object = \OxidEsales\Eshop\Core\UtilsObject::getInstance();
        } else {
            $object = oxNew($className);
        }
        return $object;
    }
}
