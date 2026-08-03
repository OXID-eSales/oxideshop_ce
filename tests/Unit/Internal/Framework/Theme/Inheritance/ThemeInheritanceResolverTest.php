<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Inheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeInheritanceDepthExceededException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use PHPUnit\Framework\TestCase;

final class ThemeInheritanceResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';
    private const PARENT_THEME_ID = 'parent';

    public function testResolveReturnsThemeWithoutParentWhenNoParentDeclared(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(false);

        $inheritance = (new ThemeInheritanceResolver($themeParentProvider))->resolve(self::THEME_ID, self::SHOP_ID);

        $this->assertSame(self::THEME_ID, $inheritance->getThemeId());
        $this->assertFalse($inheritance->hasParentTheme());
    }

    public function testResolveReturnsThemeAndItsParent(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturnMap([
            [self::THEME_ID, self::SHOP_ID, true],
            [self::PARENT_THEME_ID, self::SHOP_ID, false],
        ]);
        $themeParentProvider->method('getParentThemeId')->willReturn(self::PARENT_THEME_ID);

        $inheritance = (new ThemeInheritanceResolver($themeParentProvider))->resolve(self::THEME_ID, self::SHOP_ID);

        $this->assertSame(self::THEME_ID, $inheritance->getThemeId());
        $this->assertSame(self::PARENT_THEME_ID, $inheritance->getParentThemeId());
    }

    public function testResolveThrowsWhenThemeDeclaresItselfAsItsOwnParent(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(true);
        $themeParentProvider->method('getParentThemeId')->willReturn(self::THEME_ID);

        $this->expectException(ThemeInheritanceCycleException::class);

        (new ThemeInheritanceResolver($themeParentProvider))->resolve(self::THEME_ID, self::SHOP_ID);
    }

    public function testResolveThrowsWhenParentThemeIsItselfAChildTheme(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturnMap([
            [self::THEME_ID, self::SHOP_ID, true],
            [self::PARENT_THEME_ID, self::SHOP_ID, true],
        ]);
        $themeParentProvider->method('getParentThemeId')->willReturn(self::PARENT_THEME_ID);

        $this->expectException(ThemeInheritanceDepthExceededException::class);

        (new ThemeInheritanceResolver($themeParentProvider))->resolve(self::THEME_ID, self::SHOP_ID);
    }
}
