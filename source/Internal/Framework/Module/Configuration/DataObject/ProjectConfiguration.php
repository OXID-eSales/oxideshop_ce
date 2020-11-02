<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use DomainException;

class ProjectConfiguration
{
    /**
     * @var ShopConfiguration[]
     */
    private $projectConfiguration = [];

    public function getShopConfiguration(int $shopId): ShopConfiguration
    {
        if (\array_key_exists($shopId, $this->projectConfiguration)) {
            return $this->projectConfiguration[$shopId];
        }
        throw new DomainException('There is no configuration for shop id ' . $shopId);
    }

    public function getShopConfigurations(): array
    {
        return $this->projectConfiguration;
    }

    public function getShopConfigurationIds(): array
    {
        return array_keys($this->projectConfiguration);
    }

    public function addShopConfiguration(int $shopId, ShopConfiguration $shopConfiguration): void
    {
        $this->projectConfiguration[$shopId] = $shopConfiguration;
    }

    /**
     * @throws DomainException
     */
    public function deleteShopConfiguration(int $shopId): void
    {
        if (\array_key_exists($shopId, $this->projectConfiguration)) {
            unset($this->projectConfiguration[$shopId]);
        } else {
            throw new DomainException('There is no configuration for shop id ' . $shopId);
        }
    }
}
