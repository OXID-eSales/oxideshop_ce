<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

class ShopConfigurationDataMapper implements ShopConfigurationDataMapperInterface
{
    /**
     * @var ModuleConfigurationDataMapperInterface
     */
    private $moduleConfigurationDataMapper;

    /**
     * ProjectConfigurationDataMapper constructor.
     */
    public function __construct(ModuleConfigurationDataMapperInterface $moduleConfigurationDataMapper)
    {
        $this->moduleConfigurationDataMapper = $moduleConfigurationDataMapper;
    }

    public function toData(ShopConfiguration $configuration): array
    {
        $data = [];

        $data['modules'] = $this->getModulesConfigurationData($configuration);
        $data['moduleChains'] = $this->getModuleChainData($configuration);

        return $data;
    }

    public function fromData(array $data): ShopConfiguration
    {
        $shopConfiguration = new ShopConfiguration();

        if (isset($data['modules'])) {
            $this->setModulesConfiguration($shopConfiguration, $data['modules']);
        }

        if (isset($data['moduleChains'])) {
            $this->setModuleChains($shopConfiguration, $data['moduleChains']);
        }

        return $shopConfiguration;
    }

    private function setModulesConfiguration(ShopConfiguration $shopConfiguration, array $modulesData): void
    {
        foreach ($modulesData as $moduleId => $moduleData) {
            $moduleConfiguration = new ModuleConfiguration();
            $moduleConfiguration = $this->moduleConfigurationDataMapper->fromData($moduleConfiguration, $moduleData);
            $moduleConfiguration->setId($moduleId);

            $shopConfiguration->addModuleConfiguration($moduleConfiguration);
        }
    }

    private function getModulesConfigurationData(ShopConfiguration $shopConfiguration): array
    {
        $data = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleId => $moduleConfiguration) {
            $data[$moduleId] = $this->moduleConfigurationDataMapper->toData($moduleConfiguration);
        }

        return $data;
    }

    private function setModuleChains(ShopConfiguration $shopConfiguration, array $chainsData): void
    {
        if (isset($chainsData[ClassExtensionsChain::NAME])) {
            $chain = new ClassExtensionsChain();
            $chain->setChain($chainsData[ClassExtensionsChain::NAME]);

            $shopConfiguration->setClassExtensionsChain($chain);
        }
    }

    private function getModuleChainData(ShopConfiguration $shopConfiguration): array
    {
        $chain = $shopConfiguration->getClassExtensionsChain();

        return [
            $chain->getName() => $chain->getChain(),
        ];
    }
}
