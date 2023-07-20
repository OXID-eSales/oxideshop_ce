<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Routing;

use OxidEsales\Eshop\Core\Contract\ClassNameResolverInterface;
use OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;

/**
 * This class maps controller id to controller class name and vice versa.
 * It looks up map from ShopControllerMapProvider and if no match is found checks ModuleControllerMapProvider.
 *
 * @internal Do not make a module extension for this class.
 */
class ControllerClassNameResolver implements ClassNameResolverInterface
{
    public function __construct(
        private ?ControllerMapProviderInterface           $shopControllerMapProvider = null,
        private ?ActiveModulesDataProviderBridgeInterface $activeModulesDataProvider = null
    ) {
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
     * @param string $classId
     *
     * @return string|null
     */
    protected function getClassNameFromModuleMap($classId)
    {
        $controllersArray = [];
        foreach ($this->getActiveModulesDataProvider()->getControllers() as $controller)
        {
            $controllersArray[$controller->getId()] = $controller->getControllerClassNameSpace();
        }

        return $this->arrayLookup($classId, $controllersArray);
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
        $controllersArray = [];
        foreach ($this->getActiveModulesDataProvider()->getControllers() as $controller)
        {
            $controllersArray[$controller->getId()] = $controller->getControllerClassNameSpace();
        }

        return $this->arrayLookup($className, array_flip($controllersArray));
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
        if ($this->shopControllerMapProvider === null) {
            $this->shopControllerMapProvider = oxNew(\OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider::class);
        }

        return $this->shopControllerMapProvider;
    }

    private function getActiveModulesDataProvider(): ActiveModulesDataProviderBridgeInterface
    {
        if ($this->activeModulesDataProvider === null) {
            $this->activeModulesDataProvider = ContainerFacade::get(ActiveModulesDataProviderBridgeInterface::class);
        }

        return $this->activeModulesDataProvider;
    }
}
