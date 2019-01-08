<?php declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

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
    private $chains = [];

    /**
     * ShopConfiguration constructor.
     */
    public function __construct()
    {
        $classExtensionChain = new Chain();
        $classExtensionChain->setName(Chain::CLASS_EXTENSIONS);
        $this->addChain($classExtensionChain);
    }

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
     * @param ModuleConfiguration $moduleConfiguration
     * @return $this
     */
    public function addModuleConfiguration(ModuleConfiguration $moduleConfiguration)
    {
        $this->moduleConfigurations[$moduleConfiguration->getId()] = $moduleConfiguration;

        return $this;
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
     * @param Chain $chain
     */
    public function addChain(Chain $chain)
    {
        $this->chains[$chain->getName()] = $chain;
    }

    /**
     * @param string $name
     * @return Chain
     */
    public function getChain(string $name): Chain
    {
        return $this->chains[$name];
    }

    /**
     * @return array
     */
    public function getChains(): array
    {
        return $this->chains;
    }
}
