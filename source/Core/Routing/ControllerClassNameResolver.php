<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Routing;

use OxidEsales\Eshop\Core\Contract\ClassNameResolverInterface;
use OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface;

/**
 * This class maps controller id to controller class name and vice versa.
 * It looks up map from ShopControllerMapProvider and if no match is found checks ModuleControllerMapProvider.
 *
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
     * @param \OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider   $shopControllerMapProvider   shop map
     * @param \OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider $moduleControllerMapProvider module map
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

        return $this->arrayLookup($classId, $idToNameMap);
    }

    /**
     * Get class name from module controller provider.
     *
     * @param string $classId
     *
     * @return string|null
     */
    protected function getClassNameFromModuleMap($classId)
    {
        $moduleControllerMapProvider = $this->getModuleControllerMapProvider();
        $idToNameMap = $moduleControllerMapProvider->getControllerMap();

        return $this->arrayLookup($classId, $idToNameMap);
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

        return $this->arrayLookup($className, array_flip($idToNameMap));
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

        return $this->arrayLookup($className, array_flip($idToNameMap));
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

        return \array_key_exists($key, $keys2Values) ? $keys2Values[$key] : null;
    }

    /**
     * Getter for ShopControllerMapProvider object.
     *
     * @return \OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider
     */
    protected function getShopControllerMapProvider()
    {
        if (null === $this->shopControllerMapProvider) {
            $this->shopControllerMapProvider = oxNew(\OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider::class);
        }

        return $this->shopControllerMapProvider;
    }

    /**
     * Getter for ModuleControllerMapProvider object.
     *
     * @return \OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider
     */
    protected function getModuleControllerMapProvider()
    {
        if (null === $this->moduleControllerMapProvider) {
            $this->moduleControllerMapProvider = oxNew(\OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider::class);
        }

        return $this->moduleControllerMapProvider;
    }
}
