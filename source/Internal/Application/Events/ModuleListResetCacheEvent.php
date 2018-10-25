<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ResetCacheEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Events
 */
class ModuleListResetCacheEvent extends ResetCacheEvent
{
    const NAME = 'oxidesales.modulelist.resetCache';

    /**
     * Handle event.
     *
     * @return null
     */
    public function handleEvent()
    {

    }
}
