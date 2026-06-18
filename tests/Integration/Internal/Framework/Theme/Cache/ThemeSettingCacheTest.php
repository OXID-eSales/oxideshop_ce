<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('cache')]
final class ThemeSettingCacheTest extends TestCase
{
    use ContainerTrait;

    public function testPut(): void
    {
        $cache = $this->getThemeSettingCache();
        $cache->put('test', ['something']);

        $this->assertEquals(
            ['something'],
            $cache->get('test')
        );
    }

    public function testGetNotExistentCache(): void
    {
        $cache = $this->getThemeSettingCache();

        $this->expectException(CacheItemNotFoundException::class);
        $cache->get('nonExistent');
    }

    private function getThemeSettingCache(): ThemeSettingCacheInterface
    {
        return $this->get(ThemeSettingCacheInterface::class);
    }
}
