<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

/**
 * Class BaseControllerExecuteCacheEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Events
 */
class BaseControllerExecuteCacheEvent extends ExecuteCacheEvent
{
    const NAME = 'oxidesales.basecontroller.executeCache';

    /**
     * Handle event.
     *
     * @return null
     */
    public function handleEvent()
    {
    }
}
