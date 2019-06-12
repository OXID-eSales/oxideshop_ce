<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Include the {@link modifier.escape.php} plugin
 */
require_once $smarty->_get_plugin_filepath('modifier', 'escape');

/**
 * Smarty escape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     escape<br>
 * Purpose:  Escape the string according to escapement type
 *
 * @param string $sString  string to escape
 * @param string $sEscType escape type "html|htmlall|url|quotes|hex|hexentity|javascript" [optional]
 * @param string $sCharSet charset [optional]
 *
 * @return string
 */
function smarty_modifier_oxescape($sString, $sEscType = 'html', $sCharSet = null)
{
    $sCharSet = $sCharSet ? $sCharSet : \OxidEsales\Eshop\Core\Registry::getConfig()->getActiveView()->getCharSet();
    return smarty_modifier_escape($sString, $sEscType, $sCharSet);
}
