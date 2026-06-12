<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Factory;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;

interface FallbackMediaFactoryInterface
{
    public function create(): Media;
}
