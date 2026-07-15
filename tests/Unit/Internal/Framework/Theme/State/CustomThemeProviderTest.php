<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\CustomThemeProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\CustomThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use PHPUnit\Framework\TestCase;

final class CustomThemeProviderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testHasCustomThemeReturnsTrueWhenActiveThemeDeclaresAParent(): void
    {
        $service = $this->createService(activeThemeId: 'child', activeThemeHasParent: true);

        $this->assertTrue($service->hasCustomTheme(self::SHOP_ID));
    }

    public function testHasCustomThemeReturnsFalseWhenActiveThemeDeclaresNoParent(): void
    {
        $service = $this->createService(activeThemeId: 'theme', activeThemeHasParent: false);

        $this->assertFalse($service->hasCustomTheme(self::SHOP_ID));
    }

    public function testHasCustomThemeReturnsFalseWhenNoThemeIsActive(): void
    {
        $service = $this->createService(activeThemeId: null, activeThemeHasParent: false);

        $this->assertFalse($service->hasCustomTheme(self::SHOP_ID));
    }

    public function testHasCustomThemeReturnsFalseWhenAnInactiveThemeDeclaresAParent(): void
    {
        $service = $this->createService(activeThemeId: 'theme', activeThemeHasParent: false, inactiveThemeWithParent: 'child');

        $this->assertFalse($service->hasCustomTheme(self::SHOP_ID));
    }

    public function testGetCustomThemeIdReturnsActiveThemeIdWhenItDeclaresAParent(): void
    {
        $service = $this->createService(activeThemeId: 'child', activeThemeHasParent: true);

        $this->assertSame('child', $service->getCustomThemeId(self::SHOP_ID));
    }

    public function testGetCustomThemeIdThrowsExceptionWhenActiveThemeDeclaresNoParent(): void
    {
        $service = $this->createService(activeThemeId: 'theme', activeThemeHasParent: false);

        $this->expectException(CustomThemeNotFoundException::class);

        $service->getCustomThemeId(self::SHOP_ID);
    }

    public function testGetCustomThemeIdThrowsExceptionWhenNoThemeIsActive(): void
    {
        $service = $this->createService(activeThemeId: null, activeThemeHasParent: false);

        $this->expectException(CustomThemeNotFoundException::class);

        $service->getCustomThemeId(self::SHOP_ID);
    }

    private function createService(
        ?string $activeThemeId,
        bool $activeThemeHasParent,
        ?string $inactiveThemeWithParent = null,
    ): CustomThemeProvider {
        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        if ($activeThemeId === null) {
            $themeStateService->method('getActiveThemeId')->willThrowException(new ActiveThemeNotFoundException());
        } else {
            $themeStateService->method('getActiveThemeId')->willReturn($activeThemeId);
        }

        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturnCallback(
            fn(string $themeId): bool => match ($themeId) {
                $activeThemeId => $activeThemeHasParent,
                $inactiveThemeWithParent => true,
                default => false,
            }
        );

        return new CustomThemeProvider($themeStateService, $themeParentProvider);
    }
}