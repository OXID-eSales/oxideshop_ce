<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
