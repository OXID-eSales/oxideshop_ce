<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;

interface DataMapperInterface
{
    public function toData(ProductMedia $productMedia): array;

    public function fromData(array $data): ProductMedia;
}
