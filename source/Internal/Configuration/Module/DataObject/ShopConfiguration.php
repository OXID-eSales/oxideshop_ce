<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

use DomainException;

/**
 * @internal
 */
class ShopConfiguration
{
    /** @var ModuleConfiguration[] */
    private $moduleConfigurations = [];

    /**
     * @var array
     */
    private $chainGroups = [];

    /**
     * @param string $moduleId
     *
     * @throws DomainException
     *
     * @return ModuleConfiguration
     */
    public function getModuleConfiguration(string $moduleId) : ModuleConfiguration
    {
        if (array_key_exists($moduleId, $this->moduleConfigurations)) {
            return $this->moduleConfigurations[$moduleId];
        }
        throw new DomainException('There is no module configuration with id ' . $moduleId);
    }

    /**
     * @return array
     */
    public function getModuleConfigurations() : array
    {
        return $this->moduleConfigurations;
    }

    /**
     * @param string              $moduleId
     * @param ModuleConfiguration $moduleConfiguration
     */
    public function setModuleConfiguration(string $moduleId, ModuleConfiguration $moduleConfiguration)
    {
        $this->moduleConfigurations[$moduleId] = $moduleConfiguration;
    }

    /**
     * @param string $moduleId
     *
     * @throws DomainException
     */
    public function deleteModuleConfiguration(string $moduleId)
    {
        if (array_key_exists($moduleId, $this->moduleConfigurations)) {
            unset($this->moduleConfigurations[$moduleId]);
        } else {
            throw new DomainException('There is no module configuration with id ' . $moduleId);
        }
    }

    /**
     * @return array
     */
    public function getModuleIdsOfModuleConfigurations() : array
    {
        return array_keys($this->moduleConfigurations);
    }

    /**
     * @param string     $name
     * @param ChainGroup $group
     */
    public function setChainGroup(string $name, ChainGroup $group)
    {
        $this->chainGroups[$name] = $group;
    }

    /**
     * @param string $name
     * @return ChainGroup
     */
    public function getChainGroup(string $name): ChainGroup
    {
        return $this->chainGroups[$name];
    }

    /**
     * @return ChainGroup
     */
    public function getChainGroups(): array
    {
        return $this->chainGroups;
    }
}
