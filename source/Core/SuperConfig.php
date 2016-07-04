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
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Super config class is the base class of most classes in Oxid
 * it provided some basic access to things like 
 * config,session,current user and rights,admin(in backend) mode
 * usually all objects based on this class should be created with oxNew
 * so dependencies can get injected and classes can be overloaded by modules.
 */
class SuperConfig implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    
    /**
     * oxconfig instance
     *
     * @var oxconfig
     */
    protected static $_oConfig = null;

    /**
     * oxsession instance
     *
     * @var oxsession
     */
    protected static $_oSession = null;

    /**
     * oxrights instance
     *
     * @var oxrights
     */
    protected static $_oRights = null;

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
    public function __construct()
    {
       /* because not all objects that extending this are initialised by oxNew
       the logger can not always be set by dependency injection */
       if (Registry::instanceExists('Logger')){
          $this->logger = Registry::get('Logger');
       }
       // do not use the Logger within this constructor
       // because it can't guaranteed that it was already created
    }

    /**
     * oxConfig instance getter
     *
     * @return oxConfig
     */
    public function getConfig()
    {
        if (self::$_oConfig == null) {
            self::$_oConfig = oxRegistry::getConfig();
        }

        return self::$_oConfig;
    }

    /**
     * oxConfig instance setter
     *
     * @param oxConfig $config config object
     *
     * @return null
     */
    public function setConfig($config)
    {
        self::$_oConfig = $config;
    }

    /**
     * oxSession instance getter
     *
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
     * oxSession instance setter
     *
     * @param oxsession $session session object
     *
     * @return null
     */
    public function setSession($session)
    {
        self::$_oSession = $session;
    }

    /**
     * Active user getter
     *
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
     * Active oxuser object setter
     *
     * @param oxuser $user user object
     *
     * @return null
     */
    public function setUser($user)
    {
        self::$_oActUser = $user;
    }

    /**
     * Admin mode status getter
     *
     * @return bool
     */
    public function isAdmin()
    {
        if (self::$_blIsAdmin === null) {
            self::$_blIsAdmin = isAdmin();
        }

        return self::$_blIsAdmin;
    }

    /**
     * Admin mode setter
     *
     * @param bool $isAdmin admin mode
     */
    public function setAdminMode($isAdmin)
    {
        self::$_blIsAdmin = $isAdmin;
    }
}
