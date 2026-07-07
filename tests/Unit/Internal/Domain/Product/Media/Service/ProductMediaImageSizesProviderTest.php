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
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\Dao\ThemeSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Config\DataObject\ThemeSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

final class ProductMediaImageSizesProviderTest extends TestCase
{
    public function testFallsBackToShopSettingsWhenThemeSettingMissing(): void
    {
        $themeSettingDao = $this->createStub(ThemeSettingDaoInterface::class);
        $themeSettingDao
            ->method('get')
            ->willThrowException(new EntryDoesNotExistDaoException());
        $shopConfigurationSettingDao = $this->createStub(ShopConfigurationSettingDaoInterface::class);
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturnMap([
                ['sDetailImageSize', 1, $this->createShopConfigurationSetting('540*540')],
                ['sIconsize', 1, $this->createShopConfigurationSetting('56*56')],
                ['sZoomImageSize', 1, $this->createShopConfigurationSetting('900*900')],
                ['sThumbnailsize', 1, $this->createShopConfigurationSetting('100*100')],
            ]);
        $provider = $this->createProvider($themeSettingDao, $shopConfigurationSettingDao);

        $sizes = $provider->getSizes();

        $this->assertSame('540*540', $sizes->getDetailSize());
        $this->assertSame('56*56', $sizes->getIconSize());
        $this->assertSame('900*900', $sizes->getZoomSize());
        $this->assertSame('100*100', $sizes->getThumbnailSize());
    }

    public function testFallsBackToShopSettingsOnlyForMissingThemeSettings(): void
    {
        $themeSettingDao = $this->createStub(ThemeSettingDaoInterface::class);
        $themeSettingDao
            ->method('get')
            ->willReturnCallback(fn(string $name, int $shopId, string $themeId): ThemeSetting => match ($name) {
                'sDetailImageSize' => $this->createThemeSetting('600*600'),
                'sZoomImageSize' => $this->createThemeSetting('1200*1200'),
                default => throw new EntryDoesNotExistDaoException(),
            });
        $shopConfigurationSettingDao = $this->createStub(ShopConfigurationSettingDaoInterface::class);
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturnMap([
                ['sIconsize', 1, $this->createShopConfigurationSetting('56*56')],
                ['sThumbnailsize', 1, $this->createShopConfigurationSetting('100*100')],
            ]);
        $provider = $this->createProvider($themeSettingDao, $shopConfigurationSettingDao);

        $sizes = $provider->getSizes();

        $this->assertSame('600*600', $sizes->getDetailSize());
        $this->assertSame('56*56', $sizes->getIconSize());
        $this->assertSame('1200*1200', $sizes->getZoomSize());
        $this->assertSame('100*100', $sizes->getThumbnailSize());
    }

    private function createProvider(
        ThemeSettingDaoInterface $themeSettingDao,
        ?ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao = null,
    ): ProductMediaImageSizesProviderInterface {
        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('getActiveThemeId')->willReturn('apex');

        return new ProductMediaImageSizesProvider(
            themeSettingDao: $themeSettingDao,
            shopConfigurationSettingDao: $shopConfigurationSettingDao
                ?? $this->createStub(ShopConfigurationSettingDaoInterface::class),
            themeStateService: $themeStateService,
            context: new ContextStub()
        );
    }

    private function createThemeSetting(string $value): ThemeSetting
    {
        return (new ThemeSetting())->setValue($value);
    }

    private function createShopConfigurationSetting(string $value): ShopConfigurationSetting
    {
        return (new ShopConfigurationSetting())->setValue($value);
    }
}
