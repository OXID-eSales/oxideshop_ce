<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ActiveThemeCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

final class ActiveThemeCacheTest extends TestCase
{
    public function testGetReturnsPutActiveTheme(): void
    {
        $cache = $this->createCache();
        $cache->put(1, new ActiveTheme('child', 'parent'));

        $activeTheme = $cache->get(1);

        $this->assertSame('child', $activeTheme->getId());
        $this->assertSame('parent', $activeTheme->getParentThemeId());
    }

    public function testGetThrowsOnMiss(): void
    {
        $this->expectException(CacheItemNotFoundException::class);

        $this->createCache()->get(1);
    }

    public function testActiveThemeIsCachedPerShop(): void
    {
        $cache = $this->createCache();
        $cache->put(1, new ActiveTheme('child'));

        $this->expectException(CacheItemNotFoundException::class);

        $cache->get(2);
    }

    private function createCache(): ActiveThemeCache
    {
        $pools = [1 => new TagAwareAdapter(new ArrayAdapter()), 2 => new TagAwareAdapter(new ArrayAdapter())];

        $factory = $this->createStub(TagAwareAdapterFactoryInterface::class);
        $factory->method('create')->willReturnCallback(static fn(int $shopId) => $pools[$shopId]);

        return new ActiveThemeCache($factory);
    }
}
