<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class MediaUrlGenerator implements MediaUrlGeneratorInterface
{
    public function __construct(
        private ContextInterface $context,
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private string $alternativeImageUrl = ''
    ) {
    }

    public function generateSizedImageUrl(Media $media, string $size): string
    {
        $picturesRoot = Path::join(
            Path::makeRelative($this->context->getOutPath(), $this->context->getSourcePath()),
            'pictures'
        );
        $relativeMediaPath = Path::makeRelative((string) $media->getMediaPath(), $picturesRoot);

        return Path::join(
            $this->getGeneratedBaseUrl(),
            dirname($relativeMediaPath),
            $this->buildSizePath($size),
            rawurlencode(basename($relativeMediaPath))
        );
    }

    private function getGeneratedBaseUrl(): string
    {
        return $this->alternativeImageUrl
            ? Path::join($this->alternativeImageUrl, 'generated')
            : Path::join($this->context->getShopBaseUrl(), 'out', 'pictures', 'generated');
    }



    private function buildSizePath(string $size): string
    {
        $setting = $this->shopConfigurationSettingDao->get(
            'sDefaultImageQuality',
            $this->context->getCurrentShopId()
        );
        $quality = (int) $setting->getValue();

        [$width, $height] = explode('*', $size);
        return "{$width}_{$height}_{$quality}";
    }
}
