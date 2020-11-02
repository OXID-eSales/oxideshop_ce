<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

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
     */
    public function __construct(ShopConfigurationDataMapperInterface $shopConfigurationDataMapper)
    {
        $this->shopConfigurationDataMapper = $shopConfigurationDataMapper;
    }

    public function toData(ProjectConfiguration $configuration): array
    {
        $data = [];

        $data['shops'] = $this->getShopsConfigurationData($configuration);

        return $data;
    }

    public function fromData(array $data): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();
        $this->setProjectConfiguration($projectConfiguration, $data);

        return $projectConfiguration;
    }

    private function setProjectConfiguration(ProjectConfiguration $projectConfiguration, array $data): void
    {
        if (isset($data['shops'])) {
            $this->setShopsConfiguration($projectConfiguration, $data['shops']);
        }
    }

    private function setShopsConfiguration(ProjectConfiguration $projectConfiguration, array $shopsData): void
    {
        foreach ($shopsData as $shopId => $shopData) {
            $projectConfiguration->addShopConfiguration(
                $shopId,
                $this->shopConfigurationDataMapper->fromData($shopData)
            );
        }
    }

    private function getShopsConfigurationData(ProjectConfiguration $projectConfiguration): array
    {
        $data = [];

        foreach ($projectConfiguration->getShopConfigurations() as $shopId => $shopConfiguration) {
            $data[$shopId] = $this->shopConfigurationDataMapper->toData($shopConfiguration);
        }

        return $data;
    }
}
