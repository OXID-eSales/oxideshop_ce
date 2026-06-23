<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

interface ActiveThemeServiceInterface
{
    public function getActiveThemeId(): string;

    /**
     * Active theme inheritance chain, parent first, active (child) last.
     *
     * @return string[]
     */
    public function getActiveThemeChain(): array;

    /**
     * Merged active-theme settings (child overrides parent), keyed by setting name.
     */
    public function getSettings(): array;

    public function hasSetting(string $name): bool;

    public function getSettingValue(string $name): mixed;

    /**
     * Absolute source paths of the active theme chain, keyed by theme id (parent first, child last).
     *
     * @return array<string, string>
     */
    public function getActiveThemeSourcePaths(): array;
}
