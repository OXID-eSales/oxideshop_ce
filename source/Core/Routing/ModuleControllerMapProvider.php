<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Routing;

use OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface;
use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;

/**
 * Provide the controller mappings from the metadata of all active modules.
 *
 * @internal Do not make a module extension for this class.
 */
class ModuleControllerMapProvider implements ControllerMapProviderInterface
{
    /**
     * Get the controller map of the modules.
     *
     * Returns an associative array, where
     *  - the keys are the controller ids
     *  - the values are the routed class names
     *
     * @return array
     */
    public function getControllerMap()
    {
        $controllerMap = [];
        $moduleControllersByModuleId = $this->getModuleVariablesLocator()
            ->getModuleVariable(ShopConfigurationSetting::MODULE_CONTROLLERS);

        if (is_array($moduleControllersByModuleId)) {
            $controllerMap = $this->flattenControllersMap($moduleControllersByModuleId);
        }

        return $controllerMap;
    }

    /**
     * @param array $moduleControllersByModuleId
     *
     * @return array The input array
     */
    protected function flattenControllersMap(array $moduleControllersByModuleId)
    {
        $moduleControllersFlat = [];
        foreach ($moduleControllersByModuleId as $moduleControllersOfOneModule) {
            $moduleControllersFlat = array_merge($moduleControllersFlat, $moduleControllersOfOneModule);
        }
        return $moduleControllersFlat;
    }

    /** @return ModuleVariablesLocator */
    private function getModuleVariablesLocator(): ModuleVariablesLocator
    {
        $shopIdCalculator = new ShopIdCalculator(
            new FileCache()
        );
        $subShopSpecificCache = new SubShopSpecificFileCache($shopIdCalculator);
        return new ModuleVariablesLocator($subShopSpecificCache, $shopIdCalculator);
    }
}
