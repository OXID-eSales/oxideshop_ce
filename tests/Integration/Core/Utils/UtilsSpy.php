<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Utils;

use OxidEsales\EshopCommunity\Core\Utils;

final class UtilsSpy extends Utils
{
    public function isAdmin(): bool
    {
        return true;
    }
}
