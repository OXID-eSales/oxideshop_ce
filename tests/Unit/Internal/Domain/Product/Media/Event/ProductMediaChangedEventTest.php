<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class ProductMediaChangedEventTest extends TestCase
{
    public function testGetProductId(): void
    {
        $productId = Id::generate();

        $event = new ProductMediaChangedEvent($productId, Id::generate());

        $this->assertSame($productId, $event->getProductId());
    }

    public function testGetMediaId(): void
    {
        $mediaId = Id::generate();

        $event = new ProductMediaChangedEvent(Id::generate(), $mediaId);

        $this->assertSame($mediaId, $event->getMediaId());
    }
}
