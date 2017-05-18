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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class EditionSelector
 *
 * @package OxidEsales\EshopCommunity\Core
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
