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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: set params and render widget
 * Use [{oxid_include_dynamic file="..."}] instead of include
 * -------------------------------------------------------------
 *
 * @param array  $params  Params
 * @param Smarty $oSmarty Clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxid_include_widget($params, &$oSmarty)
{
    $class = isset($params['cl']) ? strtolower($params['cl']) : '';
    unset($params['cl']);

    $parentViews = null;
    if (!empty($params["_parent"])) {
        $parentViews = explode("|", $params["_parent"]);
        unset($params["_parent"]);
    }

    $widgetControl = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\WidgetControl::class);
    return $widgetControl->start($class, null, $params, $parentViews);
}
