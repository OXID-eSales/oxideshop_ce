<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

/**
 * Class ListComponentAjaxExecuteCacheEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Events
 */
class ListComponentAjaxExecuteCacheEvent extends ExecuteCacheEvent
{
    const NAME = 'oxidesales.listcomponentajax.executeCache';

    /**
     * Handle event.
     *
     * @return null
     */
    public function handleEvent()
    {
    }
}
