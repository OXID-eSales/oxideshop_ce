<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestData\modules\with_everything\Event;

use OxidEsales\Eshop\Core\Registry;

class MyEvents
{
    public static function onActivate(): void
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sTestActivateEvent', 'Activate');
    }

    public static function onDeactivate(): void
    {
        $oConfig = Registry::getConfig();
        $oConfig->setConfigParam('sTestDeactivateEvent', 'Deactivate');
    }
}
