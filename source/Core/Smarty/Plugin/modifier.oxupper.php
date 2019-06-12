<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty upper modifier
 * -------------------------------------------------------------
 * Name:     upper<br>
 * Purpose:  convert string to uppercase
 * -------------------------------------------------------------
 *
 * @param string $sString String to uppercase
 *
 * @return string
 */

function smarty_modifier_oxupper($sString)
{
    return getStr()->strtoupper($sString);
}
