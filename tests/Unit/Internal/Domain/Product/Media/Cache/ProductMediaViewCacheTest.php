<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaAttributes;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache\ProductMediaViewCache;
use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

final class ProductMediaViewCacheTest extends TestCase
{
    public function testGetStoresProducedViewAndReturnsItOnHitWithoutRecomputing(): void
    {
        $cache = $this->createCache();
        $productId = Id::generate();

        $produced = $cache->get($productId, 'role_detail', fn (): ProductMediaView => $this->createView('first'));
        $cached = $cache->get($productId, 'role_detail', fn () => $this->fail('value must come from cache'));

        $this->assertSame('first', $produced->getDetailUrl());
        $this->assertSame('first', $cached->getDetailUrl());
    }

    public function testGetAllStoresProducedCollectionAndReturnsItOnHitWithoutRecomputing(): void
    {
        $cache = $this->createCache();
        $productId = Id::generate();
        $views = [(string) Id::generate() => $this->createView()];

        $produced = $cache->getAll($productId, 'all_detail', fn (): array => $views);
        $cached = $cache->getAll($productId, 'all_detail', fn () => $this->fail('value must come from cache'));

        $this->assertCount(1, $produced);
        $this->assertCount(1, $cached);
    }

    public function testEntriesAreScopedByViewIdentifier(): void
    {
        $cache = $this->createCache();
        $productId = Id::generate();

        $cache->get($productId, 'role_detail', fn (): ProductMediaView => $this->createView('detail'));
        $icon = $cache->get($productId, 'role_icon', fn (): ProductMediaView => $this->createView('icon'));

        $this->assertSame('icon', $icon->getDetailUrl());
    }

    public function testInvalidateForProductForcesRecompute(): void
    {
        $cache = $this->createCache();
        $productId = Id::generate();
        $cache->get($productId, 'role_detail', fn (): ProductMediaView => $this->createView('first'));

        $cache->invalidateForProduct($productId);
        $recomputed = $cache->get($productId, 'role_detail', fn (): ProductMediaView => $this->createView('second'));

        $this->assertSame('second', $recomputed->getDetailUrl());
    }

    public function testInvalidateForProductLeavesOtherProductsCached(): void
    {
        $cache = $this->createCache();
        $productA = Id::generate();
        $productB = Id::generate();
        $cache->get($productA, 'role_detail', fn (): ProductMediaView => $this->createView());
        $cache->get($productB, 'role_detail', fn (): ProductMediaView => $this->createView('cached'));

        $cache->invalidateForProduct($productA);

        $cached = $cache->get($productB, 'role_detail', fn () => $this->fail('product B must stay cached'));
        $this->assertSame('cached', $cached->getDetailUrl());
    }

    public function testInvalidateForProductRefreshesCachedEmptyCollection(): void
    {
        $cache = $this->createCache();
        $productId = Id::generate();
        $cache->getAll($productId, 'all_detail', fn (): array => []);

        $cache->invalidateForProduct($productId);
        $recomputed = $cache->getAll(
            $productId,
            'all_detail',
            fn (): array => [(string) Id::generate() => $this->createView()]
        );

        $this->assertCount(1, $recomputed);
    }

    public function testInvalidateAllForcesRecomputeForAllProducts(): void
    {
        $cache = $this->createCache();
        $productA = Id::generate();
        $productB = Id::generate();
        $cache->get($productA, 'role_detail', fn (): ProductMediaView => $this->createView('a-first'));
        $cache->get($productB, 'role_detail', fn (): ProductMediaView => $this->createView('b-first'));

        $cache->invalidateAll();

        $recomputedA = $cache->get($productA, 'role_detail', fn (): ProductMediaView => $this->createView('a-second'));
        $recomputedB = $cache->get($productB, 'role_detail', fn (): ProductMediaView => $this->createView('b-second'));
        $this->assertSame('a-second', $recomputedA->getDetailUrl());
        $this->assertSame('b-second', $recomputedB->getDetailUrl());
    }

    private function createCache(): ProductMediaViewCache
    {
        return new ProductMediaViewCache(
            new TagAwareAdapter(new ArrayAdapter()),
            lifetimeSeconds: 3600
        );
    }

    private function createView(string $detailUrl = 'detail-url'): ProductMediaView
    {
        return new ProductMediaView(
            detailUrl: $detailUrl,
            iconUrl: 'icon-url',
            zoomUrl: 'zoom-url',
            thumbnailUrl: 'thumbnail-url',
            attributes: new MediaAttributes(['alt' => 'alt text']),
        );
    }
}
