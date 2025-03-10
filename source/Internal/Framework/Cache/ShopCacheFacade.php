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
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class ShopCacheFacade implements ShopCacheCleanerInterface
{
    public function __construct(
        private ContextInterface $context,
        private TagAwareAdapterFactoryInterface $tagAwareAdapterFactory,
        private ShopAdapterInterface $shopAdapter,
        private ShopTemplateCacheServiceInterface $templateCacheService,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function clear(int $shopId): void
    {
        $this->shopAdapter->invalidateModulesCache();
        $this->templateCacheService->invalidateCache($shopId);
        $this->tagAwareAdapterFactory->create($shopId)->clear();

        $this->eventDispatcher->dispatch(new ClearShopCacheEvent($shopId));
    }

    public function clearAll(): void
    {
        $this->shopAdapter->invalidateModulesCache();
        $this->templateCacheService->invalidateAllShopsCache();
        foreach ($this->context->getAllShopIds() as $shopId) {
            $this->tagAwareAdapterFactory->create($shopId)->clear();
            $this->eventDispatcher->dispatch(new ClearShopCacheEvent($shopId));
        }
    }
}
