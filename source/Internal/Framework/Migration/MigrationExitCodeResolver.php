<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

readonly class MigrationExitCodeResolver implements MigrationExitCodeResolverInterface
{
    private const HARD_FAILURE = 1;

    public function combine(int $first, int $second): int
    {
        if ($first === self::HARD_FAILURE || $second === self::HARD_FAILURE) {
            return self::HARD_FAILURE;
        }

        return max($first, $second);
    }
}
