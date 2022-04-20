<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use Symfony\Contracts\EventDispatcher\Event;

class ApplicationExitEvent extends Event
{
    /**
     * @deprecated constant will be removed in v7.0.
     */
    const NAME = self::class;
}
