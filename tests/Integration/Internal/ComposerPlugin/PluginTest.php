<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\Composer;
use Composer\Config;
use Composer\IO\BufferIO;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use OxidEsales\ComposerPlugin\Plugin;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class PluginTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testShopCacheIsCleared(): void
    {
        $cache = $this->get(TagAwareCacheInterface::class);
        $cache->get('plugin_cache_probe', fn (): string => 'cached');

        $this->getPlugin()->clearShopCache();

        $this->assertSame('recomputed', $cache->get('plugin_cache_probe', fn (): string => 'recomputed'));
    }

    public function testWarningIsWrittenWhenShopCacheIsNotCleared(): void
    {
        $shopCacheCleaner = $this->createStub(ShopCacheCleanerInterface::class);
        $shopCacheCleaner->method('clearAll')->willThrowException(new \RuntimeException('Some error.'));
        $this->createContainer();
        $this->replaceService(ShopCacheCleanerInterface::class, $shopCacheCleaner);
        $this->replaceContainerInstance();
        $io = new BufferIO();

        $this->getPlugin($io)->clearShopCache();

        $this->assertStringContainsString(
            'Shop cache was not cleared: Some error. Please clear the shop cache with '
            . 'vendor/bin/oe-console oe:cache:clear.',
            $io->getOutput()
        );
    }

    private function getPlugin(IOInterface $io = new NullIO()): Plugin
    {
        $composer = $this->createStub(Composer::class);
        $composer->method('getConfig')->willReturn(new Config());

        $plugin = new Plugin();
        $plugin->activate($composer, $io);

        return $plugin;
    }
}
