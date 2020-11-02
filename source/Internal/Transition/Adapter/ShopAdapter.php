<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\NamespaceInformationProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider;
use OxidEsales\EshopCommunity\Application\Model\Shop;

class ShopAdapter implements ShopAdapterInterface
{
    /**
     * @param string $string
     */
    public function translateString($string): string
    {
        $lang = Registry::getLang();

        return $lang->translateString($string);
    }

    public function invalidateModuleCache(string $moduleId): void
    {
        /**
         * @TODO we have to implement it in ModuleCacheServiceInterface or use ModuleCache::resetCache() method.
         */
        $module = oxNew(Module::class);

        $templates = $module->getTemplates($moduleId);
        $utils = Registry::getUtils();
        $utils->resetTemplateCache($templates);
        $utils->resetLanguageCache();
        $utils->resetMenuCache();

        ModuleVariablesLocator::resetModuleVariables();

        if (\extension_loaded('apc') && ini_get('apc.enabled')) {
            apc_clear_cache();
        }
    }

    public function generateUniqueId(): string
    {
        return Registry::getUtilsObject()->generateUId();
    }

    public function getShopControllerClassMap(): array
    {
        $shopControllerMapProvider = oxNew(ShopControllerMapProvider::class);

        return $shopControllerMapProvider->getControllerMap();
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

    public function getSmartyInstance(): \Smarty
    {
        return Registry::getUtilsView()->getSmarty();
    }

    public function validateShopId(int $shopId): bool
    {
        $shopModel = oxNew(Shop::class);
        $shopModel->load($shopId);

        return $shopModel->isLoaded();
    }
}
