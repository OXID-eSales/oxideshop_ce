<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   smarty_plugins
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: modifier.oxlower.php 25466 2010-02-01 14:12:07Z alfonsas $
 */

/**
 * Include the {@link modifier.escape.php} plugin
 */
require_once $smarty->_get_plugin_filepath( 'modifier', 'escape' );

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
function smarty_modifier_oxescape( $sString, $sEscType = 'html', $sCharSet = null )
{
    $sCharSet = $sCharSet ? $sCharSet : oxRegistry::getConfig()->getActiveView()->getCharSet();
    return smarty_modifier_escape( $sString, $sEscType, $sCharSet );
}