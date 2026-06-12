<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Factory;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\Media;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class FallbackMediaFactory implements FallbackMediaFactoryInterface
{
    public function __construct(
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private ContextInterface $context,
    ) {
    }

    public function create(): Media
    {
        $isWebP = $this->isWebPConversionEnabled();

        return new Media(
            Id::generate(),
            new MediaPath(
                Path::join('out', 'pictures', 'media', $isWebP ? 'nopic.webp' : 'nopic.jpg')
            ),
            new MediaType($isWebP ? 'image/webp' : 'image/jpeg')
        );
    }

    private function isWebPConversionEnabled(): bool
    {
        $setting = $this->shopConfigurationSettingDao->get(
            'blConvertImagesToWebP',
            $this->context->getCurrentShopId()
        );

        return (bool) $setting->getValue();
    }
}
