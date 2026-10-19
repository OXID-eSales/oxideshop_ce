<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Cache\Adapter;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\PhpFilesTagAwareAdapterFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class PhpFilesTagAwareAdapterFactoryTest extends TestCase
{
    private string $poolDirectory;
    private Filesystem $filesystem;
    private PhpFilesTagAwareAdapterFactory $factory;

    protected function setUp(): void
    {
        $cacheDirectory = Path::join(sys_get_temp_dir(), uniqid('cache-pool-', true));
        $this->poolDirectory = Path::join($cacheDirectory, 'pool');
        $this->filesystem = new Filesystem();
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getCacheDirectory')->willReturn($cacheDirectory);
        $context->method('getCachePoolGenerationFilePath')->willReturnCallback(
            fn (int $shopId): string => Path::join($this->poolDirectory, "shop_$shopId.generation")
        );
        $this->factory = new PhpFilesTagAwareAdapterFactory($context, $this->filesystem);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove(Path::getDirectory($this->poolDirectory));
    }

    public function testStoresItemsUnderTheInitialGenerationOfTheShop(): void
    {
        $this->storeItem(1);

        $this->assertDirectoryExists(Path::join($this->poolDirectory, 'cache_items_shop_1_0'));
    }

    public function testStoresItemsUnderTheGenerationReadFromTheMarker(): void
    {
        $this->filesystem->dumpFile(Path::join($this->poolDirectory, 'shop_1.generation'), 'abcd1234');

        $this->storeItem(1);

        $this->assertDirectoryExists(Path::join($this->poolDirectory, 'cache_items_shop_1_abcd1234'));
    }

    private function storeItem(int $shopId): void
    {
        $this->factory->create($shopId)->get('some-key', fn (): string => 'value');
    }
}
