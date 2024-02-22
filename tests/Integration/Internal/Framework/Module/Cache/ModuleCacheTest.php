<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\CacheNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ModuleCacheTest extends TestCase
{
    use ContainerTrait;

    public function testPut(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test', 1, ['something']);

        $this->assertEquals(
            ['something'],
            $cache->get('test', 1)
        );
    }

    public function testExists(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test', 1, ['something']);

        $this->assertTrue(
            $cache->exists('test', 1)
        );
    }

    public function testInvalidate(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test', 1, ['something']);

        $cache->invalidate('someModule', 1);

        $this->assertFalse(
            $cache->exists('test', 1)
        );
    }

    public function testInvalidateAll(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test', 1, ['something']);
        $cache->put('test2', 2, ['something']);

        $cache->invalidateAll();

        $this->assertFalse(
            $cache->exists('test', 1)
        );
        $this->assertFalse(
            $cache->exists('test2', 2)
        );
    }

    public function testGetNotExistentCache(): void
    {
        $cache = $this->getModuleCacheService();

        $this->expectException(CacheNotFoundException::class);
        $cache->get('nonExistent', 1);
    }

    private function getModuleCacheService(): ModuleCacheServiceInterface
    {
        return $this->get(ModuleCacheServiceInterface::class);
    }
}
