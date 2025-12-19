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

class MediaUrlGenerator implements MediaUrlGeneratorInterface
{
    private string $generatedBaseUrl;
    private string $picturesRoot;
    private int $imageQuality;

    public function __construct(
        ContextInterface $context,
        ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        string $alternativeImageUrl = ''
    ) {
        $this->generatedBaseUrl = $alternativeImageUrl
            ? Path::join($alternativeImageUrl, 'generated')
            : Path::join($context->getShopBaseUrl(), 'out', 'pictures', 'generated');

        $this->picturesRoot = Path::join(
            Path::makeRelative($context->getOutPath(), $context->getSourcePath()),
            'pictures'
        );

        $this->imageQuality = (int) $shopConfigurationSettingDao
            ->get('sDefaultImageQuality', $context->getCurrentShopId())
            ->getValue();
    }

    public function generateSizedImageUrl(Media $media, string $size): string
    {
        $relativeMediaPath = Path::makeRelative((string) $media->getMediaPath(), $this->picturesRoot);

        return Path::join(
            $this->generatedBaseUrl,
            dirname($relativeMediaPath),
            $this->buildSizePath($size),
            rawurlencode(basename($relativeMediaPath))
        );
    }

    private function buildSizePath(string $size): string
    {
        [$width, $height] = explode('*', $size);
        return "{$width}_{$height}_{$this->imageQuality}";
    }
}
