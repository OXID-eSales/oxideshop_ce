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

namespace OxidEsales\Eshop\Core;

use oxConfig;
use oxRegistry;
use oxSession;
use oxSystemComponentException;
use oxUser;

/**
 * Super config class
 */
class SuperConfig
{
    /**
     * oxsession instance
     *
     * @var oxsession
     */
    protected static $_oSession = null;

    /**
     * oxuser object
     *
     * @var oxuser
     */
    protected static $_oActUser = null;

    /**
     * Admin mode marker
     *
     * @var bool
     */
    protected static $_blIsAdmin = null;

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $method Methods name
     * @param array  $arguments   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessable in current class
     *
     * @return string
     */
    public function __call($method, $arguments)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($method, 0, 4) === 'UNIT') {
                $method = str_replace('UNIT', '_', $method);
            }
            if (method_exists($this, $method)) {
                return call_user_func_array(array(& $this, $method), $arguments);
            }
        }

        throw new oxSystemComponentException("Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL);
    }

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @var oxConfig
     */
    protected $config;

    /**
     * oxSession instance getter
     * @deprecated
     * @return oxsession
     */
    public function getSession()
    {
        if (self::$_oSession == null) {
            self::$_oSession = oxRegistry::getSession();
        }

        return self::$_oSession;
    }

    /**
     * Active user getter
     * @deprecated
     * @return oxUser
     */
    public function getUser()
    {
        if (self::$_oActUser === null) {
            self::$_oActUser = false;
            $user = oxNew('oxuser');
            if ($user->loadActiveUser()) {
                self::$_oActUser = $user;
            }
        }

        return self::$_oActUser;
    }

    /**
     * Admin mode status getter
     * @deprecated
     * @return bool
     */
    public function isAdmin()
    {
        if (self::$_blIsAdmin === null) {
            self::$_blIsAdmin = isAdmin();
        }

        return self::$_blIsAdmin;
    }
}
