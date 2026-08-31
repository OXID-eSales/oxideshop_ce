<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\MediaViewCacheInvalidator;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\ProductMediaViewCacheInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class MediaViewCacheInvalidatorTest extends TestCase
{
    public function testInvalidatesEveryProductReferencingTheMedia(): void
    {
        $mediaId = Id::generate();
        $firstProductId = Id::generate();
        $secondProductId = Id::generate();

        $productMediaDao = $this->createMock(ProductMediaDaoInterface::class);
        $productMediaDao
            ->method('getProductIdsByMedia')
            ->with($mediaId)
            ->willReturn([$firstProductId, $secondProductId]);

        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache
            ->expects($this->exactly(2))
            ->method('invalidateForProduct')
            ->with($this->logicalOr($firstProductId, $secondProductId));

        $invalidator = new MediaViewCacheInvalidator($productMediaDao, $cache);

        $invalidator->invalidateForMedia($mediaId);
    }

    public function testDoesNothingWhenNoProductReferencesTheMedia(): void
    {
        $mediaId = Id::generate();

        $productMediaDao = $this->createMock(ProductMediaDaoInterface::class);
        $productMediaDao
            ->method('getProductIdsByMedia')
            ->with($mediaId)
            ->willReturn([]);

        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache->expects($this->never())->method('invalidateForProduct');

        $invalidator = new MediaViewCacheInvalidator($productMediaDao, $cache);

        $invalidator->invalidateForMedia($mediaId);
    }
}
