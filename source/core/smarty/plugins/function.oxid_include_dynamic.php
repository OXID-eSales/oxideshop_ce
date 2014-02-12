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
 * Purpose: render or leave dynamic parts with parameters in
 * templates used by content caching algorithm.
 * Use [{ oxid_include_dynamic file="..." }] instead of include
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxid_include_dynamic($params, &$smarty)
{
    $params = array_change_key_case($params, CASE_LOWER);

    if (!isset($params['file'])) {
        $smarty->trigger_error("oxid_include_dynamic: missing 'file' parameter");
        return;
    }

    if ( $smarty->_tpl_vars["_render4cache"] ) {
        $sContent = "<oxid_dynamic>";
        foreach ($params as $key => $val) {
            $sContent .= " $key='".base64_encode($val)."'";
        }
        $sContent .= "</oxid_dynamic>";
        return $sContent;
    } else {
        $sPrefix="_";
        if ( array_key_exists('type', $params) ) {
            $sPrefix.= $params['type']."_";
        }

        foreach ($params as $key => $val) {
            if ($key != 'type' && $key != 'file') {
                $sContent .= " $key='$val'";
                $smarty->assign($sPrefix.$key, $val);
            }
        }

        $smarty->assign("__oxid_include_dynamic", true);
        $sRes = $smarty->fetch($params['file']);
        $smarty->clear_assign("__oxid_include_dynamic");
        return $sRes;
    }
}
