<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

use OxidEsales\EshopCommunity\Internal\Common\Exception\InvalidObjectIdException;

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
     * @throws InvalidObjectIdException
     *
     * @return ShopConfiguration
     */
    public function getShopConfiguration(int $shopId): ShopConfiguration
    {
        if (array_key_exists($shopId, $this->shopConfigurations)) {
            return $this->shopConfigurations[$shopId];
        }
        throw new InvalidObjectIdException();
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
    public function setShopConfiguration(int $shopId, ShopConfiguration $shopConfiguration)
    {
        $this->shopConfigurations[$shopId] = $shopConfiguration;
    }

    /**
     * @param int $shopId
     *
     * @throws InvalidObjectIdException
     */
    public function deleteShopConfiguration(int $shopId)
    {
        if (array_key_exists($shopId, $this->shopConfigurations)) {
            unset($this->shopConfigurations[$shopId]);
        } else {
            throw new InvalidObjectIdException();
        }
    }
}
