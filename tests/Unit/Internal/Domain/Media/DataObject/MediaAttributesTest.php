<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Exception\AttributeNotFoundException;
use PHPUnit\Framework\TestCase;

final class MediaAttributesTest extends TestCase
{
    public function testHasReturnsFalseForMissingAttribute(): void
    {
        $attributes = new MediaAttributes();

        $this->assertFalse($attributes->has('alt'));
    }

    public function testHasReturnsTrueForExistingAttribute(): void
    {
        $attributes = new MediaAttributes(['alt' => 'alt text']);

        $this->assertTrue($attributes->has('alt'));
    }

    public function testGetReturnsValueForExistingAttribute(): void
    {
        $attributes = new MediaAttributes(['alt' => 'alt text']);

        $this->assertSame('alt text', $attributes->get('alt'));
    }

    public function testGetThrowsForMissingAttribute(): void
    {
        $attributes = new MediaAttributes();

        $this->expectException(AttributeNotFoundException::class);
        $attributes->get('alt');
    }

    public function testMultipleAttributes(): void
    {
        $attributes = new MediaAttributes(['alt' => 'alt text', 'title' => 'title text']);

        $this->assertSame('alt text', $attributes->get('alt'));
        $this->assertSame('title text', $attributes->get('title'));
    }

    public function testGetAltReturnsAltValue(): void
    {
        $attributes = new MediaAttributes(['alt' => 'my alt text']);

        $this->assertSame('my alt text', $attributes->getAlt());
    }

    public function testGetAltThrowsWhenMissing(): void
    {
        $attributes = new MediaAttributes();

        $this->expectException(AttributeNotFoundException::class);
        $attributes->getAlt();
    }
}
