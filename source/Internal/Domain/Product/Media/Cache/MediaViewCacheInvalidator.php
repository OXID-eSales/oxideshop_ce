<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;

readonly class MediaViewCacheInvalidator implements MediaViewCacheInvalidatorInterface
{
    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
        private ProductMediaViewCacheInterface $cache,
    ) {
    }

    public function invalidateForMedia(Id $mediaId): void
    {
        foreach ($this->productMediaDao->getProductIdsByMedia($mediaId) as $productId) {
            $this->cache->invalidateForProduct($productId);
        }
    }
}
