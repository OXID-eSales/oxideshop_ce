<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

interface ModuleCacheInterface
{
    public function deleteItem(string $key): void;

    public function put(string $key, array $data): void;

    public function get(string $key): array;

    public function exists(string $key): bool;
}
