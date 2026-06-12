<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Event\ProductMediaSortedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class ProductMediaSortedEventTest extends TestCase
{
    public function testGetProductId(): void
    {
        $productId = Id::generate();

        $event = new ProductMediaSortedEvent($productId);

        $this->assertSame($productId, $event->getProductId());
    }
}
