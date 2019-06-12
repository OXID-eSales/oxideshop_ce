<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty modifier
 * -------------------------------------------------------------
 * Name:     oxaddslashes<br>
 * Purpose:  Quote string with slashes
 * -------------------------------------------------------------
 *
 * @param string $string String to escape
 *
 * @return string
 */
function smarty_modifier_oxaddslashes($string)
{
    return addslashes($string);
}
