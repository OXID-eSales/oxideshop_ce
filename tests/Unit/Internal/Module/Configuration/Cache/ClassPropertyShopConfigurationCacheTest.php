<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Cache\ClassPropertyShopConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

final class ClassPropertyShopConfigurationCacheTest extends TestCase
{
    public function testPut(): void
    {
        $configuration = new ShopConfiguration();

        $cache = new ClassPropertyShopConfigurationCache();
        $cache->put('dev', 2, $configuration);

        $this->assertSame($configuration, $cache->get('dev', 2));
    }

    public function testExists(): void
    {
        $cache = new ClassPropertyShopConfigurationCache();

        $this->assertFalse($cache->exists('dev', 1));

        $cache->put('dev', 1, new ShopConfiguration());

        $this->assertTrue($cache->exists('dev', 1));
    }

    public function testEvict(): void
    {
        $cache = new ClassPropertyShopConfigurationCache();
        $cache->put('dev', 3, new ShopConfiguration());
        $cache->evict('dev', 3);

        $this->assertFalse($cache->exists('dev', 3));
    }
}
