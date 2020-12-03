<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\with_everything\Event;

use oxRegistry;

class MyEvents
{
    public static function onActivate()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('sTestActivateEvent', 'Activate');
    }

    public static function onDeactivate()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('sTestDeactivateEvent', 'Deactivate');
    }
}
