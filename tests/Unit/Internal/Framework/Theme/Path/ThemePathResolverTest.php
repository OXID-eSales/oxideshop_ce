<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSourceCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemePathResolver;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;

final class ThemePathResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const SHOP_ROOT = '/var/www/oxideshop';

    public function testGetAbsolutePathUsesCachedSourceWithoutReadingConfiguration(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method($this->anything());

        $cache = $this->createStub(ThemeSourceCacheInterface::class);
        $cache->method('get')->willReturn('source/Application/views/cached');

        $resolver = new ThemePathResolver($dao, $this->createContext(), $cache);

        $this->assertSame(
            '/var/www/oxideshop/source/Application/views/cached',
            $resolver->getAbsolutePath('cached', self::SHOP_ID)
        );
    }

    public function testGetAbsolutePathReadsConfigurationAndCachesSourceOnMiss(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn(
            (new ThemeConfiguration())->setId('theme')->setSource('source/Application/views/theme')
        );

        $cache = $this->createMock(ThemeSourceCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException());
        $cache->expects($this->once())
            ->method('put')
            ->with('theme', self::SHOP_ID, 'source/Application/views/theme');

        $resolver = new ThemePathResolver($dao, $this->createContext(), $cache);

        $this->assertSame(
            '/var/www/oxideshop/source/Application/views/theme',
            $resolver->getAbsolutePath('theme', self::SHOP_ID)
        );
    }

    private function createContext(): BasicContextInterface
    {
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getShopRootPath')->willReturn(self::SHOP_ROOT);

        return $context;
    }
}
