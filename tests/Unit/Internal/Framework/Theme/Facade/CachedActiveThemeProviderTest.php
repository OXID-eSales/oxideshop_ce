<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\DataObject\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\CachedActiveThemeProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class CachedActiveThemeProviderTest extends TestCase
{
    public function testGetActiveThemeDelegatesOnCacheMiss(): void
    {
        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('getActiveTheme')->willReturn(new ActiveTheme('child', 'parent'));

        $provider = new CachedActiveThemeProvider($activeThemeProvider, $this->createCache(), 'child');

        $this->assertEquals(new ActiveTheme('child', 'parent'), $provider->getActiveTheme());
    }

    public function testGetActiveThemeUsesPersistentCacheOnHit(): void
    {
        $activeThemeProvider = $this->createMock(ActiveThemeProviderInterface::class);
        $activeThemeProvider->expects($this->once())->method('getActiveTheme')->willReturn(new ActiveTheme('apex'));
        $cache = $this->createCache();

        (new CachedActiveThemeProvider($activeThemeProvider, $cache, 'apex'))->getActiveTheme();
        $provider = new CachedActiveThemeProvider($activeThemeProvider, $cache, 'apex');

        $this->assertSame('apex', $provider->getActiveTheme()->getId());
    }

    public function testGetActiveThemeReadsCacheOncePerInstance(): void
    {
        $activeTheme = new ActiveTheme('apex');
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())->method('get')->with('active_theme_apex')->willReturn($activeTheme);

        $provider = new CachedActiveThemeProvider(
            $this->createStub(ActiveThemeProviderInterface::class),
            $cache,
            'apex'
        );
        $provider->getActiveTheme();

        $this->assertSame($activeTheme, $provider->getActiveTheme());
    }

    public function testGetActiveThemePropagatesMissingActiveTheme(): void
    {
        $activeThemeProvider = $this->createStub(ActiveThemeProviderInterface::class);
        $activeThemeProvider->method('getActiveTheme')->willThrowException(new ActiveThemeNotFoundException());

        $provider = new CachedActiveThemeProvider($activeThemeProvider, $this->createCache(), 'apex');

        $this->expectException(ActiveThemeNotFoundException::class);
        $provider->getActiveTheme();
    }

    private function createCache(): TagAwareCacheInterface
    {
        return new TagAwareAdapter(new ArrayAdapter());
    }
}
