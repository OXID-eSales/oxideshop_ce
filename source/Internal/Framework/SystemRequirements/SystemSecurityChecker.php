<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\SystemRequirements;

use Exception;

use function random_bytes;

class SystemSecurityChecker implements SystemSecurityCheckerInterface
{
    /** @inheritdoc */
    public function isCryptographicallySecure(): bool
    {
        try {
            random_bytes(1);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
