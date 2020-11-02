<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;

class ModuleConfigurationDao implements ModuleConfigurationDaoInterface
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * ModuleConfigurationDao constructor.
     */
    public function __construct(ShopConfigurationDaoInterface $shopConfigurationDao)
    {
        $this->shopConfigurationDao = $shopConfigurationDao;
    }

    /**
     * @throws ModuleConfigurationNotFoundException
     */
    public function get(string $moduleId, int $shopId): ModuleConfiguration
    {
        return $this
            ->shopConfigurationDao
            ->get($shopId)
            ->getModuleConfiguration($moduleId);
    }

    public function save(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        $shopConfiguration = $this
            ->shopConfigurationDao
            ->get($shopId);

        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $this->shopConfigurationDao->save($shopConfiguration, $shopId);
    }
}
