<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;

class ProjectConfigurationDataMapper implements ProjectConfigurationDataMapperInterface
{
    /**
     * @var ShopConfigurationDataMapperInterface
     */
    private $shopConfigurationDataMapper;

    /**
     * ProjectConfigurationDataMapper constructor.
     * @param ShopConfigurationDataMapperInterface $shopConfigurationDataMapper
     */
    public function __construct(ShopConfigurationDataMapperInterface $shopConfigurationDataMapper)
    {
        $this->shopConfigurationDataMapper = $shopConfigurationDataMapper;
    }

    /**
     * @param ProjectConfiguration $configuration
     * @return array
     */
    public function toData(ProjectConfiguration $configuration): array
    {
        $data = [];

        $data['shops'] = $this->getShopsConfigurationData($configuration);

        return $data;
    }

    /**
     * @param array $data
     * @return ProjectConfiguration
     */
    public function fromData(array $data): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();
        $this->setProjectConfiguration($projectConfiguration, $data);

        return $projectConfiguration;
    }

    /**
     * @param ProjectConfiguration $projectConfiguration
     * @param array                $data
     */
    private function setProjectConfiguration(ProjectConfiguration $projectConfiguration, array $data)
    {
        if (isset($data['shops'])) {
            $this->setShopsConfiguration($projectConfiguration, $data['shops']);
        }
    }

    /**
     * @param ProjectConfiguration $projectConfiguration
     * @param array                $shopsData
     */
    private function setShopsConfiguration(ProjectConfiguration $projectConfiguration, array $shopsData)
    {
        foreach ($shopsData as $shopId => $shopData) {
            $projectConfiguration->addShopConfiguration(
                $shopId,
                $this->shopConfigurationDataMapper->fromData($shopData)
            );
        }
    }

    /**
     * @param ProjectConfiguration $projectConfiguration
     *
     * @return array
     */
    private function getShopsConfigurationData(ProjectConfiguration $projectConfiguration): array
    {
        $data = [];

        foreach ($projectConfiguration->getShopConfigurations() as $shopId => $shopConfiguration) {
            $data[$shopId] = $this->shopConfigurationDataMapper->toData($shopConfiguration);
        }

        return $data;
    }
}
