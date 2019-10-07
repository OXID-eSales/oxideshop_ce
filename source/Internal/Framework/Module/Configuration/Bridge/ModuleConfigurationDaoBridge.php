<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ModuleConfigurationDaoBridge implements ModuleConfigurationDaoBridgeInterface
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ShopEnvironmentConfigurationDaoInterface
     */
    private $shopEnvironmentConfigurationDao;

    /**
     * @param ContextInterface                         $context
     * @param ModuleConfigurationDaoInterface          $moduleConfigurationDao
     * @param ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao
     */
    public function __construct(
        ContextInterface $context,
        ModuleConfigurationDaoInterface $moduleConfigurationDao,
        ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao
    ) {
        $this->context = $context;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->shopEnvironmentConfigurationDao = $shopEnvironmentConfigurationDao;
    }

    /**
     * @param string $moduleId
     * @return ModuleConfiguration
     */
    public function get(string $moduleId): ModuleConfiguration
    {
        return $this->moduleConfigurationDao->get($moduleId, $this->context->getCurrentShopId());
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     */
    public function save(ModuleConfiguration $moduleConfiguration)
    {
        $this->moduleConfigurationDao->save($moduleConfiguration, $this->context->getCurrentShopId());
        $this->shopEnvironmentConfigurationDao->remove($this->context->getCurrentShopId());
    }
}
