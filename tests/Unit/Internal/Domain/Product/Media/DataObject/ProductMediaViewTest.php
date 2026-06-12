<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use PHPUnit\Framework\TestCase;

final class ProductMediaViewTest extends TestCase
{
    public function testGetAttributesReturnsEmptyCollection(): void
    {
        $view = $this->createView(new MediaAttributes());

        $this->assertFalse($view->getAttributes()->has('alt'));
    }

    public function testGetAttributesReturnsProvidedCollection(): void
    {
        $view = $this->createView(new MediaAttributes(['alt' => 'my alt text']));

        $this->assertSame('my alt text', $view->getAttributes()->getAlt());
    }

    public function testGetAltReturnsAltAttributeWhenSet(): void
    {
        $view = $this->createView(new MediaAttributes(['alt' => 'Red back view']));

        $this->assertSame('Red back view', $view->getAlt('Fallback Title'));
    }

    public function testGetAltReturnsFallbackWhenAltMissing(): void
    {
        $view = $this->createView(new MediaAttributes());

        $this->assertSame('Stan Smith', $view->getAlt('Stan Smith'));
    }

    public function testGetAltReturnsFallbackWhenAltIsEmptyString(): void
    {
        $view = $this->createView(new MediaAttributes(['alt' => '']));

        $this->assertSame('Stan Smith', $view->getAlt('Stan Smith'));
    }

    public function testGetAltReturnsEmptyWhenAltMissingAndNoFallback(): void
    {
        $view = $this->createView(new MediaAttributes());

        $this->assertSame('', $view->getAlt());
    }

    public function testGetAltStripsHtmlFromFallback(): void
    {
        $view = $this->createView(new MediaAttributes());

        $this->assertSame('Stan Smith', $view->getAlt('<b>Stan</b> Smith'));
    }

    public function testGetAltTrimsFallback(): void
    {
        $view = $this->createView(new MediaAttributes());

        $this->assertSame('Stan Smith', $view->getAlt('  Stan Smith   '));
    }

    public function testGetAltDoesNotStripOrTrimExplicitAlt(): void
    {
        $view = $this->createView(new MediaAttributes(['alt' => '  <em>Bold</em> alt  ']));

        $this->assertSame('  <em>Bold</em> alt  ', $view->getAlt('fallback'));
    }

    private function createView(MediaAttributes $attributes): ProductMediaView
    {
        return new ProductMediaView(
            detailUrl: '',
            iconUrl: '',
            zoomUrl: '',
            thumbnailUrl: '',
            attributes: $attributes,
        );
    }
}
