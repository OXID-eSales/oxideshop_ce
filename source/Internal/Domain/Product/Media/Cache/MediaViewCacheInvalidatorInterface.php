<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

interface MediaViewCacheInvalidatorInterface
{
    public function invalidateForMedia(Id $mediaId): void;
}
