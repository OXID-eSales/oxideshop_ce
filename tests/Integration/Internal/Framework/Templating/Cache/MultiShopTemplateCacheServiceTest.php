<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Templating\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class MultiShopTemplateCacheServiceTest extends TestCase
{
    use ContainerTrait;

    private string $testFixturesDirectory = __DIR__ . '/cache_fixtures';
    private int $shopId1 = 123;
    private int $shopId2 = 456;
    private array $allShopIds = [];
    private array $cacheFixturesForShopId = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->allShopIds = [
            $this->shopId1,
            $this->shopId2,
        ];
        $this->stubContext();
        $this->generateCacheFixtures();
    }

    public function tearDown(): void
    {
        $this->cleanupCache();

        parent::tearDown();
    }

    public function testInvalidateCacheWillKeepOtherShopsCacheFile(): void
    {
        $this->get(ShopTemplateCacheServiceInterface::class)->invalidateCache($this->shopId1);

        $this->assertFileDoesNotExist($this->cacheFixturesForShopId[$this->shopId1]);
        $this->assertFileExists($this->cacheFixturesForShopId[$this->shopId2]);
    }

    public function testInvalidateAllShopsCacheWillRemoveAllCacheFiles(): void
    {
        $this->get(ShopTemplateCacheServiceInterface::class)->invalidateAllShopsCache();

        $this->assertFileDoesNotExist($this->cacheFixturesForShopId[$this->shopId1]);
        $this->assertFileDoesNotExist($this->cacheFixturesForShopId[$this->shopId2]);
    }

    private function stubContext(): void
    {
        $context = new ContextStub();
        $context->setAllShopIds($this->allShopIds);

        $this->container = (new TestContainerFactory())->create();
        $this->container->set(ContextInterface::class, $context);
        $this->container->setParameter('oxid_build_directory', $this->testFixturesDirectory);
        $this->container->autowire(ContextInterface::class, ContextInterface::class);
        $this->container->compile();
    }

    private function generateCacheFixtures(): void
    {
        $filesystem = $this->get('oxid_esales.symfony.file_system');

        foreach ($this->allShopIds as $shopId) {
            $templateCacheDirectory = $this->get(ShopTemplateCacheServiceInterface::class)->getCacheDirectory($shopId);
            $filesystem->mkdir($templateCacheDirectory);
            $this->cacheFixturesForShopId[$shopId] = Path::join($templateCacheDirectory, 'some-cache-file');
            $filesystem->touch($this->cacheFixturesForShopId[$shopId]);
        }

        $this->assertFileExists($this->cacheFixturesForShopId[$this->shopId1]);
        $this->assertFileExists($this->cacheFixturesForShopId[$this->shopId2]);
    }

    private function cleanupCache(): void
    {
        $this->get('oxid_esales.symfony.file_system')->remove($this->testFixturesDirectory);
    }
}
