<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ActiveThemeCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use PHPUnit\Framework\TestCase;

final class ActiveThemeProviderTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetActiveThemeReturnsCachedThemeWithoutReadingConfiguration(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('getAll');

        $metaDataProvider = $this->createMock(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->expects($this->never())->method('getById');

        $provider = new ActiveThemeProvider(
            $dao,
            $metaDataProvider,
            $this->createWarmCache(new ActiveTheme('cached', 'parent')),
        );

        $activeTheme = $provider->getActiveTheme(self::SHOP_ID);

        $this->assertSame('cached', $activeTheme->getId());
        $this->assertSame('parent', $activeTheme->getParentThemeId());
    }

    public function testGetActiveThemeReadsConfigurationAndCachesItOnMiss(): void
    {
        $cache = $this->createMock(ActiveThemeCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException());
        $cache->expects($this->once())
            ->method('put')
            ->with(self::SHOP_ID, $this->callback(
                static fn(ActiveTheme $theme): bool => $theme->getId() === 'active'
                    && $theme->getParentThemeId() === 'parent'
            ));

        $activeTheme = $this->createProvider('active', 'parent', $cache)->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertTrue($activeTheme->hasParentTheme());
        $this->assertSame('parent', $activeTheme->getParentThemeId());
    }

    public function testGetActiveThemeHasNoParentThemeIdForStandaloneTheme(): void
    {
        $activeTheme = $this->createProvider('active', '', $this->createColdCache())->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertFalse($activeTheme->hasParentTheme());
    }

    public function testGetActiveThemeThrowsAndCachesNothingWhenNoThemeIsActive(): void
    {
        $cache = $this->createMock(ActiveThemeCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException());
        $cache->expects($this->never())->method('put');

        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createProviderWithoutActiveTheme($cache)->getActiveTheme(self::SHOP_ID);
    }

    public function testGetActiveThemeIdReturnsActiveThemeId(): void
    {
        $provider = new ActiveThemeProvider(
            $this->createStub(ThemeConfigurationDaoInterface::class),
            $this->createStub(ThemeMetaDataByIdProviderInterface::class),
            $this->createWarmCache(new ActiveTheme('cached')),
        );

        $this->assertSame('cached', $provider->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIdCachesActiveThemeOnMiss(): void
    {
        $cache = $this->createMock(ActiveThemeCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException());
        $cache->expects($this->once())
            ->method('put')
            ->with(self::SHOP_ID, $this->callback(
                static fn(ActiveTheme $theme): bool => $theme->getId() === 'active'
                    && $theme->getParentThemeId() === 'parent'
            ));

        $this->assertSame(
            'active',
            $this->createProvider('active', 'parent', $cache)->getActiveThemeId(self::SHOP_ID)
        );
    }

    public function testGetActiveThemeIdReturnsActiveThemeIdWithoutCachingWhenMetaDataIsNotLoadable(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);

        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willThrowException(new InvalidThemeMetaDataException());

        $cache = $this->createMock(ActiveThemeCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException());
        $cache->expects($this->never())->method('put');

        $provider = new ActiveThemeProvider($dao, $metaDataProvider, $cache);

        $this->assertSame('active', $provider->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIdThrowsWhenNoThemeIsActive(): void
    {
        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createProviderWithoutActiveTheme($this->createColdCache())->getActiveThemeId(self::SHOP_ID);
    }

    public function testIsActiveIsTrueForTheActiveTheme(): void
    {
        $this->assertTrue(
            $this->createProvider('active', '', $this->createColdCache())->isActive('active', self::SHOP_ID)
        );
    }

    public function testIsActiveIsFalseForAnotherTheme(): void
    {
        $this->assertFalse(
            $this->createProvider('active', '', $this->createColdCache())->isActive('other', self::SHOP_ID)
        );
    }

    public function testIsActiveIsFalseWhenNoThemeIsActive(): void
    {
        $this->assertFalse(
            $this->createProviderWithoutActiveTheme($this->createColdCache())->isActive('inactive', self::SHOP_ID)
        );
    }

    private function createProvider(
        string $themeId,
        string $parentThemeId,
        ActiveThemeCacheInterface $cache,
    ): ActiveThemeProvider {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'inactive' => (new ThemeConfiguration())->setId('inactive'),
            $themeId => (new ThemeConfiguration())->setId($themeId)->setActivated(true),
        ]);

        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willReturn(
            (new ThemeMetaData())->setId($themeId)->setParentTheme($parentThemeId)
        );

        return new ActiveThemeProvider($dao, $metaDataProvider, $cache);
    }

    private function createProviderWithoutActiveTheme(ActiveThemeCacheInterface $cache): ActiveThemeProvider
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn(['inactive' => (new ThemeConfiguration())->setId('inactive')]);

        return new ActiveThemeProvider($dao, $this->createStub(ThemeMetaDataByIdProviderInterface::class), $cache);
    }

    private function createWarmCache(ActiveTheme $activeTheme): ActiveThemeCacheInterface
    {
        $cache = $this->createStub(ActiveThemeCacheInterface::class);
        $cache->method('get')->willReturn($activeTheme);

        return $cache;
    }

    private function createColdCache(): ActiveThemeCacheInterface
    {
        $cache = $this->createStub(ActiveThemeCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException());

        return $cache;
    }
}
