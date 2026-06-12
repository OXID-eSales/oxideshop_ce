<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\Event;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Event\MediaAttributeChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;

final class MediaAttributeChangedEventTest extends TestCase
{
    public function testGetMediaId(): void
    {
        $mediaId = Id::generate();
        $event = new MediaAttributeChangedEvent($mediaId);

        $this->assertSame($mediaId, $event->getMediaId());
    }
}
