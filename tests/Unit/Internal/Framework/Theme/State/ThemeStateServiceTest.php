<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeParentProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateService;
use PHPUnit\Framework\TestCase;

final class ThemeStateServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testIsActiveReturnsTrueWhenThemeIsActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $dao->method('get')->willReturn((new ThemeConfiguration())->setActivated(true));

        $this->assertTrue($this->createService($dao)->isActive('theme', self::SHOP_ID));
    }

    public function testIsActiveReturnsFalseWhenThemeIsNotActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $dao->method('get')->willReturn(new ThemeConfiguration());

        $this->assertFalse($this->createService($dao)->isActive('theme', self::SHOP_ID));
    }

    public function testIsActiveReturnsFalseWhenThemeDoesNotExist(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);

        $this->assertFalse($this->createService($dao)->isActive('unknown', self::SHOP_ID));
    }

    public function testGetActiveThemeIdReturnsIdOfActivatedTheme(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'inactive' => (new ThemeConfiguration())->setId('inactive'),
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);

        $this->assertSame('active', $this->createService($dao)->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIdThrowsExceptionWhenNoThemeIsActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme1' => (new ThemeConfiguration())->setId('theme1'),
            'theme2' => (new ThemeConfiguration())->setId('theme2'),
        ]);

        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createService($dao)->getActiveThemeId(self::SHOP_ID);
    }

    public function testGetActiveThemeIdResolvesThemeOnlyOnce(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->once())->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $service = $this->createService($dao);

        $service->getActiveThemeId(self::SHOP_ID);

        $this->assertSame('active', $service->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIsChildThemeWhenActiveThemeDeclaresAParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(true);

        $activeTheme = $this->createService($dao, $themeParentProvider)->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertTrue($activeTheme->isChildTheme());
    }

    public function testGetActiveThemeIsNotChildThemeWhenActiveThemeHasNoParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeParentProvider = $this->createStub(ThemeParentProviderInterface::class);
        $themeParentProvider->method('hasParentTheme')->willReturn(false);

        $this->assertFalse(
            $this->createService($dao, $themeParentProvider)->getActiveTheme(self::SHOP_ID)->isChildTheme()
        );
    }

    public function testGetActiveThemeThrowsExceptionWhenNoThemeIsActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme1' => (new ThemeConfiguration())->setId('theme1'),
        ]);

        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createService($dao)->getActiveTheme(self::SHOP_ID);
    }

    public function testGetActiveThemeResolvesThemeOnlyOnce(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeParentProvider = $this->createMock(ThemeParentProviderInterface::class);
        $themeParentProvider->expects($this->once())->method('hasParentTheme')->willReturn(true);
        $service = $this->createService($dao, $themeParentProvider);

        $service->getActiveTheme(self::SHOP_ID);

        $this->assertTrue($service->getActiveTheme(self::SHOP_ID)->isChildTheme());
    }

    private function createService(
        ThemeConfigurationDaoInterface $dao,
        ?ThemeParentProviderInterface $themeParentProvider = null,
    ): ThemeStateService {
        return new ThemeStateService($dao, $themeParentProvider ?? $this->createStub(ThemeParentProviderInterface::class));
    }
}
