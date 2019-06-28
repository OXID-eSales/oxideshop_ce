<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ShopConfigurationDaoInterface;
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
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * ShopConfigurationDaoBridge constructor.
     * @param ContextInterface $context
     * @param ShopConfigurationDaoInterface $shopConfigurationDao
     */
    public function __construct(ContextInterface $context, ShopConfigurationDaoInterface $shopConfigurationDao)
    {
        $this->context = $context;
        $this->shopConfigurationDao = $shopConfigurationDao;
    }

    /**
     * @return ShopConfiguration
     */
    public function get(): ShopConfiguration
    {
        return $this->shopConfigurationDao->get(
            $this->context->getCurrentShopId(),
            $this->context->getEnvironment()
        );
    }

    /**
     * @param ShopConfiguration $shopConfiguration
     */
    public function save(ShopConfiguration $shopConfiguration)
    {
        $this->shopConfigurationDao->save(
            $shopConfiguration,
            $this->context->getCurrentShopId(),
            $this->context->getEnvironment()
        );
    }
}
