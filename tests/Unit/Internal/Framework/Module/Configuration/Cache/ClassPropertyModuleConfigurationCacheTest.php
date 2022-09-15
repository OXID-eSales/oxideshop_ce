<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Cache\ClassPropertyModuleConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;

final class ClassPropertyModuleConfigurationCacheTest extends TestCase
{
    public function testPut(): void
    {
        $configuration = new ModuleConfiguration();
        $configuration->setId('test');

        $cache = new ClassPropertyModuleConfigurationCache();
        $cache->put(2, $configuration);

        $this->assertSame($configuration, $cache->get('test',2));
    }

    public function testExists(): void
    {
        $cache = new ClassPropertyModuleConfigurationCache();

        $this->assertFalse($cache->exists('test',1));

        $configuration = new ModuleConfiguration();
        $configuration->setId('test');

        $cache->put(1, $configuration);

        $this->assertTrue($cache->exists('test',1));
    }

    public function testEvict(): void
    {
        $cache = new ClassPropertyModuleConfigurationCache();

        $configuration = new ModuleConfiguration();
        $configuration->setId('test');

        $cache->put(3, $configuration);
        $cache->evict('test', 3);

        $this->assertFalse($cache->exists('test', 3));
    }
}
