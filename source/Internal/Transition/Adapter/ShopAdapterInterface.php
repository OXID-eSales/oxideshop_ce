<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter;

interface ShopAdapterInterface
{
    public function translateString($string): string;

    public function invalidateModuleCache(string $moduleId);

    public function invalidateModulesCache();

    /**
     * @deprecated use Id::generate() instead
     */
    public function generateUniqueId(): string;

    public function getShopControllerClassMap(): array;

    public function isNamespace(string $namespace): bool;

    public function isShopUnifiedNamespace(string $namespace): bool;

    public function isShopEditionNamespace(string $namespace): bool;

    public function validateShopId(int $shopId): bool;

    /**
     * @deprecated
     */
    public function getActiveThemesList(): array;

    /**
     * @deprecated
     */
    public function getCustomTheme(): string;

    /**
     * @deprecated
     */
    public function getActiveThemeId(): string;

    /**
     * @deprecated
     */
    public function themeExists(string $themeId): bool;

    /**
     * @deprecated
     */
    public function activateTheme(string $themeId): void;

    public function generateDatabaseViewName(string $tableName, int $languageId, int $shopId): string;
}
