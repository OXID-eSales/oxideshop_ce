<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty modifier
 * -------------------------------------------------------------
 * Name:     oxfilesize<br>
 * Purpose:  {$var|oxfilesize} Convert integer file size to readable format
 * -------------------------------------------------------------
 *
 * @param int $iSize Integer size value
 * @deprecated will be moved to the separate smarty component
 * @return string
 */
function smarty_modifier_oxfilesize($iSize)
{
    if ($iSize < 1024) {
        return $iSize . " B";
    }

    $iSize = $iSize / 1024;

    if ($iSize < 1024) {
        return sprintf("%.1f KB", $iSize);
    }

    $iSize = $iSize / 1024;

    if ($iSize < 1024) {
        return sprintf("%.1f MB", $iSize);
    }

    $iSize = $iSize / 1024;

    return sprintf("%.1f GB", $iSize);
}
