<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

interface MigrationSqlOutputInterface
{
    public function resolveFile(array $options): ?string;

    public function prepare(string $file): bool;
}
