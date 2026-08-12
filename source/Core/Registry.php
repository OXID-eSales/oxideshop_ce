<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

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
    protected static $instances = [];

    /**
     * Instance getter. Return an existing or new instance for a given class name.
     * Consider using the getter methods over the generic Registry::get() method.
     * In order to avoid issues with different shop editions, the given class name must be from the Unified Namespace.
     *
     * @template T
     * @param class-string<T> $className The class name from the Unified Namespace.
     * param mixed  ...$args   constructor arguments
     *
     * @static
     *
     * @return T
     */
    public static function get($className)
    {
        return self::getObject($className);
    }

    /**
     * Instance setter
     *
     * @param string       $className Class name
     * @param null|object  $instance  Object instance
     *
     * @static
     *
     * @return null
     */
    public static function set($className, $instance)
    {
        if (is_null($instance)) {
            unset(self::$instances[$className]);

            return;
        }

        self::$instances[$className] = $instance;

        return;
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\Config
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    public static function getConfig()
    {
        return self::getObject(\OxidEsales\Eshop\Core\Config::class);
    }

    /**
     * Returns an instance of \OxidEsales\Eshop\Core\Session
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\Session
     */
    public static function getSession()
    {
        return self::getObject(\OxidEsales\Eshop\Core\Session::class);
    }

    /**
     * Returns an instance of \OxidEsales\Eshop\Core\Language
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\Language
     */
    public static function getLang()
    {
        return self::getObject(\OxidEsales\Eshop\Core\Language::class);
    }

    /**
     * Returns an instance of \OxidEsales\Eshop\Core\Utils
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\Utils
     */
    public static function getUtils()
    {
        return self::getObject(\OxidEsales\Eshop\Core\Utils::class);
    }

    /**
     * Returns an instance of OxidEsales\Eshop\Core\UtilsObject
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsObject
     */
    public static function getUtilsObject()
    {
        return \OxidEsales\Eshop\Core\UtilsObject::getInstance();
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\InputValidator
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\InputValidator
     */
    public static function getInputValidator()
    {
        return self::getObject(\OxidEsales\Eshop\Core\InputValidator::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\PictureHandler
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\PictureHandler
     */
    public static function getPictureHandler()
    {
        return self::getObject(\OxidEsales\Eshop\Core\PictureHandler::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\Request
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\Request
     */
    public static function getRequest()
    {
        return self::getObject(\OxidEsales\Eshop\Core\Request::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\SeoEncoder
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\SeoEncoder
     */
    public static function getSeoEncoder()
    {
        return self::getObject(\OxidEsales\Eshop\Core\SeoEncoder::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\SeoDecoder
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\SeoDecoder
     */
    public static function getSeoDecoder()
    {
        return self::getObject(\OxidEsales\Eshop\Core\SeoDecoder::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsCount
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsCount
     */
    public static function getUtilsCount()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsCount::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsDate
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsDate
     */
    public static function getUtilsDate()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsDate::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsFile
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsFile
     */
    public static function getUtilsFile()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsFile::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsPic
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsPic
     */
    public static function getUtilsPic()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsPic::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsServer
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsServer
     */
    public static function getUtilsServer()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsServer::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsString
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsString
     */
    public static function getUtilsString()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsString::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsUrl
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsUrl
     */
    public static function getUtilsUrl()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsUrl::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsXml
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsXml
     */
    public static function getUtilsXml()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsXml::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\UtilsView
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\UtilsView
     */
    public static function getUtilsView()
    {
        return self::getObject(\OxidEsales\Eshop\Core\UtilsView::class);
    }

    /**
     * Return an instance of \OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver
     *
     * @static
     *
     * @return \OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver
     */
    public static function getControllerClassNameResolver()
    {
        return self::getObject(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class);
    }

    /**
     * Returns Logger
     *
     * @static
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger()
    {
        if (!self::instanceExists('logger')) {
            self::set('logger', getLogger());
        }
        return self::get('logger');
    }

    /**
     * Return all class instances, which are currently set in the registry
     *
     * @return array
     */
    public static function getKeys()
    {
        return array_keys(self::$instances);
    }

    /**
     * Check if an instance of a given class is set in the registry
     *
     * @param string $className
     *
     * @return bool
     */
    public static function instanceExists($className)
    {
        return isset(self::$instances[$className]);
    }

    /**
     * Special case handling: The recommended way to get an instance of UtilsObject is to use Registry::getUtilsObject
     * IMPORTANT: UtilsObject is not delivered from Registry::instances this way, so Registry::set
     *            will have no effect on which UtilsObject is delivered.
     *            Also Registry::instanceExists will always return false for UtilsObject.
     *
     * @param string $className Class name.
     *
     * @return object
     */
    protected static function createObject($className)
    {
        if (\OxidEsales\Eshop\Core\UtilsObject::class === $className) {
            $object = \OxidEsales\Eshop\Core\UtilsObject::getInstance();
        } else {
            $object = \oxNew($className);
        }

        return $object;
    }

    /**
     * Return a well known object from the registry
     *
     * @template T
     * @param class-string<T> $className A unified namespace class name
     * param mixed  ...$args   constructor arguments
     *
     * @static
     *
     * @return T
     */
    protected static function getObject($className)
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = self::createObject($className);
        }

        return self::$instances[$className];
    }
}
