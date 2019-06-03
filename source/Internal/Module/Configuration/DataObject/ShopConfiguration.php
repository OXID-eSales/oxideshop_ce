<?php declare(strict_types = 1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Adapter\Exception\ModuleConfigurationNotFoundException;

/**
 * @internal
 */
class ShopConfiguration
{
    /** @var ModuleConfiguration[] */
    private $moduleConfigurations = [];

    /**
     * @var ClassExtensionsChain
     */
    private $chain;

    /**
     * ShopConfiguration constructor.
     */
    public function __construct()
    {
        $classExtensionChain = new ClassExtensionsChain();
        $this->setClassExtensionsChain($classExtensionChain);
    }

    /**
     * @param string $moduleId
     *
     * @return ModuleConfiguration
     * @throws ModuleConfigurationNotFoundException
     */
    public function getModuleConfiguration(string $moduleId): ModuleConfiguration
    {
        if (array_key_exists($moduleId, $this->moduleConfigurations)) {
            return $this->moduleConfigurations[$moduleId];
        }
        throw new ModuleConfigurationNotFoundException('There is no module configuration with id ' . $moduleId);
    }

    /**
     * @return ModuleConfiguration[]
     */
    public function getModuleConfigurations(): array
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
     * @throws ModuleConfigurationNotFoundException
     */
    public function deleteModuleConfiguration(string $moduleId)
    {
        if (array_key_exists($moduleId, $this->moduleConfigurations)) {
            unset($this->moduleConfigurations[$moduleId]);
        } else {
            throw new ModuleConfigurationNotFoundException('There is no module configuration with id ' . $moduleId);
        }
    }

    /**
     * @return array
     */
    public function getModuleIdsOfModuleConfigurations(): array
    {
        return array_keys($this->moduleConfigurations);
    }

    /**
     * @param ClassExtensionsChain $chain
     */
    public function setClassExtensionsChain(ClassExtensionsChain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * @return ClassExtensionsChain
     */
    public function getClassExtensionsChain(): ClassExtensionsChain
    {
        return $this->chain;
    }

    /**
     * @param string $moduleId
     * @return bool
     */
    public function hasModuleConfiguration(string $moduleId): bool
    {
        return isset($this->moduleConfigurations[$moduleId]);
    }
}
