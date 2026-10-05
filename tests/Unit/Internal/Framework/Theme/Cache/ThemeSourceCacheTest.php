<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSourceCache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

final class ThemeSourceCacheTest extends TestCase
{
    public function testGetReturnsPutSource(): void
    {
        $cache = $this->createCache();
        $cache->put('theme', 1, 'source/Application/views/theme');

        $this->assertSame('source/Application/views/theme', $cache->get('theme', 1));
    }

    public function testGetThrowsOnMiss(): void
    {
        $this->expectException(CacheItemNotFoundException::class);

        $this->createCache()->get('theme', 1);
    }

    public function testSourceIsCachedPerTheme(): void
    {
        $cache = $this->createCache();
        $cache->put('theme', 1, 'source/Application/views/theme');

        $this->expectException(CacheItemNotFoundException::class);

        $cache->get('otherTheme', 1);
    }

    public function testSourceIsCachedPerShop(): void
    {
        $cache = $this->createCache();
        $cache->put('theme', 1, 'source/Application/views/theme');

        $this->expectException(CacheItemNotFoundException::class);

        $cache->get('theme', 2);
    }

    private function createCache(): ThemeSourceCache
    {
        $pools = [1 => new TagAwareAdapter(new ArrayAdapter()), 2 => new TagAwareAdapter(new ArrayAdapter())];

        $factory = $this->createStub(TagAwareAdapterFactoryInterface::class);
        $factory->method('create')->willReturnCallback(static fn(int $shopId) => $pools[$shopId]);

        return new ThemeSourceCache($factory);
    }
}
