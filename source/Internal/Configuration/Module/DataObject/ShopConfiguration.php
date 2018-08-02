<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

use OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdException;

/**
 * @internal
 */
class ShopConfiguration
{
    /** @var ModuleConfiguration[] */
    private $moduleConfigurations = [];

    /** @var array */
    private $extensionChain = [];

    /** @var array */
    private $blockChain = [];

    /** @var array */
    private $moduleSmartyPluginDirectories = [];

    /**
     * @param string $moduleId
     *
     * @throws InvalidObjectIdException
     *
     * @return ModuleConfiguration
     */
    public function getModuleConfiguration(string $moduleId) : ModuleConfiguration
    {
        if (array_key_exists($moduleId, $this->moduleConfigurations)) {
            return $this->moduleConfigurations[$moduleId];
        }
        throw new InvalidObjectIdException();
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
     * @throws InvalidObjectIdException
     */
    public function deleteModuleConfiguration(string $moduleId)
    {
        if (array_key_exists($moduleId, $this->moduleConfigurations)) {
            unset($this->moduleConfigurations[$moduleId]);
        } else {
            throw new InvalidObjectIdException();
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
     * @return array
     */
    public function getExtensionChain() : array
    {
        return $this->extensionChain;
    }

    /**
     * @param array $extensionChain
     */
    public function setExtensionChain(array $extensionChain)
    {
        $this->extensionChain = $extensionChain;
    }

    /**
     * @return array
     */
    public function getBlockChain() : array
    {
        return $this->extensionChain;
    }

    /**
     * @param array $blockChain
     */
    public function setBlockChain(array $blockChain)
    {
        $this->blockChain = $blockChain;
    }

    /**
     * @return array
     */
    public function getModuleSmartyPluginDirectories() : array
    {
        return $this->moduleSmartyPluginDirectories;
    }

    /**
     * @param array $moduleSmartyPluginDirectories
     */
    public function setModuleSmartyPluginDirectories(array $moduleSmartyPluginDirectories)
    {
        $this->moduleSmartyPluginDirectories = $moduleSmartyPluginDirectories;
    }
}
