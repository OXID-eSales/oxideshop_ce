<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use PHPUnit\Framework\TestCase;

final class ActiveThemeProviderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetActiveThemeIdReturnsActivatedThemeId(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'inactive' => (new ThemeConfiguration())->setId('inactive'),
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);

        $provider = new ActiveThemeProvider($dao, $this->createStub(ThemeMetaDataByIdProviderInterface::class));

        $this->assertSame('active', $provider->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIdThrowsWhenNoThemeIsActive(): void
    {
        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createProviderWithoutActiveTheme()->getActiveThemeId(self::SHOP_ID);
    }

    public function testGetActiveThemeCarriesParentThemeId(): void
    {
        $activeTheme = $this->createProvider('active', 'parent')->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertTrue($activeTheme->hasParentTheme());
        $this->assertSame('parent', $activeTheme->getParentThemeId());
    }

    public function testGetActiveThemeHasNoParentThemeIdForStandaloneTheme(): void
    {
        $activeTheme = $this->createProvider('active', '')->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertFalse($activeTheme->hasParentTheme());
        $this->assertSame('', $activeTheme->getParentThemeId());
    }

    public function testGetActiveThemeThrowsWhenNoThemeIsActive(): void
    {
        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createProviderWithoutActiveTheme()->getActiveTheme(self::SHOP_ID);
    }

    private function createProvider(string $themeId, string $parentThemeId): ActiveThemeProvider
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            $themeId => (new ThemeConfiguration())->setId($themeId)->setActivated(true),
        ]);

        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willReturn(
            (new ThemeMetaData())->setId($themeId)->setParentTheme($parentThemeId)
        );

        return new ActiveThemeProvider($dao, $metaDataProvider);
    }

    private function createProviderWithoutActiveTheme(): ActiveThemeProvider
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn(['inactive' => (new ThemeConfiguration())->setId('inactive')]);

        return new ActiveThemeProvider($dao, $this->createStub(ThemeMetaDataByIdProviderInterface::class));
    }
}
