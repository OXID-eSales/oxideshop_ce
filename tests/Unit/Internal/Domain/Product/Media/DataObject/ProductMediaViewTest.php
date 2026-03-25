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
        $view = new ProductMediaView('detail', 'icon', 'zoom', 'thumbnail', new MediaAttributes());

        $this->assertFalse($view->getAttributes()->has('alt'));
    }

    public function testGetAttributesReturnsProvidedCollection(): void
    {
        $view = new ProductMediaView(
            'detail',
            'icon',
            'zoom',
            'thumbnail',
            new MediaAttributes(['alt' => 'my alt text'])
        );

        $this->assertSame('my alt text', $view->getAttributes()->getAlt());
    }
}
