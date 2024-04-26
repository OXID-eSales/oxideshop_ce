<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Autoload;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache;

/**
 * Autoloader for module classes and extensions.
 *
 * @internal Do not make a module extension for this class.
 */
class ModuleAutoload
{
    /** @var array Classes, for which extension class chain was created. */
    public $triedClasses = [];

    /**
     * @var null|ModuleAutoload A singleton instance of this class or a sub class of this class
     */
    private static $instance = null;

    /**
     * ModuleAutoload constructor.
     *
     * Make constructor protected to ensure Singleton pattern
     */
    protected function __construct()
    {
    }

    /**
     * Magic clone method.
     *
     * Make method private to ensure Singleton pattern
     */
    private function __clone()
    {
    }

    /**
     * Tries to autoload given class. Searches for the class in module extensions.
     *
     * @param string $class Class name.
     *
     * @return bool
     */
    public static function autoload($class)
    {
        /**
         * Classes from unified namespace cannot be loaded by this auto loader.
         * Do not try to load them in order to avoid strange errors in edge cases.
         */
        if (false !== strpos($class, 'OxidEsales\Eshop\\')) {
            return false;
        }

        $instance = static::getInstance();
        $class = strtolower(basename($class));
        $class = preg_replace('/_parent$/i', '', $class);

        if (!in_array($class, $instance->triedClasses)) {
            $instance->triedClasses[] = $class;
            $instance->createExtensionClassChain($class);
        }
    }

    /**
     * Returns the singleton instance of this class or of a sub class of this class.
     *
     * @return ModuleAutoload The singleton instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * When module is extending other module's extension (module class, which is extending shop class),
     * this class comes to autoload and class chain has to be created.
     *
     * @param string $class
     */
    protected function createExtensionClassChain($class)
    {
        $extensions = $this->getModuleVariablesLocator()
            ->getModuleVariable('aModules');

        if (is_array($extensions)) {
            $class = preg_quote($class, '/');

            foreach ($extensions as $parentClass => $extensionPaths) {
                foreach ($extensionPaths as $extensionPath) {
                    if (preg_match('/\b' . $class . '($|\&)/i', $extensionPath)) {
                        Registry::getUtilsObject()->getClassName($parentClass);
                        break;
                    }
                }
            }
        }
    }

    private function getModuleVariablesLocator(): ModuleVariablesLocator
    {
        $shopIdCalculator = new ShopIdCalculator(
            new FileCache()
        );
        $subShopSpecificCache = new SubShopSpecificFileCache($shopIdCalculator);
        return new ModuleVariablesLocator($subShopSpecificCache, $shopIdCalculator);
    }
}
