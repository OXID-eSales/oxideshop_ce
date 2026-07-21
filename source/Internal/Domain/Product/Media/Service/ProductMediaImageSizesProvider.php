<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaImageSizes;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;

readonly class ProductMediaImageSizesProvider implements ProductMediaImageSizesProviderInterface
{
    public function __construct(
        private ThemeSettingServiceInterface $themeSettingService,
    ) {
    }

    public function getSizes(): ProductMediaImageSizes
    {
        return new ProductMediaImageSizes(
            detailSize: $this->themeSettingService->getString('detailImageSize'),
            iconSize: $this->themeSettingService->getString('iconSize'),
            zoomSize: $this->themeSettingService->getString('zoomImageSize'),
            thumbnailSize: $this->themeSettingService->getString('thumbnailSize'),
        );
    }
}
