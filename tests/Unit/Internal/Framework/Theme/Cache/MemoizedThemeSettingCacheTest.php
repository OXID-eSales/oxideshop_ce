<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\MemoizedThemeSettingCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use PHPUnit\Framework\TestCase;

final class MemoizedThemeSettingCacheTest extends TestCase
{
    private const KEY = 'theme-apex-setting-showWishlist';
    private const DATA = ['exists' => true, 'value' => true];

    public function testGetAsksInnerCacheOnlyOnce(): void
    {
        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->with(self::KEY)->willReturn(self::DATA);

        $cache = new MemoizedThemeSettingCache($innerCache);
        $cache->get(self::KEY);

        $this->assertSame(self::DATA, $cache->get(self::KEY));
    }

    public function testPutWritesToInnerCacheAndServesGetFromMemory(): void
    {
        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->once())->method('put')->with(self::KEY, self::DATA);
        $innerCache->expects($this->never())->method('get');

        $cache = new MemoizedThemeSettingCache($innerCache);
        $cache->put(self::KEY, self::DATA);

        $this->assertSame(self::DATA, $cache->get(self::KEY));
    }

    public function testMissIsNotMemoized(): void
    {
        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new CacheItemNotFoundException()),
                self::DATA,
            );

        $cache = new MemoizedThemeSettingCache($innerCache);

        try {
            $cache->get(self::KEY);
        } catch (CacheItemNotFoundException) {
        }

        $this->assertSame(self::DATA, $cache->get(self::KEY));
    }

    public function testForgetDropsMemoizedSettings(): void
    {
        $reloaded = ['exists' => true, 'value' => false];

        $innerCache = $this->createMock(ThemeSettingCacheInterface::class);
        $innerCache->expects($this->once())->method('get')->with(self::KEY)->willReturn($reloaded);

        $cache = new MemoizedThemeSettingCache($innerCache);
        $cache->put(self::KEY, self::DATA);
        $cache->forget(new ClearShopCacheEvent(1));

        $this->assertSame($reloaded, $cache->get(self::KEY));
    }

    public function testSubscribesToShopCacheClear(): void
    {
        $this->assertSame(
            [ClearShopCacheEvent::class => 'forget'],
            MemoizedThemeSettingCache::getSubscribedEvents()
        );
    }
}
