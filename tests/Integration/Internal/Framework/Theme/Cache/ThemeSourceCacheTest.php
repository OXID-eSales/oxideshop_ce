<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSourceCacheInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('cache')]
final class ThemeSourceCacheTest extends TestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;

    public function testGetReturnsPutSource(): void
    {
        $cache = $this->get(ThemeSourceCacheInterface::class);
        $cache->put('theme', self::SHOP_ID, 'source/Application/views/theme');

        $this->assertSame('source/Application/views/theme', $cache->get('theme', self::SHOP_ID));
    }

    public function testShopCacheClearRemovesSource(): void
    {
        $cache = $this->get(ThemeSourceCacheInterface::class);
        $cache->put('theme', self::SHOP_ID, 'source/Application/views/theme');

        $this->get(ShopCacheCleanerInterface::class)->clear(self::SHOP_ID);

        $this->expectException(CacheItemNotFoundException::class);

        $cache->get('theme', self::SHOP_ID);
    }
}
