<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ModuleCleanupDoneEvent
 *
 * @deprecated in b-6.x (2019-01-23); Temporary event, remove before release.
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ModuleCleanupDoneEvent extends Event
{
    const NAME = self::class;
}
