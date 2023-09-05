<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter;

use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\NamespaceInformationProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Routing\ShopControllerMapProvider;
use OxidEsales\Eshop\Core\Theme;
use OxidEsales\EshopCommunity\Application\Model\Shop;

class ShopAdapter implements ShopAdapterInterface
{
    public function translateString($string): string
    {
        return Registry::getLang()->translateString($string);
    }

    /**
     * @deprecated since v7.0.0 (2023-03-14).
     * Please use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface instead.
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
     * Please use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface instead.
     */
    public function invalidateModulesCache(): void
    {
        $utils = Registry::getUtils();
        $utils->resetLanguageCache();
        $utils->resetMenuCache();
        $utils->oxResetFileCache(true);

        ModuleVariablesLocator::resetModuleVariables();

        if (extension_loaded('apc') && ini_get('apc.enabled')) {
            apc_clear_cache();
        }
    }

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

    /**
     * Get active themes list.
     * Examples:
     *      if flow theme is active we will get ['flow']
     *      if azure is extended by some other we will get ['azure', 'extending_theme']
     *
     * @return array
     */
    public function getActiveThemesList(): array
    {
        $config = Registry::getConfig();

        $activeThemeList = [];
        if (!$config->isAdmin()) {
            $activeThemeList[] = $config->getConfigParam('sTheme');

            if ($customThemeId = $config->getConfigParam('sCustomTheme')) {
                $activeThemeList[] = $customThemeId;
            }
        }

        return $activeThemeList;
    }

    public function getCustomTheme(): string
    {
        return (string)Registry::getConfig()->getConfigParam('sCustomTheme');
    }

    public function getActiveThemeId(): string
    {
        $customTheme = Registry::getConfig()->getConfigParam('sCustomTheme');
        if ($customTheme) {
            return $customTheme;
        }

        return (string)Registry::getConfig()->getConfigParam('sTheme');
    }

    public function themeExists(string $themeId): bool
    {
        return oxNew(Theme::class)->load($themeId);
    }

    public function activateTheme(string $themeId): void
    {
        $theme = oxNew(Theme::class);
        $theme->load($themeId);
        $theme->activate();
    }
}
