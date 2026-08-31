<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Event\MediaAttributeChangedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\InvalidateProductMediaCacheEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\MediaViewCacheInvalidatorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\ProductMediaViewCacheInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaChangedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaSortedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class InvalidateProductMediaCacheEventSubscriberTest extends TestCase
{
    public function testInvalidatesChangedProductForProductMediaChangedEvent(): void
    {
        $productId = Id::generate();
        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache->expects($this->once())->method('invalidateForProduct')->with($productId);
        $mediaInvalidator = $this->createStub(MediaViewCacheInvalidatorInterface::class);

        $subscriber = new InvalidateProductMediaCacheEventSubscriber($cache, $mediaInvalidator);

        $subscriber->invalidateForProduct(new ProductMediaChangedEvent($productId, Id::generate()));
    }

    public function testInvalidatesSortedProductForProductMediaSortedEvent(): void
    {
        $productId = Id::generate();
        $cache = $this->createMock(ProductMediaViewCacheInterface::class);
        $cache->expects($this->once())->method('invalidateForProduct')->with($productId);
        $mediaInvalidator = $this->createStub(MediaViewCacheInvalidatorInterface::class);

        $subscriber = new InvalidateProductMediaCacheEventSubscriber($cache, $mediaInvalidator);

        $subscriber->invalidateForProduct(new ProductMediaSortedEvent($productId));
    }

    public function testInvalidatesProductsForChangedMediaAttribute(): void
    {
        $mediaId = Id::generate();
        $cache = $this->createStub(ProductMediaViewCacheInterface::class);
        $mediaInvalidator = $this->createMock(MediaViewCacheInvalidatorInterface::class);
        $mediaInvalidator->expects($this->once())->method('invalidateForMedia')->with($mediaId);

        $subscriber = new InvalidateProductMediaCacheEventSubscriber($cache, $mediaInvalidator);

        $subscriber->invalidateForMedia(new MediaAttributeChangedEvent($mediaId));
    }

    public function testSubscribesToMediaChangeEvents(): void
    {
        $this->assertSame(
            [
                ProductMediaChangedEvent::class => 'invalidateForProduct',
                ProductMediaSortedEvent::class => 'invalidateForProduct',
                MediaAttributeChangedEvent::class => 'invalidateForMedia',
            ],
            InvalidateProductMediaCacheEventSubscriber::getSubscribedEvents()
        );
    }
}
