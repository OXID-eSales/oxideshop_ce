<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;

interface ShopConfigurationDataMapperInterface
{
    public function toData(ShopConfiguration $configuration): array;

    public function fromData(array $data): ShopConfiguration;
}
