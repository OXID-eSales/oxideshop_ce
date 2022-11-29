<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty modifier
 * -------------------------------------------------------------
 * Name:     smarty_modifier_oxformattime<br>
 * Purpose:  Converts integer (seconds) type value to time (hh:mm:ss) format
 * Example:  {$seconds|oxformattime}
 * -------------------------------------------------------------
 *
 * @param int $iSeconds timespan in seconds
 * @deprecated will be moved to the separate smarty component
 * @return string
 */
function smarty_modifier_oxformattime($iSeconds)
{
    $iHours = floor($iSeconds / 3600);
    $iMins  = floor($iSeconds % 3600 / 60);
    $iSecs  = $iSeconds % 60;

    return sprintf("%02d:%02d:%02d", $iHours, $iMins, $iSecs);
}

/* vim: set expandtab: */
