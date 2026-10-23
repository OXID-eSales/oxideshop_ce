<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
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

    public function testGetForThemeResolvesPathOfCurrentShopOnCacheMiss(): void
    {
        $themePathResolver = $this->createMock(ThemePathResolverInterface::class);
        $themePathResolver->expects($this->once())
            ->method('getAbsolutePath')
            ->with('apex', self::SHOP_ID)
            ->willReturn('/shop/apex');

        $provider = new ThemePathProvider(
            $themePathResolver,
            $this->createStub(ThemeConfigurationDaoInterface::class),
            $this->createCache(),
            self::SHOP_ID
        );

        $this->assertSame('/shop/apex', $provider->getForTheme('apex'));
    }

    public function testGetForThemeUsesCacheOnHit(): void
    {
        $themePathResolver = $this->createMock(ThemePathResolverInterface::class);
        $themePathResolver->expects($this->once())->method('getAbsolutePath')->willReturn('/shop/apex');

        $provider = new ThemePathProvider(
            $themePathResolver,
            $this->createStub(ThemeConfigurationDaoInterface::class),
            $this->createCache(),
            self::SHOP_ID
        );
        $provider->getForTheme('apex');

        $this->assertSame('/shop/apex', $provider->getForTheme('apex'));
    }

    public function testGetForThemePropagatesExceptionOfResolver(): void
    {
        $themePathResolver = $this->createStub(ThemePathResolverInterface::class);
        $themePathResolver->method('getAbsolutePath')->willThrowException(new ThemeConfigurationNotFoundException());

        $provider = new ThemePathProvider(
            $themePathResolver,
            $this->createStub(ThemeConfigurationDaoInterface::class),
            $this->createCache(),
            self::SHOP_ID
        );

        $this->expectException(ThemeConfigurationNotFoundException::class);
        $provider->getForTheme('apex');
    }

    public function testGetForInstalledThemesResolvesPathsOfThemesInstalledInCurrentShop(): void
    {
        $themeConfigurationDao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->expects($this->once())
            ->method('getAll')
            ->with(self::SHOP_ID)
            ->willReturn([
                'apex' => (new ThemeConfiguration())->setId('apex'),
                'childTheme' => (new ThemeConfiguration())->setId('childTheme'),
            ]);
        $themePathResolver = $this->createStub(ThemePathResolverInterface::class);
        $themePathResolver->method('getAbsolutePath')->willReturnMap([
            ['apex', self::SHOP_ID, '/vendor/apex'],
            ['childTheme', self::SHOP_ID, '/vendor/child-theme'],
        ]);

        $provider = new ThemePathProvider(
            $themePathResolver,
            $themeConfigurationDao,
            $this->createCache(),
            self::SHOP_ID
        );

        $this->assertSame(['/vendor/apex', '/vendor/child-theme'], $provider->getForInstalledThemes());
    }

    public function testGetForInstalledThemesReturnsEmptyListWhenNoThemeIsInstalled(): void
    {
        $themeConfigurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $themeConfigurationDao->method('getAll')->willReturn([]);

        $provider = new ThemePathProvider(
            $this->createStub(ThemePathResolverInterface::class),
            $themeConfigurationDao,
            $this->createCache(),
            self::SHOP_ID
        );

        $this->assertSame([], $provider->getForInstalledThemes());
    }

    private function createCache(): TagAwareCacheInterface
    {
        return new TagAwareAdapter(new ArrayAdapter());
    }
}
