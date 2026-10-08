<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\DataObject\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Provider\ThemeMetaDataByIdProviderInterface;
use PHPUnit\Framework\TestCase;

final class ActiveThemeProviderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetActiveThemeCarriesParentThemeId(): void
    {
        $activeTheme = $this->createProvider('active', 'parent')->getActiveTheme();

        $this->assertSame('active', $activeTheme->getId());
        $this->assertTrue($activeTheme->hasParentTheme());
        $this->assertSame('parent', $activeTheme->getParentThemeId());
    }

    public function testGetActiveThemeHasNoParentThemeIdForStandaloneTheme(): void
    {
        $activeTheme = $this->createProvider('active', '')->getActiveTheme();

        $this->assertSame('active', $activeTheme->getId());
        $this->assertFalse($activeTheme->hasParentTheme());
        $this->assertSame('', $activeTheme->getParentThemeId());
    }

    public function testGetActiveThemeThrowsWhenNoThemeIsConfigured(): void
    {
        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createProvider('', '')->getActiveTheme();
    }

    public function testGetActiveThemeThrowsWhenConfiguredThemeIsNotInstalled(): void
    {
        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createProvider('active', '', installed: false)->getActiveTheme();
    }

    private function createProvider(string $activeThemeId, string $parentThemeId, bool $installed = true): ActiveThemeProvider
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn($installed);

        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        if ($activeThemeId !== '') {
            $metaDataProvider->method('getById')->willReturn(
                (new ThemeMetaData())->setId($activeThemeId)->setParentTheme($parentThemeId)
            );
        }

        return new ActiveThemeProvider($activeThemeId, self::SHOP_ID, $dao, $metaDataProvider);
    }
}
