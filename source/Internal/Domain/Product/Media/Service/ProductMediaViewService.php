<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

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
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Filesystem\Path;

class ProductMediaViewService implements ProductMediaViewServiceInterface
{
    private string $detailSize;
    private string $iconSize;
    private string $zoomSize;
    private string $thumbnailSize;

    public function __construct(
        private ProductMediaDaoInterface $productMediaDao,
        private MediaUrlGeneratorInterface $mediaUrlGenerator,
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private ThemeConfigurationSettingDaoInterface $themeConfigurationSettingDao,
        private ShopAdapterInterface $shopAdapter,
        private ContextInterface $context,
        private TagAwareAdapterInterface $cache
    ) {
        $this->detailSize = $this->getConfiguredSize('sDetailImageSize');
        $this->iconSize = $this->getConfiguredSize('sIconsize');
        $this->zoomSize = $this->getConfiguredSize('sZoomImageSize');
        $this->thumbnailSize = $this->getConfiguredSize('sThumbnailsize');
    }

    public function getByRole(Id $productId, ProductMediaRole $role): MediaView
    {
        $cacheKey = $this->getCacheKey('role', $productId, $role->value());
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $productMedia = $this->getMediaWithFallback($productId, $role);
        $mediaView = $productMedia
            ? $this->createMediaViewWithAllSizes($productMedia)
            : $this->createFallbackMediaView();

        $cacheItem->set($mediaView);
        $cacheItem->tag(['product_media', 'product_' . $productId]);
        $cacheItem->expiresAfter(100);
        $this->cache->save($cacheItem);

        return $mediaView;
    }

    public function getByPosition(Id $productId, int $position): MediaView
    {
        $cacheKey = $this->getCacheKey('pos', $productId, (string) $position);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $productMedia = $this->productMediaDao->getActiveByPosition($productId, $position);
        $mediaView = $productMedia
            ? $this->createMediaViewWithAllSizes($productMedia)
            : $this->createFallbackMediaView();

        $cacheItem->set($mediaView);
        $cacheItem->tag(['product_media', 'product_' . $productId]);
        $cacheItem->expiresAfter(100);
        $this->cache->save($cacheItem);

        return $mediaView;
    }

    /** @return array<string, MediaView> */
    public function getAllByRole(Id $productId, ProductMediaRole $role): array
    {
        $productMediaCollection = $this->productMediaDao->getAllActiveByRole(
            $productId,
            $role
        );
        $mediaViews = [];

        foreach ($productMediaCollection as $productMedia) {
            $mediaViews[(string) $productMedia->getMedia()->getId()] =
                $this->createMediaViewWithAllSizes($productMedia);
        }

        return $mediaViews;
    }

    private function getMediaWithFallback(Id $productId, ProductMediaRole $role): ?ProductMedia
    {
        $productMedia = $this->productMediaDao->getActiveByRole($productId, $role);
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
        return new MediaView(
            url: $this->mediaUrlGenerator->generateSizedImageUrl($media, $this->detailSize),
            iconUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $this->iconSize),
            zoomUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $this->zoomSize),
            thumbnailUrl: $this->mediaUrlGenerator->generateSizedImageUrl($media, $this->thumbnailSize),
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

    private function getCacheKey(string $type, Id $productId, string $qualifier): string
    {
        return sprintf('product_media_%s_%s_%s', $type, $productId, $qualifier);
    }
}
