<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Enum;

enum SubscriptionOptedInStatus: int
{
    case Disabled = 0;
    case Active = 1;
    case Pending = 2;
}
