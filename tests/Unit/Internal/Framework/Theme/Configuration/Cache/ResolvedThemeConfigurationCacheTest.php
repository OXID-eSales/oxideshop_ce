<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ResolvedThemeConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

final class ResolvedThemeConfigurationCacheTest extends TestCase
{
    public function testGetReturnsPutConfiguration(): void
    {
        $cache = $this->createCache();
        $configuration = (new ThemeConfiguration())
            ->setId('theme')
            ->addThemeSetting((new Setting())->setName('detailImageSize')->setType('str')->setValue('999*999'));

        $cache->put(1, $configuration);

        $this->assertEquals($configuration, $cache->get('theme', 1));
    }

    public function testExistsIsTrueForPutConfiguration(): void
    {
        $cache = $this->createCache();
        $cache->put(1, (new ThemeConfiguration())->setId('theme'));

        $this->assertTrue($cache->exists('theme', 1));
    }

    public function testExistsIsFalseOnMiss(): void
    {
        $this->assertFalse($this->createCache()->exists('theme', 1));
    }

    public function testConfigurationIsCachedPerTheme(): void
    {
        $cache = $this->createCache();
        $cache->put(1, (new ThemeConfiguration())->setId('theme'));

        $this->assertFalse($cache->exists('otherTheme', 1));
    }

    public function testConfigurationIsCachedPerShop(): void
    {
        $cache = $this->createCache();
        $cache->put(1, (new ThemeConfiguration())->setId('theme'));

        $this->assertFalse($cache->exists('theme', 2));
    }

    public function testEvictRemovesConfiguration(): void
    {
        $cache = $this->createCache();
        $cache->put(1, (new ThemeConfiguration())->setId('theme'));

        $cache->evict('theme', 1);

        $this->assertFalse($cache->exists('theme', 1));
    }

    private function createCache(): ResolvedThemeConfigurationCache
    {
        $pools = [1 => new TagAwareAdapter(new ArrayAdapter()), 2 => new TagAwareAdapter(new ArrayAdapter())];

        $factory = $this->createStub(TagAwareAdapterFactoryInterface::class);
        $factory->method('create')->willReturnCallback(static fn(int $shopId) => $pools[$shopId]);

        return new ResolvedThemeConfigurationCache($factory);
    }
}
