<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade;

interface ThemeSettingServiceInterface
{
    public function getInteger(string $name): int;
    public function getFloat(string $name): float;
    public function getString(string $name): string;
    public function getBoolean(string $name): bool;
    public function getCollection(string $name): array;

    public function exists(string $name): bool;
}
