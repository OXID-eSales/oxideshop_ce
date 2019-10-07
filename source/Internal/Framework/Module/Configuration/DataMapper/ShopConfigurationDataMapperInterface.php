<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

interface ShopConfigurationDataMapperInterface
{
    /**
     * @param ShopConfiguration $configuration
     * @return array
     */
    public function toData(ShopConfiguration $configuration): array;

    /**
     * @param array $data
     * @return ShopConfiguration
     */
    public function fromData(array $data): ShopConfiguration;
}
