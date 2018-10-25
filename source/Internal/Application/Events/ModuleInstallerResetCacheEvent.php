<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ModuleInstallerResetCacheEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Events
 */
class ModuleInstallerResetCacheEvent extends ResetCacheEvent
{
    const NAME = 'oxidesales.moduleinstaller.resetCache';

    /**
     * Handle event.
     *
     * @return null
     */
    public function handleEvent()
    {

    }
}
