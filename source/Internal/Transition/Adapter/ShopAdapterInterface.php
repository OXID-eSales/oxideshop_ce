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

    public function generateUniqueId(): string;

    public function getShopControllerClassMap(): array;

    public function isNamespace(string $namespace): bool;

    public function isShopUnifiedNamespace(string $namespace): bool;

    public function isShopEditionNamespace(string $namespace): bool;

    public function validateShopId(int $shopId): bool;

    public function getActiveThemesList(): array;

    public function getCustomTheme(): string;

    public function getActiveThemeId(): string;

    public function themeExists(string $themeId): bool;

    public function activateTheme(string $themeId): void;
}
