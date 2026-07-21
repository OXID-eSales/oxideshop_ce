<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaImageSizesProvider;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaImageSizesProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

final class ProductMediaImageSizesProviderTest extends TestCase
{
    public function testFallsBackToShopSettingsWhenThemeSettingMissing(): void
    {
        $themeSettingService = $this->createStub(ThemeSettingServiceInterface::class);
        $themeSettingService->method('exists')->willReturn(false);

        $shopConfigurationSettingDao = $this->createStub(ShopConfigurationSettingDaoInterface::class);
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturnMap([
                ['detailImageSize', 1, $this->createShopConfigurationSetting('540*540')],
                ['iconSize', 1, $this->createShopConfigurationSetting('56*56')],
                ['zoomImageSize', 1, $this->createShopConfigurationSetting('900*900')],
                ['thumbnailSize', 1, $this->createShopConfigurationSetting('100*100')],
            ]);
        $provider = $this->createProvider($themeSettingService, $shopConfigurationSettingDao);

        $sizes = $provider->getSizes();

        $this->assertSame('540*540', $sizes->getDetailSize());
        $this->assertSame('56*56', $sizes->getIconSize());
        $this->assertSame('900*900', $sizes->getZoomSize());
        $this->assertSame('100*100', $sizes->getThumbnailSize());
    }

    public function testFallsBackToShopSettingsOnlyForMissingThemeSettings(): void
    {
        $themeSettingService = $this->createStub(ThemeSettingServiceInterface::class);
        $themeSettingService
            ->method('exists')
            ->willReturnCallback(fn(string $name): bool => match ($name) {
                'detailImageSize', 'zoomImageSize' => true,
                default => false,
            });
        $themeSettingService
            ->method('getString')
            ->willReturnCallback(fn(string $name): string => match ($name) {
                'detailImageSize' => '600*600',
                'zoomImageSize' => '1200*1200',
            });

        $shopConfigurationSettingDao = $this->createStub(ShopConfigurationSettingDaoInterface::class);
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturnMap([
                ['iconSize', 1, $this->createShopConfigurationSetting('56*56')],
                ['thumbnailSize', 1, $this->createShopConfigurationSetting('100*100')],
            ]);
        $provider = $this->createProvider($themeSettingService, $shopConfigurationSettingDao);

        $sizes = $provider->getSizes();

        $this->assertSame('600*600', $sizes->getDetailSize());
        $this->assertSame('56*56', $sizes->getIconSize());
        $this->assertSame('1200*1200', $sizes->getZoomSize());
        $this->assertSame('100*100', $sizes->getThumbnailSize());
    }

    private function createProvider(
        ThemeSettingServiceInterface $themeSettingService,
        ?ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao = null,
    ): ProductMediaImageSizesProviderInterface {
        return new ProductMediaImageSizesProvider(
            themeSettingService: $themeSettingService,
            shopConfigurationSettingDao: $shopConfigurationSettingDao
                ?? $this->createStub(ShopConfigurationSettingDaoInterface::class),
            context: new ContextStub()
        );
    }

    private function createShopConfigurationSetting(string $value): ShopConfigurationSetting
    {
        return (new ShopConfigurationSetting())->setValue($value);
    }
}
