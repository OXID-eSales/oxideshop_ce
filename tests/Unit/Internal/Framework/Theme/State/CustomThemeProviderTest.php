<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\CustomThemeProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\CustomThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeParentProviderInterface;
use PHPUnit\Framework\TestCase;

final class CustomThemeProviderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testHasCustomThemeReturnsTrueWhenAThemeDeclaresAParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme' => (new ThemeConfiguration())->setId('theme'),
            'child' => (new ThemeConfiguration())->setId('child'),
        ]);

        $service = $this->createService($dao, ['child' => true]);

        $this->assertTrue($service->hasCustomTheme(self::SHOP_ID));
    }

    public function testHasCustomThemeReturnsFalseWhenNoThemeDeclaresAParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme' => (new ThemeConfiguration())->setId('theme'),
        ]);

        $service = $this->createService($dao, []);

        $this->assertFalse($service->hasCustomTheme(self::SHOP_ID));
    }

    public function testGetCustomThemeIdReturnsIdOfThemeDeclaringAParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme' => (new ThemeConfiguration())->setId('theme'),
            'child' => (new ThemeConfiguration())->setId('child'),
        ]);

        $service = $this->createService($dao, ['child' => true]);

        $this->assertSame('child', $service->getCustomThemeId(self::SHOP_ID));
    }

    public function testGetCustomThemeIdThrowsExceptionWhenNoThemeDeclaresAParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme' => (new ThemeConfiguration())->setId('theme'),
        ]);

        $service = $this->createService($dao, []);

        $this->expectException(CustomThemeNotFoundException::class);

        $service->getCustomThemeId(self::SHOP_ID);
    }

    public function testThemeWithUnreadableMetadataIsTreatedAsHavingNoParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'broken' => (new ThemeConfiguration())->setId('broken'),
            'child' => (new ThemeConfiguration())->setId('child'),
        ]);

        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturnCallback(function (string $themeId): bool {
            if ($themeId === 'broken') {
                throw new \InvalidArgumentException('Theme metadata file not readable');
            }

            return $themeId === 'child';
        });

        $service = new CustomThemeProvider($dao, $themeParentProvider);

        $this->assertSame('child', $service->getCustomThemeId(self::SHOP_ID));
    }

    /** @param array<string, bool> $parentByThemeId */
    private function createService(ThemeConfigurationDaoInterface $dao, array $parentByThemeId): CustomThemeProvider
    {
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturnCallback(
            fn(string $themeId): bool => $parentByThemeId[$themeId] ?? false
        );

        return new CustomThemeProvider($dao, $themeParentProvider);
    }
}
