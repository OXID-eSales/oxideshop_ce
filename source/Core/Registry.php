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
        $key = strtolower($className);
        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        } else {
            self::$instances[$key] = oxNew($className);

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
        $className = strtolower($className);

        if (is_null($instance)) {
            unset(self::$instances[$className]);

            return;
        }

        self::$instances[$className] = $instance;
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
        return isset(self::$instances[strtolower($className)]);
    }
}
