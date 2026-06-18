<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCache;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

final class ThemeSettingCacheTest extends TestCase
{
    public function testPutSavesToPool(): void
    {
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->expects($this->once())->method('set')->with(['value' => 'test']);

        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->method('getItem')->with('key')->willReturn($cacheItem);
        $pool->expects($this->once())->method('save')->with($cacheItem);

        $cache = new ThemeSettingCache($pool);
        $cache->put('key', ['value' => 'test']);
    }

    public function testGetReturnsDataOnHit(): void
    {
        $cacheItem = $this->createStub(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(true);
        $cacheItem->method('get')->willReturn(['value' => 'test']);

        $pool = $this->createStub(CacheItemPoolInterface::class);
        $pool->method('getItem')->willReturn($cacheItem);

        $cache = new ThemeSettingCache($pool);

        $this->assertSame(['value' => 'test'], $cache->get('key'));
    }

    public function testGetThrowsOnMiss(): void
    {
        $cacheItem = $this->createStub(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(false);

        $pool = $this->createStub(CacheItemPoolInterface::class);
        $pool->method('getItem')->willReturn($cacheItem);

        $cache = new ThemeSettingCache($pool);

        $this->expectException(CacheItemNotFoundException::class);
        $cache->get('nonExistent');
    }
}
