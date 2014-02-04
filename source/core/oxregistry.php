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
 * Object registry design pattern implementation. Stores the instances of objects
 */
class oxRegistry
{
    /**
     * Instance array
     *
     * @var array
     */
    protected static $_aInstances = array();

    /**
     * Instance getter. Return existing instance or initializes the new one
     *
     * @param string $sClassName Class name
     *
     * @static
     *
     * @return Object
     */
    public static function get( $sClassName )
    {
        $sClassName = strtolower( $sClassName );
        if ( isset( self::$_aInstances[$sClassName] ) ) {
            return self::$_aInstances[$sClassName];
        } else {
            self::$_aInstances[$sClassName] = oxNew( $sClassName );
            return self::$_aInstances[$sClassName];
        }
    }

    /**
     * Instance setter
     *
     * @param string $sClassName Class name
     * @param object $oInstance  Object instance
     *
     * @static
     *
     * @return null
     */
    public static function set( $sClassName, $oInstance )
    {
        $sClassName = strtolower( $sClassName );

        if ( is_null( $oInstance ) ) {
            unset( self::$_aInstances[$sClassName] );
            return;
        }

        self::$_aInstances[$sClassName] = $oInstance;
    }

    /**
     * Returns OxConfig instance
     *
     * @static
     *
     * @return OxConfig
     */
    public static function getConfig()
    {
        return self::get( "oxConfig" );
    }

    /**
     * Returns OxSession instance
     *
     * @static
     *
     * @return OxSession
     */
    public static function getSession()
    {
        return self::get( "oxSession" );
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
        return array_keys( self::$_aInstances );
    }
}