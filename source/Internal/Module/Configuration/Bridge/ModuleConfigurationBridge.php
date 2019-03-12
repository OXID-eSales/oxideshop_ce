<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class ModuleConfigurationBridge implements ModuleConfigurationBridgeInterface
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
     * ModuleConfigurationBridge constructor.
     * @param ContextInterface                $context
     * @param ModuleConfigurationDaoInterface $moduleConfigurationDao
     */
    public function __construct(ContextInterface $context, ModuleConfigurationDaoInterface $moduleConfigurationDao)
    {
        $this->context = $context;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
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
    }
}
