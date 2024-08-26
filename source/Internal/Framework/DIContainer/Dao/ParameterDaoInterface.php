<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao;

interface ParameterDaoInterface
{
    public function add(string $name, array|bool|string|int|float|\UnitEnum|null $value, int $shopId): void;

    public function remove(string $name, int $shopId): void;

    public function has(string $name, int $shopId): bool;
}
