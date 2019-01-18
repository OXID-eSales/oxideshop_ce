<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class NamespaceInformationProvider
 *
 * @package OxidEsales\EshopCommunity\Core
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class NamespaceInformationProvider
{
    /**
     * Array contains names of the official OXID eShop edition namespaces.
     *
     * @var array
     */
    protected static $shopEditionNamespaces = [
        'CE' => 'OxidEsales\\EshopCommunity\\',
        'PE' => 'OxidEsales\\EshopProfessional\\',
        'EE' => 'OxidEsales\\EshopEnterprise\\'
    ];

    /**
     * Array contains names of the official OXID eShop edition namespaces for tests.
     *
     * @var array
     */
    protected static $shopEditionTestNamespaces = [
        'CE' => 'OxidEsales\\EshopCommunity\\Tests\\',
        'PE' => 'OxidEsales\\EshopProfessional\\Tests\\',
        'EE' => 'OxidEsales\\EshopEnterprise\\Tests\\'
    ];

    /**
     * OXID eShop unified namespace.
     *
     * @var string
     */
    protected static $unifiedNamespace = 'OxidEsales\\Eshop\\';

    /**
     * Getter for array with official OXID eShop Edition namespaces.
     *
     * @return array
     */
    public static function getShopEditionNamespaces()
    {
        return static::$shopEditionNamespaces;
    }

    /**
     * Getter for official OXID eShop Unified Namespace.
     *
     * @return string
     */
    public static function getUnifiedNamespace()
    {
        return static::$unifiedNamespace;
    }


    /**
     * @param string $className
     *
     * @return bool
     */
    public static function isNamespacedClass($className)
    {
        return strpos($className, '\\') !== false;
    }

    /**
     * Check if given class belongs to a shop edition namespace.
     *
     * @param string $className
     *
     * @return bool
     */
    public static function classBelongsToShopEditionNamespace($className)
    {
        return static::classBelongsToNamespace($className, static::getShopEditionNamespaces());
    }

    /**
     * Check if given class belongs to a shop edition namespace.
     *
     * @param string $className
     *
     * @return bool
     */
    public static function classBelongsToShopUnifiedNamespace($className)
    {
        $lcClassName = strtolower(ltrim($className, '\\'));
        $unifiedNamespace = static::getUnifiedNamespace();
        $belongsToUnifiedNamespace = (false !== strpos($lcClassName, strtolower($unifiedNamespace)));

        return $belongsToUnifiedNamespace;
    }

    /**
     * Check if given class belongs to one of the supplied namespaces.
     *
     * @param string $className
     * @param array  $namespaces
     *
     * @return bool
     */
    private static function classBelongsToNamespace($className, $namespaces)
    {
        $belongsToNamespace = false;
        $check = array_values($namespaces);
        $lcClassName = strtolower(ltrim($className, '\\'));

        foreach ($check as $namespace) {
            if (false !== strpos($lcClassName, strtolower($namespace))) {
                $belongsToNamespace = true;
                continue;
            }
        }
        return $belongsToNamespace;
    }
}
