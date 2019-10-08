<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     *
     * @param ContextInterface                         $context
     * @param ShopConfigurationDaoInterface            $shopConfigurationDao
     * @param ShopEnvironmentConfigurationDaoInterface $shopEnvironmentConfigurationDao
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

        $this->shopEnvironmentConfigurationDao->remove($this->context->getCurrentShopId());
    }
}
