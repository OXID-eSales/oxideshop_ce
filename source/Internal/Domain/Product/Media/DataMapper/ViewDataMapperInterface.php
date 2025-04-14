<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataMapper;

use Doctrine\Common\Collections\ArrayCollection;

interface ViewDataMapperInterface
{
    public function toData(ArrayCollection $productMedia): array;
}
