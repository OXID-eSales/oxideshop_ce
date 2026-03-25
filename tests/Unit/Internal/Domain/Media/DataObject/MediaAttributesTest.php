<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttribute;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Exception\AttributeNotFoundException;
use PHPUnit\Framework\TestCase;

final class MediaAttributesTest extends TestCase
{
    public function testHasReturnsFalseForMissingAttribute(): void
    {
        $attributes = new MediaAttributes();

        $this->assertFalse($attributes->has(MediaAttribute::ALT));
    }

    public function testHasReturnsTrueForExistingAttribute(): void
    {
        $attributes = new MediaAttributes([MediaAttribute::ALT => 'alt text']);

        $this->assertTrue($attributes->has(MediaAttribute::ALT));
    }

    public function testGetReturnsValueForExistingAttribute(): void
    {
        $attributes = new MediaAttributes([MediaAttribute::ALT => 'alt text']);

        $this->assertSame('alt text', $attributes->get(MediaAttribute::ALT));
    }

    public function testGetThrowsForMissingAttribute(): void
    {
        $attributes = new MediaAttributes();

        $this->expectException(AttributeNotFoundException::class);
        $attributes->get(MediaAttribute::ALT);
    }

    public function testMultipleAttributes(): void
    {
        $attributes = new MediaAttributes([MediaAttribute::ALT => 'alt text', 'title' => 'title text']);

        $this->assertSame('alt text', $attributes->get(MediaAttribute::ALT));
        $this->assertSame('title text', $attributes->get('title'));
    }

    public function testGetAltReturnsAltValue(): void
    {
        $attributes = new MediaAttributes([MediaAttribute::ALT => 'my alt text']);

        $this->assertSame('my alt text', $attributes->getAlt());
    }

    public function testGetAltThrowsWhenMissing(): void
    {
        $attributes = new MediaAttributes();

        $this->expectException(AttributeNotFoundException::class);
        $attributes->getAlt();
    }
}
