<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class ShopConfigurationDaoBridge implements ShopConfigurationDaoBridgeInterface
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * ShopConfigurationDaoBridge constructor.
     * @param ContextInterface                 $context
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     */
    public function __construct(ContextInterface $context, ProjectConfigurationDaoInterface $projectConfigurationDao)
    {
        $this->context = $context;
        $this->projectConfigurationDao = $projectConfigurationDao;
    }

    /**
     * @return ShopConfiguration
     */
    public function get(): ShopConfiguration
    {
        return $this
            ->projectConfigurationDao
            ->getConfiguration()
            ->getEnvironmentConfiguration($this->context->getEnvironment())
            ->getShopConfiguration($this->context->getCurrentShopId());
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     */
    public function save(ShopConfiguration $shopConfiguration)
    {
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $environmentConfiguration = $projectConfiguration->getEnvironmentConfiguration($this->context->getEnvironment());

        $environmentConfiguration->addShopConfiguration(
            $this->context->getCurrentShopId(),
            $shopConfiguration
        );

        $this->projectConfigurationDao->persistConfiguration($projectConfiguration);
    }
}
