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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\MemoizedActiveThemeCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use PHPUnit\Framework\TestCase;

final class MemoizedActiveThemeCacheTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testGetAsksInnerCacheOnlyOnce(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->willReturn(new ActiveTheme('active'));

        $cache = new MemoizedActiveThemeCache($innerCache);
        $cache->get(self::SHOP_ID);

        $this->assertSame('active', $cache->get(self::SHOP_ID)->getId());
    }

    public function testPutWritesToInnerCacheAndServesGetFromMemory(): void
    {
        $activeTheme = new ActiveTheme('active');

        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->once())->method('put')->with(self::SHOP_ID, $activeTheme);
        $innerCache->expects($this->never())->method('get');

        $cache = new MemoizedActiveThemeCache($innerCache);
        $cache->put(self::SHOP_ID, $activeTheme);

        $this->assertSame($activeTheme, $cache->get(self::SHOP_ID));
    }

    public function testMissIsNotMemoized(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new CacheItemNotFoundException()),
                new ActiveTheme('active'),
            );

        $cache = new MemoizedActiveThemeCache($innerCache);

        try {
            $cache->get(self::SHOP_ID);
        } catch (CacheItemNotFoundException) {
        }

        $this->assertSame('active', $cache->get(self::SHOP_ID)->getId());
    }

    public function testForgetDropsMemoizedThemeOfClearedShop(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->willReturn(new ActiveTheme('reloaded'));

        $cache = new MemoizedActiveThemeCache($innerCache);
        $cache->put(self::SHOP_ID, new ActiveTheme('memoized'));
        $cache->forget(new ClearShopCacheEvent(self::SHOP_ID));

        $this->assertSame('reloaded', $cache->get(self::SHOP_ID)->getId());
    }

    public function testForgetKeepsMemoizedThemeOfOtherShop(): void
    {
        $innerCache = $this->createMock(ActiveThemeCacheInterface::class);
        $innerCache->expects($this->never())->method('get');

        $cache = new MemoizedActiveThemeCache($innerCache);
        $cache->put(self::SHOP_ID, new ActiveTheme('memoized'));
        $cache->forget(new ClearShopCacheEvent(2));

        $this->assertSame('memoized', $cache->get(self::SHOP_ID)->getId());
    }

    public function testSubscribesToShopCacheClear(): void
    {
        $this->assertSame(
            [ClearShopCacheEvent::class => 'forget'],
            MemoizedActiveThemeCache::getSubscribedEvents()
        );
    }
}
