<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Cache\Event\ClearShopCacheEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class ShopCacheFacade implements ShopCacheCleanerInterface
{
    public function __construct(
        private ContextInterface $context,
        private TagAwareAdapterFactoryInterface $tagAwareAdapterFactory,
        private ShopTemplateCacheServiceInterface $templateCacheService,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function clear(int $shopId): void
    {
        $this->templateCacheService->invalidateCache($shopId);
        $this->tagAwareAdapterFactory->create($shopId)->clear();
        $this->clearApcCache();

        $this->eventDispatcher->dispatch(new ClearShopCacheEvent($shopId));
    }

    public function clearAll(): void
    {
        $this->templateCacheService->invalidateAllShopsCache();
        $this->clearApcCache();
        foreach ($this->context->getAllShopIds() as $shopId) {
            $this->tagAwareAdapterFactory->create($shopId)->clear();
            $this->eventDispatcher->dispatch(new ClearShopCacheEvent($shopId));
        }
    }

    private function clearApcCache(): void
    {
        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            apc_clear_cache();
        }
    }
}
