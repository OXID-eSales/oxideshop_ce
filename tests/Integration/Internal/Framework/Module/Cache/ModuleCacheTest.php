<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\CacheNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('cache')]
final class ModuleCacheTest extends TestCase
{
    use ContainerTrait;

    public function testPut(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test', ['something']);

        $this->assertEquals(
            ['something'],
            $cache->get('test')
        );
    }

    public function testExists(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test', ['something']);

        $this->assertTrue(
            $cache->exists('test')
        );
    }

    public function testInvalidate(): void
    {
        $cache = $this->getModuleCacheService();
        $cache->put('test_key', ['something']);

        $cache->deleteItem('test_key');

        $this->assertFalse(
            $cache->exists('test_key')
        );
    }

    public function testGetNotExistentCache(): void
    {
        $cache = $this->getModuleCacheService();

        $this->expectException(CacheNotFoundException::class);
        $cache->get('nonExistent');
    }

    private function getModuleCacheService(): ModuleCacheInterface
    {
        return $this->get(ModuleCacheInterface::class);
    }
}
