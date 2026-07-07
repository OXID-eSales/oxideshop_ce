<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter;

use OxidEsales\Eshop\Core\NamespaceInformationProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\Eshop\Application\Model\Shop;

class ShopAdapter implements ShopAdapterInterface
{
    public function translateString($string): string
    {
        return Registry::getLang()->translateString($string);
    }

    /**
     * @deprecated since v7.0.0 (2023-03-14).
     * Please use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface instead.
     */
    public function invalidateModuleCache(string $moduleId): void
    {
        /**
         * @TODO we have to implement it in ModuleCacheServiceInterface or use ModuleCache::resetCache() method.
         */

        $this->invalidateModulesCache();
    }

    /**
     * @deprecated since v7.0.0 (2023-03-14).
     * Please use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface instead.
     */
    public function invalidateModulesCache(): void
    {
        $utils = Registry::getUtils();
        $utils->resetLanguageCache();
        $utils->resetMenuCache();
        $utils->oxResetFileCache(true);

        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            apc_clear_cache();
        }
    }

    /**
     * @deprecated use Id::generate() instead
     */
    public function generateUniqueId(): string
    {
        return Registry::getUtilsObject()->generateUId();
    }

    public function getShopControllerClassMap(): array
    {
        return oxNew(ShopControllerMapProvider::class)->getControllerMap();
    }

    public function isNamespace(string $namespace): bool
    {
        return NamespaceInformationProvider::isNamespacedClass($namespace);
    }

    public function isShopUnifiedNamespace(string $namespace): bool
    {
        return NamespaceInformationProvider::classBelongsToShopUnifiedNamespace($namespace);
    }

    public function isShopEditionNamespace(string $namespace): bool
    {
        return NamespaceInformationProvider::classBelongsToShopEditionNamespace($namespace);
    }

    public function validateShopId(int $shopId): bool
    {
        $shopModel = oxNew(Shop::class);
        $shopModel->load($shopId);
        return $shopModel->isLoaded();
    }

    public function generateDatabaseViewName(string $tableName, int $languageId, int $shopId): string
    {
        return oxNew(TableViewNameGenerator::class)->getViewName($tableName, $languageId, $shopId);
    }
}
