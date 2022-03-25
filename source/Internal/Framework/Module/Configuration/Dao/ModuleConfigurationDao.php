<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class ModuleConfigurationDao implements ModuleConfigurationDaoInterface
{
    /**
     * ModuleConfigurationDao constructor.
     */
    public function __construct(private ShopConfigurationDaoInterface $shopConfigurationDao)
    {
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return ModuleConfiguration
     * @throws ModuleConfigurationNotFoundException
     */
    public function get(string $moduleId, int $shopId): ModuleConfiguration
    {
        return $this
            ->shopConfigurationDao
            ->get($shopId)
            ->getModuleConfiguration($moduleId);
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function save(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        $shopConfiguration = $this
            ->shopConfigurationDao
            ->get($shopId);

        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $this->shopConfigurationDao->save($shopConfiguration, $shopId);
    }
}
