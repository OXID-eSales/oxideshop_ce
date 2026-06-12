<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Cache;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\DataObject\ProductMediaView;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Id;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class ProductMediaViewCache implements ProductMediaViewCacheInterface
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private int $lifetimeSeconds,
    ) {
    }

    public function get(Id $productId, string $viewIdentifier, callable $callback): ProductMediaView
    {
        return $this->cache->get(
            $this->getCacheKey($productId, $viewIdentifier),
            function (ItemInterface $item) use ($productId, $callback): ProductMediaView {
                $this->prepareItem($item, $productId);

                return $callback();
            }
        );
    }

    /** @return array<string, ProductMediaView> */
    public function getAll(Id $productId, string $viewIdentifier, callable $callback): array
    {
        return $this->cache->get(
            $this->getCacheKey($productId, $viewIdentifier),
            function (ItemInterface $item) use ($productId, $callback): array {
                $this->prepareItem($item, $productId);

                return $callback();
            }
        );
    }

    public function invalidateForProduct(Id $productId): void
    {
        $this->cache->invalidateTags([$this->getProductTag($productId)]);
    }

    public function invalidateAll(): void
    {
        $this->cache->invalidateTags([$this->getBaseTag()]);
    }

    private function prepareItem(ItemInterface $item, Id $productId): void
    {
        $item->expiresAfter($this->lifetimeSeconds);
        $item->tag([$this->getBaseTag(), $this->getProductTag($productId)]);
    }

    private function getCacheKey(Id $productId, string $viewIdentifier): string
    {
        return 'product_media_view_' . md5($productId . '_' . $viewIdentifier);
    }

    private function getProductTag(Id $productId): string
    {
        return $this->getBaseTag() . '.product.' . md5((string) $productId);
    }

    private function getBaseTag(): string
    {
        return 'oxid_esales.cache.product_media_view';
    }
}
