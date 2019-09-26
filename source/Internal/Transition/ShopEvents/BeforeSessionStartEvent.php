<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class BeforeSessionStartEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class BeforeSessionStartEvent extends Event
{
    const NAME = self::class;
}
