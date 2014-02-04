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
 * Factory class responsible for redirecting string handling functions to specific
 * string handling class. String handler basically is intended for dealing with multibyte string
 * and is NOT supposed to replace all string handling functions.
 * We use the handler for shop data and user input, but prefer not to use it for ascii strings
 * (eg. field or file names).
 *
 */
class oxStr
{
    /**
     * Specific string handler
     *
     * @var object
     */
    static protected $_oHandler;

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null;
     */
    public function __construct()
    {
    }

    /**
     * Static method initializing new string handler or returning the existing one.
     *
     * @return object
     */
    static public function getStr()
    {
        if (!isset(self::$_oHandler)) {
            //let's init now non-static instance of oxStr to get the instance of str handler
            self::$_oHandler = oxNew("oxStr")->_getStrHandler();
        }

        return self::$_oHandler;
    }

    /**
     * Non static getter returning str handler. The sense of getStr() and _getStrHandler() is
     * to be possible to call this method statically ( oxStr::getStr() ), yet leaving the
     * possibility to extend it in modules by overriding _getStrHandler() method.
     *
     * @return object
     */
    protected function _getStrHandler()
    {
        if (oxRegistry::getConfig()->isUtf() && function_exists('mb_strlen')) {
            return oxNew("oxStrMb");
        }

        return oxNew("oxStrRegular");
    }

}
