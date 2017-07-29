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
