<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaImageSizesProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;
use PHPUnit\Framework\TestCase;

final class ProductMediaImageSizesProviderTest extends TestCase
{
    public function testReadsSizesFromThemeSettings(): void
    {
        $themeSettingService = $this->createStub(ThemeSettingServiceInterface::class);
        $themeSettingService
            ->method('getString')
            ->willReturnMap([
                ['detailImageSize', '600*600'],
                ['iconSize', '56*56'],
                ['zoomImageSize', '1200*1200'],
                ['thumbnailSize', '100*100'],
            ]);

        $sizes = (new ProductMediaImageSizesProvider($themeSettingService))->getSizes();

        $this->assertSame('600*600', $sizes->getDetailSize());
        $this->assertSame('56*56', $sizes->getIconSize());
        $this->assertSame('1200*1200', $sizes->getZoomSize());
        $this->assertSame('100*100', $sizes->getThumbnailSize());
    }
}
