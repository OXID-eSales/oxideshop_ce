<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class BeforeAppendSessionIdToUrlEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class BeforeAppendSessionIdToUrlEvent extends Event
{
    const NAME = self::class;
}
