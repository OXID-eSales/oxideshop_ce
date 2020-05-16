<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use Symfony\Contracts\EventDispatcher\Event;

class BeforeSessionStartEvent extends Event
{
    public const NAME = self::class;
}
