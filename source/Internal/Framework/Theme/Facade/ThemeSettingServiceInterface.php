<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;

interface ThemeSettingServiceInterface
{
    /** @throws ThemeSettingNotFoundException */
    public function getInteger(string $name): int;

    /** @throws ThemeSettingNotFoundException */
    public function getFloat(string $name): float;

    /** @throws ThemeSettingNotFoundException */
    public function getString(string $name): string;

    /** @throws ThemeSettingNotFoundException */
    public function getBoolean(string $name): bool;

    /** @throws ThemeSettingNotFoundException */
    public function getCollection(string $name): array;

    public function exists(string $name): bool;
}
