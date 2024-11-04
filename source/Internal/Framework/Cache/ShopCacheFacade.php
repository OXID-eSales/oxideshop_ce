<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Adapter\TagAwareAdapterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\ShopTemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ShopCacheFacade implements ShopCacheCleanerInterface
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly TagAwareAdapterFactoryInterface $tagAwareAdapterFactory,
        private readonly ShopAdapterInterface $shopAdapter,
        private readonly ShopTemplateCacheServiceInterface $templateCacheService,
    ) {
    }

    public function clear(int $shopId): void
    {
        $this->shopAdapter->invalidateModulesCache();
        $this->templateCacheService->invalidateCache($shopId);
        $this->tagAwareAdapterFactory->create($shopId)->clear();
    }

    public function clearAll(): void
    {
        $this->shopAdapter->invalidateModulesCache();
        $this->templateCacheService->invalidateAllShopsCache();
        foreach ($this->context->getAllShopIds() as $shopId) {
            $this->tagAwareAdapterFactory->create($shopId)->clear();
        }
    }
}
