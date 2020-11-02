<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Routing\Module;

use OxidEsales\Eshop\Core\Contract\ClassProviderStorageInterface;
use OxidEsales\Eshop\Core\Registry;

/**
 * Handler class for the storing of the metadata controller field of the modules.
 *
 * @deprecated since v6.4.0 (2019-03-22); Use `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ModuleConfigurationDaoBridgeInterface`.
 *
 * @internal do not make a module extension for this class
 *
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ClassProviderStorage implements ClassProviderStorageInterface
{
    /**
     * @var string the key under which the value will be stored
     */
    public const STORAGE_KEY = 'aModuleControllers';

    /**
     * Get the stored controller value from the oxconfig.
     *
     * @return array|null the controllers field of the modules metadata
     */
    public function get()
    {
        return (array)Registry::getConfig()->getShopConfVar(self::STORAGE_KEY);
    }

    /**
     * Set the stored controller value from the oxconfig.
     *
     * @param array $value the controllers field of the modules metadata
     */
    public function set($value): void
    {
        $value = $this->toLowercase($value);

        Registry::getConfig()->saveShopConfVar('aarr', self::STORAGE_KEY, $value);
    }

    /**
     * Add the controllers for the module, given by its ID, to the storage.
     *
     * @param string $moduleId    the ID of the module controllers to add
     * @param array  $controllers the controllers to add to the storage
     */
    public function add($moduleId, $controllers): void
    {
        $controllerMap = $this->get();
        $controllerMap[$moduleId] = $controllers;

        $this->set($controllerMap);
    }

    /**
     * Delete the controllers for the module, given by its ID, from the storage.
     *
     * @param string $moduleId the ID of the module, for which we want to delete the controllers from the storage
     */
    public function remove($moduleId): void
    {
        $controllerMap = $this->get();
        unset($controllerMap[strtolower($moduleId)]);

        $this->set($controllerMap);
    }

    /**
     * Change the module IDs and the controller keys to lower case.
     *
     * @param array $modulesControllers the controller arrays of several modules
     *
     * @return array the given controller arrays of several modules, with the module IDs and the controller keys in lower case
     */
    private function toLowercase($modulesControllers)
    {
        $result = [];

        if (null !== $modulesControllers) {
            foreach ($modulesControllers as $moduleId => $controllers) {
                $result[strtolower($moduleId)] = $this->controllerKeysToLowercase($controllers);
            }
        }

        return $result;
    }

    /**
     * Change the controller keys to lower case.
     *
     * @param array $controllers the controllers array of one module
     *
     * @return array the given controllers array with the controller keys in lower case
     */
    private function controllerKeysToLowercase($controllers)
    {
        $result = [];

        foreach ($controllers as $controllerKey => $controllerClass) {
            $result[strtolower($controllerKey)] = $controllerClass;
        }

        return $result;
    }
}
