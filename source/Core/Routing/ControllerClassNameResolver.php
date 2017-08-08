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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Routing;

use OxidEsales\Eshop\Core\Contract\ClassNameResolverInterface;
use OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface;

/**
 * This class maps controller id to controller class name and vice versa.
 * It looks up map from ShopControllerMapProvider and if no match is found checks ModuleControllerMapProvider.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ControllerClassNameResolver implements ClassNameResolverInterface
{
    /**
     * @var \OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider
     */
    private $moduleControllerMapProvider = null;

    /**
     * @var \OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider
     */
    private $shopControllerMapProvider = null;

    /**
     * @param \OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider   $shopControllerMapProvider   Shop map.
     * @param \OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider $moduleControllerMapProvider Module map.
     */
    public function __construct(ControllerMapProviderInterface $shopControllerMapProvider = null, ControllerMapProviderInterface $moduleControllerMapProvider = null)
    {
        $this->shopControllerMapProvider = $shopControllerMapProvider;
        $this->moduleControllerMapProvider = $moduleControllerMapProvider;
    }

    /**
     * Map argument classId to related className.
     *
     * @param string $classId
     *
     * @return string|null
     */
    public function getClassNameById($classId)
    {
        $className = $this->getClassNameFromShopMap($classId);

        if (empty($className)) {
            $className = $this->getClassNameFromModuleMap($classId);
        }
        return $className;
    }

    /**
     * Map argument className to related classId.
     *
     * @param string $className
     *
     * @return string|null
     */
    public function getIdByClassName($className)
    {
        $classId = $this->getClassIdFromShopMap($className);

        if (empty($classId)) {
            $classId = $this->getClassIdFromModuleMap($className);
        }
        return $classId;
    }

    /**
     * Get class name from shop controller provider.
     *
     * @param string $classId
     *
     * @return string|null
     */
    protected function getClassNameFromShopMap($classId)
    {
        $shopControllerMapProvider = $this->getShopControllerMapProvider();
        $idToNameMap = $shopControllerMapProvider->getControllerMap();
        $className = $this->arrayLookup($classId, $idToNameMap);

        return $className;
    }

    /**
     * Get class name from module controller provider.
     *
     *  @param string $classId
     *
     * @return string|null
     */
    protected function getClassNameFromModuleMap($classId)
    {
        $moduleControllerMapProvider = $this->getModuleControllerMapProvider();
        $idToNameMap = $moduleControllerMapProvider->getControllerMap();
        $className = $this->arrayLookup($classId, $idToNameMap);

        return $className;
    }

    /**
     * Get class id from shop controller provider.
     *
     * @param string $className
     *
     * @return string|null
     */
    protected function getClassIdFromShopMap($className)
    {
        $shopControllerMapProvider = $this->getShopControllerMapProvider();
        $idToNameMap = $shopControllerMapProvider->getControllerMap();
        $classId = $this->arrayLookup($className, array_flip($idToNameMap));

        return $classId;
    }

    /**
     * Get class id from module controller provider.
     *
     * @param string $className
     *
     * @return string|null
     */
    protected function getClassIdFromModuleMap($className)
    {
        $moduleControllerMapProvider = $this->getModuleControllerMapProvider();
        $idToNameMap = $moduleControllerMapProvider->getControllerMap();
        $classId = $this->arrayLookup($className, array_flip($idToNameMap));

        return $classId;
    }

    /**
     * @param string $key
     * @param array  $keys2Values
     *
     * @return string|null
     */
    protected function arrayLookup($key, $keys2Values)
    {
        $keys2Values = array_change_key_case($keys2Values);
        $key = strtolower($key);
        $match = array_key_exists($key, $keys2Values) ? $keys2Values[$key] : null;

        return $match;
    }

    /**
     * Getter for ShopControllerMapProvider object
     *
     * @return \OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider
     */
    protected function getShopControllerMapProvider()
    {
        if (is_null($this->shopControllerMapProvider)) {
            $this->shopControllerMapProvider = oxNew(\OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider::class);
        }

        return $this->shopControllerMapProvider;
    }

    /**
     * Getter for ModuleControllerMapProvider object
     *
     * @return \OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider
     */
    protected function getModuleControllerMapProvider()
    {
        if (is_null($this->moduleControllerMapProvider)) {
            $this->moduleControllerMapProvider = oxNew(\OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider::class);
        }

        return $this->moduleControllerMapProvider;
    }
}
