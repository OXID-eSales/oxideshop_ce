<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaImageSizes;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\Dao\ThemeSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

readonly class ProductMediaImageSizesProvider implements ProductMediaImageSizesProviderInterface
{
    public function __construct(
        private ThemeSettingDaoInterface $themeSettingDao,
        private ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        private ThemeStateServiceInterface $themeStateService,
        private ContextInterface $context,
    ) {
    }

    public function getSizes(): ProductMediaImageSizes
    {
        return new ProductMediaImageSizes(
            detailSize: $this->getConfiguredSize('sDetailImageSize'),
            iconSize: $this->getConfiguredSize('sIconsize'),
            zoomSize: $this->getConfiguredSize('sZoomImageSize'),
            thumbnailSize: $this->getConfiguredSize('sThumbnailsize'),
        );
    }

    private function getConfiguredSize(string $settingName): string
    {
        try {
            $setting = $this->themeSettingDao->get(
                $settingName,
                $this->context->getCurrentShopId(),
                $this->themeStateService->getActiveThemeId($this->context->getCurrentShopId())
            );
        } catch (EntryDoesNotExistDaoException) {
            $setting = $this->shopConfigurationSettingDao->get(
                $settingName,
                $this->context->getCurrentShopId()
            );
        }

        return (string) $setting->getValue();
    }
}
