<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUrlGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\MediaAttributeViewServiceInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\Dao\ThemeSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ProductMediaViewService implements ProductMediaViewServiceInterface
{
    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
        private MediaUrlGeneratorInterface $mediaUrlGenerator,
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private ThemeSettingDaoInterface $themeSettingDao,
        private ShopAdapterInterface $shopAdapter,
        private ContextInterface $context,
        private MediaAttributeViewServiceInterface $attributeService,
        private ActiveLocaleProviderInterface $activeLocaleProvider,
    ) {
    }

    public function getByRole(Id $productId, ProductMediaRole $role): ProductMediaView
    {
        $productMedia = $this->getMediaWithFallback($productId, $role);
        return $productMedia
            ? $this->createMediaViewWithAllSizes($productMedia)
            : $this->createFallbackMediaView();
    }

    public function getByPosition(Id $productId, int $position): ProductMediaView
    {
        $productMedia = $this->productMediaDao->getActiveByPosition($productId, $position);

        if (!$productMedia) {
            return $this->createFallbackMediaView();
        }

        return $this->createMediaViewWithAllSizes($productMedia);
    }

    /** @return array<string, ProductMediaView> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): array
    {
        $productMediaCollection = $this->productMediaDao->getAllActiveByRole($productId, $role);
        if ($productMediaCollection->isEmpty()) {
            return [];
        }

        $activeLocale = $this->activeLocaleProvider->getActiveLocale();

        $mediaViews = [];
        foreach ($productMediaCollection as $productMedia) {
            $media = $productMedia->getMedia();
            $mediaId = (string) $media->getId();
            $mediaViews[$mediaId] = $this->createMediaView(
                $media,
                $this->attributeService->getAttributes($media, $activeLocale->getCode()),
                false
            );
        }

        return $mediaViews;
    }

    private function getMediaWithFallback(Id $productId, ProductMediaRole $role): ?ProductMedia
    {
        $productMedia = $this->productMediaDao->getActiveByRole($productId, $role);
        return $productMedia ?: $this->productMediaDao->getFirstActive($productId);
    }

    private function createMediaViewWithAllSizes(ProductMedia $productMedia): ProductMediaView
    {
        $media = $productMedia->getMedia();
        return $this->createMediaView(
            $media,
            $this->attributeService->getAttributes(
                $media,
                $this->activeLocaleProvider->getActiveLocale()->getCode()
            ),
            false
        );
    }

    private function createFallbackMediaView(): ProductMediaView
    {
        return $this->createMediaView($this->createFallbackMedia(), new MediaAttributes(), true);
    }

    private function createMediaView(
        Media $media,
        MediaAttributes $attributes,
        bool $isFallback
    ): ProductMediaView {
        $detailSize = $this->getConfiguredSize('sDetailImageSize');
        $iconSize = $this->getConfiguredSize('sIconsize');
        $zoomSize = $this->getConfiguredSize('sZoomImageSize');
        $thumbnailSize = $this->getConfiguredSize('sThumbnailsize');

        return new ProductMediaView(
            detailUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $detailSize),
            iconUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $iconSize),
            zoomUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $zoomSize),
            thumbnailUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $thumbnailSize),
            attributes: $attributes,
            isFallback: $isFallback,
        );
    }

    private function createFallbackMedia(): Media
    {
        $fallbackFilename = $this->getFallbackFilename();
        $mimeType = str_ends_with($fallbackFilename, '.webp') ? 'image/webp' : 'image/jpeg';

        return new Media(
            Id::generate(),
            new MediaPath(Path::join('out', 'pictures', 'media', $fallbackFilename)),
            new MediaType($mimeType)
        );
    }

    private function getFallbackFilename(): string
    {
        $convertToWebP = $this->shopConfigurationSettingDao->get(
            'blConvertImagesToWebP',
            $this->context->getCurrentShopId()
        );

        return $convertToWebP->getValue() ? 'nopic.webp' : 'nopic.jpg';
    }

    private function getConfiguredSize(string $sizeConfigKey): string
    {
        try {
            $setting = $this->themeSettingDao->get(
                $sizeConfigKey,
                $this->context->getCurrentShopId(),
                $this->shopAdapter->getActiveThemeId()
            );
        } catch (EntryDoesNotExistDaoException $e) {
            $setting = $this->shopConfigurationSettingDao->get(
                $sizeConfigKey,
                $this->context->getCurrentShopId()
            );
        }

        return (string) $setting->getValue();
    }
}
