<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache\ClassPropertyShopConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

final class ClassPropertyShopConfigurationCacheTest extends TestCase
{
    public function testPut(): void
    {
        $configuration = new ShopConfiguration();

        $cache = new ClassPropertyShopConfigurationCache();
        $cache->put(2, $configuration);

        $this->assertSame($configuration, $cache->get(2));
    }

    public function testExists(): void
    {
        $cache = new ClassPropertyShopConfigurationCache();

        $this->assertFalse($cache->exists(1));

        $cache->put(1, new ShopConfiguration());

        $this->assertTrue($cache->exists(1));
    }

    public function testEvict(): void
    {
        $cache = new ClassPropertyShopConfigurationCache();
        $cache->put(3, new ShopConfiguration());
        $cache->evict(3);

        $this->assertFalse($cache->exists(3));
    }
}
