<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaImageSizes;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class ProductMediaImageSizesProvider implements ProductMediaImageSizesProviderInterface
{
    public function __construct(
        private ThemeSettingServiceInterface $themeSettingService,
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private ContextInterface $context,
    ) {
    }

    public function getSizes(): ProductMediaImageSizes
    {
        return new ProductMediaImageSizes(
            detailSize: $this->getConfiguredSize('detailImageSize'),
            iconSize: $this->getConfiguredSize('iconSize'),
            zoomSize: $this->getConfiguredSize('zoomImageSize'),
            thumbnailSize: $this->getConfiguredSize('thumbnailSize'),
        );
    }

    private function getConfiguredSize(string $settingName): string
    {
        if ($this->themeSettingService->exists($settingName)) {
            return $this->themeSettingService->getString($settingName);
        }

        return (string) $this->shopConfigurationSettingDao->get(
            $settingName,
            $this->context->getCurrentShopId()
        )->getValue();
    }
}
