<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_events\Event;

use oxRegistry;

class MyEvents
{
    public static function onActivate(): void
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('sTestActivateEvent', 'Activate');
    }

    public static function onDeactivate(): void
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('sTestDeactivateEvent', 'Deactivate');
    }
}
