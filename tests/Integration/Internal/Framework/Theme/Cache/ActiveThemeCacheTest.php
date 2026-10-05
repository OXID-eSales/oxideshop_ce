<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ActiveThemeCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('cache')]
final class ActiveThemeCacheTest extends TestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;

    public function testGetReturnsPutActiveTheme(): void
    {
        $cache = $this->get(ActiveThemeCacheInterface::class);
        $cache->put(self::SHOP_ID, new ActiveTheme('child', 'parent'));

        $activeTheme = $cache->get(self::SHOP_ID);

        $this->assertSame('child', $activeTheme->getId());
        $this->assertSame('parent', $activeTheme->getParentThemeId());
    }

    public function testShopCacheClearRemovesActiveTheme(): void
    {
        $cache = $this->get(ActiveThemeCacheInterface::class);
        $cache->put(self::SHOP_ID, new ActiveTheme('child'));

        $this->get(ShopCacheCleanerInterface::class)->clear(self::SHOP_ID);

        $this->expectException(CacheItemNotFoundException::class);

        $cache->get(self::SHOP_ID);
    }
}
