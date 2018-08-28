<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\Chain;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ChainGroup;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ShopConfigurationDataMapper implements ShopConfigurationDataMapperInterface
{
    /**
     * @var ModuleConfigurationDataMapperInterface
     */
    private $moduleConfigurationDataMapper;

    /**
     * ProjectConfigurationDataMapper constructor.
     * @param ModuleConfigurationDataMapperInterface $moduleConfigurationDataMapper
     */
    public function __construct(ModuleConfigurationDataMapperInterface $moduleConfigurationDataMapper)
    {
        $this->moduleConfigurationDataMapper = $moduleConfigurationDataMapper;
    }

    /**
     * @param ShopConfiguration $configuration
     * @return array
     */
    public function toData(ShopConfiguration $configuration): array
    {
        $data = [];

        $data['modules'] = $this->getModulesConfigurationData($configuration);
        $data['moduleChains'] = $this->getModuleChainGroupsData($configuration);

        return $data;
    }

    /**
     * @param array $data
     * @return ShopConfiguration
     */
    public function fromData(array $data): ShopConfiguration
    {
        $shopConfiguration = new ShopConfiguration();

        if (isset($data['modules'])) {
            $this->setModulesConfiguration($shopConfiguration, $data['modules']);
        }

        if (isset($data['moduleChains'])) {
            $this->setModuleChainGroups($shopConfiguration, $data['moduleChains']);
        }

        return $shopConfiguration;
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param array             $modulesData
     */
    private function setModulesConfiguration(ShopConfiguration $shopConfiguration, array $modulesData)
    {
        foreach ($modulesData as $moduleId => $moduleData) {
            $moduleData = array_merge(
                [
                    'id' => $moduleId,
                ],
                $moduleData
            );

            $shopConfiguration->setModuleConfiguration(
                $moduleId,
                $this->moduleConfigurationDataMapper->fromData($moduleData)
            );
        }
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @return array
     */
    private function getModulesConfigurationData(ShopConfiguration $shopConfiguration): array
    {
        $data = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleId => $moduleConfiguration) {
            $data[$moduleId] = $this->moduleConfigurationDataMapper->toData($moduleConfiguration);
        }

        return $data;
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param array             $chainGroupsData
     */
    private function setModuleChainGroups(ShopConfiguration $shopConfiguration, array $chainGroupsData)
    {
        foreach ($chainGroupsData as $groupName => $chainGroupData) {
            $chainGroup = new ChainGroup();
            $this->setChains($chainGroup, $chainGroupsData[$groupName]);

            $shopConfiguration->setChainGroup($groupName, $chainGroup);
        }
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @return array
     */
    private function getModuleChainGroupsData(ShopConfiguration $shopConfiguration): array
    {
        $data = [];

        foreach ($shopConfiguration->getChainGroups() as $groupName => $group) {
            $data[$groupName] = $this->getChainsData($group);
        }

        return $data;
    }

    /**
     * @param ChainGroup $chainGroup
     * @param array      $chainsData
     */
    private function setChains(ChainGroup $chainGroup, array $chainsData)
    {
        foreach ($chainsData as $chainId => $chainData) {
            $chain = new Chain();
            $chain
                ->setName($chainId)
                ->setChain($chainData);

            $chainGroup->setChain($chain);
        }
    }

    /**
     * @param ChainGroup $chainGroup
     * @return array
     */
    private function getChainsData(ChainGroup $chainGroup): array
    {
        $data = [];

        foreach ($chainGroup->getChains() as $chain) {
            $data[$chain->getName()] = $chain->getChain();
        }

        return $data;
    }
}
