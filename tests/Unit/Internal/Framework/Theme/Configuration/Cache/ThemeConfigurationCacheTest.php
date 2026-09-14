<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ThemeConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationCacheTest extends TestCase
{
    public function testPutAndGet(): void
    {
        $cache = new ThemeConfigurationCache();

        $configuration = (new ThemeConfiguration())->setId('apex');
        $cache->put($configuration, 1);

        $this->assertSame($configuration, $cache->get('apex', 1));
    }

    public function testExistsReturnsTrueForCachedEntry(): void
    {
        $cache = new ThemeConfigurationCache();

        $cache->put((new ThemeConfiguration())->setId('apex'), 1);

        $this->assertTrue($cache->exists('apex', 1));
    }

    public function testExistsReturnsFalseForMissingEntry(): void
    {
        $cache = new ThemeConfigurationCache();

        $this->assertFalse($cache->exists('apex', 1));
    }

    public function testEvictRemovesCachedEntry(): void
    {
        $cache = new ThemeConfigurationCache();

        $cache->put((new ThemeConfiguration())->setId('apex'), 1);
        $cache->evict('apex', 1);

        $this->assertFalse($cache->exists('apex', 1));
    }

    public function testEvictAllRemovesEveryEntryOfTheShopOnly(): void
    {
        $cache = new ThemeConfigurationCache();

        $cache->put((new ThemeConfiguration())->setId('apex'), 1);
        $cache->put((new ThemeConfiguration())->setId('childTheme'), 1);
        $cache->put((new ThemeConfiguration())->setId('apex'), 2);
        $cache->evictAll(1);

        $this->assertFalse($cache->exists('apex', 1));
        $this->assertFalse($cache->exists('childTheme', 1));
        $this->assertTrue($cache->exists('apex', 2));
    }

    public function testEvictNonExistentEntryDoesNotFail(): void
    {
        $cache = new ThemeConfigurationCache();

        $cache->evict('nonExistent', 1);

        $this->assertFalse($cache->exists('nonExistent', 1));
    }

    public function testCacheIsScopedPerShop(): void
    {
        $cache = new ThemeConfigurationCache();

        $cache->put((new ThemeConfiguration())->setId('apex')->setSource('shop1'), 1);

        $this->assertTrue($cache->exists('apex', 1));
        $this->assertFalse($cache->exists('apex', 2));
    }
}
