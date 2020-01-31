<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use oxSystemComponentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Basic class which is used as parent class by other OXID eShop classes.
 * It provides access to some basic objects and some basic functionality.
 */
class Base
{
    /**
     * oxuser object
     *
     * @var \OxidEsales\Eshop\Application\Model\User
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
     * @param string $method    Methods name
     * @param array  $arguments Argument array
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
                return call_user_func_array([& $this, $method], $arguments);
            }
        }

        throw new \OxidEsales\Eshop\Core\Exception\SystemComponentException("Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL);
    }

    /**
     * Class constructor. The constructor is defined in order to be possible to call parent::__construct() in modules.
     *
     * @return null
     */
    public function __construct()
    {
    }

    /**
     * Active user getter
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    public function getUser()
    {
        if (self::$_oActUser === null) {
            self::$_oActUser = false;
            $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            if ($user->loadActiveUser()) {
                self::$_oActUser = $user;
            }
        }

        return self::$_oActUser;
    }

    /**
     * Active oxuser object setter
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user user object
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

    /**
     * Dispatch given event.
     *
     * @param \Symfony\Contracts\EventDispatcher\Event $event Event to dispatch
     *
     * @return \Symfony\Contracts\EventDispatcher\Event
     */
    public function dispatchEvent(\Symfony\Contracts\EventDispatcher\Event $event)
    {
        $container = \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
        $dispatcher = $container->get(EventDispatcherInterface::class);
        return $dispatcher->dispatch($event, $event::NAME);
    }

    /**
     * @internal
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
