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
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ShopTemplateCacheServiceTest extends TestCase
{
    use ContainerTrait;

    private int $shopId;
    private array $shopsIds;
    private string $shopTemplateCachePath;
    private Filesystem $filesystem;
    private ShopTemplateCacheServiceInterface $shopTemplateCacheService;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->shopId = $this->get(ContextInterface::class)->getCurrentShopId();
        $this->shopsIds = $this->get(ContextInterface::class)->getAllShopIds();
        $this->shopTemplateCachePath = $this->get(ShopTemplateCacheServiceInterface::class)
            ->getCacheDirectory($this->shopId);

        $this->clearTemplateCache();
        $this->populateTemplateCache();

        parent::setUp();
    }

    public function testInvalidateCache(): void
    {
        $this->assertNotEquals(0, $this->countShopCacheFiles($this->shopId));

        $this->get(ShopTemplateCacheServiceInterface::class)->invalidateCache($this->shopId);

        $this->assertEquals(0, $this->countShopCacheFiles($this->shopId));
    }

    public function testInvalidateAllShopsCache(): void
    {
        $this->assertNotEquals(0, $this->countCacheFiles());

        $this->get(ShopTemplateCacheServiceInterface::class)->invalidateAllShopsCache();

        $this->assertEquals(0, $this->countCacheFiles());
    }

    private function clearTemplateCache(): void
    {
        foreach ($this->shopsIds as $shopId) {
            $this->filesystem->remove(
                $this->get(ShopTemplateCacheServiceInterface::class)
                    ->getCacheDirectory($shopId)
            );
        }
    }

    private function countShopCacheFiles($shopId): int
    {
        return count(\glob($this->get(ShopTemplateCacheServiceInterface::class)
            ->getCacheDirectory($shopId)));
    }

    private function countCacheFiles(): int
    {
        $files = 0;
        foreach ($this->shopsIds as $shopId) {
            $files += $this->countShopCacheFiles($shopId);
        }
        return $files;
    }

    private function populateTemplateCache(): void
    {
        $numberOfTestFiles = 3;
        foreach ($this->shopsIds as $shopId) {
            $templateCachePath = $this->get(ShopTemplateCacheServiceInterface::class)
                ->getCacheDirectory($shopId);
            $this->filesystem->mkdir($templateCachePath);
            for ($i = 0; $i < $numberOfTestFiles; $i++) {
                $this->filesystem->touch(
                    Path::join(
                        $templateCachePath,
                        uniqid('template-file-' . $shopId, true)
                    )
                );
            }
        }
    }
}
