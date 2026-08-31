<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Event\MediaAttributeChangedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaChangedEvent;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaSortedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class InvalidateProductMediaCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ProductMediaViewCacheInterface $cache,
        private MediaViewCacheInvalidatorInterface $mediaInvalidator,
    ) {
    }

    public function invalidateForProduct(ProductMediaChangedEvent|ProductMediaSortedEvent $event): void
    {
        $this->cache->invalidateForProduct($event->getProductId());
    }

    public function invalidateForMedia(MediaAttributeChangedEvent $event): void
    {
        $this->mediaInvalidator->invalidateForMedia($event->getMediaId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductMediaChangedEvent::class => 'invalidateForProduct',
            ProductMediaSortedEvent::class => 'invalidateForProduct',
            MediaAttributeChangedEvent::class => 'invalidateForMedia',
        ];
    }
}
