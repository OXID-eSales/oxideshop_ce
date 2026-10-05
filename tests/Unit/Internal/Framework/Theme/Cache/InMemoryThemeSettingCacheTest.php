<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\InMemoryThemeSettingCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use PHPUnit\Framework\TestCase;

final class InMemoryThemeSettingCacheTest extends TestCase
{
    private const KEY = 'theme-apex-settings';
    private const DATA = ['showWishlist' => true, 'showVouchers' => false];

    public function testGetAsksInnerCacheOnlyOnce(): void
    {
        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->with(self::KEY)->willReturn(self::DATA);

        $cache = new InMemoryThemeSettingCache($innerCache);
        $cache->get(self::KEY);

        $this->assertSame(self::DATA, $cache->get(self::KEY));
    }

    public function testPutWritesToInnerCacheAndServesGetFromMemory(): void
    {
        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->once())->method('put')->with(self::KEY, self::DATA);
        $innerCache->expects($this->never())->method('get');

        $cache = new InMemoryThemeSettingCache($innerCache);
        $cache->put(self::KEY, self::DATA);

        $this->assertSame(self::DATA, $cache->get(self::KEY));
    }

    public function testGetThrowsOnMiss(): void
    {
        $innerCache = $this->createStub(ThemeSettingCacheInterface::class);
        $innerCache->method('get')->willThrowException(new CacheItemNotFoundException());

        $this->expectException(CacheItemNotFoundException::class);

        (new InMemoryThemeSettingCache($innerCache))->get(self::KEY);
    }

    public function testForgetDropsSettingsFromMemory(): void
    {
        $reloaded = ['showWishlist' => false, 'showVouchers' => false];

        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->with(self::KEY)->willReturn($reloaded);

        $cache = new InMemoryThemeSettingCache($innerCache);
        $cache->put(self::KEY, self::DATA);
        $cache->forget(new ClearShopCacheEvent(1));

        $this->assertSame($reloaded, $cache->get(self::KEY));
    }
}
