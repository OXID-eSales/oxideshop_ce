<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Event\MediaAttributeChangedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\InvalidateProductMediaViewCacheEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\ProductMediaViewCacheInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaChangedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaSortedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class InvalidateProductMediaViewCacheEventSubscriberTest extends TestCase
{
    public function testInvalidatesChangedProductForProductMediaChangedEvent(): void
    {
        $productId = Id::generate();
        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache->expects($this->once())->method('invalidateForProduct')->with($productId);

        $subscriber = new InvalidateProductMediaViewCacheEventSubscriber($cache);

        $subscriber->invalidateChangedProduct(new ProductMediaChangedEvent($productId, Id::generate()));
    }

    public function testInvalidatesSortedProductForProductMediaSortedEvent(): void
    {
        $productId = Id::generate();
        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache->expects($this->once())->method('invalidateForProduct')->with($productId);

        $subscriber = new InvalidateProductMediaViewCacheEventSubscriber($cache);

        $subscriber->invalidateChangedProduct(new ProductMediaSortedEvent($productId));
    }

    public function testInvalidatesAllProductsForMediaAttributeChangedEvent(): void
    {
        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache->expects($this->once())->method('invalidateAll');

        $subscriber = new InvalidateProductMediaViewCacheEventSubscriber($cache);

        $subscriber->invalidateAllProducts(new MediaAttributeChangedEvent(Id::generate()));
    }

    public function testSubscribesToMediaChangeEvents(): void
    {
        $this->assertSame(
            [
                ProductMediaChangedEvent::class => 'invalidateChangedProduct',
                ProductMediaSortedEvent::class => 'invalidateChangedProduct',
                MediaAttributeChangedEvent::class => 'invalidateAllProducts',
            ],
            InvalidateProductMediaViewCacheEventSubscriber::getSubscribedEvents()
        );
    }
}
