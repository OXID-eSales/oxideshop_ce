<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */


namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

class ShopModuleCacheService implements ModuleCacheServiceInterface
{

    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * ShopModuleCacheService constructor.
     *
     * @param ShopAdapterInterface $shopAdapter
     */
    public function __construct(ShopAdapterInterface $shopAdapter)
    {
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * Invalidate all module related cache items for a given module and a given shop
     *
     * @param string $moduleId
     * @param int    $shopId
     */
    public function invalidateModuleCache(string $moduleId, int $shopId)
    {
        $this->shopAdapter->invalidateModuleCache($moduleId);
    }
}
