<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;

/**
 * @internal
 */
class ProjectConfigurationDataMapper implements ProjectConfigurationDataMapperInterface
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
     * @param ProjectConfiguration $configuration
     * @return array
     */
    public function toData(ProjectConfiguration $configuration): array
    {

    }

    /**
     * @param array $data
     * @return ProjectConfiguration
     */
    public function fromData(array $data): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName($data['project_name']);

        $this->setEnvironmentConfigurations($projectConfiguration, $data['environments']);

        return $projectConfiguration;
    }

    /**
     * @param ProjectConfiguration $projectConfiguration
     * @param array                $environmentsData
     */
    private function setEnvironmentConfigurations(ProjectConfiguration $projectConfiguration, array $environmentsData)
    {
        foreach ($environmentsData as $environmentName => $environmentData) {
            $environmentConfiguration = new EnvironmentConfiguration();

            if (isset($environmentData['shops'])) {
                $this->setShopsConfiguration($environmentConfiguration, $environmentData['shops']);
            }

            $projectConfiguration->setEnvironmentConfiguration(
                $environmentName,
                $environmentConfiguration
            );
        }
    }

    /**
     * @param EnvironmentConfiguration $environmentConfiguration
     * @param array                    $shopsData
     */
    private function setShopsConfiguration(EnvironmentConfiguration $environmentConfiguration, array $shopsData)
    {
        foreach ($shopsData as $shopId => $shopData) {
            $shopConfiguration = new ShopConfiguration();

            if (isset($shopData['modules'])) {
                $this->setModulesConfiguration($shopConfiguration, $shopData['modules']);
            }

            $environmentConfiguration->setShopConfiguration($shopId, $shopConfiguration);
        }
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     * @param array             $modulesData
     */
    private function setModulesConfiguration(ShopConfiguration $shopConfiguration, array $modulesData)
    {
        foreach ($modulesData as $moduleId => $moduleData) {
            $shopConfiguration->setModuleConfiguration(
                $moduleId,
                $this->moduleConfigurationDataMapper->fromData($moduleData)
            );
        }
    }
}
