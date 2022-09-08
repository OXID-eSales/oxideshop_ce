<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ShopConfigurationDaoBridge implements ShopConfigurationDaoBridgeInterface
{
    public function __construct(
        private ContextInterface $context,
        private ShopConfigurationDaoInterface $shopConfigurationDao
    ) {
    }

    /**
     * @return ShopConfiguration
     */
    public function get(): ShopConfiguration
    {
        return $this->shopConfigurationDao->get(
            $this->context->getCurrentShopId()
        );
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     */
    public function save(ShopConfiguration $shopConfiguration)
    {
        $this->shopConfigurationDao->save(
            $shopConfiguration,
            $this->context->getCurrentShopId()
        );
    }
}
