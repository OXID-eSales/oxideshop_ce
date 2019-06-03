<?php declare(strict_types = 1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject;

use DomainException;

/**
 * @internal
 */
class EnvironmentConfiguration
{
    /** @var ShopConfiguration[] */
    private $shopConfigurations = [];

    /**
     * @param int $shopId
     *
     * @throws DomainException
     *
     * @return ShopConfiguration
     */
    public function getShopConfiguration(int $shopId): ShopConfiguration
    {
        if (array_key_exists($shopId, $this->shopConfigurations)) {
            return $this->shopConfigurations[$shopId];
        }
        throw new DomainException('There is no configuration for shop id ' . $shopId);
    }

    /**
     * @return array
     */
    public function getShopConfigurations(): array
    {
        return $this->shopConfigurations;
    }

    /**
     * @return array
     */
    public function getShopIdsOfShopConfigurations() :array
    {
        return array_keys($this->shopConfigurations);
    }

    /**
     * @param int               $shopId
     * @param ShopConfiguration $shopConfiguration
     */
    public function addShopConfiguration(int $shopId, ShopConfiguration $shopConfiguration)
    {
        $this->shopConfigurations[$shopId] = $shopConfiguration;
    }

    /**
     * @param int $shopId
     *
     * @throws DomainException
     */
    public function deleteShopConfiguration(int $shopId)
    {
        if (array_key_exists($shopId, $this->shopConfigurations)) {
            unset($this->shopConfigurations[$shopId]);
        } else {
            throw new DomainException('There is no configuration for shop id ' . $shopId);
        }
    }
}
