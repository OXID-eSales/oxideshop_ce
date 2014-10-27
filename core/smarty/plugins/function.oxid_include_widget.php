<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: set params and render widget
 * Use [{ oxid_include_dynamic file="..." }] instead of include
 * -------------------------------------------------------------
 *
 * @param array  $params   params
 * @param Smarty &$oSmarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxid_include_widget($params, &$oSmarty)
{
    $myConfig = oxRegistry::getConfig();
    $blNoScript = ($params['noscript']?$params['noscript']:false);
    $sClass     = strtolower($params['cl']);
    $params['cl'] = $sClass;
    $aParentViews = null;


    unset($params['cl']);

    $aParentViews = null;

    if ( !empty($params["_parent"]) ) {
        $aParentViews = explode("|", $params["_parent"]);
        unset( $params["_parent"] );
    }

    $oShopControl = oxRegistry::get('oxWidgetControl');

    return $oShopControl->start( $sClass, null, $params, $aParentViews );
}
