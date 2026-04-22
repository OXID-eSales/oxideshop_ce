<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\ShopKernel;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
final class SubshopCachingTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->removeDir(sys_get_temp_dir() . '/oxid_kernel_test');
    }

    public function testDifferentShopsGetDifferentCacheDirectories(): void
    {
        $kernel1 = new ShopKernel('test', true, 1);
        $kernel2 = new ShopKernel('test', true, 2);

        $this->assertNotSame($kernel1->getCacheDir(), $kernel2->getCacheDir());
        $this->assertStringContainsString('shop_1', $kernel1->getCacheDir());
        $this->assertStringContainsString('shop_2', $kernel2->getCacheDir());
    }

    public function testDifferentShopsGetDifferentContainers(): void
    {
        $kernel1 = new ShopKernel('test', true, 1);
        $kernel1->boot();

        $kernel2 = new ShopKernel('test', true, 2);
        $kernel2->boot();

        $this->assertSame(1, $kernel1->getContainer()->getParameter('oxid_esales.current_shop_id'));
        $this->assertSame(2, $kernel2->getContainer()->getParameter('oxid_esales.current_shop_id'));

        $kernel1->shutdown();
        $kernel2->shutdown();
    }

    public function testShopSpecificParametersSurviveCacheReload(): void
    {
        $kernel1 = new ShopKernel('test', true, 1);
        $kernel1->boot();
        $kernel1->shutdown();

        $kernel2 = new ShopKernel('test', true, 2);
        $kernel2->boot();
        $kernel2->shutdown();

        $kernel1Reloaded = new ShopKernel('test', true, 1);
        $kernel1Reloaded->boot();

        $kernel2Reloaded = new ShopKernel('test', true, 2);
        $kernel2Reloaded->boot();

        $this->assertSame(1, $kernel1Reloaded->getContainer()->getParameter('test.shop_id'));
        $this->assertSame(2, $kernel2Reloaded->getContainer()->getParameter('test.shop_id'));

        $kernel1Reloaded->shutdown();
        $kernel2Reloaded->shutdown();
    }

    public function testShop1CacheDoesNotAffectShop2(): void
    {
        $kernel1 = new ShopKernel('test', true, 1);
        $kernel1->boot();
        $this->assertSame(1, $kernel1->getContainer()->getParameter('test.shop_id'));
        $kernel1->shutdown();

        $kernel2 = new ShopKernel('test', true, 2);
        $kernel2->boot();
        $this->assertSame(2, $kernel2->getContainer()->getParameter('test.shop_id'));
        $kernel2->shutdown();
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        ) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
