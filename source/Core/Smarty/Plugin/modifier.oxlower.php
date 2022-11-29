<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty lower modifier
 * -------------------------------------------------------------
 * Name:     lower<br>
 * Purpose:  convert string to lowercase
 * -------------------------------------------------------------
 *
 * @param string $sString String to lowercase
 * @deprecated will be moved to the separate smarty component
 * @return string
 */
function smarty_modifier_oxlower($sString)
{
    return getStr()->strtolower($sString);
}
