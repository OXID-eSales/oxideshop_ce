<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaView;
use OxidEsales\EshopCommunity\Internal\Domain\Media\MediaUrlGeneratorInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Dao\ProductMediaDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMedia;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaRole;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ThemeConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use Symfony\Component\Filesystem\Path;

readonly class ProductMediaViewService implements ProductMediaViewServiceInterface
{
    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
        private MediaUrlGeneratorInterface $mediaUrlGenerator,
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private ThemeConfigurationSettingDaoInterface $themeConfigurationSettingDao,
        private ShopAdapterInterface $shopAdapter,
        private ContextInterface $context
    ) {
    }

    public function getMedia(Id $productId, int $position): MediaView
    {
        $productMedia = $this->productMediaDao->getByPosition($productId, $position);

        if (!$productMedia) {
            return $this->createFallbackMediaView();
        }

        return $this->createMediaViewWithAllSizes($productMedia);
    }

    public function getIcon(Id $productId): MediaView
    {
        $productMedia = $this->getMediaWithFallback($productId, ProductMediaRole::from(ProductMediaRole::ICON));
        return $productMedia ? $this->createMediaViewWithAllSizes($productMedia) : $this->createFallbackMediaView();
    }

    public function getThumbnail(Id $productId): MediaView
    {
        $productMedia = $this->getMediaWithFallback($productId, ProductMediaRole::from(ProductMediaRole::THUMBNAIL));
        return $productMedia ? $this->createMediaViewWithAllSizes($productMedia) : $this->createFallbackMediaView();
    }

    /**
     * @return array<string, MediaView>
     */
    public function getActiveByProductId(Id $productId): array
    {
        $productMediaCollection = $this->productMediaDao->getActiveByProductId($productId);
        $mediaViews = [];

        foreach ($productMediaCollection as $productMedia) {
            $mediaViews[(string)$productMedia->getMedia()->getId()] = $this->createMediaViewWithAllSizes($productMedia);
        }

        return $mediaViews;
    }

    private function getMediaWithFallback(Id $productId, ProductMediaRole $role): ?ProductMedia
    {
        $productMedia = $this->productMediaDao->getByRole($productId, $role->value());
        return $productMedia ?: $this->productMediaDao->getFirstActive($productId);
    }

    private function createMediaViewWithAllSizes(ProductMedia $productMedia): MediaView
    {
        return $this->createMediaView($productMedia->getMedia(), false);
    }

    private function createFallbackMediaView(): MediaView
    {
        return $this->createMediaView($this->createFallbackMedia(), true);
    }

    private function createMediaView(Media $media, bool $isFallback): MediaView
    {
        $detailSize = $this->getConfiguredSize('sDetailImageSize');
        $iconSize = $this->getConfiguredSize('sIconsize');
        $zoomSize = $this->getConfiguredSize('sZoomImageSize');
        $thumbnailSize = $this->getConfiguredSize('sThumbnailsize');

        return new MediaView(
            url: $this->mediaUrlGenerator->generateSizedImageUrl($media, $detailSize),
            iconUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $iconSize),
            zoomUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $zoomSize),
            thumbnailUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $thumbnailSize),
            isFallback: $isFallback
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
            $setting = $this->themeConfigurationSettingDao->get(
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
