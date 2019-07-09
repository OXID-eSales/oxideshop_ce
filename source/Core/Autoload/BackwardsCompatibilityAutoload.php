<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Autoload;

/**
 * This class autoloads backwards compatible classes by triggering the composer autoloader via a unified namespace
 * class.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class BackwardsCompatibilityAutoload
{
    /**
     * Autoload method.
     *
     * @param string $class Name of the class to be loaded
     *
     * @return bool
     */
    public static function autoload($class)
    {
        /**
         * Classes from unified namespace canot be loaded by this auto loader.
         * Do not try to load them in order to avoid strange errors in edge cases.
         */
        if (false !== strpos($class, 'OxidEsales\Eshop\\')) {
            return false;
        }

        $unifiedNamespaceClassName = static::getUnifiedNamespaceClassForBcAlias($class);
        if (!empty($unifiedNamespaceClassName)) {
            static::forceBackwardsCompatiblityClassLoading($unifiedNamespaceClassName);
        }
    }

    /**
     * Return the name of a Unified Namespace class for a given backwards compatible class
     *
     * @param string $bcAlias Name of the backwards compatible class like oxArticle
     *
     * @return string Name of the unified namespace class like OxidEsales\Eshop\Application\Model\Article
     */
    private static function getUnifiedNamespaceClassForBcAlias($bcAlias)
    {
        $classMap = static::getBackwardsCompatibilityClassMap();
        $bcAlias = strtolower($bcAlias);
        $result = isset($classMap[$bcAlias]) ? $classMap[$bcAlias] : "";

        return $result;
    }

    /**
     * This triggers loading the unified namespace class via composer autoloader and also the
     * aliasing of the backwards compatible class.
     *
     * @param string $class Name of the class to load
     */
    private static function forceBackwardsCompatiblityClassLoading($class)
    {
        class_exists($class);
    }

    /**
     * Return the backwards compatible class map.
     *
     * @return array Mapping of Unified Namespace to backwards compatible classes.
     */
    private static function getBackwardsCompatibilityClassMap()
    {
        return (new BackwardsCompatibilityClassMapProvider())->getMap();
    }
}
