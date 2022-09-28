<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ProjectConfigurationIsEmptyException;

class ProjectConfigurationDao implements ProjectConfigurationDaoInterface
{
    public function __construct(private ShopConfigurationDaoInterface $shopConfigurationDao)
    {
    }

    /**
     * @return ProjectConfiguration
     * @throws ProjectConfigurationIsEmptyException
     */
    public function getConfiguration(): ProjectConfiguration
    {
        $shopConfigurations = $this->shopConfigurationDao->getAll();

        if (!$shopConfigurations) {
            throw new ProjectConfigurationIsEmptyException('Project configuration cannot be empty.');
        }

        $projectConfiguration = new ProjectConfiguration();

        foreach ($shopConfigurations as $shopId => $shopConfiguration) {
            $projectConfiguration->addShopConfiguration(
                $shopId,
                $shopConfiguration
            );
        }

        return $projectConfiguration;
    }

    /**
     * @param ProjectConfiguration $configuration
     */
    public function save(ProjectConfiguration $configuration): void
    {
        $this->shopConfigurationDao->deleteAll();

        foreach ($configuration->getShopConfigurations() as $shopId => $shopConfiguration) {
            $this->shopConfigurationDao->save($shopConfiguration, $shopId);
        }
    }

    /**
     * @return bool
     */
    public function isConfigurationEmpty(): bool
    {
        return count($this->shopConfigurationDao->getAll()) === 0;
    }
}
