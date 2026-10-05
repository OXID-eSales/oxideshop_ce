<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ActiveThemeCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\InMemoryActiveThemeCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use PHPUnit\Framework\TestCase;

final class InMemoryActiveThemeCacheTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetAsksInnerCacheOnlyOnce(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->willReturn(new ActiveTheme('active'));

        $cache = new InMemoryActiveThemeCache($innerCache);
        $cache->get(self::SHOP_ID);

        $this->assertSame('active', $cache->get(self::SHOP_ID)->getId());
    }

    public function testPutWritesToInnerCacheAndServesGetFromMemory(): void
    {
        $activeTheme = new ActiveTheme('active');

        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->once())->method('put')->with(self::SHOP_ID, $activeTheme);
        $innerCache->expects($this->never())->method('get');

        $cache = new InMemoryActiveThemeCache($innerCache);
        $cache->put(self::SHOP_ID, $activeTheme);

        $this->assertSame($activeTheme, $cache->get(self::SHOP_ID));
    }

    public function testGetThrowsOnMiss(): void
    {
        $innerCache = $this->createStub(ActiveThemeCacheInterface::class);
        $innerCache->method('get')->willThrowException(new CacheItemNotFoundException());

        $this->expectException(CacheItemNotFoundException::class);

        (new InMemoryActiveThemeCache($innerCache))->get(self::SHOP_ID);
    }

    public function testForgetDropsThemeOfClearedShopFromMemory(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->willReturn(new ActiveTheme('reloaded'));

        $cache = new InMemoryActiveThemeCache($innerCache);
        $cache->put(self::SHOP_ID, new ActiveTheme('inMemory'));
        $cache->forget(new ClearShopCacheEvent(self::SHOP_ID));

        $this->assertSame('reloaded', $cache->get(self::SHOP_ID)->getId());
    }

    public function testForgetKeepsThemeOfOtherShopInMemory(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->never())->method('get');

        $cache = new InMemoryActiveThemeCache($innerCache);
        $cache->put(self::SHOP_ID, new ActiveTheme('inMemory'));
        $cache->forget(new ClearShopCacheEvent(2));

        $this->assertSame('inMemory', $cache->get(self::SHOP_ID)->getId());
    }
}
