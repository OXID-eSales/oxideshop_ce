<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Cache\ActiveThemeCache;
use PHPUnit\Framework\TestCase;

final class ActiveThemeCacheTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testPutAndGetThemeId(): void
    {
        $cache = new ActiveThemeCache();

        $cache->putThemeId(self::SHOP_ID, 'apex');

        $this->assertSame('apex', $cache->getThemeId(self::SHOP_ID));
    }

    public function testHasThemeIdReturnsFalseForMissingEntry(): void
    {
        $cache = new ActiveThemeCache();

        $this->assertFalse($cache->hasThemeId(self::SHOP_ID));
    }

    public function testHasThemeIdReturnsTrueForCachedEntry(): void
    {
        $cache = new ActiveThemeCache();

        $cache->putThemeId(self::SHOP_ID, 'apex');

        $this->assertTrue($cache->hasThemeId(self::SHOP_ID));
    }

    public function testPutAndGetTheme(): void
    {
        $cache = new ActiveThemeCache();
        $activeTheme = new ActiveTheme(new ThemeInheritance('apex', null));

        $cache->putTheme(self::SHOP_ID, $activeTheme);

        $this->assertSame($activeTheme, $cache->getTheme(self::SHOP_ID));
    }

    public function testHasThemeReturnsFalseForMissingEntry(): void
    {
        $cache = new ActiveThemeCache();

        $this->assertFalse($cache->hasTheme(self::SHOP_ID));
    }

    public function testHasThemeReturnsTrueForCachedEntry(): void
    {
        $cache = new ActiveThemeCache();

        $cache->putTheme(self::SHOP_ID, new ActiveTheme(new ThemeInheritance('apex', null)));

        $this->assertTrue($cache->hasTheme(self::SHOP_ID));
    }

    public function testEvictRemovesThemeIdAndTheme(): void
    {
        $cache = new ActiveThemeCache();
        $cache->putThemeId(self::SHOP_ID, 'apex');
        $cache->putTheme(self::SHOP_ID, new ActiveTheme(new ThemeInheritance('apex', null)));

        $cache->evict(self::SHOP_ID);

        $this->assertFalse($cache->hasThemeId(self::SHOP_ID));
        $this->assertFalse($cache->hasTheme(self::SHOP_ID));
    }

    public function testEvictNonExistentEntryDoesNotFail(): void
    {
        $cache = new ActiveThemeCache();

        $cache->evict(self::SHOP_ID);

        $this->assertFalse($cache->hasThemeId(self::SHOP_ID));
    }

    public function testEvictDoesNotAffectOtherShops(): void
    {
        $cache = new ActiveThemeCache();
        $cache->putThemeId(self::SHOP_ID, 'apex');
        $cache->putThemeId(self::SHOP_ID + 1, 'apex');

        $cache->evict(self::SHOP_ID + 1);

        $this->assertTrue($cache->hasThemeId(self::SHOP_ID));
    }
}
