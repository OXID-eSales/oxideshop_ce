<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class GenerationalTagAwareAdapter implements TagAwareAdapterInterface, TagAwareCacheInterface
{
    /**
     * @var array<string, TagAwareAdapter>
     */
    private array $adapters = [];

    public function __construct(
        private readonly CachePoolGenerationInterface $cachePoolGeneration,
        private readonly int $shopId,
    ) {
    }

    public function getItem(mixed $key): CacheItem
    {
        return $this->getAdapter()->getItem($key);
    }

    public function getItems(array $keys = []): iterable
    {
        return $this->getAdapter()->getItems($keys);
    }

    public function hasItem(string $key): bool
    {
        return $this->getAdapter()->hasItem($key);
    }

    public function clear(string $prefix = ''): bool
    {
        return $this->getAdapter()->clear($prefix);
    }

    public function deleteItem(string $key): bool
    {
        return $this->getAdapter()->deleteItem($key);
    }

    public function deleteItems(array $keys): bool
    {
        return $this->getAdapter()->deleteItems($keys);
    }

    public function save(CacheItemInterface $item): bool
    {
        return $this->getAdapter()->save($item);
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->getAdapter()->saveDeferred($item);
    }

    public function commit(): bool
    {
        return $this->getAdapter()->commit();
    }

    public function invalidateTags(array $tags): bool
    {
        return $this->getAdapter()->invalidateTags($tags);
    }

    public function get(string $key, callable $callback, ?float $beta = null, ?array &$metadata = null): mixed
    {
        return $this->getAdapter()->get($key, $callback, $beta, $metadata);
    }

    public function delete(string $key): bool
    {
        return $this->getAdapter()->delete($key);
    }

    private function getAdapter(): TagAwareAdapter
    {
        $directory = $this->cachePoolGeneration->getDirectory($this->shopId);

        return $this->adapters[$directory] ??= new TagAwareAdapter(
            new PhpFilesAdapter(namespace: 'items', directory: $directory),
            new FilesystemAdapter(namespace: 'tags', directory: $directory)
        );
    }
}
