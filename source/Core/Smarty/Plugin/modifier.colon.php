<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty colon modifier plugin
 *
 * Type:     modifier<br>
 * Name:     colon<br>
 * Date:     Mar 12 2013
 * Purpose:  Add simple or specific colon
 * Input:    string to add colon to
 * Example:  [{assign var="variable" value="TRANSLATION_INDENT"|oxmultilangassign|colon}]
 * TRANSLATION_INDENT = 'translation' COLON = ' :', $variable = 'translation :'
 *
 * @param string $string String to add colon to.
 *
 * @return string
 */
function smarty_modifier_colon($string)
{
    $colon = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('COLON');

    return $string . $colon;
}
