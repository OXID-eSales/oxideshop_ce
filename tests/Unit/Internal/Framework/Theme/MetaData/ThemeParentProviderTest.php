<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ParentThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProvider;
use PHPUnit\Framework\TestCase;

final class ThemeParentProviderTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';

    public function testHasParentThemeReturnsTrueWhenParentThemeIsDeclared(): void
    {
        $service = $this->createService('apex');

        $this->assertTrue($service->hasParentTheme(self::THEME_ID, self::SHOP_ID));
    }

    public function testHasParentThemeReturnsFalseWhenNoParentThemeIsDeclared(): void
    {
        $service = $this->createService('');

        $this->assertFalse($service->hasParentTheme(self::THEME_ID, self::SHOP_ID));
    }

    public function testGetParentThemeIdReturnsDeclaredParentThemeId(): void
    {
        $service = $this->createService('apex');

        $this->assertSame('apex', $service->getParentThemeId(self::THEME_ID, self::SHOP_ID));
    }

    public function testGetParentThemeIdThrowsExceptionWhenNoParentThemeIsDeclared(): void
    {
        $service = $this->createService('');

        $this->expectException(ParentThemeNotFoundException::class);

        $service->getParentThemeId(self::THEME_ID, self::SHOP_ID);
    }

    public function testHasParentThemeReturnsFalseWhenMetaDataIsUnreadable(): void
    {
        $service = $this->createServiceWithUnreadableMetaData();

        $this->assertFalse($service->hasParentTheme(self::THEME_ID, self::SHOP_ID));
    }

    private function createService(string $declaredParentThemeId): ThemeParentProvider
    {
        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('get')->willReturn(
            (new ThemeMetaData())->setId(self::THEME_ID)->setParentTheme($declaredParentThemeId)
        );

        return new ThemeParentProvider($themeMetaDataByIdProvider);
    }

    private function createServiceWithUnreadableMetaData(): ThemeParentProvider
    {
        $themeMetaDataByIdProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $themeMetaDataByIdProvider->method('get')->willThrowException(
            new \InvalidArgumentException('Theme metadata file not readable')
        );

        return new ThemeParentProvider($themeMetaDataByIdProvider);
    }
}
