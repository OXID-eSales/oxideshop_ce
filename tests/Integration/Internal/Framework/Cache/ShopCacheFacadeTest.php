<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\CachePoolGenerationInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

#[Group('cache')]
final class ShopCacheFacadeTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $cacheDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheDirectory = Path::join(sys_get_temp_dir(), uniqid('shop-cache-facade-', true));
        $this->get(BasicContextInterface::class)->setCacheDirectory($this->cacheDirectory);
    }

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->cacheDirectory);

        parent::tearDown();
    }

    public function testClearInvalidatesModuleCache(): void
    {
        $moduleCache = $this->get(ModuleCacheInterface::class);
        $moduleCache->put('test', ['something']);

        $this->get(ShopCacheCleanerInterface::class)->clear(1);

        $this->assertFalse($moduleCache->exists('test'));
    }

    public function testClearDropsCachedItemsOfShop(): void
    {
        $this->readItem(default: 'old');

        $this->get(ShopCacheCleanerInterface::class)->clear(1);

        $this->assertSame('new', $this->readItem(default: 'new'));
    }

    public function testClearAllDropsCachedItemsOfShops(): void
    {
        $this->readItem(default: 'old');

        $this->get(ShopCacheCleanerInterface::class)->clearAll();

        $this->assertSame('new', $this->readItem(default: 'new'));
    }

    public function testClearRemovesOldGenerationOfPool(): void
    {
        $this->readItem(default: 'old');
        $oldDirectory = $this->get(CachePoolGenerationInterface::class)->getDirectory(1);

        $this->get(ShopCacheCleanerInterface::class)->clear(1);

        $this->assertDirectoryDoesNotExist($oldDirectory);
    }

    private function readItem(string $default): string
    {
        return $this->get(TagAwareAdapterFactoryInterface::class)
            ->create(1)
            ->get('some-key', static fn (): string => $default);
    }
}
