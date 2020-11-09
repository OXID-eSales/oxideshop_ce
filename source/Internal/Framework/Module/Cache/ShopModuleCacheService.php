<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

class ShopModuleCacheService implements ModuleCacheServiceInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @var TemplateCacheServiceInterface
     */
    private $templateCacheService;

    /**
     * ShopModuleCacheService constructor.
     *
     * @param ShopAdapterInterface          $shopAdapter
     * @param TemplateCacheServiceInterface $templateCacheService
     */
    public function __construct(ShopAdapterInterface $shopAdapter, TemplateCacheServiceInterface $templateCacheService)
    {
        $this->shopAdapter = $shopAdapter;
        $this->templateCacheService = $templateCacheService;
    }

    /**
     * Invalidate all module related cache items for a given module and a given shop
     *
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidateModuleCache(string $moduleId, int $shopId)
    {
        $this->templateCacheService->invalidateTemplateCache();
        $this->shopAdapter->invalidateModuleCache($moduleId);
    }
}
