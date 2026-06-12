<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Factory;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUrlGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaImageSizesProviderInterface;

readonly class ProductMediaViewFactory implements ProductMediaViewFactoryInterface
{
    public function __construct(
        private MediaUrlGeneratorInterface $mediaUrlGenerator,
        private ProductMediaImageSizesProviderInterface $imageSizesProvider,
        private MediaAttributeViewServiceInterface $attributeViewService,
        private FallbackMediaFactoryInterface $fallbackMediaFactory,
    ) {
    }

    public function create(Media $media): ProductMediaView
    {
        return $this->createView(
            $media,
            $this->attributeViewService->getAttributes($media),
            false
        );
    }

    public function createFallback(): ProductMediaView
    {
        return $this->createView(
            $this->fallbackMediaFactory->create(),
            new MediaAttributes(),
            true
        );
    }

    private function createView(Media $media, MediaAttributes $attributes, bool $isFallback): ProductMediaView
    {
        $sizes = $this->imageSizesProvider->getSizes();

        return new ProductMediaView(
            detailUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $sizes->getDetailSize()),
            iconUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $sizes->getIconSize()),
            zoomUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $sizes->getZoomSize()),
            thumbnailUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $sizes->getThumbnailSize()),
            attributes: $attributes,
            isFallback: $isFallback,
        );
    }
}
