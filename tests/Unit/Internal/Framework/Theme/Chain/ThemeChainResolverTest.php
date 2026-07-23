<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\Exception\ThemeInheritanceCycleException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChainResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use PHPUnit\Framework\TestCase;

final class ThemeChainResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';
    private const PARENT_THEME_ID = 'parent';

    public function testGetThemeChainReturnsSingleThemeWhenNoParentDeclared(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(false);

        $chain = (new ThemeChainResolver($themeParentProvider))->getThemeChain(self::THEME_ID, self::SHOP_ID);

        $this->assertSame([self::THEME_ID], $chain->getThemeIds());
    }

    public function testGetThemeChainReturnsThemeAndItsParent(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(true);
        $themeParentProvider->method('getParentThemeId')->willReturn(self::PARENT_THEME_ID);

        $chain = (new ThemeChainResolver($themeParentProvider))->getThemeChain(self::THEME_ID, self::SHOP_ID);

        $this->assertSame([self::THEME_ID, self::PARENT_THEME_ID], $chain->getThemeIds());
    }

    public function testGetThemeChainThrowsWhenThemeDeclaresItselfAsItsOwnParent(): void
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(true);
        $themeParentProvider->method('getParentThemeId')->willReturn(self::THEME_ID);

        $this->expectException(ThemeInheritanceCycleException::class);

        (new ThemeChainResolver($themeParentProvider))->getThemeChain(self::THEME_ID, self::SHOP_ID);
    }
}