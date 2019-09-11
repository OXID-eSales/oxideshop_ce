<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * Interface for Handling the storing/loading of the metadata controller field of the modules.
 *
 * @deprecated since v6.4.0 (2019-03-22); Use `OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ModuleConfigurationDaoBridgeInterface`.
 * @internal Do not make a module extension for this class.
 * @see      https://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
interface ClassProviderStorageInterface
{
    /**
     * Get the stored controller value from the storage.
     *
     * @return array The controllers field of the modules metadata.
     */
    public function get();

    /**
     * Set the stored controller value from the storage.
     *
     * @param array $value The controllers field of the modules metadata.
     */
    public function set($value);

    /**
     * Add the controllers for the module, given by its ID, to the storage.
     *
     * @param string $moduleId    The ID of the module controllers to add.
     * @param array  $controllers The controllers to add to the storage.
     */
    public function add($moduleId, $controllers);

    /**
     * Delete the controllers for the module, given by its ID, from the storage.
     *
     * @param string $moduleId The ID of the module, for which we want to delete the controllers from the storage.
     */
    public function remove($moduleId);
}
