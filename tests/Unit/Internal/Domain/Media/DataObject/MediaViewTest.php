<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaView;
use PHPUnit\Framework\TestCase;

final class MediaViewTest extends TestCase
{
    public function testReturnsProvidedUrl(): void
    {
        $url = 'https://shop.example.com/out/pictures/generated/product/87_87_75/image.jpg';
        $mediaView = new MediaView($url);

        $this->assertEquals($url, $mediaView->getUrl());
    }
}
