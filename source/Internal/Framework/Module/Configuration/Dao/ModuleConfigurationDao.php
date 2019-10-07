<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class ModuleConfigurationDao implements ModuleConfigurationDaoInterface
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * ModuleConfigurationDao constructor.
     * @param ShopConfigurationDaoInterface $shopConfigurationDao
     * @param BasicContextInterface $context
     */
    public function __construct(ShopConfigurationDaoInterface $shopConfigurationDao, BasicContextInterface $context)
    {
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
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
