<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ThemeParentNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ActiveThemeDirectoryResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use PHPUnit\Framework\TestCase;

final class ActiveThemeDirectoryResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'child';
    private const PARENT_THEME_ID = 'parent';
    private const THEME_PATH = '/var/www/vendor/oxid-esales/child-theme';

    public function testGetDirectoryReturnsPathWithTrailingSeparator(): void
    {
        $resolver = $this->createResolver($this->createPathResolverStub(self::THEME_ID, self::THEME_PATH));

        $this->assertSame(
            self::THEME_PATH . DIRECTORY_SEPARATOR,
            $resolver->getDirectory($this->createActiveTheme(hasParent: false), self::SHOP_ID)
        );
    }

    public function testHasParentDirectoryReturnsFalseWhenThemeHasNoParent(): void
    {
        $themePathResolver = $this->createMock(ThemePathResolverInterface::class);
        $themePathResolver->expects($this->never())->method('getFullThemePathFromConfiguration');

        $resolver = $this->createResolver($themePathResolver);

        $this->assertFalse(
            $resolver->hasParentDirectory($this->createActiveTheme(hasParent: false), self::SHOP_ID)
        );
    }

    public function testHasParentDirectoryReturnsTrueWhenParentPathResolves(): void
    {
        $resolver = $this->createResolver($this->createPathResolverStub(self::PARENT_THEME_ID, self::THEME_PATH));

        $this->assertTrue(
            $resolver->hasParentDirectory($this->createActiveTheme(hasParent: true), self::SHOP_ID)
        );
    }

    public function testHasParentDirectoryReturnsFalseWhenParentConfigurationIsMissing(): void
    {
        $themePathResolver = $this->createStub(ThemePathResolverInterface::class);
        $themePathResolver->method('getFullThemePathFromConfiguration')
            ->willThrowException(new ThemeConfigurationNotFoundException());

        $resolver = $this->createResolver($themePathResolver);

        $this->assertFalse(
            $resolver->hasParentDirectory($this->createActiveTheme(hasParent: true), self::SHOP_ID)
        );
    }

    public function testGetParentDirectoryReturnsParentPath(): void
    {
        $resolver = $this->createResolver($this->createPathResolverStub(self::PARENT_THEME_ID, self::THEME_PATH));

        $this->assertSame(
            self::THEME_PATH . DIRECTORY_SEPARATOR,
            $resolver->getParentDirectory($this->createActiveTheme(hasParent: true), self::SHOP_ID)
        );
    }

    public function testGetParentDirectoryThrowsWhenThemeHasNoParent(): void
    {
        $resolver = $this->createResolver($this->createStub(ThemePathResolverInterface::class));

        $this->expectException(ThemeParentNotFoundException::class);

        $resolver->getParentDirectory($this->createActiveTheme(hasParent: false), self::SHOP_ID);
    }

    private function createActiveTheme(bool $hasParent): ActiveTheme
    {
        return new ActiveTheme(
            new ThemeInheritance(self::THEME_ID, $hasParent ? self::PARENT_THEME_ID : null)
        );
    }

    private function createPathResolverStub(string $expectedThemeId, string $path): ThemePathResolverInterface
    {
        $themePathResolver = $this->createStub(ThemePathResolverInterface::class);
        $themePathResolver->method('getFullThemePathFromConfiguration')->willReturnCallback(
            fn(string $themeId, int $shopId): string => $themeId === $expectedThemeId && $shopId === self::SHOP_ID
                ? $path
                : throw new ThemeConfigurationNotFoundException()
        );

        return $themePathResolver;
    }

    private function createResolver(ThemePathResolverInterface $themePathResolver): ActiveThemeDirectoryResolver
    {
        return new ActiveThemeDirectoryResolver($themePathResolver);
    }
}
