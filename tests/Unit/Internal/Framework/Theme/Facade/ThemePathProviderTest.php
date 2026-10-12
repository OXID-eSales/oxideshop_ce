<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemePathProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\Service\ThemePathResolverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ThemePathProviderTest extends TestCase
{
    private const SHOP_ID = 2;

    public function testGetAbsolutePathResolvesPathOfCurrentShopOnCacheMiss(): void
    {
        $themePathResolver = $this->createMock(ThemePathResolverInterface::class);
        $themePathResolver->expects($this->once())
            ->method('getAbsolutePath')
            ->with('apex', self::SHOP_ID)
            ->willReturn('/shop/apex');

        $provider = new ThemePathProvider($themePathResolver, $this->createCache(), self::SHOP_ID);

        $this->assertSame('/shop/apex', $provider->getAbsolutePath('apex'));
    }

    public function testGetAbsolutePathUsesCacheOnHit(): void
    {
        $themePathResolver = $this->createMock(ThemePathResolverInterface::class);
        $themePathResolver->expects($this->once())->method('getAbsolutePath')->willReturn('/shop/apex');

        $provider = new ThemePathProvider($themePathResolver, $this->createCache(), self::SHOP_ID);
        $provider->getAbsolutePath('apex');

        $this->assertSame('/shop/apex', $provider->getAbsolutePath('apex'));
    }

    public function testGetAbsolutePathPropagatesExceptionOfResolver(): void
    {
        $themePathResolver = $this->createStub(ThemePathResolverInterface::class);
        $themePathResolver->method('getAbsolutePath')->willThrowException(new ThemeConfigurationNotFoundException());

        $provider = new ThemePathProvider($themePathResolver, $this->createCache(), self::SHOP_ID);

        $this->expectException(ThemeConfigurationNotFoundException::class);
        $provider->getAbsolutePath('apex');
    }

    private function createCache(): TagAwareCacheInterface
    {
        return new TagAwareAdapter(new ArrayAdapter());
    }
}
