<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core\Routing;

use OxidEsales\Eshop\Core\Routing\Module\ClassProviderStorage;
use OxidEsales\Eshop\Core\Contract\ControllerMapProviderInterface;
use OxidEsales\Eshop\Core\Registry;

/**
 * Provide the controller mappings from the metadata of all active modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
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
        $moduleControllersByModuleId = Registry::getUtilsObject()->getModuleVar(ClassProviderStorage::STORAGE_KEY);

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
}
