<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaImageSizes;

interface ProductMediaImageSizesProviderInterface
{
    public function getSizes(): ProductMediaImageSizes;
}
