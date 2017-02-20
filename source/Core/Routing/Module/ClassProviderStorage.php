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
namespace OxidEsales\EshopCommunity\Core\Routing\Module;

use OxidEsales\Eshop\Core\Contract\ClassProviderStorageInterface;
use OxidEsales\Eshop\Core\Registry;

/**
 * Handler class for the storing of the metadata controller field of the modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ClassProviderStorage implements ClassProviderStorageInterface
{
    /**
     * @var string The key under which the value will be stored.
     */
    const STORAGE_KEY = 'aModuleControllers';

    /**
     * Get the stored controller value from the oxconfig.
     *
     * @return null|array The controllers field of the modules metadata.
     */
    public function get()
    {
        return (array) $this->getConfig()->getShopConfVar(self::STORAGE_KEY);
    }

    /**
     * Set the stored controller value from the oxconfig.
     *
     * @param array $value The controllers field of the modules metadata.
     */
    public function set($value)
    {
        $value = $this->toLowercase($value);

        /**
         * @todo see Implementation of \OxidEsales\Eshop\Core\Module\ModuleInstaller::_saveToConfig
         *       setConfigParam and saveShopConfVar is called there. Ask someone (Vilma) why ;-)
         */
        $this->getConfig()->saveShopConfVar('aarr', self::STORAGE_KEY, $value);
    }

    /**
     * Add the controllers for the module, given by its ID, to the storage.
     *
     * @param string $moduleId    The ID of the module controllers to add.
     * @param array  $controllers The controllers to add to the storage.
     */
    public function add($moduleId, $controllers)
    {
        $controllerMap = $this->get();
        $controllerMap[$moduleId] = $controllers;

        $this->set($controllerMap);
    }

    /**
     * Delete the controllers for the module, given by its ID, from the storage.
     *
     * @param string $moduleId The ID of the module, for which we want to delete the controllers from the storage.
     */
    public function remove($moduleId)
    {
        $controllerMap = $this->get();
        unset($controllerMap[strtolower($moduleId)]);

        $this->set($controllerMap);
    }

    /**
     * Change the module IDs and the controller keys to lower case.
     *
     * @param array $modulesControllers The controller arrays of several modules.
     *
     * @return array The given controller arrays of several modules, with the module IDs and the controller keys in lower case.
     */
    private function toLowercase($modulesControllers)
    {
        $result = [];

        if (!is_null($modulesControllers)) {
            foreach ($modulesControllers as $moduleId => $controllers) {
                $result[strtolower($moduleId)] = $this->controllerKeysToLowercase($controllers);
            }
        }

        return $result;
    }

    /**
     * Change the controller keys to lower case.
     *
     * @param array $controllers The controllers array of one module.
     *
     * @return array The given controllers array with the controller keys in lower case.
     */
    private function controllerKeysToLowercase($controllers)
    {
        $result = [];

        foreach ($controllers as $controllerKey => $controllerClass) {
            $result[strtolower($controllerKey)] = $controllerClass;
        }

        return $result;
    }

    /**
     * Get the config object.
     *
     * @return \oxConfig The config object.
     */
    private function getConfig()
    {
        return Registry::getConfig();
    }
}
