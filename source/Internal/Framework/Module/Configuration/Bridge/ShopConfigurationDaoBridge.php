<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ShopConfigurationDaoBridge implements ShopConfigurationDaoBridgeInterface
{
    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ShopEnvironmentConfigurationDaoInterface
     */
    private $shopEnvironmentConfigurationDao;

    /**
     * ShopConfigurationDaoBridge constructor.
     */
    public function __construct(
        ContextInterface $context,
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao
    ) {
        $this->context = $context;
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->shopEnvironmentConfigurationDao = $shopEnvironmentConfigurationDao;
    }

    public function get(): ShopConfiguration
    {
        return $this->shopConfigurationDao->get(
            $this->context->getCurrentShopId()
        );
    }

    public function save(ShopConfiguration $shopConfiguration): void
    {
        $this->shopConfigurationDao->save(
            $shopConfiguration,
            $this->context->getCurrentShopId()
        );

        $this->shopEnvironmentConfigurationDao->remove($this->context->getCurrentShopId());
    }
}
