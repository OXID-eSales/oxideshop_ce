<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\InstalledThemeProvider;
use PHPUnit\Framework\TestCase;

final class InstalledThemeProviderTest extends TestCase
{
    private const SHOP_ID = 2;

    public function testGetThemeIdsReturnsIdsOfThemesInstalledInCurrentShop(): void
    {
        $themeConfigurationDao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->expects($this->once())
            ->method('getAll')
            ->with(self::SHOP_ID)
            ->willReturn([
                'apex' => new ThemeConfiguration(),
                'childTheme' => new ThemeConfiguration(),
            ]);

        $provider = new InstalledThemeProvider($themeConfigurationDao, self::SHOP_ID);

        $this->assertSame(['apex', 'childTheme'], $provider->getThemeIds());
    }

    public function testGetThemeIdsReturnsNumericThemeIdsAsStrings(): void
    {
        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('getAll')->willReturn(['123' => new ThemeConfiguration()]);

        $provider = new InstalledThemeProvider($themeConfigurationDao, self::SHOP_ID);

        $this->assertSame(['123'], $provider->getThemeIds());
    }

    public function testGetThemeIdsReturnsEmptyListWhenNoThemeIsInstalled(): void
    {
        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('getAll')->willReturn([]);

        $provider = new InstalledThemeProvider($themeConfigurationDao, self::SHOP_ID);

        $this->assertSame([], $provider->getThemeIds());
    }
}
