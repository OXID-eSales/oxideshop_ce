<?php declare(strict_types = 1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use DomainException;

class ProjectConfiguration
{
    /** @var ShopConfiguration[] */
    private $projectConfiguration = [];

    /**
     * @param int $shopId
     *
     * @return ShopConfiguration
     */
    public function getShopConfiguration(int $shopId): ShopConfiguration
    {
        if (array_key_exists($shopId, $this->projectConfiguration)) {
            return $this->projectConfiguration[$shopId];
        }
        throw new DomainException('There is no configuration for shop id ' . $shopId);
    }

    /**
     * @return array
     */
    public function getShopConfigurations(): array
    {
        return $this->projectConfiguration;
    }

    /**
     * @return array
     */
    public function getShopConfigurationIds() :array
    {
        return array_keys($this->projectConfiguration);
    }

    /**
     * @param int               $shopId
     * @param ShopConfiguration $shopConfiguration
     */
    public function addShopConfiguration(int $shopId, ShopConfiguration $shopConfiguration): void
    {
        $this->projectConfiguration[$shopId] = $shopConfiguration;
    }

    /**
     * @param int $shopId
     *
     * @throws DomainException
     */
    public function deleteShopConfiguration(int $shopId): void
    {
        if (array_key_exists($shopId, $this->projectConfiguration)) {
            unset($this->projectConfiguration[$shopId]);
        } else {
            throw new DomainException('There is no configuration for shop id ' . $shopId);
        }
    }
}
