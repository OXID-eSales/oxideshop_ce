<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

interface MigrationExitCodeResolverInterface
{
    public function combine(int $first, int $second): int;
}
